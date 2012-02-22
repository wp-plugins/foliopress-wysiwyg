window.kfm_renameFiles=function(nameTemplate){
	if(nameTemplate && nameTemplate.toString()!==nameTemplate)nameTemplate='';
	var ok=false;
	kfm_prompt(kfm.lang.HowWouldYouLikeToRenameTheseFiles,nameTemplate,function(nameTemplate){
		var asterisks=nameTemplate.replace(/[^*]/g,'').length;
		if(!nameTemplate)return;
		if(!/\*/.test(nameTemplate))alert(kfm.lang.YouMustPlaceTheWildcard);
		else if(/\*[^*]+\*/.test(nameTemplate))alert(kfm.lang.IfYouUseMultipleWildcards);
		else if(asterisks<(''+selectedFiles.length).length)alert(kfm.lang.YouNeedMoreThan(asterisks,selectedFiles.length));
		else ok=true;
		if(!ok)return kfm_renameFiles(nameTemplate);
		x_kfm_renameFiles(selectedFiles,nameTemplate,kfm_refreshFiles);
	});
}
