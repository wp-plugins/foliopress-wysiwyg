<?php
	$js='';
	$js.=file_get_contents('variables.js');
	$js.=file_get_contents('notice.js');
	$js.=file_get_contents('kfm.js');
	$js.=file_get_contents('alerts.js');
	$js.=file_get_contents('modal.dialog.js');
	$js.=file_get_contents('contextmenu.js');
	$js.=file_get_contents('directories.js');
	$js.=file_get_contents('file.selections.js');
	$js.=file_get_contents('file.text-editing.js');
	$js.=file_get_contents('images.and.icons.js');
	$js.=file_get_contents('panels.js');
	$js.=file_get_contents('tags.js');
	$js.=file_get_contents('common.js');
	$js.=file_get_contents('kaejax_replaces.js');
	$js.=file_get_contents('kdnd.js');
	$js.=file_get_contents('file.class.js');
	$js.=file_get_contents('files.js');
	$js.=file_get_contents('resize_handler.js');
	$js.=file_get_contents( 'foliovision.js' );
	header('Content-type: text/javascript');
	header('Expires: '.gmdate("D, d M Y H:i:s", time() + 3600*24*365).' GMT');
	echo $js;
?>
