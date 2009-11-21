<?php

require_once( dirname( __FILE__ ).'/configuration.php' );

function KFMChangePermissions( $strDir ){
   if( !is_dir( $strDir ) ) return array();
   echo '<p>'.$strDir.'</p>';
	
	$aDirsFiles = scandir( $strDir );
	if( !$aDirsFiles ) return array();

	$aDirs = array();
	foreach( $aDirsFiles as $strName )
		if( '.' != substr( $strName, 0, 1 ) && is_dir( $strDir.'/'.$strName ) ) $aDirs[] = realpath( $strDir.'/'.$strName );

	foreach( $aDirs as $strFilePath ){
		if( is_file( $strFilePath ) ) chmod( $strFilePath, octdec( '0' . $kfm_default_upload_permission ) );
		elseif( is_dir( $strFilePath ) ){
         chmod( $strFilePath, octdec( '0' . $kfm_default_upload_permission ) );
         KFMChangePermissions( $strFilePath );
      }
	}

}

$strStartDir = $_SERVER['DOCUMENT_ROOT'] . '/images';

KFMChangePermissions( $strStartDir );

?>