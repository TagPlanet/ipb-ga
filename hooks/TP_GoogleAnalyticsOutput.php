<?php
session_start();

/**
 * (TP) Google Analytics
 * Allow us to include our libraries at all times
 */
class TP_GoogleAnalyticsOutput extends output
{
    /**
     * {@inherit}
     */
    public function __construct( ipsRegistry $registry, $initialize = FALSE )
    {
        // Load our Google Analytics class, to use in templates
        if ( ! ipsRegistry::isClassLoaded( 'googleAnalytics' ) )
        {
            $classToLoad = IPSLib::loadLibrary( IPSLib::getAppDir('TP_GoogleAnalytics') . '/sources/classes/TP_GoogleAnalytics.php' , 'TP_GoogleAnalytics_core' , 'core' );
            $registry->setClass( 'googleAnalytics' , new $classToLoad( $registry ) );
        }
        
        parent::__construct( $registry, $initialize );
    }
    
    /**
     * {@inherit}
     */
    public function sendOutput( $return = false )
    {
        // If we have our own GA code, hook into the IP.SEO (now integrated)
        //   code. If we don't have anything, leave it alone and let the 
        //   default IP.SEO code in. This could be from a user not configuring
        //   the Google Analytics in the extended section.
        $rendering = $this->registry->googleAnalytics->render();
        if( $rendering != '' )
            $this->settings['ipseo_ga'] = $rendering;
        
        return parent::sendOutput( $return );
    }
}