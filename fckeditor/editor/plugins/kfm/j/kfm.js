// see license.txt for licensing
var KFM=new Class({
	about:function(){
		var div=new Element('div',{
			'styles':{
				'width':400
			}
		});
		var html='<h1>KFM '+kfm_vars.version+'</h1>';
		{ // sponsors
			html+='<h2>Sponsors</h2>';
			html+='KFM is free software. Here are some recent sponsors:<br />';
			html+='<a href="http://tinyurl.com/2uerfm" style="float:right;display:block;border:1px solid red;background:#fff;text-decoration:none;text-align:center;margin-right:10px" target="_blank">Donate to KFM</a>';
			html+='<a href="http://webworks.ie/" target="_blank"><strong>webworks.ie</strong></a><br />';
			html+='<a href="http://www.bluenectar.com.au/" target="_blank">Blue Nectar</a><br />';
		}
		{ // developers
			html+='<h2>Developers</h2>';
			html+='<a href="http://verens.com/" target="_blank"><strong>Kae Verens</strong></a><br />';
			html+='<a href="http://www.companytools.nl/cmsms/file-image-manager" target="_blank">Benjamin ter Kuile</a><br />';
		}
		{ // translators
			html+='<h2>Translators</h2>';
			html+='bg (Bulgarian): Tondy<br />';
			html+='cz (Czech): Petr Kamenik<br />';
			html+='da (Danish): Janich Rasmussen<br />';
			html+='de (German): Just Agens<br />';
			html+='en (English): Kae Verens<br />';
			html+='es (Spanish): Ramón Ramos<br />';
			html+='fi (Finnish): Hannu (hlpilot)<br />';
			html+='fr (French): Hubert Garrido<br />';
			html+='ga (Irish): Kae Verens<br />';
			html+='it (Italian): Stefano Luchetta<br />';
			html+='nl (Dutch): Roy Lubbers<br />';
			html+='pl (Polish): Jan Kurek<br />';
			html+='ro (Romanian): Andrei Suscov<br />';
			html+='ru (Russian): Andrei Suscov<br />';
		}
		{ // bug testers
			html+='<h2>Bug Testers</h2>';
			html+='<em>list coming!</em><br />';
		}
		div.setHTML(html);
		kfm_modal_open(div,kfm.lang.AboutKfm);
	},
	addCell:function(o,b,c,d,e){
		var f=$(o.insertCell(b));
		if(c)f.colSpan=c;
		if(d)kfm.addEl(f,d);
		if(e)f.className=e;
		return f;
	},
	addEl:function(o,a){
		if(!o)return;
		if(!a)return o;
		if($type(a)!='array')a=[a];
		for(var i=0;i<a.length;++i){
			if($type(a[i])=='array')kfm.addEl(o,a[i]);
			else o.appendChild(a[i].toString()===a[i]?newText(a[i]):a[i]);
		}
		return o;
	},
	addRow:function(t,p,c){
		var o=t.insertRow(p===parseInt(p)?p:t.rows.length);
		o.className=c;
		return o;
	},
	alert:function(txt){
		window.inPrompt=1;
		alert(txt);
		setTimeout('window.inPrompt=0',1);
	},
	showErrors:function(errors){
		var div=new Element('div',{
			'styles':{
				'width':400
			}
		});
		var html='';
		for(var i=0;i<errors.length;i++){
			html+='<span>'+errors[i].message+'</span><br/>';
			/* Add tooltip or do something with:
			 *errors[i].level
			 *errors[i].function
			 *errors[i].class
			 *errors[i].file
			 */
		}
		div.setHTML(html);
		kfm_modal_open(div,kfm.lang.Errors);
	},
	showMessages:function(messages){
		var message='';
		for(var i=0;i<messages.length;i++){
			message+=messages[i].message+'<hr>';
		}
		new Notice(message);
	},
	build:function(){
		var form_panel,form,right_column,directories,logs,logHeight=64,w=window.getSize().size,j,i;
		{ // extend language objects
			for(var j in kfm.lang){
				if(kfm_regexps.percent_numbers.test(kfm.lang[j])){
					kfm.lang[j]=(function(str){
						return function(){
							var tmp=str;
							for(i=1;i<arguments.length+1;++i)tmp=tmp.replace("%"+i,arguments[i-1]);
							return tmp;
						};
					})(kfm.lang[j]);
				}
			}
		}
		kfm_cwd_name=starttype;
		$(document.body).setStyle('overflow','hidden');
		/*kfm_addContextMenu(document.body,function(e){
			var links=[['kfm.about()',kfm.lang.AboutKfm]];
			kfm_createContextMenu(e.page,links);
		});*/
		{ // create left column
			var left_column=kfm_createPanelWrapper('kfm_left_column');
			kfm_resizeHandler_addMaxHeight( 'kfm_left_column' );
			
			/// #### Change		pBaran		12/12/2007		Foliovision
			/// ## Rearanged and disabled panels
			kfm_addPanel(left_column,'kfm_search_panel');
			kfm_addPanel(left_column,'kfm_directories_panel');
			//kfm_addPanel(left_column,'kfm_widgets_panel');
			//kfm_addPanel(left_column,'kfm_directory_properties_panel');
			/// #### End of Change		pBaran		12/12/2007
			
			///foliovision
			//kfm_addPanel( left_column, 'kfm_admin_panel' );
			
			if(!kfm_inArray('kfm_logs_panel',kfm_hidden_panels))kfm_addPanel(left_column,'kfm_logs_panel');
			left_column.panels_unlocked=1;
			left_column.setStyles('height:'+w.y+'px');
			kfm_addContextMenu(left_column,function(e){
				var links=[],i;
				var l=left_column.panels_unlocked;
				links.push(['kfm_togglePanelsUnlocked()',l?kfm.lang.LockPanels:kfm.lang.UnlockPanels,l?'lock':'unlock']);
				var ps=left_column.panels;
				for(var i=0;i<ps.length;++i){
					var p=$(ps[i]);
					if(!p.visible && !kfm_inArray(ps[i],kfm_hidden_panels))links.push(['kfm_addPanel("kfm_left_column","'+ps[i]+'")',kfm.lang.ShowPanel(p.panel_title),'show_panel']);
				}
				kfm_createContextMenu(e.page,links);
			});
		}
		{ // create right_column
			right_column=new Element('div',{
				'id':'kfm_right_column'
			});
			right_column.addEvent('click',function(e){
				e=new Event(e);
				if(e.rightClick)return;
				if(!window.dragType)kfm_selectNone()
			});
			right_column.addEvent('mousedown',function(e){
				e=new Event(e);
				if(e.rightClick)return;
				window.mouseAt=e.page;
				if(this.contentMode=='file_icons' && this.fileids.length)window.dragSelectionTrigger=setTimeout(function(){kfm_selection_dragStart()},200);
				right_column.addEvent('mouseup',kfm_selection_dragFinish);
			});
			/*kfm_addContextMenu(right_column,function(e){
				var links=[];
				//links.push(['kfm_createEmptyFile()',kfm.lang.CreateEmptyFile,'filenew',!kfm_vars.permissions.file.mk]);
				//if(selectedFiles.length>1)links.push(['kfm_renameFiles()',kfm.lang.RenameFile,'edit',!kfm_vars.permissions.file.ed]);
				//if(selectedFiles.length>1)links.push(['kfm_zip()',kfm.lang.ZipUpFiles,'',!kfm_vars.permissions.file.mk]);
				//if(selectedFiles.length!=$('kfm_right_column').fileids.length)links.push(['kfm_selectAll()',kfm.lang.SelectAll,'ark_selectall']);
				if(selectedFiles.length){
					links.push(['kfm_selectNone()',kfm.lang.SelectNone,'select_none']);
					links.push(['kfm_selectInvert()',kfm.lang.InvertSelection,'invert_selection']);
				}
				//links.push([]);
				kfm_createContextMenu(e.page,links);
			});*/
			right_column.parentResized=kfm_files_panelResized;
		}
		{ // draw areas to screen and load files and directory info
			kfm.addEl($(document.body).empty(),[left_column,right_column]);
			x_kfm_loadFiles(1,kfm_refreshFiles);
			x_kfm_loadDirectories(1,kfm_refreshDirectories);
		}
		document.addEvent('keyup',kfm.keyup);
		window.addEvent('resize',kfm_resizeHandler);
		kfm_contextmenuinit();
	},
	confirm:function(txt){
		window.inPrompt=1;
		var ret=confirm(txt);
		setTimeout('window.inPrompt=0',1);
		return ret;
	},
	getContainer:function(p,c){
		for(var i=0;i<c.length;++i){
			var a=c[i],x=getOffset(a,'Left'),y=getOffset(a,'Top');
			if(x<p.x&&y<p.y&&x+a.offsetWidth>p.x&&y+a.offsetHeight>p.y)return a;
		}
	},
	getParentEl:function(c,t){
		while(c.tagName!=t&&c)c=c.parentNode;
		return c;
	},
	initialize:function(){
		document.addEvent('domready',this.build);
	},
	keyup:function(e){
		var e=new Event(e);
		var key=e.code;
		var cm=$('kfm_right_column').contentMode;
		switch(key){
			case 13:{ // enter
				if(!selectedFiles.length||window.inPrompt||cm!='file_icons')return;
				kfm_chooseFile();
				break;
			}
			case 27:{ // escape
				if(cm=='lightbox')kfm_img_stopLightbox();
				else if(!window.inPrompt&&kfm.confirm(kfm.lang.AreYouSureYouWantToCloseKFM))window.close();
				break;
			}
			case 37:{ // left arrow
				if(cm=='file_icons'){
					if(!kfm_listview)kfm_shiftFileSelectionLR(-1);
				}
				else if(cm=='lightbox'){
					window.kfm_slideshow_stopped=1;
					if(window.lightbox_slideshowTimer)clearTimeout(window.lightbox_slideshowTimer);
					window.kfm_slideshow.at-=2;
					kfm_img_startLightbox();
				}
				else break;
				e.stopPropagation();
				break;
			}
			case 38:{ // up arrow
				if(cm=='file_icons'){
					if(kfm_listview)kfm_shiftFileSelectionLR(-1);
					else kfm_shiftFileSelectionUD(-1);
				}
				break;
			}
			case 39:{ // right arrow
				if(cm=='file_icons'){
					if(!kfm_listview)kfm_shiftFileSelectionLR(1);
				}
				else if(cm=='lightbox'){
					window.kfm_slideshow_stopped=1;
					if(window.lightbox_slideshowTimer)clearTimeout(window.lightbox_slideshowTimer);
					kfm_img_startLightbox();
				}
				else break;
				e.stopPropagation();
				break;
			}
			case 40:{ // down arrow
				if(cm=='file_icons'){
					if(kfm_listview)kfm_shiftFileSelectionLR(1);
					else kfm_shiftFileSelectionUD(1);
				}
				break;
			}
			case 46:{ // delete
				if(!selectedFiles.length||cm!='file_icons')return;
				if(selectedFiles.length>1)kfm_deleteSelectedFiles();
				else kfm_deleteFile(selectedFiles[0]);
				break;
			}
			case 65:{ // a
				if(e.control&&cm=='file_icons'){
					clearSelections(e);
					kfm_selectAll();
				}
				break;
			}
			case 85:{ // u
				if(e.control&&cm=='file_icons'){
					clearSelections(e);
					kfm_selectNone();
				}
				break;
			}
			case 113:{ // f2
				if(cm!='file_icons')return;
				if(!selectedFiles.length)return kfm.alert(kfm.lang.PleaseSelectFileBeforeRename);
				if(selectedFiles.length==1){
					kfm_renameFile(selectedFiles[0]);
				}
				else kfm.alert(kfm.lang.RenameOnlyOneFile);
				break;
			}
		}
	}
});
function kfm_inArray(needle,haystack){
	/// #### Change		pBaran		18/12/2007		Foliovision
	/// Serious bug. DO NOT increment i before using it in for cycle when iterating through array. Array starts at "0" and ends at "haystack.length - 1"
	//for(var i=0;i<haystack.length;++i)if(haystack[i]==needle)return true;
	for(var i=0;i<haystack.length;i++)if(haystack[i]==needle)return true;
	/// #### End of Change		pBaran		18/12/2007
	return false;
}
function kfm_log(msg){
	var wrapper=$('kfm_logs_panel');
	if(!wrapper){
		if(msg.indexOf(kfm.lang.ErrorPrefix)!=0 && msg.indexOf('error: ')!=0)return;
		if(kfm_inArray('kfm_logs_panel',kfm_hidden_panels))return kfm.alert(msg.replace(kfm.lang.ErrorPrefix,'').replace('error: ',''));
		kfm_addPanel('kfm_left_column','kfm_logs_panel');
		kfm_refreshPanels('kfm_left_column');
		wrapper=$('kfm_logs_panel');
	}
	wrapper.visible=1;
	var el=$E('#kfm_logs_panel div.kfm_panel_body'),p=(new Element('p')).setHTML(msg);
	if(msg.indexOf(kfm.lang.ErrorPrefix)==0)p.setStyles('background:#ff0;fontWeight:bold;color:red');
	kfm.addEl(el,p);
	el.scrollTop=el.scrollTop+p.offsetHeight;
}
function kfm_prompt(txt,val,fn){
	window.inPrompt=1;
	var table=new Element('table',{
		'id':'kfm_prompt_table'
	});
	var row=table.insertRow(0),inp=newInput('kfm_prompt',0,val);
	row.insertCell(0).innerHTML=txt.replace(/\n/g,'<br />');
	row.insertCell(1).appendChild(inp);
	kfm_modal_open(table,'prompt',[[kfm.lang.Ok,function(){
		var v=$('kfm_prompt').value;
		kfm_modal_close();
		window.inPrompt=0;
		fn(v);
	}]]);
	$('kfm_prompt').focus();
}
/// Added		pBaran		08/01/2008		Foliovision
function kfmAdd_run_delayed( strName, strCall, iDelay ){
	strName = strName + '_timeout';
	if( window[strName] ) $clear( window[strName] );
	window[strName] = setTimeout( strCall, iDelay );
}
/// End of add		pBaran		08/01/2008
function kfm_run_delayed(name,call){
	name=name+'_timeout';
	if(window[name])$clear(window[name]);
	window[name]=setTimeout(call,500);
}
function kfm_shrinkName(name,wrapper,text,size,maxsize,extension){
	var position=step=Math.ceil(name.length/2),postfix='[...]'+extension,prefix=size=='offsetHeight'?'. ':'';
	do{
		text.innerHTML=prefix+name.substring(0,position)+postfix;
		step=Math.ceil(step/2);
		position+=(wrapper[size]>maxsize)?-step:step;
	}while(step>1);
	var html='<span class="filename">'+name.substring(0,position+(prefix?0:-1))+'</span><span style="color:red;text-decoration:none">[...]</span>';
	if(extension)html+='<span class="filename">'+extension+'</span>';
	text.innerHTML=html;
}
var kfm_regexps={
	all_up_to_last_dot:/.*\./,
	all_up_to_last_slash:/.*\//,
	ascii_stuff:/%([89A-F][A-Z0-9])/g,
	get_filename_extension:/.*\.([^.]*)$/,
	percent_numbers:/%[1-9]/,
	plus:/\+/g,
	remove_filename_extension:/\.[^.]*$/
}
var kfm=new KFM();
