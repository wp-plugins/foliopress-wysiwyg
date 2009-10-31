<?php
# see docs/license.txt for licensing
include('initialise.php');

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
	
	$to = $toDir->path . '/' . $filename;
	
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
		}
	}
}
else $errors[]=kfm_lang( 'permissionDeniedUpload' );




?>
<html>
	<head>
		<script type="text/javascript">
		<?php if( $bRename ){ ?>
		
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
