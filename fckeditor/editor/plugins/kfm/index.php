<?php
# see docs/license.txt for licensing
error_reporting(E_ALL);

require_once( 'initialise.php' );
require_once( 'foliovision.php' );
require_once(KFM_BASE_PATH.'includes/kaejax.php');

//require_once( realpath( $_SERVER['DOCUMENT_ROOT'] . '/wordpress/wp-config.php' ) );
//if( !isset( $kfm_sec ) || sha1( 'security' ) != $kfm_sec ) die( var_export( $kfm_sec, true ) );
/// Addition		pBaran		11/07/2008		Foliovision
/// KFM will send cookie about last opened dir and will open it on start
$iDir = 0;
if( isset( $_COOKIE['kfm_last_opened_dir'] ) ){
   try{
   	$iDir = intval( $_COOKIE['kfm_last_opened_dir'] );
   	if( 1 < $iDir ){
   		//$strFiles = json_encode( kfm_loadFiles( $iDir ) );
   		$aobjDirsTemp = array();
   		$iCur = $iDir;
   		$iIndex = 0;
   		while( $iCur >= 1 ){
   			$objDir = kfmDirectory::getInstance( $iCur );
   			//var_dump( $objDir->name );
   			//var_dump( $objDir->path );
   			$aobjDirsTemp[$iIndex] = $objDir;
   			$iCur = $objDir->pid;
   			$iIndex++;
   		}
   		
   		$aobjDirs= array();
   		for( $i=0; $i<$iIndex; $i++ ) $aobjDirs[$i] = $aobjDirsTemp[$iIndex-$i-1];
   		
   		$aaDirs = array();
   		require_once( KFM_BASE_PATH.'includes/directories.php' );
   		for( $i=0; $i<$iIndex; $i++ ) $aaDirs[$i] = _loadDirectories( $aobjDirsTemp[$iIndex-$i-1]->id );
   		
   		$strDirs = json_encode( $aaDirs );
   	}else $iDir = 0;
   }catch( Exception $ex ){
      $iDir = 0;
   }
}
/// End of addition		pBaran		11/07/2008
			


header('Content-type: text/html; Charset=utf-8');

{ # export kaejax stuff
	kfm_kaejax_export(
		'kfm_changeCaption','kfm_copyFiles','kfm_createDirectory','kfm_createEmptyFile','kfm_deleteDirectory','kfm_downloadFileFromUrl',
		'kfm_extractZippedFile','kfm_getFileDetails','kfm_getFileUrl','kfm_getFileUrls','kfm_getTagName','kfm_getTextFile','kfm_getThumbnail','kfm_loadDirectories',
		'kfm_loadFiles','kfm_moveDirectory','kfm_moveFiles','kfm_renameDirectory','kfm_renameFile','kfm_renameFiles','kfm_resizeImage','kfm_rm',
		'kfm_rotateImage','kfm_cropToOriginal','kfm_cropToNew','kfm_saveTextFile','kfm_search','kfm_tagAdd','kfm_tagRemove','kfm_zip', 
		'kfm_getImageSize', 'kfm_getFilesUrlAndSizes', 'kfm_SetFileDetailsCookie', 'kfm_move_uploaded_file'
	);
	if(!empty($_POST['kaejax']))kfm_kaejax_handle_client_request();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<style type="text/css">@import "themes/<?php echo $kfm_theme; ?>/kfm.css";</style>
		<title>KFM - Kae's File Manager</title>
		<script type="text/javascript" src="j/mootools.v1.11/mootools.v1.11.js"></script>
		<script type="text/javascript">
			var kfm_vars={
				files:{
					name_length_displayed:<?php echo $kfm_files_name_length_displayed; ?>,
					return_id_to_cms:<?php echo $kfm_return_file_id_to_cms?'true':'false'; ?>,
					allow_multiple_returns:<?php echo $kfm_allow_multiple_file_returns?'true':'false'; ?>
				},
				get_params:"<?php echo GET_PARAMS; ?>",
				permissions:{
					dir:{
						ed:<?php echo $kfm_allow_directory_edit; ?>,
						mk:<?php echo $kfm_allow_directory_create; ?>,
						mv:<?php echo $kfm_allow_directory_move; ?>,
						rm:<?php echo $kfm_allow_directory_delete; ?>
					},
					file:{
						rm:<?php echo $kfm_allow_file_delete; ?>,
						ed:<?php echo $kfm_allow_file_edit; ?>,
						mk:<?php echo $kfm_allow_file_create; ?>,
						mv:<?php echo $kfm_allow_file_move; ?>
					},
					image:{
						manip:<?php echo $kfm_allow_image_manipulation; ?>
					}
				},
				root_folder_name:"<?php echo $kfm_root_folder_name; ?>",
				show_disabled_contextmenu_links:<?php echo $kfm_show_disabled_contextmenu_links; ?>,
				use_multiple_file_upload:<?php echo $kfm_use_multiple_file_upload; ?>,
				version:'<?php echo KFM_VERSION; ?>'
			};
			var kfm_widgets=[];
			function kfm_addWidget(obj){
				kfm_widgets.push(obj);
			}
		</script>
		<?php
			if(isset($kfm_dev)){
				$js_files=array('variables.js','notice.js','kfm.js','alerts.js','modal.dialog.js',
					'contextmenu.js','directories.js','file.selections.js','file.text-editing.js',
					'images.and.icons.js','kdnd.js','panels.js','tags.js','common.js', 'foliovision.js',
					'kaejax_replaces.js','file.class.js','files.js','resize_handler.js');
				echo '<script type="text/javascript" src="j/'.join("\"></script>\n		<script type=\"text/javascript\" src=\"j/",$js_files).'"></script>'."\n";
			}
			else echo '<script type="text/javascript" src="j/all.php"></script>';
			$h=opendir(KFM_BASE_PATH.'widgets');
			while(false!==($dir=readdir($h))){
				if($dir[0]!='.'&&is_dir(KFM_BASE_PATH.'widgets/'.$dir)){
					echo '
					<script type="text/javascript" src="widgets/'.$dir.'/widget.js"></script>';
				}
			}
		?>
		<script type="text/javascript" src="j/swfuploadr52_0002/swfupload.js"></script>
		<script type="text/javascript" src="lang/<?php echo $kfm_language; ?>.js"></script>
		<?php
			//if( $kfm_js_config ) print( '<script type="text/javascript" src="' . $kfm_js_config . '"></script>' );
		?>
		<script type="text/javascript">
			/// Add		pBaran		03/01/2008		Foliovision
			var iThumbSizes = <?php print( count( $kfm_special_thumbs_sizes ) ); ?>;
			var aThumbSizes = new Array( iThumbSizes );
			var bMultipleImagePosting = <?php if($bMultipleImagePosting) echo 'true'; else echo 'false'; ?>;
			<?php for( $i=0; $i<count( $kfm_special_thumbs_sizes ); $i++ ) print( 'aThumbSizes['.$i.'] = '.$kfm_special_thumbs_sizes[$i].";\n" ); ?>
			
			var iFileDetailsState = 1;
			<?php if( isset( $_COOKIE['kfm_file_details_state'] ) ) print( 'iFileDetailsState = '.$_COOKIE['kfm_file_details_state'].";\n" ); ?>
			
			var iImageLink = <?php print( $kfm_return_image_link ); ?>;
			var iLinkLightbox = <?php print( $kfm_link_lightbox ); ?>;
			/// End of add		pBaran		03/01/2008

			/// Addition		pBaran		11/07/2008		Foliovision
			var kfm_cwd_id_startup = <?php echo $iDir; ?>;
			kfm_directory_over = <?php echo $iDir; ?>;
			<?php if( 1 < $iDir ) : ?>
				var kfm_startup_dirs = Json.evaluate( '<?php echo $strDirs; ?>', true );
			<?php endif; ?>
			/// End of addition		pBaran		11/07/2008

			var phpsession = "<?php echo session_id(); ?>";
			var session_key="<?php echo $kfm_session->key; ?>";
			var starttype="<?php echo isset($_GET['type'])?$_GET['type']:''; ?>";
			var fckroot="<?php echo $kfm_userfiles; ?>";
			var fckrootOutput="<?php echo $kfm_userfiles_output; ?>";
			var kfm_file_handler="<?php echo $kfm_file_handler; ?>";
			var kfm_log_level=<?php echo $kfm_log_level; ?>;
			var kfm_return_directory=<?php echo isset($_GET['return_directory'])?'1':'0'; ?>;
			var kfm_theme="<?php echo $kfm_theme; ?>";
			var kfm_hidden_panels="<?php echo $kfm_hidden_panels; ?>".split(',');
			var kfm_show_files_in_groups_of=<?php echo $kfm_show_files_in_groups_of; ?>;
			var kfm_slideshow_delay=<?php echo ((int)$kfm_slideshow_delay)*1000; ?>;
			var kfm_listview=<?php echo $kfm_listview;?>;
			for(var i=0;i<kfm_hidden_panels.length;++i)kfm_hidden_panels[i]='kfm_'+kfm_hidden_panels[i]+'_panel';
			<?php echo kfm_kaejax_get_javascript(); ?>
			<?php if(isset($_GET['kfm_caller_type']))echo 'window.kfm_caller_type="'.addslashes($_GET['kfm_caller_type']).'";'; ?>
			var editable_extensions=["<?php echo join('","',$kfm_editable_extensions);?>"];
			var viewable_extensions=["<?php echo join('","',$kfm_viewable_extensions);?>"];
			
			//kfm_changeDirectory( <?php echo $iDir; ?> );
			
		</script>
	</head>
	<body onunload="KFMSetFileDetailsWindowCookie();" >
		<p>Please Wait - loading...</p>
		<noscript>KFM relies on JavaScript. Please either turn on JavaScript in your browser, or <a href="http://www.getfirefox.com/">get Firefox</a> if your browser does not support JavaScript.</noscript>
		<?php
			if(!$kfm_dont_send_metrics){
				$today=date('Y-m-d');
				$last_registration=isset($kfm_parameters['last_registration'])?$kfm_parameters['last_registration']:'';
				if($last_registration!=$today){
					echo '<img src="http://kfm.verens.com/extras/register.php?version='.urlencode(KFM_VERSION).'&amp;domain_name='.urlencode($_SERVER['SERVER_NAME']).'&amp;db_type='.$kfm_db_type.'" />';
					$kfmdb->query("delete from ".KFM_DB_PREFIX."parameters where name='last_registration'");
					$kfmdb->query("insert into ".KFM_DB_PREFIX."parameters (name,value) values ('last_registration','".$today."')");
					$kfm_parameters['last_registration']=$today;
				}
			}
		?>
	</body>
</html>