<?php
/**
 * Plugin Name: Digital Ideas Tracking Postback
 * Description: This plugin is a simple implementation of the postback functionality in Voluum - us
 * Version: 0.0.1
 * Author: Digital Ideas
 * Author URI: http://www.digitalideas.io
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
defined( 'DITRACKING_PUSHOVER_APP_KEY' ) or die( 'Please define DITRACKING_PUSHOVER_APP_KEY' );
defined( 'DITRACKING_PUSHOVER_USER_KEY' ) or die( 'Please define DITRACKING_PUSHOVER_USER_KEY' );

function divoluum_add_query_vars_filter( $vars ){
  $vars[] = "cid";
  return $vars;
}
add_filter( 'query_vars', 'divoluum_add_query_vars_filter' );

add_action('wp', 'divoluum_track', 1, 0);

function divoluum_track($result, $orderId) {
	if ( !is_admin() ) {
		$cidToStore = get_query_var('cid', NULL);
		if(!empty($cidToStore)) {
			setcookie("civoluum_cid", $cidToStore, time() + (86400 * 30), "/"); 
		}
		
		$doVoluumPostback = array_shift(get_post_custom_values('do_voluum_postback'));
		
		if($doVoluumPostback == 'YES') {
			if(isset($_COOKIE['civoluum_cid']) && (!empty($_COOKIE['civoluum_cid']))) {
				$cidToDoPostbackWith = $_COOKIE['civoluum_cid'];
				$query = http_build_query(array('cid' => $cidToDoPostbackWith));
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "http://c2shp.voluumtrk2.com/postback");
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(
						array(
							'cid' => $cidToDoPostbackWith
						)
					));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$returnInfo = curl_exec($ch);
				
				$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close ($ch);
				
				if($httpCode == 400) {
					curl_setopt_array($ch = curl_init(), array(
                      CURLOPT_URL => "https://api.pushover.net/1/messages.json",
                      CURLOPT_POSTFIELDS => array(
                        "token" => DITRACKING_PUSHOVER_APP_KEY,
                        "user" => DITRACKING_PUSHOVER_USER_KEY,
                        "message" => "Got a 400 error when doing postback with CID $cidToDoPostbackWith returned $$returnInfo on //$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
                      ),
                      CURLOPT_SAFE_UPLOAD => true,
                      CURLOPT_RETURNTRANSFER => true,
                    ));
                    curl_exec($ch);
                    curl_close($ch);
				}
			}
			else {
    			curl_setopt_array($ch = curl_init(), array(
                  CURLOPT_URL => "https://api.pushover.net/1/messages.json",
                  CURLOPT_POSTFIELDS => array(
                    "token" => DITRACKING_PUSHOVER_APP_KEY,
                    "user" => DITRACKING_PUSHOVER_USER_KEY,
                    "message" => "(Test) postback not set on //$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
                  ),
                  CURLOPT_SAFE_UPLOAD => true,
                  CURLOPT_RETURNTRANSFER => true,
                ));
                $return = curl_exec($ch);
                curl_close($ch);
			}
		}
	}
	return true;
}
