<?php

function mykfm_FileExistsUnderDirTree( $strName, $strRoot, $aBannedFolders ){
	$aFiles = scandir( $strRoot );
	if( $aFiles == false ) return false;
	
	foreach( $aFiles as $strFile ){
		if( is_dir( $strRoot . '/' . $strFile ) && !in_array( $strFile, $aBannedFolders, false ) ){
			$bRet = false;
			if( strcasecmp( $strFile, "." ) != 0 && strcasecmp( $strFile, ".." ) != 0 ) $bRet = mykfm_FileExistsUnderDirTree( $strName, $strRoot . '/' . $strFile );
			if( $bRet == true ) return true;
		}else{
			if( strcasecmp( $strName, $strFile ) == 0 ) return true;
		}
	}
	
	return false;
}

function mykfm_CreateTempNameForFile( $strName, $strPath ){
	$i = 1;
	while( file_exists( $strPath . "\\" . $strName . $i ) ){
		$i++;
	}
	$strFile = $strName . $i;
	return $strFile;
}

?>