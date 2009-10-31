<?php

function kfm_SetFileDetailsCookie( $iState ){
	$bCookie = setcookie( 'kfm_file_details_state', $iState, time() + 60 * 60 * 24 * 90, '/' );
	return $bCookie;
}

if( isset( $_GET['state'] ) ) kfm_SetFileDetailsCookie( $_GET['state'] );

?>