window.kfm_removeFilesFromView=function(files){
	kfm_selectNone();
	if($type(files)!='array' || !files.length)return;
	var i=0,right_column=document.getElementById('documents_body');
	for(var i=0;i<files.length;++i){
		if(kfm_listview)$j('#kfm_files_listview_table_row'+files[i]).remove();
		else $j('#kfm_file_icon_'+files[i]).remove();
	}
	right_column.fileids=array_remove_values(right_column.fileids,files);
	kfm_files_reflowIcons();
}
