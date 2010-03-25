<?php
#### Added		pBaran		06/12/2007		Foliovision
# Change to config of kfm to load database information from wp-config.php (Wordpress config file)


$kfm_db_host='';
$kfm_db_name='';
$kfm_db_username='';
$kfm_db_password='';


function fvkfmGetDBInfo( $aMatches ){
   global $kfm_db_name, $kfm_db_username, $kfm_db_password, $kfm_db_host;

   if( 'DB_NAME' == $aMatches[1] ) $kfm_db_name = $aMatches[2];
   if( 'DB_USER' == $aMatches[1] ) $kfm_db_username = $aMatches[2];
   if( 'DB_PASSWORD' == $aMatches[1] ) $kfm_db_password = $aMatches[2];
   if( 'DB_HOST' == $aMatches[1] ) $kfm_db_host = $aMatches[2];

   return '';
}

function MyCheckForDBInfo( $strText ){
	global $kfm_db_name, $kfm_db_username, $kfm_db_password, $kfm_db_host;

	preg_replace_callback( '/define\((?:.*?)\'(DB_NAME)\'(?:.*?)\'(.*?)\'/', 'fvkfmGetDBInfo', $strText );
	preg_replace_callback( '/define\((?:.*?)\'(DB_USER)\'(?:.*?)\'(.*?)\'/', 'fvkfmGetDBInfo', $strText );
	preg_replace_callback( '/define\((?:.*?)\'(DB_PASSWORD)\'(?:.*?)\'(.*?)\'/', 'fvkfmGetDBInfo', $strText );
	preg_replace_callback( '/define\((?:.*?)\'(DB_HOST)\'(?:.*?)\'(.*?)\'/', 'fvkfmGetDBInfo', $strText );
}

function MyLoadWPConfig( $strPath ){
   if( !file_exists( $strPath ) ) return false;
	$strConf = file_get_contents( $strPath );

	MyCheckForDBInfo( $strConf );
}

?>