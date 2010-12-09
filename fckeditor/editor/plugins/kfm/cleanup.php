<?php

require_once( dirname( __FILE__ ).'/configuration.php' );
require_once( dirname( __FILE__ ).'/includes/image-non-class.php' );

function KFM_RecreateThumbnails( $strDir, $aSpecialThumbs, $strKFMWorkDir, $aOptions = array() ){
	return KFM_LoopDirs( $strDir, $aSpecialThumbs, $strKFMWorkDir, $aOptions, true, true );
}

function KFM_ReadRecreatableThumbnails( $strDir, $aSpecialThumbs, $strKFMWorkDir, $aOptions = array() ){
	return KFM_LoopDirs( $strDir, $aSpecialThumbs, $strKFMWorkDir, $aOptions, true, false );
}

function KFM_RecreateThumbnailsSilent( $strDir, $aSpecialThumbs, $strKFMWorkDir, $aOptions = array() ){
	return KFM_LoopDirs( $strDir, $aSpecialThumbs, $strKFMWorkDir, $aOptions, false, true );
}

function KFM_RecreateThumbs( $strDir, $aOptions = array(), $bEcho = true, $bRecreate = false ){
	if( !is_dir( $strDir ) ) return array();
	
	if( $bEcho ) echo '<li>'.$strDir.':<ul>';
	
	$aDirsFiles = scandir( $strDir );
	if( !$aDirsFiles ) return array();
	
	$aFiles = array();
	foreach( $aDirsFiles as $strName )
		if( '.' != substr( $strName, 0, 1 ) && is_file( $strDir.'/'.$strName ) ) $aFiles[] = $strName;

	$aRecreatedImages = array();
	$iWidth = intval( basename( $strDir ) );
	foreach( $aFiles as $strName ){
		if( file_exists( dirname( $strDir ).'/'.$strName ) ){
			if( $bEcho ) echo '<li>'.$strName.'</li>';
			if( $bRecreate ){
				$bRename = rename( $strDir.'/'.$strName, $strDir.'/'.$strName.'-temp' );
				$bCreated = FV_RecreateSpecialThumb( $iWidth, dirname( $strDir ).'/'.$strName, $strDir.'/'.$strName, $aOptions );
				if( $bRename && !$bCreated ) rename( $strDir.'/'.$strName.'-temp', $strDir.'/'.$strName );
				elseif( $bCreated ){
					@unlink( $strDir.'/'.$strName.'-temp' );
					$aRecreatedImages[] =  $strDir.'/'.$strName;
				}
			}
		}
	}
	
	if( $bEcho ) echo '</ul></li>';
	
	return $aRecreatedImages;
}

function KFM_DeleteThumbs( $strDir ){
	if( !is_dir( $strDir ) ) return;
	
	$aDirsFiles = scandir( $strDir );
	if( !$aDirsFiles ) return;
	
	$aDirs = array();
	$aFiles = array();
	foreach( $aDirsFiles as $strName ){
		if( '.' != substr( $strName, 0, 1 ) && is_dir( $strDir.'/'.$strName ) ) $aDirs[] = realpath( $strDir.'/'.$strName );
		if( '.' != substr( $strName, 0, 1 ) && is_file( $strDir.'/'.$strName ) ) $aFiles[] = realpath( $strDir.'/'.$strName );
	}
	
	foreach( $aFiles as $strFilePath ) @unlink( $strFilePath );
	foreach( $aDirs as $strDirPath ) KFM_DeleteThumbs( $strDirPath );
}

function KFM_LoopDirs( $strDir, $aSpecialThumbs, $strKFMWorkDir, $aOptions = array(), $bEcho = true, $bRecreate = false ){
	if( !is_dir( $strDir ) ) return array();
	
	$aDirsFiles = scandir( $strDir );
	if( !$aDirsFiles ) return array();

	$aDirs = array();
	foreach( $aDirsFiles as $strName )
		if( '.' != substr( $strName, 0, 1 ) && is_dir( $strDir.'/'.$strName ) ) $aDirs[] = realpath( $strDir.'/'.$strName );

	$aRecreatedImages = array();
	foreach( $aDirs as $strDirPath ){
		if( in_array( basename( $strDirPath ), $aSpecialThumbs ) ) 
			$aRecreatedImages = array_merge( $aRecreatedImages, KFM_RecreateThumbs( $strDirPath, $aOptions, $bEcho, $bRecreate ) );
		elseif( $strKFMWorkDir != basename( $strDirPath ) ){
			$aRecreatedImages = array_merge( $aRecreatedImages, KFM_LoopDirs( $strDirPath, $aSpecialThumbs, $strKFMWorkDir, $aOptions, $bEcho, $bRecreate ) );
		}elseif( $strKFMWorkDir == basename( $strDirPath ) ){
			if( $bEcho ) echo '<li>Deleting content of: '.$strDirPath.'</li>';
			if( $bRecreate ) KFM_DeleteThumbs( $strDirPath );
		}
	}

	return $aRecreatedImages;
}

?>