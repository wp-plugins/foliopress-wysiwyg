function kfm_plugin_tags(){
	this.name='tags';
	this.title='tags Plugin'; //will be set dynamically
	this.category='edit';
	this.mode=2;
	this.writable=2;
	this.extensions='all';
	this.doFunction=function(files){alert('Tags doFunction is not set')};
}
//removed zUhrikova 3/2/2010 Foliovision
//kfm_addHook(new kfm_plugin_tags(),{name:'tags_add',title:"add tags to files",doFunction:function(files){kfm_tagAdd(files[0])}});
//kfm_addHook(new kfm_plugin_tags(),{name:'tags_remove',title:"remove tags from files",doFunction:function(files){kfm_tagRemove(files[0])}});

function kfm_tagAdd(id){
	kfm_prompt(kfm.lang.WhatIsTheNewTag,'',function(newTag){
		if(newTag){
			files=selectedFiles.length?selectedFiles:id;
			x_kfm_tagAdd(files,newTag,function(res){
				if(selectedFiles.length && selectedFiles.length > 1){
					for(var i=0;i<files.length; i++)  x_kfm_getFileDetails(files[i],File_setData);
				}else{
					File_setData(res);
				}
			});
		}
	});
}
function kfm_tagDraw(id){
	var ret;
	if($type(id)!='array'){
		if(kfm_tags[id]){
			ret=document.createElement('span');
			ret.innerHTML=kfm_tags[id];
			return ret;
		}
		x_kfm_getTagName(id,kfm_tagDraw);
		ret=document.createElement('span');
		ret.className='kfm_unknown_tag';
		ret.innerHTML=id;
		return ret;
	}
	var name=id[1],id=id[0];
	kfm_tags[id]=name;
	$j('span.kfm_unknown_tag').each(function(key,el){
		if(el.innerHTML==id){
			el.innerHTML=name;
			$j(el).removeClass('kfm_unknown_tag');
		}
	});
}
function kfm_tagRemove(id){
	kfm_prompt(kfm.lang.WhichTagsDoYouWantToRemove,'',function(tagsToRemove){
		if(tagsToRemove){
			files=selectedFiles.length?selectedFiles:id;
			x_kfm_tagRemove(files,tagsToRemove,function(res){
				if(selectedFiles.length && selectedFiles.length > 1){
					for(var i=0;i<files.length; i++)  x_kfm_getFileDetails(files[i],File_setData);
				}else{
					File_setData(res);
				}
			});
		}
	});
}
