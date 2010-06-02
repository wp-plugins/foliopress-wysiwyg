window.kfm_renameFile=function(id){
	var filename,sfilename;
	filename=sfilename=File_getInstance(id).name;
	if(sfilename.length>30)sfilename=sfilename.substring(0,25)+'[...]';
	kfm_prompt(kfm.lang.RenameFileToWhat(sfilename),filename,function(newName){
		if(!newName||newName==filename)return;
		x_kfm_renameFile(id,newName,kfm_refreshFiles);
	});
}
