// see ../license.txt for licensing
function kfm_changeCaption(id){
	var table=$extend(new Element('table',{
		'id':'kfm_newCaptionDetails'
	}),{kfm_caption_for:id});
	var row=table.insertRow(0),textarea=newInput('kfm_new_caption','textarea',File_getInstance(id).caption);
	textarea.setStyles('height:50px;width:200px');
	row.insertCell(0).appendChild(newText(kfm.lang.NewCaption));
	row.insertCell(1).appendChild(textarea);
	kfm_modal_open(table,kfm.lang.ChangeCaption,[[kfm.lang.ChangeCaption,kfm_changeCaption_set]]);
	$('kfm_new_caption').focus();
}
function kfm_changeCaption_set(){
	var id=$('kfm_newCaptionDetails').kfm_caption_for,newCaption=$('kfm_new_caption').value;
	if(!newCaption||newCaption==File_getInstance(id).caption)return;
	kfm_modal_close();
	if(kfm.confirm(kfm.lang.NewCaptionIsThisCorrect(newCaption))){
		kfm_log(kfm.lang.Log_ChangeCaption(id,newCaption));
		x_kfm_changeCaption(id,newCaption,kfm_refreshFiles);
	}
}
function kfm_img_startLightbox(id){
	window.lightbox_oldCM=$('kfm_right_column').contentMode;
	$('kfm_right_column').contentMode='lightbox';
	if(id&&$type(id)=='array'){
		window.kfm_slideshow={ids:id,at:0};
		id=0;
	}
	if(!id){
		window.kfm_slideshow.at++;
		document.title='KFM Slideshow: '+window.kfm_slideshow.at;
		id=window.kfm_slideshow.ids[window.kfm_slideshow.at%window.kfm_slideshow.ids.length];
	}
	var el,data=File_getInstance(id),ws=window.getSize().size,oldEl=$('kfm_lightboxImage'),wrapper=$('kfm_lightboxWrapper');
	if(!wrapper){
		wrapper=new Element('div',{
			'id':'kfm_lightboxWrapper',
			'styles':{
				'position':'absolute',
				'left':0,
				'z-index':1,
				'top':0,
				'width':ws.x,
				'height':ws.y
			}
		});
		wrapper.addEvent('click',kfm_img_stopLightbox);
		document.body.appendChild(wrapper);
		wrapper.focus();
	}
	if(!$('kfm_lightboxShader')){
		el=new Element('div',{
			'id':'kfm_lightboxShader',
			'styles':{
				'width':ws.x,
				'height':ws.y,
				'background':'#000',
				'opacity':'.7'
			}
		});
		wrapper.appendChild(el);
	}
	if(oldEl)oldEl.remove();
	var w=data.width,h=data.height,url='get.php?id='+id,r=0;
	if(!w||!h){
		kfm_log(kfm.lang.NotAnImageOrImageDimensionsNotReported);
		return kfm_img_stopLightbox();
	}
	if(w>ws.x*.9||h>ws.y*.9){
		if(w>ws.x*.9){
			r=.9*ws.x/w;
			w*=r;
			h*=r;
		}
		if(h>ws.y*0.9){
			r=.9*ws.y/h;
			w*=r;
			h*=r;
		}
		url+='&width='+parseInt(w)+'&height='+parseInt(h);
	}
	el=new Element('img',{
		'id':'kfm_lightboxImage',
		'src':url,
		'styles':{
			'position':'absolute',
			'left':parseInt((ws.x-w)/2),
			'top':parseInt((ws.y-h)/2),
			'z-index':2
		}
	});
	if(window.kfm_slideshow&&!window.kfm_slideshow_stopped){
		el.addEvent('load',function(){
			window.lightbox_slideshowTimer=setTimeout('kfm_img_startLightbox()',kfm_slideshow_delay);
		});
	}
	wrapper.appendChild(el);
	kfm_resizeHandler_add('kfm_lightboxShader');
	kfm_resizeHandler_add('kfm_lightboxWrapper');
}
function kfm_img_stopLightbox(e){
	e=new Event(e);
	if(e.rightClick)return;
	var wrapper=$('kfm_lightboxWrapper');
	if(wrapper)wrapper.remove();
	window.kfm_slideshow=window.kfm_slideshow_stopped=null;
	if(window.lightbox_slideshowTimer)clearTimeout(window.lightbox_slideshowTimer);
	$('kfm_right_column').contentMode=window.lightbox_oldCM;
	kfm_resizeHandler_remove('kfm_lightboxShader');
	kfm_resizeHandler_remove('kfm_lightboxWrapper');
}
function kfm_resizeImage(id){
	var data=File_getInstance(id);
	var txt=kfm.lang.CurrentSize(data.width,data.height);
	kfm_prompt(txt+kfm.lang.NewWidth,data.width,function(x){
		x=parseInt(x);
		if(!x)return;
		txt+=kfm.lang.NewWidthConfirmTxt(x);
		kfm_prompt(txt+kfm.lang.NewHeight,Math.ceil(data.height*(x/data.width)),function(y){
			y=parseInt(y);
			if(!y)return;
			if(kfm.confirm(txt+kfm.lang.NewHeightConfirmTxt(y)))x_kfm_resizeImage(id,x,y,kfm_refreshFiles);
		});
	});
}
function kfm_cropImage(id){
	var data=File_getInstance(id);
	var div=document.createElement('DIV');
	div.style.position='absolute';
	div.id='cropperdiv';
	div.style.top=0;
	div.style.left=0;
	div.style.width='100%';
	div.style.height='100%';
	div.style.backgroundColor='#ddf';
	div.onclick=function(){this.style.display='none';}

	var ifr = document.createElement('IFRAME');
	ifr.src = 'plugins/cropper/croparea.php?id='+id+'&width='+data.width+'&height='+data.height;
	ifr.style.width = '100%';
	ifr.style.height = '100%'; //100% - 25px
	div.appendChild(ifr);
	document.body.appendChild(div);
}
function kfm_cropToOriginal(id,coords,dimensions){
	var F=File_getInstance(id);
	document.getElementById('cropperdiv').style.display = 'none';
	x_kfm_cropToOriginal(id, coords.x1, coords.y1, dimensions.width, dimensions.height, function(id){
		if($type(id)=='string')return kfm_log(id);
		F.setThumbnailBackground($('kfm_file_icon_'+id),true);
	});
}
function kfm_cropToNew(id, coords, dimensions){
	var filename=File_getInstance(id).name;
	kfm_prompt(kfm.lang.RenameFileToWhat(filename),filename,function(newName){
		if(!newName||newName==filename)return;
		document.getElementById('cropperdiv').style.display = 'none';
		x_kfm_cropToNew(id, coords.x1, coords.y1, dimensions.width, dimensions.height, newName, kfm_refreshFiles);
	});
}
function kfm_returnThumbnail(id,size){
	if(!size)size='64x64';
	valid=1;
	kfm_prompt(kfm.lang.WhatMaximumSize,size,function(size){
		if(!size)return;
		if(!/^[0-9]+x[0-9]+$/.test(size)){
			alert('The size must be in the format XXxYY, where X is the width and Y is the height');
			valid=0;
		}
		if(!valid)return kfm_returnThumbnail(id,size);
		var x=size.replace(/x.*/,''),y=size.replace(/.*x/,'');
		x_kfm_getFileUrl(id,x,y,function(url){
			if(kfm_file_handler=='return'||kfm_file_handler=='fckeditor'){
				window.opener.SetUrl(url,0,0,File_getInstance(id).caption);
				/// Addition    29/10/09    Foliovision
				if(bMultipleImagePosting!=1)
				/// End of addition
				    window.close();
			}
			else if(kfm_file_handler=='download'){
				if(/get.php/.test(url))url+='&forcedownload=1';
				document.location=url;
			}
		});
	});
}
function kfm_rotateImage(id,direction){
	var F=File_getInstance(id);
	x_kfm_rotateImage(id,direction,function(id){
		if($type(id)=='string')return kfm_log(id);
		F.setThumbnailBackground($('kfm_file_icon_'+id),true);
	});
}

/// #### Add		pBaran		07/12/2007 - 18/12/2007		Foliovision

/**
 * returns file name striped of extension and certain characters( '-', '.', '_' ) are changed to white space
 *
 *	sName:			String - Name of the image, or any other string that should be changed
 *
 **/ 
function kfmAdd_correctImageName( sName ){
	var iPos = sName.lastIndexOf( '.' );
	var sReturn = sName;
	if( iPos > 0 ) sReturn = sName.substring( 0, iPos );

	sReturn = sReturn.replace( /-/g, " " );
	sReturn = sReturn.replace( /\./g, " " );
	sReturn = sReturn.replace( /_/g, " " );
	
	return sReturn;
}

/**
 *	returns XHTML code of image back from input info
 *
 *	aImageSize:		Array - Array returned from "getimagesize" PHP function ([0] int-ImageWidth; [1] int-ImageHeight; [2] ?; [3] String-Properly Formated IMG width and height properties of HTML)
 *	sUrl:				String - Url of the image
 *	id:				Integer - Id of the image file in KFM hierarchy
 *	bFormat:			Bool - Specifies if output should be also wraped in H5 tag (Foliovision style to insert nicely formated images)
 *	sOriginalUrl:	String - Url of the original image used
 *	 
 **/ 
function kfmAdd_ImageTagText( aImageSize, sUrl, id, bFormat, sOriginalUrl ){
	var sHtmlCode = "";
	
	var myFile = File_getInstance( id );
   if( myFile ){
   	var sAlt = kfmAdd_correctImageName( myFile.name );
   	
   	var iWidth = 0;
   	try{
   		iWidth = aImageSize[0];
   	}catch( ex ){}

      sOriginalUrl = sOriginalUrl.replace( '//', '/' );
		if( iImageLink == 1 ) sHtmlCode += '<a href="' + sOriginalUrl + '" title="' + sAlt + '"';
		if( iImageLink == 1 && iLinkLightbox == 1 ) sHtmlCode += ' rel="lightbox[slideshow]"';
		if( iImageLink == 1 ) sHtmlCode += '>';
      sHtmlCode += '<img src="' + sUrl + '"';
      if( iWidth != 0 ) sHtmlCode += aImageSize[3] + " ";
      sHtmlCode += 'alt="' + sAlt + '" />';
      if( iImageLink == 1 ) sHtmlCode += '</a>';
      
      if( bFormat ) sHtmlCode = "<h5>" + sHtmlCode + "<br />" + sAlt + "</h5>";
   }

   return sHtmlCode;
}

/**
 * returns corretly formated html code of <img> enclosed in <h5> tag of the requested to opener
 * Image taken is not resized
 *
 *	id:				Integer - Id of the image file in KFM hierarchy
 *	size:				Integer - Size of image to return to sender (0 is for original)
 *
 **/ 
function kfmAdd_returnImageWithFormating( id, size ){
	x_kfm_getFilesUrlAndSizes( id, size, size, function( aUrlSize ){
		if( kfm_file_handler=='return' || kfm_file_handler=='fckeditor' ){
			var sHtmlCode = kfmAdd_ImageTagText( aUrlSize[1], aUrlSize[0], id, true, aUrlSize[2] );
			window.opener.FCKSetHTML( sHtmlCode );
			/// Addition    29/10/09    Foliovision
			if(bMultipleImagePosting!=1)
			/// End of addition
                window.close();
		}
	});
}

/**
 * returns html code of <img> requested to opener
 * Image taken is not resized
 **/
function kfmAdd_returnImage( id ){
	x_kfm_getFileUrl(id,0,0,function(url){
      if( kfm_file_handler=='return' || kfm_file_handler=='fckeditor' ){
      	x_kfm_getImageSize( url, function( aImageSize ){
      		var sHtmlCode = kfmAdd_ImageTagText( aImageSize, url, id, false, url );
         	window.opener.FCKSetHTML( sHtmlCode );
         	/// Addition    29/10/09    Foliovision
			if(bMultipleImagePosting!=1)
			/// End of addition
				window.close();
			});
		}
   });
}

///  Addition    23/07/09    Foliovision
/**
 * returns html code of <img> requested to opener
 * Image taken is not resized
 **/
function kfmAdd_returnImageThumbnail( id, size ){
	x_kfm_getFilesUrlAndSizes( id, size, size, function( aUrlSize ){
		/*if( kfm_file_handler=='return' || kfm_file_handler=='fckeditor' ){
			var sHtmlCode = kfmAdd_ImageTagText( aUrlSize[1], aUrlSize[0], id, true, aUrlSize[2] );
			window.opener.FCKSetHTML( sHtmlCode );
			window.close();
		}*/
		if( g_objPreviewWindow == null || g_objPreviewWindow.closed ){
			g_objPreviewWindow = window.open( aUrlSize[0], "preview" );
		}else if( g_strPreviousUrl != aUrlSize[0] ){
			g_objPreviewWindow = window.open( aUrlSize[0], "preview" );
			g_objPreviewWindow.focus();
		}else{
			g_objPreviewWindow.focus();
		}

		g_strPreviousUrl = aUrlSize[0];
		/// Addition    29/10/09    Foliovision
		if(bMultipleImagePosting!=1)
		/// End of addition
            window.close();
	});
	

}
///  End of Addition    23/07/09    Foliovision

/**
 *	returns url of image selected to send to native FCK Image Editor
 *
 *	id:				Integer - Id of the image file in KFM hierarchy
 *	size:				Integer - Size of image to return to sender (0 is for original)
 *
 **/   
function kfmAdd_returnSetUrl( id, size ){
	x_kfm_getFileUrl( id, size, size, function(url){
      if( kfm_file_handler=='return' || kfm_file_handler=='fckeditor' ){
      	var sAlt = kfmAdd_correctImageName( File_getInstance( id ).name );
        window.opener.SetUrl( url, 0, 0, sAlt );
        /// Addition    29/10/09    Foliovision
        if(bMultipleImagePosting!=1)
        /// End of addition
            window.close();
		}
   });
}

/**
 *	returns url of any file to FCKEditor window on position of cursor
 *
 *	id:				Integer - Id of file in KFM hierarchy 
 *
 **/     
function kfmAdd_returnPlainUrl( id ){
	x_kfm_getFileUrl( id, 0, 0, function(url){
      if( kfm_file_handler=='return' || kfm_file_handler=='fckeditor' ){
        window.opener.FCKSetHTML( url );
        /// Addition    29/10/09    Foliovision
        if(bMultipleImagePosting!=1)
        /// End of addition
            window.close();
		}
   });
}

/**
 *	Function that copies file url into clipboard
 *
 *	id:				Integer - Id of file in KFM hierarchy
 *	size:				Integer - Size of image thumbnail, if it is an image
 *
 **/
function kfmAdd_copyUrl( id, size ){
	x_kfm_getFileUrl( id, size, size, function(url){
      CopyToClipboard( url );
   });
}

/**
 *	Function that copies text into clipboard
 *
 *	sText:			String - Text to be copied into clipboard
 *
 **/
function CopyToClipboard( sText ){
	if( is_ie5up ){
	
		window.clipboardData.setData( "Text", sText );
		
   }else if( is_gecko ){
   	try {
			netscape.security.PrivilegeManager.enablePrivilege( "UniversalXPConnect" );
		}catch (err) {   
			alert( "Sorry, but mozilla blocks clipboard copy:\n\n" +err+ "." );
			return false;
		}

   	var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance( Components.interfaces.nsIClipboard );
		if( !clip ) return false;

		var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance( Components.interfaces.nsITransferable );
		if( !trans ) return false;

		trans.addDataFlavor('text/unicode');

		var str = Components.classes["@mozilla.org/supports-string;1"].createInstance( Components.interfaces.nsISupportsString );
		var copytext = sText;

		str.data=copytext;
		trans.setTransferData( "text/unicode", str, copytext.length*2 );

		var clipid = Components.interfaces.nsIClipboard;
		if( !clipid ) return false;

		clip.setData( trans, null, clipid.kGlobalClipboard );
	}
	
   return true;
}