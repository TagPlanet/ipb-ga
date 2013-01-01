<?php

/**
 * (TP) Google Analytics: IP.Board Page Name Corrections
 * Allow us see better on what the page names are instead of rolling them up into index.php
 */

/**
 *  Action Overloader
 *   Fires a GA event upon post reply
 */
class TP_GoogleAnalyticsActionOverloaderTopics extends public_forums_post_post
{    
    
    public function showForm( $type )
    {
    
        // Verify our class is loaded
        if ( ipsRegistry::isClassLoaded( 'TP_GoogleAnalytics' ) )
        {
            // Figure out what type of form we have
            switch( $type ) 
            {
                case 'reply':
                    $action = 'new-reply';
                    $topic = $this->_postClass->getTopicData();
                    $fullURL = ipsRegistry::getClass('output')->buildSEOUrl( 'showtopic=' . $topic['tid'], 'public', $topic['title_seo'], 'showtopic');
                break;
                case 'new':
                    $action = 'new-topic';
                    $forum = $this->_postClass->getForumData();
                    $fullURL = ipsRegistry::getClass('output')->buildSEOUrl( 'showforum=' . $forum['id'], 'public', $forum['name_seo'], 'showforum');
                break;
                case 'edit':
                    $action = 'edit';
                    $topic = $this->_postClass->getTopicData();
                    $fullURL = ipsRegistry::getClass('output')->buildSEOUrl( 'showtopic=' . $topic['tid'], 'public', $topic['title_seo'], 'showtopic');
                break;
                default:
                    $fullURL = $action = '';
                break;
                
            }
            
            $pageName = $this->registry->TP_GoogleAnalytics->cleanPageName($fullURL, $action);
            
            if( $type == 'edit' )
            {
                $pageName .= '?p=' . $this->_postClass->getPostID();
            }
            
            $this->registry->TP_GoogleAnalytics->_trackPageview($pageName);
        }
        
        parent::showForm( $type );
    }
    
}