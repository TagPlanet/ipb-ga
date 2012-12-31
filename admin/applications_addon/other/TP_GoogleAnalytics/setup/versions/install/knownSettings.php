<?php
/*
 * TagPla.net Google Analytics Extension
 *
 * See if we can grab known settings while installing
 */
// Is GA already configured (e.g. was IP.SEO / 3.4 configured with GA)?
$tpga_existingSettings = IPSSetUp::getSavedData('ipseo_ga') ? IPSSetUp::getSavedData('ipseo_ga') : ipsRegistry::$settings['ipseo_ga'];

// Load up defaults
$knownSettings = array();

if( $tpga_existingSettings != '')
{
    // We did have something previously, let's sort through it to see what we can use by default
    
    // Does an account exist (it should!)
    /*
    if( preg_match( '~(?:UA|MO)-\d{4,10}-\d{1,3}~i' , $tpga_existingSettings , $matches ) )
    {
        // Push it into the known array
        $knownSettings['tpga_account'] = $matches[0];
    }
    */
    /*
    
    // Below is a list of methods that should be checked against for existing values
    $_availableMethods = array
    (
        '_addIgnoredOrganic' => array('default' => '', 'priority' => 25, 'type' => array( 'string' ) ),
        '_addIgnoredRef' => array('default' => '', 'priority' => 25, 'type' => array( 'string' ) ),
        '_addOrganic' => array('priority' => 25, 'type' => array( 'string', 'string', 'bool') ),
        '_anonymizeIp' => array( 'priority' => 5, 'type' => array( 'null' ) ),
        '_clearIgnoredOrganic' => array( 'priority' => 20, 'type' => array( 'null' ) ),
        '_clearIgnoredRef' => array( 'priority' => 20, 'type' => array( 'null' ) ),
        '_clearOrganic' => array( 'priority' => 20, 'type' => array( 'null' ) ),
        '_cookiePathCopy' => array( 'default' => '', 'priority' => 20, 'type' => array( 'string' ) ),
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
    );
    
    foreach( $_availableMethods as $method => $info )
    {
        
    }
    */
    
}