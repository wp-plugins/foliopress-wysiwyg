<?php
# see docs/license.txt for licensing

# user access details. all users may use get.php without logging in, but
#   if the following details are filled in, then login will be required
#   for the main KFM application
# for more details, see http://kfm.verens.com/security
$kfm_username='';
$kfm_password='';

#### Change		pBaran		06/12/2007		Foliovision
require_once( dirname(__FILE__).'/includes/load.wp.conf.php' );
MyLoadWPConfig( dirname(__FILE__).'/../../../../../../../wp-config.php' );
#### Addition	12/02/2010	let's check if db info was found
global $kfm_db_name;
if( trim($kfm_db_name) == '')	#	if not, let's try another location of wp-config file
	MyLoadWPConfig( dirname(__FILE__).'/../../../../../../../../wp-config.php' );
#### End of addition
#### End of change		pBaran		06/12/2007

#### Check if user is logged in
#### Modification	12/02/2010
require_once( dirname(__FILE__).'/../../../../../../../wp-load.php' );
#### End of modification
$current_user;
if(!$current_user->id)
    die('Access denied.');

# what type of database to use
$kfm_db_type='mysql'; # values allowed: mysql, pgsql, sqlite, sqlitepdo

# the following options should only be filled if you are not using sqlite/sqlitepdo as the database
$kfm_db_prefix='kfm_';
$kfm_db_port=''; # leave blank if using default port

# where are the files located on the hard-drive, relative to the website's root directory?
# In the default example, the user-files are at http://kfm.verens.com/sandbox/UserFiles/
# Note that this is the actual file-system location of the files.
# This value must begin and end in '/'.
$kfm_userfiles = '/images/';

# what should be added to the server's root URL to find the URL of the user files?
# Note that this is usually the same as $kfm_userfiles, but could be different in the case
#   that the server uses mod_rewrite or personal web-sites, etc
# Use the value 'get.php' if you want to use the KFM file handler script to manager file downloads.
# If you are not using get.php, this value must end in '/'.
# Examples:
#   $kfm_userfiles_output='http://thisdomain.com/files/';
#   $kfm_userfiles_output='/files/';
#   $kfm_userfiles_output='http://thisdomain.com/kfm/get.php';
#   $kfm_userfiles_output='/kfm/get.php';
$kfm_userfiles_output = '/images/';

# if you want to hide any panels, add them here as a comma-delimited string
# for example, $kfm_hidden_panels='logs,file_details,file_upload,search,directory_properties';
$kfm_hidden_panels='logs';

# what happens if someone double-clicks a file or presses enter on one? use 'return' for FCKeditor
$kfm_file_handler='return'; # values allowed: download, return

# if 'return' is chosen above, do you want to allow multiple file returns?
$kfm_allow_multiple_file_returns=true;

# directory in which KFM keeps its database and generated files
# if this starts with '/', then the address is absolute. otherwise, it is relative to $kfm_userfiles.
# $kfm_workdirectory='.files';
# $kfm_workdirectory='/home/kae/files_cache';
# warning: if you use the '/' method, then you must use the get.php method for $kfm_userfiles_output.
$kfm_workdirectory = 'tmp_thumbs';

# maximum length of filenames displayed. use 0 to turn this off, or enter the number of letters.
$kfm_files_name_length_displayed=20;

# 1 = users are allowed to delete directories
# 0 = users are not allowed to delete directories
$kfm_allow_directory_delete=1;

# 1 = users are allowed to edit directories
# 0 = users are not allowed to edit directories
$kfm_allow_directory_edit=1;

# 1 = users are allowed to move directories
# 0 = users are not allowed to move directories
$kfm_allow_directory_move=1;

# 1 = users are allowed to create directories
# 0 = user are not allowed create directories
$kfm_allow_directory_create=1;

# 1 = users are allowed to create files
# 0 = users are not allowed to create files
$kfm_allow_file_create=1;

# 1 = users are allowed to delete files
# 0 = users are not allowed to delete files
$kfm_allow_file_delete=1;

# 1 = users are allowed to edit files
# 0 = users are not allowed to edit files
$kfm_allow_file_edit=1;

# 1 = users are allowed to move files
# 0 = users are not allowed to move files
$kfm_allow_file_move=1;

# 1 = users are allowed to upload files
# 0 = user are not allowed upload files
$kfm_allow_file_upload=1;

# use this array to ban dangerous files from being uploaded.
$kfm_banned_extensions=array('asp','cfm','cgi','php','php3','php4','phtm','pl','sh','shtm','shtml');

# you can use regular expressions in this one.
# for exact matches, use lowercase.
# for regular expressions, use eithe '/' or '@' as the delimiter
$kfm_banned_files=array('thumbs.db','/^\./');

# this array tells KFM what extensions indicate files which may be edited online.
$kfm_editable_extensions=array('css','html','js','txt','xhtml','xml');

# this array tells KFM what extensions indicate files which may be viewed online.
# the contents of $kfm_editable_extensions will be added automatically.
$kfm_viewable_extensions=array('sql','php');

# 1 = users can only upload images
# 0 = don't restrict the types of uploadable file
$kfm_only_allow_image_upload=0;

# 0 = only errors will be logged
# 1 = everything will be logged
$kfm_log_level=0;

# use this array to show the order in which language files will be checked for
$kfm_preferred_languages=array('en','de','da','es','fr','nl','ga');

# themes are located in ./themes/
# to use a different theme, replace 'default' with the name of the theme's directory.
$kfm_theme='default';

# use ImageMagick's 'convert' program?
$kfm_use_imagemagick = false;

# where is the 'convert' program kept, if you have it installed?
$kfm_imagemagick_path='kfm/usr/bin/convert';

# show files in groups of 'n', where 'n' is a number (helps speed up files display - use low numbers for slow machines)
$kfm_show_files_in_groups_of=10;

# should disabled links be shown (but grayed out and unclickable), or completely hidden?
# you might use this if you want your users to not know what it is that's been disabled, for example.
$kfm_show_disabled_contextmenu_links=1;

# multiple file uploads are handled through the external SWFUpload flash application.
# this can cause difficulties on some systems, so if you have problems uploading, then disable this.
$kfm_use_multiple_file_upload=0;

# seconds between slides in a slideshow
$kfm_slideshow_delay=4;

# allow users to resize/rotate images
$kfm_allow_image_manipulation=1;

# set root folder name
$kfm_root_folder_name='root';

# if you are using a CMS and want to return the file's DB id instead of the URL, set this
$kfm_return_file_id_to_cms=0;

#Permissions for uploaded files.
$kfm_default_upload_permission = '666';

#Permissions for created directories.
$kfm_default_directory_permission = '777';

#Listview or icons
$kfm_listview = 0;

# how many files to attempt to draw at a time (use a low value for old client machines, and a higher value for newer machines)
$kfm_show_files_in_groups_of=10;

# we would like to keep track of installations, to see how many there are, and what versions are in use.
# if you do not want us to have this information, then set the following variable to '1'.
$kfm_dont_send_metrics=0;

/// Addition		pBaran		18/07/2008		Foliovision
$bTransformTrueColorToPalette = true;
$iTrueColorToPaletteLimit = 5000;
$iJPGQuality = 90;
/// End of addition		pBaran		18/07/2008

### Added		pBaran		10/12/2007		Foliovision
$kfm_special_thumbs_sizes = array( 400, 200, 150 );
$kfm_return_image_link = 1;
$kfm_link_lightbox = 1;
$custom_config_php = '';

require( dirname(__FILE__) . '/includes/foliovision-ini-parser.php' );

/// Change		pBaran		20/08/2008		Foliovision
function KFMLoadPHPConfig( $strHost, $strPath, $iPort = 80 ){
	$strHTTPReq = "GET $strPath HTTP/1.0\r\n";
	$strHTTPReq .= "Host: $strHost\r\n\r\n";
	
	$iErr = 0;
	$strErr = '';
	$strResponse = '';

	if( false !== ( $fs = @fsockopen( $strHost, $iPort, $iErr, $strErr, 20 ) ) && is_resource( $fs ) ){
		fwrite( $fs, $strHTTPReq );
		while( !feof( $fs ) ) $strResponse .= fgets( $fs, 1160 );
		fclose( $fs );

		$strText = explode( "\r\n\r\n", $strResponse, 2 );
		$strText = $strText[1];
	}
	
	return $strText;
}

// Loading of config file
function KFMLoadConfigIniFile( $strPath ){
	try{
		global $kfm_userfiles, $kfm_userfiles_output, $kfm_return_image_link, $kfm_link_lightbox, $kfm_special_thumbs_sizes, $custom_config_php;
		global $kfm_php_config, $iJPGQuality, $iTrueColorToPaletteLimit, $bTransformTrueColorToPalette;
		$objIni = new FVIniParser();
		$objIni->LoadIniFile( $strPath );
		
		$aKeys = array_keys( $objIni->aOptions );
		foreach( $aKeys as $strKey ) $$strKey = $objIni->aOptions[$strKey];
		
		if( isset( $custom_config_ini ) ){
			if( file_exists( dirname( __FILE__ ).$custom_config_ini ) && is_file( dirname( __FILE__ ).$custom_config_ini ) ) 
				$custom_config_ini = dirname( __FILE__ ).$custom_config_ini;
			if( file_exists( $custom_config_ini ) && is_file( $custom_config_ini ) ) KFMLoadConfigIniFile( $custom_config_ini );
		}

	}catch( Exception $ex ){}
}


KFMLoadConfigIniFile( dirname( __FILE__ ) . '/config.ini' );

if( $custom_config_php ){
	if( is_file( $custom_config_php ) ) $custom_config_php = realpath( $custom_config_php );
	if( is_file( dirname( __FILE__ ).'/'.$custom_config_php ) ) $custom_config_php = realpath( dirname( __FILE__ ).$custom_config_php );
	
	if( preg_match( '/http:\/\//', $custom_config_php ) ){
		$aURL = @parse_url( $custom_config_php );
		if( false !== $aURL ){
			$strHost = '';
			$strPath = '';
			if( isset( $aURL['host'] ) ) $strHost = $aURL['host'];
			if( isset( $aURL['path'] ) ) $strPath = $aURL['path'];
			$strConfig = KFMLoadPHPConfig( $strHost, $strPath );
		}
	}else{
		$strFile = realpath( $custom_config_php );
		if( file_exists( $strFile ) ){
			$strHost = $_SERVER['SERVER_NAME'];
			$strPath = str_replace( realpath( $_SERVER['DOCUMENT_ROOT'] ), '', $strFile );
			$strConfig = KFMLoadPHPConfig( $strHost, $strPath );
		}
	}
	
	$aOptions = explode( ';', $strConfig );
	foreach( $aOptions as $strOption ){
		$strOption = trim( $strOption );
		$iPos = strpos( $strOption, ':' );
		if( $strOption && $iPos ){
			$strVariable = trim( substr( $strOption, 0, $iPos ) );
			$strValue = trim( substr( $strOption, $iPos + 1 ) );
			$$strVariable = $strValue;
		}
	}
}

$iTrueColorToPaletteLimit = intval( $iTrueColorToPaletteLimit );
$iJPGQuality = intval( $iJPGQuality );

if( 255 >= $iTrueColorToPaletteLimit || 50000 < $iTrueColorToPaletteLimit ) $iTrueColorToPaletteLimit = 5000;
if( 0 >= $iJPGQuality || 100 < $iJPGQuality ) $iJPGQuality = 90;
if( is_string( $kfm_special_thumbs_sizes ) ){
	$kfm_special_thumbs_sizes = explode( ',', $kfm_special_thumbs_sizes );
	foreach( $kfm_special_thumbs_sizes as $strValue )
		if( is_string( $strValue ) ) $strValue = intval( trim( $strValue ) );
}
/// End of change		pBaran		20/08/2008

### Folders that will not be shown inside KFM. 400 and 150 are special folders used to store thumbnails of sizes 400 and 150 respectively
$kfm_banned_folders = array();
foreach( $kfm_special_thumbs_sizes as $iSize ) $kfm_banned_folders[] = sprintf( '%d', $iSize ); 
$kfm_banned_folders[] = $kfm_workdirectory;

### ### End of add		pBaran		10/12/2007

define('ERROR_LOG_LEVEL',1); # 0=none, 1=errors, 2=1+warnings, 3=2+notices, 4=3+unknown
//require_once(dirname(__FILE__).'/initialise.php');

?>