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

/**
 * editPostData Data Hook
 *   Fires a GA event upon post edit
 */
class TP_GoogleAnalyticsDataHookEditPostData 
{    
    public function handleData( $data ) 
    {
        // Verify our class is loaded
        if ( ipsRegistry::isClassLoaded( 'TP_GoogleAnalytics' ) )
        {
            $googleAnalytics = ipsRegistry::getClass( 'TP_GoogleAnalytics' );
            $googleAnalytics->_trackEvent('forums', 'topic', 'edit post');
        }
        
        // No data should be changed
        return;
    }
}

/**
 * messengerSendTopicData Data Hook
 *   Fires a GA event upon new PM
 */
class TP_GoogleAnalyticsDataHookMessengerSendTopicData
{    
    public function handleData( $data ) 
    {
        // Verify our class is loaded
        if ( ipsRegistry::isClassLoaded( 'TP_GoogleAnalytics' ) )
        {
            $googleAnalytics = ipsRegistry::getClass( 'TP_GoogleAnalytics' );
            $googleAnalytics->_trackEvent('members', 'private message', 'new message');
            $googleAnalytics->setAllDeferred();
        }
        
        // No data should be changed
        return;
    }
}

/**
 * messengerSendReplyData Data Hook
 *   Fires a GA event upon new PM
 */
class TP_GoogleAnalyticsDataHookMessengerSendReplyData
{    
    public function handleData( $data ) 
    {
        // Verify our class is loaded
        if ( ipsRegistry::isClassLoaded( 'TP_GoogleAnalytics' ) )
        {
            $googleAnalytics = ipsRegistry::getClass( 'TP_GoogleAnalytics' );
            $googleAnalytics->_trackEvent('members', 'private message', 'new reply');
        }
        
        // No data should be changed
        return;
    }
}

/**
 * statusUpdateNew Data Hook
 *   Fires a GA event upon new user Status
 */
class TP_GoogleAnalyticsDataHookStatusUpdateNew 
{    
    public function handleData( $data ) 
    {
        // Verify our class is loaded
        if ( ipsRegistry::isClassLoaded( 'TP_GoogleAnalytics' ) )
        {
            $googleAnalytics = ipsRegistry::getClass( 'TP_GoogleAnalytics' );
            $googleAnalytics->_trackEvent('members', 'status', 'new status');
        }
        
        // No data should be changed
        return;
    }
}

/**
 * statusCommentNew Data Hook
 *   Fires a GA event upon comment on a user status
 */
class TP_GoogleAnalyticsDataHookStatusCommentNew
{    
    public function handleData( $data ) 
    {
        // Verify our class is loaded
        if ( ipsRegistry::isClassLoaded( 'TP_GoogleAnalytics' ) )
        {
            $googleAnalytics = ipsRegistry::getClass( 'TP_GoogleAnalytics' );
            $googleAnalytics->_trackEvent('members', 'status', 'new comment');
        }
        
        // No data should be changed
        return;
    }
}