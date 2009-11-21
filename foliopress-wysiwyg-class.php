<?php 
/**
 * Foliopress WYSIWYG class file
 *
 * Main class that handles all implementation of plugin into WordPress. All WordPress actions and filters are handled here
 *  
 * @author Foliovision s.r.o. <info@foliovision.com>
 * @version 0.9.7
 * @package foliopress-wysiwyg
 */

/**
 * Including wordpress
 */ 
require_once( realpath( dirname(__FILE__) . '/../../../wp-config.php' ) );
/**
 * Some basic functions for this class to work
 */
require_once( 'include/foliopress-wysiwyg-load.php' );

if( isset( $_POST['recreate_submit'] ) ){
	require_once( dirname(__FILE__).'/fckeditor/editor/plugins/kfm/cleanup.php' );
}
/**
 * Main Foliopress WYSIWYG class
 *
 * Main class that handles all implementation of plugin into WordPress. All WordPress actions and filters are handled in it
 *
 * @author Foliovision s.r.o. <info@foliovision.com>
 */
class fp_wysiwyg_class{

///  --------------------------------------------------------------------------------------------------------------------
///  --------------------------------------------------   Properties   --------------------------------------------------
///  --------------------------------------------------------------------------------------------------------------------

	/**
	 * Correctly formated url to blog
	 * @var string
	 */
	var $strSiteUrl = "";
	/**
	 * Correctly formated url to fckeditor installed in here
	 * @var string
	 */
	var $strFCKEditorPath = "";
	/**
	 * Correctly formated url to this plugin
	 * @var string
	 */
	var $strPluginPath = "";
	/**
	 * Height of FCKEditor
	 * @var integer
	 */
	var $iEditorSize = 240;
	/**
	 * Plugin version
	 * @var string
	 */
	var $strVersion = '0.9';
	/**
	 * Custom options array.
	 * Array of options that are stored in database:
	 * - 'images' {@link fp_wysiwyg_class::FVC_IMAGES related constant} : Relative path to images folder on server from document root
	 * - 'FCKToolbar' {@link fp_wysiwyg_class::FVC_TOOLBAR related constant} : Currently used toolbar in FCKEditor
	 * - 'FCKSkin' {@link fp_wysiwyg_class::FVC_SKIN related constant} : Currently used skin in FCKEditor
	 * - 'FCKWidth' {@link fp_wysiwyg_class::FVC_WIDTH related constant} : Width of FCKEditor text area
	 * - 'KFMLink' {@link fp_wysiwyg_class::FVC_KFM_LINK related constant} : Tells KFM to wrap sended images in link to pure original image
	 * - 'KFMLightbox' {@link fp_wysiwyg_class::FVC_KFM_LIGHTBOX related constant} : Tells KFM to add 'rel="lightbox"' to link, to lunch lightbox
	 * - 'KFMThumbs' {@link fp_wysiwyg_class::FVC_KFM_THUMBS related constant} : Array of thumbnail sizes for KFM
	 * - 'FPCTexts' {@link fp_wysiwyg_class::FVC_FPC_TEXTS related constant} : Array of FP-Regex texts which should not be enclosed with <p> or <div> tag that FCK puts everywhere
	 *
	 * @var array
	 */
	var $aOptions = array();
	/**
	 * Stores if Rich text editing is turned on for current user
	 * @var bool
	 */
	var $bUseFCK = false;





///  -------------------------------------------------------------------------------------------------------------------
///  --------------------------------------------------   Constants   --------------------------------------------------
///  -------------------------------------------------------------------------------------------------------------------
	
	/**
	 * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
	 */
	const FVC_IMAGES = 'images';
	/**
	 * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
	 */
	const FVC_TOOLBAR = 'FCKToolbar';
	/**
	 * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
	 */
	const FVC_SKIN = 'FCKSkin';
	/**
	 * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
	 */
	const FVC_WIDTH = 'FCKWidth';
	/**
	 * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
	 */
	const FVC_KFM_LINK = 'KFMLink';
	/**
	 * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
	 */
	const FVC_KFM_LIGHTBOX = 'KFMLightbox';
	/**
	 * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
	 */
	const FVC_KFM_THUMBS = 'KFMThumbs';
	/**
	 * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
	 */
	const FVC_FPC_TEXTS = 'FPCTexts';
	/**
	 * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
	 */
	const FVC_JPEG = 'JPEGQuality';
	/**
	 * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
	 */
	const FVC_PNG = 'PNGTransform';
	/**
	 * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
	 */
	const FVC_PNG_LIMIT = 'PNGLimit';
	
	/**
	 * Relative path to FCKEditor skins folder
	 */
	const FVC_SKINS_RELATIVE_PATH = '/fckeditor/editor/skins';
	/**
	 * Relative path to custom FCKEditor config file
	 */
	const FVC_FCK_CONFIG_RELATIVE_PATH = 'custom-config/foliopress-wysiwyg-config-js.php';
	/**
	 * Relative path to options page js file
	 */
	const FVC_OPTIONS_JS_PATH = 'foliopress-wysiwyg.js';
	/**
	 * Relative path to Foliovision Regex class file
	 */
	const FVC_FV_REGEX_PATH = 'include/foliovision-regex.js';
	
   const FVC_HIDEMEDIA = 'HideMediaButtons';




///  -----------------------------------------------------------------------------------------------------------------
///  --------------------------------------------------   Methods   --------------------------------------------------
///  -----------------------------------------------------------------------------------------------------------------
	
	/**
	 * Class constructor. Sets all basic variables ({@link fp_wysiwyg_class::$strSiteUrl $strSiteUrl}, {@link fp_wysiwyg_class::$strPluginPath $strPluginPath},
	 * {@link fp_wysiwyg_class::$strFCKEditorPath $strFCKEditorPath}, {@link fp_wysiwyg_class::$iEditorSize $iEditorSize},
	 * {@link fp_wysiwyg_class::$aOptions $aOptions}) to proper values
	 */
	function __construct(){
      ///   Modification   2009/06/24
      if(function_exists('site_url'))
         $strSite = trailingslashit( site_url() );
      else
         $strSite = trailingslashit( get_option('siteurl') );
		//$strSite = trailingslashit( get_option( 'siteurl' ) );
		/// End of modification
		$this->strSiteUrl = $strSite;
		$this->strPluginPath = $strSite . 'wp-content/plugins/' . basename( dirname( __FILE__ ) ) . '/';
		$this->strFCKEditorPath = $strSite . 'wp-content/plugins/' . basename( dirname( __FILE__ ) ) . '/fckeditor/';
		
		$this->iEditorSize = 20 * intval( get_option( 'default_post_edit_rows' ) );
		if( $this->iEditorSize < 240 ) $this->iEditorSize = 240;

		$this->aOptions = get_option( FV_FCK_OPTIONS );
		if( !isset( $this->aOptions[self::FVC_IMAGES] ) ) $this->aOptions[self::FVC_IMAGES] = '/images/';
		if( !isset( $this->aOptions[self::FVC_TOOLBAR] ) ) $this->aOptions[self::FVC_TOOLBAR] = 'Foliovision';
		if( !isset( $this->aOptions[self::FVC_SKIN] ) ) $this->aOptions[self::FVC_SKIN] = 'foliovision';
		if( !isset( $this->aOptions[self::FVC_WIDTH] ) ) $this->aOptions[self::FVC_WIDTH] = 0;
		if( !isset( $this->aOptions[self::FVC_KFM_LINK] ) ) $this->aOptions[self::FVC_KFM_LINK] = true;
		if( !isset( $this->aOptions[self::FVC_KFM_LIGHTBOX] ) ) $this->aOptions[self::FVC_KFM_LIGHTBOX] = true;
		if( !isset( $this->aOptions[self::FVC_KFM_THUMBS] ) ) $this->aOptions[self::FVC_KFM_THUMBS] = array( 400, 200, 150 );
		if( !isset( $this->aOptions[self::FVC_FPC_TEXTS] ) ) $this->aOptions[self::FVC_FPC_TEXTS] = array( "*** (\\\\w\\\\*) ***", "\\\\[sniplet (\\\\w\\\\*)\\\\]" );
		if( !isset( $this->aOptions[self::FVC_JPEG] ) ) $this->aOptions[self::FVC_JPEG] = 80;
		if( !isset( $this->aOptions[self::FVC_PNG] ) ) $this->aOptions[self::FVC_PNG] = true;
		if( !isset( $this->aOptions[self::FVC_PNG_LIMIT] ) ) $this->aOptions[self::FVC_PNG_LIMIT] = 5000;
		/// Addition 2009/06/02  mVicenik Foliovision
		if( !isset( $this->aOptions[self::FVC_HIDEMEDIA] ) ) $this->aOptions[self::FVC_HIDEMEDIA] = false;
		/// End of addition
		/// Addition 2009/10/29   Foliovision
		if( !isset( $this->aOptions['customtoolbar'] ) ) $this->aOptions['customtoolbar'] =
		//    this would be for all the possible buttons, you can get the button names here
        /* 
"['Cut','Copy','Paste','foliopress-paste','-','Bold','Italic','-','RemoveFormat','-','OrderedList','UnorderedList','-','Outdent','Indent','Blockquote','-','Link','Unlink','Anchor','-','foliopress-more','-','kfmBridge','-','Source','-','FitWindow'],
'/',
['DocProps','-','Save','NewPage','Preview','-','Templates'], ['PasteText','PasteWord','-','Print','SpellCheck'], ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'], ['Form','Checkbox','Radio','TextField','Textarea','Select','Button','ImageButton','HiddenField'],
'/',
['Underline','StrikeThrough','-','Subscript','Superscript'], ['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'], ['Image','Flash','Table','Rule','Smiley','SpecialChar','PageBreak'],['TextColor','BGColor'], ['About'],
'/',
['Style','FontFormat','FontName','FontSize']";*/
            "['Cut','Copy','Paste','foliopress-paste','-','Bold','Italic','-','RemoveFormat','-','OrderedList','UnorderedList','-','Outdent','Indent','Blockquote','-','Link','Unlink','Anchor','-','foliopress-more','-','kfmBridge','-','Source','-','FitWindow']";
        
        //  todo - add content
        if( !isset( $this->aOptions['customdropdown'] ) ) $this->aOptions['customdropdown'] = '<h5 class="">Centered image</h5>
<h5 class="left">Left aligned image</h5>
<h5 class="right">Right aligned image</h5>
<p>Normal paragraph</p>
<h1>Header 1</h1>
<h2>Header 2</h2>
<h3>Header 3</h3>
<h4>Header 4</h4>';
        $this->parse_dropdown_menu();
		
		if ( !isset( $this->aOptions['multipleimageposting'] ) ) $this->aOptions['multipleimageposting'] = true;
		
		if ( !isset( $this->aOptions['autowpautop'] ) ) $this->aOptions['autowpautop'] = true;
		/// End of addition	

    update_option( FV_FCK_OPTIONS, $this->aOptions );    

		//$this->KillTinyMCE( null );
	}
	
	/**
	 * Returns option if images should be wrapped in link (<a>). This function returns integer '1' and '0' depending on settings stored by user.
	 * If user haven't specified this option default value is '1'.
	 *
	 * @return int '1' if images returned by KFM into FCKEditor should be wrapped in <a> tag with link to the original image, '0' otherwise.
	 */
	function getLink(){
		$iLink = 1;
		if( isset( $this->aOptions[self::FVC_KFM_LINK] ) ){
			if( !$this->aOptions[self::FVC_KFM_LINK] ) $iLink = 0;
		}
		return $iLink;
	}
	
	/**
	 * Returns option if link to original image should contain 'rel="lightbox"', which triggers lightbox. Return values are integers '1' and '0' 
	 * depending on settings stored by user. If user haven't specified this option default value is '1'.
	 *
	 * @return int '1' if link to original image should contain 'rel="lightbox"', '0' otherwise
	 */
	function getLightbox(){
		$iLightbox = 1;
		if( isset( $this->aOptions[self::FVC_KFM_LIGHTBOX] ) ){
			if( !$this->aOptions[self::FVC_KFM_LIGHTBOX] ) $iLightbox = 0;
		}
		return $iLightbox;
	}
	
	/**
	 * Returns special KFM thumnails in string separated by this string ', '. Again this options is editable from Options page. Default value is
	 * '400, 200, 150'.
	 *
	 * @return string All sizes for KFM special thumbnails separated by string ', '.
	 */
	function getThumbsString(){
		$strThumbs = '400, 200, 150';
		if( isset( $this->aOptions[self::FVC_KFM_THUMBS] ) ){
			$strThumbs = '';
			$aThumbs = $this->aOptions[self::FVC_KFM_THUMBS];
			$iThumbs = count( $aThumbs );
			for( $i = 0; $i < $iThumbs; $i++ ){
				if( $i < $iThumbs - 1 ) $strThumbs .= $aThumbs[$i] . ', ';
				else $strThumbs .= $aThumbs[$i];
			}
		}
		
		return $strThumbs;
	}
	
	/**
	 * Searches $strOption in $strText and extracts value after it till end of file or $strEndText. Return value is trimed of all white spaces. 
	 *
	 * @param string $strText Text from where the data should be extracted. It is passed as reference.
	 * @param string $strOption Option which should be extracted. The real value is after this text until end of file or $strEndText.
	 * @param string $strEndText This string indicates the string that is used in ending of each option.
	 *
	 * @return string Extracted value, of false otherwise.
	 */
	function ExtractOption( &$strText, $strOption, $strEndText ){
		$iStart = strpos( $strText, $strOption );
		if( false === $iStart ) return false;
		$iEnd = strpos( $strText, $strEndText, $iStart );
		$iOptionLength = strlen( $strOption );
		
		if( false === $iEnd ) return trim( substr( $strText, $iStart + $iOptionLength ) );
		else return trim( substr( $strText, $iStart + $iOptionLength, $iEnd - $iStart - $iOptionLength ) );
	}
	
	/**
	 * This function connects to foliovision website and checks if there is a newer version of Foliopress WYSIWYG
	 *
	 * @return array Array with two values, or empty array on failure:
	 * - ['version'] is string containing version information
	 * - ['changes'] is URL address of changelog 
	 */
	function GetLatestVersion(){
		$strPathToUpdate = "/version.php?version=".urlencode( $this->strVersion )."&blog=".urlencode( $this->strSiteUrl );
		$strUpdateHost = 'www.foliovision.com';
		
		$strHTTPReq = "GET $strPathToUpdate HTTP/1.0\r\n";
		$strHTTPReq .= "Host: $strUpdateHost\r\n\r\n";
		
		$iErr = 0;
		$strErr = '';
		$strResponse = '';
		$aReturn = array();
		
		if( false !== ( $fs = @fsockopen( $strUpdateHost, 80, $iErr, $strErr, 10 ) ) && is_resource( $fs ) ){
			fwrite( $fs, $strHTTPReq );
			while( !feof( $fs ) ) $strResponse .= fgets( $fs, 1160 );
			fclose( $fs );
			
			$strText = explode( "\r\n\r\n", $strResponse, 2 );
			$strText = $strText[1];
		
			$objValue = $this->ExtractOption( $strText, '@ver:', "\n" );
			if( false !== $objValue ) $aReturn['version'] = $objValue;
			else return false;
			$objValue = $this->ExtractOption( $strText, '@changes:', "\n" );
			if( false !== $objValue ) $aReturn['changes'] = $objValue;	
		}
		
		return $aReturn;
	}
	
	/**
	 * This function disables TinyMCE and sets {@link $bUseFCK} to true or false depending on which page is loaded
	 */
	function KillTinyMCE( $in ){
		global $current_user;
	
		if ( 'true' == $current_user->rich_editing && strpos($_SERVER['REQUEST_URI'], 'wp-admin') !== false && strpos($_SERVER['REQUEST_URI'], 'wp-admin/profile.php') === false ){
			$this->bUseFCK = true;
			$current_user->rich_editing = 'false';
		}elseif ( isset($current_user) && $this->bUseFCK === null ){
			$this->bUseFCK = false;
		}

		return $in;
	}
	
	/**
	 * Outputs into head section of html document script for FCK to load
	 */
	function FckLoadAdminHead(){
		if( strpos( $_SERVER['REQUEST_URI'], 'post-new.php' ) || strpos( $_SERVER['REQUEST_URI'], 'page-new.php' ) || strpos( $_SERVER['REQUEST_URI'], 'post.php' ) || strpos( $_SERVER['REQUEST_URI'], 'page.php' ) ) :
?>
		<script type="text/javascript" src="<?php print( $this->strFCKEditorPath ); ?>fckeditor.js"></script>
		<style type="text/css">
					#quicktags { display: none; }
		</style>
<?php
		endif;
   }
   
   /**
    * This function starts FCKEditor through javascript.
    */
	function LoadFCKEditor(){
?>		
		<script type="text/javascript">
		function fv_wysiwyg_load(){
			var oFCKeditor = new FCKeditor( 'content', 
					'<?php
						if( $this->aOptions[self::FVC_WIDTH] != 0 ) print( $this->aOptions[self::FVC_WIDTH] . "px" );
						else print( "100%" );
					?>', 
					<?php print( $this->iEditorSize ); ?> );
			oFCKeditor.Config["CustomConfigurationsPath"] = "<?php print( $this->strPluginPath . self::FVC_FCK_CONFIG_RELATIVE_PATH ); ?>";
			oFCKeditor.BasePath = "<?php print( $this->strFCKEditorPath ); ?>";
			oFCKeditor.Config["BaseHref"] = "<?php print( $_SERVER['SERVER_NAME'] ); ?>";
			oFCKeditor.ToolbarSet = "<?php print( $this->aOptions[self::FVC_TOOLBAR] ); ?>";
			oFCKeditor.ReplaceTextarea();
		}
		
		<?php if( $this->bUseFCK ) print( 'fv_wysiwyg_load();' ); ?>
		</script>
<?php 
	}
	
	/**
	 * Adds Options page to Wordpress.
	 */
	function AddOptionPage(){
		add_options_page( FV_FCK_NAME, FV_FCK_NAME, 8, 'fv_wysiwyg', array( &$this, 'OptionsMenuPage' ) );
	}
	
	/**
	 * Checks if some specified file with relative path is allowed to be editable by user. Note that this not checks if the file is also
	 * available for editing by file system, for that see php native function 'is_writable'.
	 *
	 * @param string $strFile <b>RELATIVE</b> path to file
	 *
	 * @return bool True if the file is editable by user, false otherwise
	 */
	function IsEditableFile( $strFile ){
		if( false !== strpos( str_replace( "\\", '/', $strFile ), self::FVC_FCK_CONFIG_RELATIVE_PATH ) ) return true;
		
		return false;
	}
	
	/**
	 * Loads Options page. It also stores all changes and also loads KFM thumbnails recreation.
	 */
	function OptionsMenuPage(){
		$bError = false;
		$strCustomError = '';
		$strErrDesc = '';
		
		$strLatestVersion = '';
		$strLinkToChangesLog = '';
		$aResult = $this->GetLatestVersion();
		if( false !== $aResult ){
			if( isset( $aResult['version'] ) ) $strLatestVersion = $aResult['version'];
			if( isset( $aResult['changes'] ) ) $strLinkToChangesLog = $aResult['changes'];
		}
		$strPath = dirname( __FILE__ );
		
		$iOldErrorSettings = error_reporting( 0 );
		
		try{
			/// Saving of file changes to editable file
			if( isset( $_POST['save_file'] ) && isset( $_GET['edit'] ) ){
				
				$strFile = realpath( $strPath.'/'.urldecode( $_GET['edit'] ) );
				if( is_writable( $strFile ) && $this->IsEditableFile( $strFile ) ){
					
					$strText = $_POST['textFile'];
					if( ini_get( 'magic_quotes_gpc' ) ) $strText = stripslashes( $strText );
					
					if( false === file_put_contents( $strFile, stripslashes( $_POST['textFile'] ) ) ) $strMessage = "Error while saving file '".basename( $strFile )."' !";
					else $strMessage = "File '".basename( $strFile )."' saved successfully.";
					
				}
			}

			/// When user returns from file editing page we need to remove $_GET['edit'] from it
			if( (isset( $_POST['save_file'] ) || isset( $_POST['cancel_file'] )) && isset( $_GET['edit'] ) ){ 
				$_SERVER['REQUEST_URI'] = str_replace( $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI'] ).'page='.$_GET['page'];
				unset( $_GET['edit'] );
			}
			
			/// When user returns from recreate page, thumbnails will be recreated
			if( isset( $_POST['recreate_submit'] ) ){
				set_time_limit( 300 );
				
				$aSetup = array();
				$aSetup['JPGQuality'] = $this->aOptions[self::FVC_JPEG];
				$aSetup['transform'] = $this->aOptions[self::FVC_PNG];
				$aSetup['transform_limit'] = $this->aOptions[self::FVC_PNG_LIMIT];
				
				KFM_RecreateThumbnailsSilent( realpath( $_SERVER['DOCUMENT_ROOT'].$this->aOptions[self::FVC_IMAGES] ), $this->aOptions[self::FVC_KFM_THUMBS], $kfm_workdirectory, $aSetup );
			}
			
			/// This is regular saving of options that are on the main Options page
			if( isset( $_POST['options_save'] ) ){
				$this->aOptions[self::FVC_SKIN] = $_POST['FCKSkins'];
				$this->aOptions[self::FVC_TOOLBAR] = $_POST['FCKToolbar'];
				///  Modification   03/04/09 mVicenik Foliovision			  
				if(strrpos($_POST['ImagesPath'],'/')!=(strlen($_POST['ImagesPath'])-1) && $_POST['ImagesPath']!='/')
				  $_POST['ImagesPath'] = $_POST['ImagesPath'].'/';
				if($_POST['ImagesPath']=='')
				  $this->aOptions[self::FVC_IMAGES] = '/';
			   else
				  $this->aOptions[self::FVC_IMAGES] = $_POST['ImagesPath'];
				///  End of modification   03/04/09 mVicenik Foliovision
				$this->aOptions[self::FVC_WIDTH] = $_POST['FCKWidth'];

				$this->aOptions[self::FVC_KFM_LINK] = false;
				if( isset( $_POST['KFMLink'] ) && 'yes' == $_POST['KFMLink'] ) $this->aOptions[self::FVC_KFM_LINK] = true;
				$this->aOptions[self::FVC_KFM_LIGHTBOX] = false;
				if( isset( $_POST['KFMLightbox'] ) && 'yes' == $_POST['KFMLightbox'] ) $this->aOptions[self::FVC_KFM_LIGHTBOX] = true;
				
				///  Addition 2009/06/02  mVicenik Foliovision
				$this->aOptions[self::FVC_HIDEMEDIA] = false;
				if( isset( $_POST['HideMediaButtons'] ) ) $this->aOptions[self::FVC_HIDEMEDIA] = true;
				///  End of addition
				
				///  Addition 2009/10/29  mVicenik Foliovision
				$this->aOptions['multipleimageposting'] = false;
				if( isset( $_POST['MultipleImagePosting'] ) ) $this->aOptions['multipleimageposting'] = true;
				
				$this->aOptions['autowpautop'] = false;
				if( isset( $_POST['PreWPAutop'] ) ) $this->aOptions['autowpautop'] = true;
				///  End of addition
				
				/// Addition 2009/11/04 Foliovision
				if( isset( $_POST['bodyid'] ) ) $this->aOptions['bodyid'] = $_POST['bodyid'];
				if( isset( $_POST['bodyclass'] ) ) $this->aOptions['bodyclass'] = $_POST['bodyclass'];
				if( isset( $_POST['customtoolbar'] ) ) $this->aOptions['customtoolbar'] = stripslashes($_POST['customtoolbar']);
				if( isset( $_POST['customdropdown'] ) ) $this->aOptions['customdropdown'] = stripslashes($_POST['customdropdown']);
				
                $this->parse_dropdown_menu();
				/// End of addition
				

				if( isset( $_POST['KFMThumbCount'] ) ){
					$aThumbs = array();
					for( $i=0; $i<$_POST['KFMThumbCount']; $i++ )
						if( isset( $_POST['KFMThumb'.$i] ) ) $aThumbs[] = $_POST['KFMThumb'.$i];
					$this->aOptions[self::FVC_KFM_THUMBS] = $aThumbs;
				}
				
				if( isset( $_POST['FPCleanCount'] ) ){
					$aFPTexts = array();
					for( $i=0; $i<$_POST['FPCleanCount']; $i++ )
						if( isset( $_POST['FPClean'.$i] ) ) $aFPTexts[] = $_POST['FPClean'.$i];
					$this->aOptions[self::FVC_FPC_TEXTS] = $aFPTexts;
				}
				
				$this->aOptions[self::FVC_JPEG] = intval( $_POST['JPEGQuality'] );
				if( $this->aOptions[self::FVC_JPEG] < 0 || $this->aOptions[self::FVC_JPEG] > 100 ) $this->aOptions[self::FVC_JPEG] = 80;
				$this->aOptions[self::FVC_PNG] = isset( $_POST['PNGTransform'] ) ? true : false;
				$this->aOptions[self::FVC_PNG_LIMIT] = intval( $_POST['PNGLimit'] );
				if( $this->aOptions[self::FVC_PNG_LIMIT] < 0 || $this->aOptions[self::FVC_PNG_LIMIT] > 50000 ) $this->aOptions[self::FVC_PNG_LIMIT] = 5000;
				
				update_option( FV_FCK_OPTIONS, $this->aOptions );
			}
		}catch( Exception $ex ){
			$bError = true;
			$strErrDesc = $ex->getMessage();
			
			$iPos = strpos( $strErrDesc, 'file_put_contents' );
			if( false !== $iPos ){
				if( strpos( $strErrDesc, 'config.ini' ) ){
					$strCustomError = 'KFM (SEO Images) config.ini file is read-only. In order to change options this file has to be rewritten.';
					$strCustomError .= 'Please adjust the file permissions to this file. For further help, read the manual !';
				}
			}
		}
		
		error_reporting( $iOldErrorSettings );
		
		/// Loading of pages based on request
		if( isset( $_POST['recreate'] ) ) include( $strPath . '/view/recreate.php' );
		elseif( !isset( $_GET['edit'] ) ) include( $strPath . '/view/options.php' );
		else{
			$strFile = realpath( $strPath.'/'.urldecode( $_GET['edit'] ) );
			if( is_writable( $strFile ) && $this->IsEditableFile( $strFile ) ) include( $strPath . '/view/edit.php' );
			else{
				$strMessage = 'You cannot edit this file. The requested link is invalid !';
				include( $strPath . '/view/message.php' );
			}
		}
	}
	
	///  Addition 25/03/09 mVicenik Foliopress
	//   this is from Dean's FCK, thank you!
	function add_admin_js()
	{
		wp_deregister_script(array('media-upload')); 
		wp_enqueue_script('media-upload', $this->strPluginPath .'media-upload.js', array('thickbox'), '20080710'); 
		//wp_enqueue_script('fckeditor', $this->fckeditor_path . 'fckeditor.js');
	}
	
    function do_wpautop($content) {
        //var_dump($content);
 
        if($this->aOptions['autowpautop']!=true) {
            return $content;
        }
        if(strlen($content)>0) {   // try to guess if the post should use wpautop
            if(stripos($content,'<p>')===FALSE && (stripos($content,'<')!==FALSE || stripos($content,'>')!==FALSE))
                return wpautop($content);      
            /*if(stripos($content,'&lt;p&gt;')===FALSE && (stripos($content,'<')===FALSE || stripos($content,'>')===FALSE) )
                return wpautop($content);*/      
        }
        return $content;
    }
	///  End of addition
	
	///   Addition 20/03/09 mVicenik Foliovision
	function fv_remove_mediabuttons($content) {
      return '';
   }
   ///  End of addition
   
   ///   Addition 29/06/09 mVicenik Foliovision
   function remove_blank_p($content) {
      return str_replace('<p>&nbsp;</p>', '', $content);
   }
   
   ///  End of addition
   
   function parse_dropdown_menu() {
        $items = explode("\r\n",$this->aOptions['customdropdown']);             //  one item per line
        $i = 0;                                                                 //  counter
        foreach ($items AS $item) {
            $i++;
            preg_match('/<(.*?)>/i',$item,$match);                              //  take only the part inside <>
            if(!$match[0])
                continue;
            preg_match('/<([^>]*?)[\s>]/i',$match[0],$element);                    //  match the element name
            preg_match('/>([^<]*?)</i',$item,$name);                            //  match the enclosed text - name
            //echo 'Name: \''.$name[1].'\' Element: \''.$element[1].'\', ';
            preg_match_all('/([a-z]*?)="(.*?)"/i',$match[0],$attributes);       //  match the attributes
            $attr_text = '';
            $styles_text = '';
            if(isset($attributes[1])) {
                foreach($attributes[1] AS $key => $attribute) {
                    if(strcasecmp('style',$attribute)==0) {                     //  style
                        $styles_text .= $attributes[2][$key];
                    }
                    else {                                                      //  everything else
                        if(isset($attributes[2][$key]))
                            $attr_text .= '\''.$attribute.'\' : \''.$attributes[2][$key].'\', ';
                        else
                            $attr_text .= '\''.$attribute.'\', ';
                    }
                }
            }
            $styles_text = preg_replace('/\b([^;]*?):/i','\'$1\' :',$styles_text);  //  put css property into ''
            $styles_text = preg_replace('/\b([^\s]*?);/i','\'$1\', ',$styles_text);  //  put css values into ''
            
            $attr_text = rtrim($attr_text,', ');
            $styles_text = rtrim($styles_text,', ');
            
            if(strlen($corestyles)>0)
                $corestyles .= ",\r\n";
            $corestyles .= "'".$element[1]."_".$i."' : { Element : '".$element[1]."'";        //  do the proper output
            if(strlen($attr_text)>0)
                $corestyles .= ", Attributes : { ".$attr_text." } ";
            if(strlen($styles_text)>0)
                $corestyles .= ", Styles : { ".$styles_text." } ";
            
            $corestyles .= " }";
            
            $fontformats .= "".$element[1]."_".$i.";";
            if(strlen($fontformatnames)>0)
                $fontformatnames .= ",\r\n";
            $fontformatnames .= "".$element[1]."_".$i." : '".$name[1]."'"; 
            
        }
        $this->aOptions['customdropdown-fontformats'] = rtrim($fontformats,';');
        $this->aOptions['customdropdown-corestyles'] = rtrim($corestyles,',');
        $this->aOptions['customdropdown-fontformatnames'] = rtrim($fontformatnames,','); 
    }
}

$fp_wysiwyg = new fp_wysiwyg_class();

?>