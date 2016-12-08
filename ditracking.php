<?php
/**
 * Plugin Name: Digital Ideas Tracking Postback
 * Description: This plugin is a simple implementation of the postback functionality in Voluum - us
 * Version: 0.0.1
 * Author: Digital Ideas
 * Author URI: http://www.digitalideas.io
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function divoluum_add_query_vars_filter( $vars ){
  $vars[] = "cid";
  return $vars;
}
add_filter( 'query_vars', 'divoluum_add_query_vars_filter' );

add_action('wp', 'divoluum_track', 1, 0);

function divoluum_track($result, $orderId) {
	if ( !is_admin() ) {
		$cid = get_query_var('cid', NULL);
		if(!empty($cid)) {
			setcookie("civoluum_cid", $cid, time() + (86400 * 30), "/"); 
		}
		
		
		/*
			
			$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,"http://www.mysite.com/tester.phtml");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,
            "postvar1=value1&postvar2=value2&postvar3=value3");

// in real life you should use something like:
// curl_setopt($ch, CURLOPT_POSTFIELDS, 
//          http_build_query(array('postvar1' => 'value1')));

// receive server response ...
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec ($ch);

curl_close ($ch);

// further processing ....
if ($server_output == "OK") { ... } else { ... }

*/
		
		//$voluumProductIdToDoPostbackFor = intval(array_shift(get_post_custom_values('voluumPostbackProductId')));
	}
	return true;
}
