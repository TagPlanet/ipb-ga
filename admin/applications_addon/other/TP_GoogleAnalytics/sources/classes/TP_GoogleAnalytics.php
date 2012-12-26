<?php

/**
 *
 *
 */
class TP_GoogleAnalytics_core
{
    /**
     * Account ID
     * @var string
     */
    protected $_account = false;
    
    /**
     * Prefix
     * @var string
     */
    protected $_prefix = '';

    /**
     * Should we render a URL string for mobile, or a JS block?
     * @var bool
     */
    protected $_renderMobile = false;

    /**
     * Type of quotes to use for values
     */
    const Q = "'";
    
    /**
     * Setting prefix
     */
    const P = 'tpga_';
    
    /**
     * Available options, pulled (May 4, 2012) from
     * https://developers.google.com/analytics/devguides/collection/gajs/methods/
     * @var array
     */
    protected $_availableOptions = array
    (
        '_addIgnoredOrganic',
        '_addIgnoredRef',
        '_addItem',
        '_addOrganic',
        '_addTrans',
        '_anonymizeIp',
        '_clearIgnoredOrganic',
        '_clearIgnoredRef',
        '_clearOrganic',
        '_cookiePathCopy',
        '_createTracker',
        '_deleteCustomVar',
        '_setAccount',
        '_setAllowAnchor',
        '_setAllowLinker',
        '_setCampContentKey',
        '_setCampMediumKey',
        '_setCampNOKey',
        '_setCampNameKey',
        '_setCampSourceKey',
        '_setCampTermKey',
        '_setCampaignCookieTimeout',
        '_setCampaignTrack',
        '_setClientInfo',
        '_setCookiePath',
        '_setCustomVar',
        '_setDetectFlash',
        '_setDetectTitle',
        '_setDomainName',
        '_setLocalGifPath',
        '_setLocalRemoteServerMode',
        '_setLocalServerMode',
        '_setReferrerOverride',
        '_setRemoteServerMode',
        '_setSampleRate',
        '_setSessionCookieTimeout',
        '_setSiteSpeedSampleRate',
        '_setVisitorCookieTimeout',
        '_trackEvent',
        '_trackPageview',
        '_trackSocial',
        '_trackTiming',
        '_trackTrans',
    );
    
    /**
     * Method data to be pushed into the _gaq object
     * @var array
     */
    protected $_data = array();

    /**
     * An array of all the methods called for _gaq
     * @var array
     */
    protected $_calledOptions = array();
    
    /**
     * 
     * @var bool
     */
    protected $_init = false;
    
    /**
     * 
     * 
     */
    public function __construct( ipsRegistry $registry )
    {
        // @TODO: Delete any unused shortcuts
		$this->registry   =  $registry;
		$this->DB         =  $this->registry->DB();
		$this->settings   =& $this->registry->fetchSettings();
		$this->request    =& $this->registry->fetchRequest();
		$this->lang       =  $this->registry->getClass('class_localization');
		$this->member     =  $this->registry->member();
		$this->memberData =& $this->registry->member()->fetchMemberData();
		$this->cache      =  $this->registry->cache();
		$this->caches     =& $this->registry->cache()->fetchCaches();
        
        // Initialize this bad boy
        $this->_init();
    }
    
    protected function _init( )
    {
        // Verify we're not initialized already
        if( $this->_init )
            return true;
        
        // Verify we have a working account ID
        $account = $this->_parseAccountID( $this->settings[self::P . 'account'] );
        if( ! $account )
        {
            $this->_debug('The account ID (' . IPSText::cleanPermString( $this->settings[self::P . 'account'] ) . ') does not match the correct format');
            return false;
        }
        
        // Prefix?
        if( $this->settings[self::P . 'prefix'] )
        {
            $this->_prefix = $this->settings[self::P . 'prefix'];
        }
        
        $this->_setAccount( $account );
        
        // These are all of the GA specific options we will allow to be set in the IPB admin interface
        $allowedOptions = array(
            self::P . 'domain' => '_setDomainName',
            self::P . 'anchor' => '_setAllowAnchor',
            self::P . 'linker' => '_setAllowLinker',
            self::P . 'campContent' => '_setCampContentKey',
            self::P . 'campMedium' => '_setCampMediumKey',
            self::P . 'campName' => '_setCampNameKey',
            self::P . 'campSource' => '_setCampSourceKey',
            self::P . 'campTerm' => '_setCampTermKey',
            self::P . 'campaignTimeout' => '_setCampaignCookieTimeout',
            self::P . 'sampleRate' => '_setSampleRate',
            self::P . 'sessionTimeout' => '_setSessionCookieTimeout',
            self::P . 'speedRate' => '_setSiteSpeedSampleRate',
            self::P . 'visitorTimeout' => '_setVisitorCookieTimeout',
            self::P . 'cookiePath' => '_setCookiePath',
        );
                
        // Loop through all the GA options we allow
        foreach( $allowedOptions as $option => $callback )
        {
            if( isset( $this->settings[$option] ) && $this->settings[$option] != '' )
            {
                $this->$callback( $this->settings[$option] );
            }
        }
        
        // Return all clear!
        $this->_init = true;
        return true;
    }   

    protected function _checkPermissions( )
    {
        // Verify this user is allowed to ping GA
        if( IPSMember::isInGroup( $this->memberData, explode( ',' , IPSText::cleanPermString( $this->settings[self::P . 'excludeGroups'] ) ), true ) )
        {
            $this->_debug('This user is in the excluded group, no GA code for you!');
            return false;
        }
        
        // All clear!
        return true;
    }
    
    /**
     * Cleans up and verifies the Google Analytics account ID
     * @param string $accountID
     * @return string
     */
    protected function _parseAccountID( $accountID )
    {
        if( $this->_account == false )
        {
            $account = false;
            if( preg_match( '~^(?:UA|MO)-\d{4,10}-\d{1,3}$~i' , $accountID ) )
            {
                $account = strtoupper( $accountID );
            }
            else
            {
                $this->_debug( 'Invalid Google Analytics account ID' , 'warning' );
            }
        }
        return $account;
    }

    /**
     * Magic Method for options
     * @param string $name
     * @param array  $arguments
     */
    public function __call( $name , $arguments )
    {
        if( $name[0] != '_' )
            $name = '_' . $name;
        if( in_array( $name , $this->_availableOptions ) )
        {
            // Clean up the debugging a bit
            if( is_array( $arguments ) )
                $this->_debug( 'Setting method "' . $name . '" with arguments: "' . implode( '", "' , $arguments ) . '"' );
            else
                $this->_debug( 'Setting method "' . $name . '" with argument: "' . $arguments .'"' );
            $this->_push( $name , $arguments );
            return true;
        }
        
        $this->_debug( 'Method "' . $name . '" does not exist and cannot be called' , 'warning' );
        return false;
    }

    /**
     * Push data into the array
     * @param string $variable
     * @param array  $arguments
     * @protected
     */
    protected function _push( $variable , $arguments )
    {
        $data = array_merge( array( $variable ) , $arguments );
        array_push( $this->_data, $data );
        $this->_calledOptions[] = $variable;
    }


    /**
     * Render and return the Google Analytics code
     * @return string (HTML)
     */
    public function render( )
    {
        // Verify we are initialized & have permissions
        if( ! $this->_init() || ! $this->_checkPermissions() )
            return '';
            
        // Check to see if we need to throw in the trackPageview call
        if( ! in_array( '_trackPageview' , $this->_calledOptions ) )
        {
            $this->_trackPageview();
        }
        
        // Get the prefix information
        if( $this->_prefix != '' )
        {
            if( strpos( $this->_prefix , '.' ) === false )
            {
                $this->_prefix .= '.';
            }
        }
        else
        {
            $this->_prefix = '';
        }

        // Start the JS string
        $js = '<script type="text/javascript">' . PHP_EOL;
        $js.= 'var _gaq = _gaq || [];' . PHP_EOL;
        foreach( $this->_data as $data )
        {
            // No prefixes for the first argument.
            $prefixed = false;
            
            // Clean up each item
            foreach( $data as $key => $item )
            {
                
                if( is_string( $item ) )
                {
                    $data[$key] = self::Q . ( ( ! $prefixed ) ? $this->_prefix : '' ) . preg_replace( '~(?<!\\\)' . self::Q . '~' , '\\' . ( ( $prefixed ) ? $this->_prefix : '' ) . self::Q , $item ) . self::Q;
                }
                else if( is_bool( $item ) )
                {
                    $data[$key] = ( $item ) ? 'true' : 'false';
                }
                
                $prefixed = true;
            }

            $js.= '_gaq.push([' . implode( ',' , $data ) . ']);' . PHP_EOL;
        }
        
        //Set the debug url?
        $url = $this->debug ? 'u/ga_debug.js' : 'ga.js';
            
        $js.= <<<EOJS
(function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/{$url}';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
// Google Analytics IPB Extension provided by TagPla.net
// Copyright 2012, TagPla.net & Philip Lawrence
</script>
EOJS;

        return $js;
    }
    
    /**
     * Output debugging, if enabled
     * @param string $message
     * @param string $level
     * @protected
     */
    protected function _debug( $message = '' , $level = 'info' )
    {
        IPSDebug::addMessage( 'GA ' . strtoupper( $level ) . ': ' . $message );
    }
    
    
}