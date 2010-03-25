var File=new Class({
	getText:function(varname){
		var el=new Element('span',{
			'class':varname+' file_'+varname+'_'+this.id
		});
		this.setText(el,varname);
		if(!this.textInstances[varname])this.textInstances[varname]=[];
		this.textInstances[varname].push(el);
		return el;
	},
	initialize:function(id,data){
		this.id=id;
		this.textInstances=[];
		if(data){
			File_Instances[id]=this;
			File_setData(data,this);
		}
		else x_kfm_getFileDetails(id,File_setData);
	},
	setText:function(el,varname){
		el.empty();
		var v=$pick(this[varname],'');
		if(varname=='filename' && kfm_vars.files.name_length_displayed && kfm_vars.files.name_length_displayed<v.length){
			el.title=v;
			v=v.substring(0,kfm_vars.files.name_length_displayed-3)+'...';
		}
		if(varname=='modified' && !v){
			var v=(new Date(this.ctime*1000)).toGMTString();
			this.modified=v;
		}
		el.appendText(v);
	},
	setThumbnailBackground:function(el,reset){
		if(this.icon_loaded && !reset) el.setStyle('background-image','url("'+this.icon_url+'")');
		else{
			/// #### Change		pBaran		05/12/2007		Foliovision
			/// this trigered a code injection error on high security php setings
			//var url='get.php?id='+this.id+'&width=64&height=64&get_params='+kfm_vars.get_params+'&r'+Math.random();
			
			/// ## TODO: 64 is hardcoded which should be changed and parametrized
			x_kfm_getFileUrl( this.id, 64, 64, function ( url ){
				var img=new Element('img',{
					src:url,
					styles:{
						width:1,
						height:1
					}
				});
				var id=this.id;
				img.addEvent('load',function(){
					var p=this.parentNode;
					p.setStyle('background-image','url("'+url+'")');
					var F=File_getInstance(id);
					F.icon_loaded=1;
					F.icon_url=url;
					this.remove();
				});
				kfm.addEl(el,img);

			});
			/// #### End of change			pBaran		05/12/2007		Foliovision
		}
	}
});
function File_getInstance(id,data){
	id=parseInt(id);
	if(isNaN(id))return;
	if(!File_Instances[id] || data)File_Instances[id]=new File(id,data);
	return File_Instances[id];
}
function File_setData(el,F){
	var id=parseInt(el.id);
	el=$H(el);
	if(!F)F=File_Instances[id]={};
	el.each(function(varvalue,varname){
		F[varname]=varvalue;
		if(!F.textInstances[varname])return;
		F.textInstances[varname].each(function(t){
			F.setText(t,varname);
		});
	});
}
var File_Instances=[];
