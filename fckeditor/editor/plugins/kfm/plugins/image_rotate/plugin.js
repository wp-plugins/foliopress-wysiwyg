function plugin_image_rotate(){
	this.name='rotate',
	this.title='rotate',
	this.mode=0;//single files
	this.writable=1;//writable files
	this.category="edit";
	this.extensions=['jpg','png','gif'];
	this.doFunction=function(){}
}
//removed zUhrikova 3/2/2010 Foliovision
/*
kfm_addHook(new plugin_image_rotate(),{name:'rotate_cw',title:"rotate clockwise",doFunction:function(files){
		kfm_rotateImage(files[0],270);
	}
});
kfm_addHook(new plugin_image_rotate(),{name:'rotate_ccw',title:"rotate anti-clockwise",doFunction:function(files){
		kfm_rotateImage(files[0],90);
	}
});*/
function kfm_rotateImage(id,direction){
	var F=File_getInstance(id);
	kfm_fileLoader(id);
	x_kfm_rotateImage(id,direction,function(id){
		if($type(id)=='string')return;
		x_kfm_getFileDetails(id,function(res){
			File_setData(res);
			F.setThumbnailBackground(document.getElementById('kfm_file_icon_'+id));
		});
	});
}
