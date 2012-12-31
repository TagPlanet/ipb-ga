<?php

/**
 * (TP) Google Analytics: IP.Board Default Events
 * Allow us to include our default data hooks for IP.Board
 */

/**
 * postAddReply Data Hook
 *   Fires a GA event upon post reply
 */
class TP_GoogleAnalyticsDataHookPostAddReply 
{    
    public function handleData( $data ) 
    {
        // Verify our class is loaded
        if ( ipsRegistry::isClassLoaded( 'TP_GoogleAnalytics' ) )
        {
            $googleAnalytics = ipsRegistry::getClass( 'TP_GoogleAnalytics' );
            $googleAnalytics->_trackEvent('forums', 'topic', 'new reply');
        }
        
        // No data should be changed
        return;
    }
}

/**
 * postAddTopic Data Hook
 *   Fires a GA event upon post reply
 */
class TP_GoogleAnalyticsDataHookPostAddTopic 
{    
    public function handleData( $data ) 
    {
        // Verify our class is loaded
        if ( ipsRegistry::isClassLoaded( 'TP_GoogleAnalytics' ) )
        {
            $googleAnalytics = ipsRegistry::getClass( 'TP_GoogleAnalytics' );
            $googleAnalytics->_trackEvent('forums', 'topic', 'new topic');
        }
        
        // No data should be changed
        return;
    }
}