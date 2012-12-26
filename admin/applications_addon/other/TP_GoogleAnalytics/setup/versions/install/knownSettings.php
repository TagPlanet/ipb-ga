<?php
/*
 * TagPla.net Google Analytics Extension
 *
 * See if we can grab known settings while installing
 */
// Is GA already configured (e.g. was IP.SEO / 3.4 configured with GA)?
$tpga_existingSettings = IPSSetUp::getSavedData('ipseo_ga') ? IPSSetUp::getSavedData('ipseo_ga') : ipsRegistry::$settings['ipseo_ga'];

// Load up defaults
$tpga_account = '';

if( $tpga_existingSettings != '')
{
    // We did have something previously, let's sort through it to see what we can use by default
    
    // Does an account exist (it should!)
    if( preg_match( '~(?:UA|MO)-\d{4,10}-\d{1,3}~i' , $tpga_existingSettings , $matches ) )
    {
        $tpga_account = $matches[0];
    }
    
    // @TODO: Check for more values (any of the _set's)
}

// Setup the array with known settings
$knownSettings = array(
     'tpga_account'    => $tpga_account,
);