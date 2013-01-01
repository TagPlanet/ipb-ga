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
     * Rendered flag
     * @var bool
     */
    public $rendered = false;

    /**
     * Type of quotes to use for values
     */
    const Q = "'";
    
    /**
     * Setting prefix
     */
    const P = 'tpga_';
    
    /**
     * Available options, updated Dec 27, 2012 from
     * https://developers.google.com/analytics/devguides/collection/gajs/methods/
     * @var array
     */
    protected $_availableMethods = array
    (
        '_addIgnoredOrganic' => array('default' => '', 'priority' => 25, 'type' => array( 'string' ) ),
        '_addIgnoredRef' => array('default' => '', 'priority' => 25, 'type' => array( 'string' ) ),
        '_addItem' => array('priority' => 90, 'type' => array( 'string', 'string', 'string', 'string', 'string', 'string' ) ),
        '_addOrganic' => array('priority' => 25, 'type' => array( 'string', 'string', 'bool') ),
        '_addTrans' => array('priority' => 91, 'type' => array( 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string' ) ),
        '_anonymizeIp' => array( 'priority' => 5, 'type' => array( 'null' ) ),
        '_clearIgnoredOrganic' => array( 'priority' => 20, 'type' => array( 'null' ) ),
        '_clearIgnoredRef' => array( 'priority' => 20, 'type' => array( 'null' ) ),
        '_clearOrganic' => array( 'priority' => 20, 'type' => array( 'null' ) ),
        '_cookiePathCopy' => array( 'default' => '', 'priority' => 20, 'type' => array( 'string' ) ),
        '_deleteCustomVar' => array( 'priority' => 50, 'type' => array( 'int' ) ),
        '_setAccount' => array( 'priority' => 1, 'type' => array( 'string' ) ),
        '_setAllowAnchor' => array( 'default' => false, 'priority' => 30, 'type' => array( 'bool' ) ),
        '_setAllowLinker' => array( 'default' => false, 'priority' => 30, 'type' => array( 'bool' ) ),
        '_setCampContentKey' => array( 'default' => '', 'priority' => 30, 'type' => array( 'string' ) ),
        '_setCampMediumKey' => array( 'default' => '', 'priority' => 30, 'type' => array( 'string' ) ),
        '_setCampNOKey' => array( 'default' => '', 'priority' => 30, 'type' => array( 'string' ) ),
        '_setCampNameKey' => array( 'default' => '', 'priority' => 30, 'type' => array( 'string' ) ),
        '_setCampSourceKey' => array( 'default' => '', 'priority' => 30, 'type' => array( 'string' ) ),
        '_setCampTermKey' => array( 'default' => '', 'priority' => 30, 'type' => array( 'string' ) ),
        '_setCampaignCookieTimeout' => array( 'priority' => 30, 'type' => array( 'int' ) ),
        '_setCampaignTrack' => array( 'default' => true, 'priority' => 30, 'type' => array( 'bool' ) ),
        '_setClientInfo' => array( 'default' => true, 'priority' => 30, 'type' => array( 'bool' ) ),
        '_setCookiePath' => array( 'default' => '/', 'priority' => 30, 'type' => array( 'string' ) ),
        '_setCustomVar' => array( 'priority' => 40, 'type' => array( 'int', 'string', 'string', 'int' ) ),
        '_setDetectFlash' => array( 'default' => true, 'priority' => 30, 'type' => array( 'bool' ) ),
        '_setDetectTitle' => array( 'default' => true, 'priority' => 30, 'type' => array( 'bool' ) ),
        '_setDomainName' => array( 'default' => '', 'priority' => 30, 'type' => array( 'string' ) ),
        '_setLocalGifPath' => array( 'default' => '', 'priority' => 30, 'type' => array( 'string' ) ),
        '_setLocalRemoteServerMode' => array( 'default' => '', 'priority' => 30, 'type' => array( 'null' ) ),
        '_setLocalServerMode' => array( 'default' => '', 'priority' => 30, 'type' => array( 'null' ) ),
        '_setReferrerOverride' => array( 'default' => '', 'priority' => 30, 'type' => array( 'string' ) ),
        '_setRemoteServerMode' => array( 'default' => '', 'priority' => 30, 'type' => array( 'null' ) ),
        '_setSampleRate' => array( 'default' => '', 'priority' => 30, 'type' => array( 'int' ) ),
        '_setSessionCookieTimeout' => array( 'default' => '', 'priority' => 30, 'type' => array( 'int' ) ),
        '_setSiteSpeedSampleRate' => array( 'default' => '', 'priority' => 30, 'type' => array( 'int' ) ),
        '_setVisitorCookieTimeout' => array( 'default' => '', 'priority' => 30, 'type' => array( 'int' ) ),
        '_trackEvent' => array( 'priority' => 95, 'type' => array( 'string', 'string', 'string', 'int', 'bool' ) ),
        '_trackPageview' => array( 'default' => '', 'priority' => 80, 'type' => array( 'string' ) ),
        '_trackSocial' => array( 'priority' => 95, 'type' => array( 'string', 'string', 'string', 'string' ) ),
        '_trackTiming' => array( 'priority' => 99, 'type' => array( 'string', 'string', 'int', 'string', 'int' ) ),
        '_trackTrans' => array( 'default' => '', 'priority' => 92, 'type' => array( 'null' ) ),
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
     * Defer
     * @var bool
     */
    public $defer = false;
    
    /**
     * Debug mode
     * @var bool
     */
    public static $debug = true;
    
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
        $this->_init = $this->_init();
    }
    
    protected function _init( )
    {
        // Verify we're not initialized already
        if( $this->_init )
            return true;
        
        // Check to see if we have space in the session for us already        
        if( ! isset( $_SESSION['tpga'] ) )
            $_SESSION['tpga'] = array();
        
        // Prefix?
        if( $this->settings[self::P . 'prefix'] )
        {
            $this->_prefix = $this->settings[self::P . 'prefix'];
        }
        
        // These are all of the GA specific options we will allow to be set in the IPB admin interface
        $allowedOptions = array(
            '_setAccount',
            '_setDomainName',
            '_setAllowAnchor',
            '_setAllowLinker',
            '_setCampContentKey',
            '_setCampMediumKey',
            '_setCampNameKey',
            '_setCampSourceKey',
            '_setCampTermKey',
            '_setCampaignCookieTimeout',
            '_setSampleRate',
            '_setSessionCookieTimeout',
            '_setSiteSpeedSampleRate',
            '_setVisitorCookieTimeout',
            '_setCookiePath',
        );
        
        // Loop through all the GA options we allow
        foreach( $allowedOptions as $option )
        {
            if( isset( $this->settings['tpga' . $option] ) && $this->settings['tpga' . $option] != '' )
            {
                $this->$option( $this->settings['tpga' . $option] );
            }
        }
        
        // Check to see if we have deferred
        $deferred = $this->getDeferred();
        
        // If we do, let's loop through them
        if( is_array( $deferred ) && count( $deferred) )
        {
            foreach($deferred as $priority => $deferredElements)
            {
                foreach($deferredElements as $element)
                {
                    // Finally, for each item, let's push it
                    $this->_push( $element[0], array_slice( $element, 1 ) );
                }
            }
            
            // Remove all deferred items
            $this->clearDeferred();
        }
        
        // Return all clear!
        return true;
    }   
    
    /**
     * hasDeferred
     * Check's for any deferred elements
     *
     * @return bool
     */
    public function hasDeferred( )
    {
        return ( isset( $_SESSION['tpga']['defer'] ) && count( $_SESSION['tpga']['defer'] ) );
    }
     
    /**
     * getDeferred
     * Retrieves any deferred elements
     *
     * @return array
     */
    public function getDeferred( )
    {
        if( $this->hasDeferred() )
        {
            return $_SESSION['tpga']['defer'];
        }
        return array();
    }
    
    /**
     * _setDeferred
     * Sets a deferred element
     * 
     * @param int $priority
     * @param array $data
     * @private
     */
    protected function _setDeferred( $priority, $data )
    {
        $_SESSION['tpga']['defer'][(int) $priority][] = $data;
    }   
    
    /**
     * setAllDeferred
     * Defers all remaining elements
     * 
     * @param int $priority
     * @param array $data
     */
    public function setAllDeferred( )
    {
        foreach( $this->_data as $priority => $dataSet )
        {
            foreach( $dataSet as $data )
            {
                $this->_setDeferred( $priority, $data );
            }
        }
        $this->_clearData();
    }  
     
    /**
     * clearDeferred
     * Clears any deferred elements
     */
    public function clearDeferred( )
    {
        $_SESSION['tpga']['defer'] = array();
    }
    
    /**
     * _clearData
     * Clears any data saved
     */
    protected function _clearData( )
    {
        $this->data = array( );
    }
    
    /**
     * checkPermissions
     * Checks to see if this user has permissions to see GA
     *
     * @return bool
     */
    public function checkPermissions( )
    {
        // Verify this user is allowed to ping GA
        if( IPSMember::isInGroup( $this->memberData, explode( ',' , IPSText::cleanPermString( $this->settings[self::P . 'excludeGroups'] ) ), true ) )
        {
            self::debug('This user is in the excluded group, no GA code for you!');
            return false;
        }
        
        // All clear!
        return true;
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
            // Clean all of the variables according to the type array
            foreach( $arguments as $key => $argument )
            {
                $arguments[$key] = $this->_cleanVar( $argument, $this->_availableMethods[$name]['type'][$key] );
            }
                
            // Check to see if we need to pass the arguments or not
            if( isset( $this->_availableMethods[$name]['default'] ) && count( $arguments ) == 1 && $this->_availableMethods[$name]['default'] == $arguments[0] )
            {
                // No arguments needed to be passed 
                return true;
            }
            
            // Clean up the debugging a bit
            if( is_array( $arguments ) && count( $arguments ) )
            {
                self::debug( 'Setting method "' . $name . '" with arguments: "' . implode( '", "' , $arguments ) . '"' );
            }
            else
            {
                self::debug( 'Setting method "' . $name . '" with no arguments' );
            }
            
            // Call the push function
            $this->_push( $name , $arguments );
            return true;
        }
        
        // No method? Shucks.
        self::debug( 'Method "' . $name . '" does not exist and cannot be called' , 'warning' );
        return false;
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
        
        if( $this->defer )
        {
            $this->setDeferred( $this->_availableMethods[$method]['priority'], $data );
        }
        else
        {
            // Push this into the data array to render, based off of the priority
            $this->_data[ $this->_availableMethods[$method]['priority'] ][ ] = $data;
            $this->_calledOptions[ ] = $method;
        }
    }


    /**
     * Render and return the Google Analytics code
     * @return string (HTML)
     */
    public function render( )
    {
        // Verify we are initialized & have permissions
        if( ! $this->_init() || ! $this->checkPermissions() || $this->rendered )
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
        $url = self::$debug ? 'u/ga_debug.js' : 'ga.js';
            
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
        $this->rendered = true;
        $this->_clearData();
        
        return $js;
    }
    
    /**
     * cleanPageName
     * Cleans up the URL passed in GA based off of an action
     */
    public function cleanPageName($fullURL, $action)
    {
        // Check to see if we have the domain in the URL (likely)
        if(strpos($fullURL, $this->settings['board_url']) === 0)
        {
            // Remove it
            $fullURL = substr( $fullURL, strlen( $this->settings['board_url'] ) );
        }
        
        // Do we have a trailing slash already?
        if(substr($fullURL, -1, 1) !== '/')
        {
            // Not yet, add it
            $fullURL = $fullURL . '/';
        }
        
        // Append the action and return the url
        return ( $fullURL . $action );
    }
    
    /**
     * _cleanVar
     * Cleans up the variables passed to us for methods
     * @param mixed $var
     * @param string $type
     * @return mixed
     * @protected
     */
    protected function _cleanVar($var, $type)
    {
        // What type of variable are we?
        switch( $type )
        {            
            case 'bool':
                $var = (bool) $var;
            break;
            
            case 'int':
                $var = (int) $var;
            break;
            
            case 'null':
                $var = null;
            break;
            
            case 'string':
            default:
                // This could be overpowering & not needed... a simple addslashes might do the trick in the end.
                $var = IPSText::htmlspecialchars( $var );
            break;
        }
        // Return the better looking variable
        return $var;
    }
    
    /**
     * Output debugging, if enabled
     * @param string $message
     * @param string $level
     */
    protected static function debug( $message = '' , $level = 'info' )
    {
        if( self::$debug )
            IPSDebug::addMessage( 'GA ' . strtoupper( $level ) . ': ' . $message );
    }
    
    
}