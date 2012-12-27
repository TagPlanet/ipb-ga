<?php
session_start();

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
     * Rendered flag
     * @var bool
     */
    protected $_rendered = false;

    /**
     * Type of quotes to use for values
     */
    const Q = "'";
    
    /**
     * Setting prefix
     */
    const P = 'tpga_';
    
    /**
     * Available options, pulled (Dec 27, 2012) from
     * https://developers.google.com/analytics/devguides/collection/gajs/methods/
     * @var array
     */
    protected $_availableMethods = array
    (
        '_addIgnoredOrganic' => array('default' => '', 'priority' => 25, 'type' => 'string'),
        '_addIgnoredRef' => array('default' => '', 'priority' => 25, 'type' => 'string'),
        '_addItem' => array('priority' => 90, 'type' => 'array'),
        '_addOrganic' => array('priority' => 25, 'type' => 'array'),
        '_addTrans' => array('priority' => 91, 'type' => 'array'),
        '_anonymizeIp' => array('priority' => 5, 'type' => 'null'),
        '_clearIgnoredOrganic' => array('priority' => 20, 'type' => 'null'),
        '_clearIgnoredRef' => array('priority' => 20, 'type' => 'null'),
        '_clearOrganic' => array('priority' => 20, 'type' => 'null'),
        '_cookiePathCopy' => array('default' => '', 'priority' => 20, 'type' => 'string'),
        '_deleteCustomVar' => array('priority' => 50, 'type' => 'int'),
        '_setAccount' => array('priority' => 1, 'type' => 'string'),
        '_setAllowAnchor' => array('default' => false, 'priority' => 30, 'type' => 'bool'),
        '_setAllowLinker' => array('default' => false, 'priority' => 30, 'type' => 'bool'),
        '_setCampContentKey' => array('default' => '', 'priority' => 30, 'type' => 'string'),
        '_setCampMediumKey' => array('default' => '', 'priority' => 30, 'type' => 'string'),
        '_setCampNOKey' => array('default' => '', 'priority' => 30, 'type' => 'string'),
        '_setCampNameKey' => array('default' => '', 'priority' => 30, 'type' => 'string'),
        '_setCampSourceKey' => array('default' => '', 'priority' => 30, 'type' => 'string'),
        '_setCampTermKey' => array('default' => '', 'priority' => 30, 'type' => 'string'),
        '_setCampaignCookieTimeout' => array('priority' => 30, 'type' => 'int'),
        '_setCampaignTrack' => array('default' => true, 'priority' => 30, 'type' => 'bool'),
        '_setClientInfo' => array('default' => true, 'priority' => 30, 'type' => 'bool'),
        '_setCookiePath' => array('default' => '/', 'priority' => 30, 'type' => 'string'),
        '_setCustomVar' => array('priority' => 40, 'type' => 'array'),
        '_setDetectFlash' => array('default' => true, 'priority' => 30, 'type' => 'bool'),
        '_setDetectTitle' => array('default' => true, 'priority' => 30, 'type' => 'bool'),
        '_setDomainName' => array('default' => '', 'priority' => 30, 'type' => 'string'),
        '_setLocalGifPath' => array('default' => '', 'priority' => 30, 'type' => 'string'),
        '_setLocalRemoteServerMode' => array('default' => '', 'priority' => 30, 'type' => 'null'),
        '_setLocalServerMode' => array('default' => '', 'priority' => 30, 'type' => 'null'),
        '_setReferrerOverride' => array('default' => '', 'priority' => 30, 'type' => 'string'),
        '_setRemoteServerMode' => array('default' => '', 'priority' => 30, 'type' => 'null'),
        '_setSampleRate' => array('default' => '', 'priority' => 30, 'type' => 'int'),
        '_setSessionCookieTimeout' => array('default' => '', 'priority' => 30, 'type' => 'int'),
        '_setSiteSpeedSampleRate' => array('default' => '', 'priority' => 30, 'type' => 'int'),
        '_setVisitorCookieTimeout' => array('default' => '', 'priority' => 30, 'type' => 'int'),
        '_trackEvent' => array('priority' => 95, 'type' => 'array'),
        '_trackPageview' => array('default' => '', 'priority' => 80, 'type' => 'string'),
        '_trackSocial' => array('priority' => 95, 'type' => 'array'),
        '_trackTiming' => array('priority' => 99, 'type' => 'array'),
        '_trackTrans' => array('default' => '', 'priority' => 92, 'type' => 'null'),
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
        
        // Check to see if we have space in the session for us already
        if( ! isset( $_SESSION['tpga'] ) )
            $_SESSION['tpga'] = array();
        
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
        // Verify we have the correct naming schema (_[method])
        if( $name[0] != '_' )
        {
            $name = '_' . $name;
        }
        
        // Check to see if this is an allowed method
        if( isset( $this->_availableMethods[$name] ) )
        {
            // This method exists, let's check the values to see if we need to clean them up a bit.
            switch( $this->_availableMethods[$name]['type'] )
            {
                case 'array':
                    // @TODO: clean up the data
                break;
                
                case 'bool':
                    $arguments = (bool) $arguments;
                break;
                
                case 'int':
                    $arguments = (int) $arguments;
                break;
                
                case 'string':
                default:
                    $arguments = IPSText::htmlspecialchars( $arguments );
                break;
            }
            
            // Check to see if we need to pass the arguments or not
            if( $this->_availableMethods[$name]['type'] != ' array' && isset( $this->_availableMethods[$name]['default'] ) && $this->_availableMethods[$name]['default'] == $arguments )
            {
                // No arguments needed to be passed 
                $arguments = array();
            }
            else if( ! is_array( $arguments ) )
            {
                $arguments = array( $arguments );
            }
            
            // Clean up the debugging a bit
            if( is_array( $arguments ) && count( $arguments ) )
            {
                $this->_debug( 'Setting method "' . $name . '" with arguments: "' . implode( '", "' , $arguments ) . '"' );
            }
            else
            {
                $this->_debug( 'Setting method "' . $name . '" with no arguments' );
            }
            
            // Call the push function
            $this->_push( $name , $arguments );
            return true;
        }
        
        // No method? Shucks.
        $this->_debug( 'Method "' . $name . '" does not exist and cannot be called' , 'warning' );
        return false;
    }
    
    /**
     * Destuctor
     * Checks to see if we need to defer loading or not
     */
    public function __destruct( )
    {
        // Did we render on this load?
        if( ! $this->_rendered )
        {
            $_SESSION['tpga']['defer'] = 'foobar';
            // Store stuff needing to be deferred here.
        }
    }

    /**
     * Push data into the array
     * @param string $method
     * @param array  $arguments
     * @protected
     */
    protected function _push( $method , $arguments )
    {
        // Merge in the data method / arguements
        $data = array_merge( array( $method ) , $arguments );
        
        // Push this into the data array to render, based off of the priority
        $this->_data[ $this->_availableMethods[$method]['priority'] ][ ] = $data;
        $this->_calledOptions[ ] = $method;
    }


    /**
     * Render and return the Google Analytics code
     * @return string (HTML)
     */
    public function render( )
    {
        // Verify we are initialized & have permissions
        if( ! $this->_init() || ! $this->_checkPermissions() || $this->rendered )
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
        
        // Sort the data array
        ksort($this->_data, SORT_NUMERIC);
        
        // Loop through the data, ordered by priority now
        foreach( $this->_data as $priority => $set )
        {
            foreach( $set as $data )
            {
                // No prefixes for the first argument.
                $prefixed = false;
                
                // Clean up each item
                foreach( $data as $method => $item )
                {
                    if( is_string( $item ) )
                    {
                        $data[$method] = self::Q . ( ( ! $prefixed ) ? $this->_prefix : '' ) . $item  . self::Q;
                    }
                    else if( is_bool( $item ) )
                    {
                        $data[$method] = ( $item ) ? 'true' : 'false';
                    }
                    else
                    {
                        // nada
                    }
                    
                    $prefixed = true;
                }
                
                // Push the final info into the JS variable
                $js.= '_gaq.push([' . implode( ', ' , $data ) . ']);' . PHP_EOL;
            }
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
        
        // Show that we've rendered
        $this->_rendered = true;
        
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