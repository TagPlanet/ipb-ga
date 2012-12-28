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
        if ( ipsRegistry::isClassLoaded( 'googleAnalytics' ) )
        {
            // Setup shortcut
            $this->_googleAnalytics = ipsRegistry::getClass( 'googleAnalytics' );
            
            // Check to see if it is ajax or not
            if( ipsRegistry::$current_module != 'ajax' )
            {
                // Defer until next page load since we will redirect here shortly
                $this->_googleAnalytics->defer = true;
            }
            
            // Send the call
            $this->_googleAnalytics->_trackEvent('forums', 'topic', 'new reply');
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
        if ( ipsRegistry::isClassLoaded( 'googleAnalytics' ) )
        {
            // Setup shortcut
            $this->_googleAnalytics = ipsRegistry::getClass( 'googleAnalytics' );
            
            // Check to see if it is ajax or not
            if( ipsRegistry::$current_module != 'ajax' )
            {
                // Defer until next page load since we will redirect here shortly
                $this->_googleAnalytics->defer = true;
            }
            
            // Send the call
            $this->_googleAnalytics->_trackEvent('forums', 'topic', 'new topic');
        }
        
        // No data should be changed
        return;
    }
}