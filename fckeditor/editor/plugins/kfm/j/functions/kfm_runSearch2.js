window.kfm_runSearch2=function(){
	var keywords='',tags='';
	var kEl=document.getElementById("kfm_search_keywords"),tEl=document.getElementById("kfm_search_tags");
	if(kEl)keywords=kEl.value;
	if(tEl)tags=tEl.value;
	if(keywords==""&&tags=="")x_kfm_loadFiles(kfm_cwd_id,kfm_refreshFiles);
	else x_kfm_search(keywords,tags,kfm_refreshFiles)
}
