<?php
# see docs/license.txt for licensing
include('initialise.php');
///	Addition	28/01/10	Foliovision
include('includes/image-non-class.php');
///	End of addition

/// Change		pBaran		07/12/2007		Foliovision
require_once( 'includes/myFileChecker.php' );

$errors=array();
$bRename = false;
$strRenameTemp = '';

if($kfm_allow_file_upload){
	$filename = NULL;
	$tmpname = NULL;
	$toDir = kfmDirectory::getInstance( $kfm_session->get( 'cwd_id' ) );
	
	if( isset( $_GET['rename'] ) ){
		$filename = $_GET['rename'];
		$tmpname = $toDir->path . '/' . $_GET['tempname'];
	}else{
		$file = isset( $_FILES['kfm_file'] ) ? $_FILES['kfm_file'] : $_FILES['Filedata'];
		$filename = $file['name'];
		$tmpname = $file['tmp_name'];
	}
	
	/// Addition		pBaran		13/03/2008-19/03/2008		Foliovision
	// files uploaded will be striped of every stupid characters
	$filename = preg_replace( '/[^\w\d\.\-\s_]/' , '' , $filename );
	// files uploaded will have their names striped of underscores and spaces and replaced with hyphen
	$filename = str_replace( array( '_', ' ', '--' ), '-', $filename );
	/// End of addition		pBaran		13/03/2008-19/03/2008
	
	///	Addition	28/01/10	Foliovision
	//	let's add / only if is missing at the end of the path
	$dirpath = $toDir->path;
	if( strrpos($dirpath,'/') != strlen($dirpath)-1 )
		$to = $toDir->path . '/' . $filename;
	else
		$to = $toDir->path . $filename;
	///	End of addition
	
	if( !kfm_checkAddr( $to ) ) $errors[] = kfm_lang( 'bannedFilenameExtension' );
	else if( !kfmFile::checkName( $filename ) ) $errors[] = 'The filename: ' . $filename . ' is not allowed';
	else{
		if( file_exists( $to ) ){
			if( is_uploaded_file( $tmpname ) ){
				$strRenameTemp = mykfm_CreateTempNameForFile( $filename, $toDir->path );
				move_uploaded_file( $tmpname, $toDir->path . '/' . $strRenameTemp );
				chmod( $toDir->path . '/' . $strRenameTemp, octdec( '0' . $kfm_default_upload_permission ) );
			}else $strRenameTemp = basename( $tmpname );

			$bRename = true;
		}else{
			$objReturn = kfm_move_uploaded_file( $filename, $to, $tmpname );
			if( true !== $objReturn && is_string( $objReturn ) ) $errors[] = $objReturn;
			
			///	Resize!
			global $iJPGQuality, $iMaxWidth, $iMaxHeight;
			
			$aImageInfo = getimagesize($to);
			
			///	Let's throw away too big images
			$freemem = (str_replace('M','',ini_get('memory_limit') )*1048576 - memory_get_usage());
			
			//	fix for PNG
			if( $aImageInfo['channels']=='' )
				$aImageInfo['channels'] = 3;
			
			$imagemem = Round(($aImageInfo[0] * $aImageInfo[1] * $aImageInfo['bits'] * $aImageInfo['channels'] / 8 + Pow(2, 16)) * 2);
			if($freemem <= $imagemem) {
				$bTooHot = true;
				unlink( $to );
			}
			else {		
				if($aImageInfo[0] > $iMaxWidth || $aImageInfo[1] > $iMaxHeight) {
					$iRatio = (int) $aImageInfo[1]/$aImageInfo[0];
					if($aImageInfo[0] > $aImageInfo[1]) {
						$iNewHeight = $iMaxWidth * $iRatio;	
						$iNewWidth = $iMaxWidth;
					} else {
						$iNewWidth = $iMaxHeight / $iRatio;
						$iNewHeight = $iMaxHeight;
					}
					FV_CreateResizedCopy($to, $to, $iNewWidth, $iNewHeight, $aImageInfo, $iJPGQuality);
				}
			}
			///
		}
	}
	
}
else $errors[]=kfm_lang( 'permissionDeniedUpload' );
//$bTooHot = true;
?>
<html>
	<head>
		<script type="text/javascript">
		//parent.document.write("<?php echo $freemem.' '.$imagemem; ?>");
		<?php
		//echo 'alert(\''.$aImageInfo[0].' '.$aImageInfo[1].' '.$aImageInfo[2].' '.$aImageInfo[3].' '.$aImageInfo['mime'].' '.$aImageInfo['bits'].' '.$aImageInfo['channels'].' '.$freemem.' '.$imagemem.'\');';
		?>
		<?php if( $bTooHot ){ ?>
			parent.document.write(""+
			"<style>"+
			"body {"+
			"	font-family: sans-serif;"+
			"	/*font-size: 0.9em;*/"+
			"	font-size: 14px;"+
			"	line-height: 1.6em;"+
			"	color: #696969 /*gray*/;"+
			"	background: white;"+
			"}"+
			"strong {font-weight: bold}"+
			"em {font-style: italic}"+
			"small {font-size: 0.85em}"+
			"a {color: #B51212;}"+
			"a:visited {color: #666;}"+
			"a:hover {color: #411;}"+
			".message { width: 650px; margin-top: 48px; margin-left: auto; margin-right: auto; }"+
			".logo { float: right; padding-left: 16px; }"+
			"</style>"+
			"<?php echo $freemem.' have and need '.$imagemem.' '; ?>"+
			"<div class='message'>"+
			"<div class='logo'><img src='foliovision-logo.png' /></div>"+
			"<h2>Image Too big</h2>"+
			"<p>Hey guys, you shouldn't be uploading so big images to our servers, we can't handle it.</p>"+
			"<p>You should try to make it maximally 2000px wide (or tall, if it's a portrait).</p>"+
			"<p>Here are some choices how to resize the image:</p>"+
			"<ul>"+
			"	<li>Use <strong>Preview</strong> or <strong>Graphicconverter</strong> if you are on a Mac computer</li>"+
			"	<li>Use <a href='http://www.photoscape.org/ps/main/download.php'>Photoscape</a> if you are in Windows</li>"+
			"	<li>If you have <strong>Photoshop</strong> use its <em>\"Save for Web\"</em> and you will get the best quality picture</li"+
			"	<li>If you have a good connection you can use some <strong>online tool</strong> like <a href='http://www.picresize.com/'>picresize.com</a></li>"+
			"</ul>"+
			"<p><a href='index.php?lang=en&kfm_caller_type=fck&type=Image'>Ok, I will resize my image and do it properly</a></p>"+
			"</div>"
			)
		<?php } else if( $bRename ){ ?>
		
			var strMess = "I'm sorry but you've already uploaded an image with that name. If you would like Google to find your image and for your image";
			strMess += "to have a good caption we recommend using words with dashes, i.e. vancouver-freighters-in-harbour.jpg. Please rename your image now!";
			var strNewName = prompt( strMess, '<?php print( $filename ); ?>' );
			if( strNewName == null || strNewName == '' ) parent.x_kfm_move_uploaded_file( '', '', '<?php print( addcslashes( realpath( $toDir->path . '/' . $strRenameTemp ), "\\" ) ); ?>', function(a){} );
			else window.location = 'upload.php?rename=' + strNewName + '&tempname=' + '<?php print( $strRenameTemp ); ?>';
			
		<?php 
		}else{
			$js = isset( $_REQUEST['js'] ) ? $js : '';
			if( isset( $_REQUEST['onload'] ) ) echo $_REQUEST['onload'];
			else if( isset( $_REQUEST['onupload'] ) ) echo $_REQUEST['onupload'];
			else if( count( $errors ) ) echo 'alert("' . addslashes( join( "\n", $errors ) ) . '");';
			else echo 'parent.x_kfm_loadFiles(' . $kfm_session->get( 'cwd_id' ) . ',parent.kfm_refreshFiles);parent.kfm_dir_openNode(' . $kfm_session->get( 'cwd_id' ) . ');' . $js;
		}
		?>
		</script>
	</head>
	<body>
	</body>
</html>
