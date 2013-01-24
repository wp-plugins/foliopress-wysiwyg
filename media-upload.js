// send html to the post editor
function send_to_editor(h) {
	var oEditor = FCKeditorAPI.GetInstance('content') ;
	if (typeof oEditor != 'undefined')
	{
		oEditor.InsertHtml(h);
	}
	/*if ( typeof tinyMCE != 'undefined' && ( ed = tinyMCE.activeEditor ) && !ed.isHidden() ) {
		ed.focus();
		if (tinymce.isIE)
			ed.selection.moveToBookmark(tinymce.EditorManager.activeEditor.windowManager.bookmark);

		if ( h.indexOf('[caption') != -1 )
			h = ed.plugins.wpeditimage._do_shcode(h);
		
		ed.execCommand('mceInsertContent', false, h);
	} else
		edInsertContent(edCanvas, h);
	*/
	tb_remove();
}

// thickbox settings
jQuery(function($) {
	tb_position = function() {
		var tbWindow = $('#TB_window');
		var width = $(window).width();
		var H = $(window).height();
		var W = ( 720 < width ) ? 720 : width;

		if ( tbWindow.size() ) {
			tbWindow.width( W - 50 ).height( H - 45 );
			$('#TB_iframeContent').width( W - 50 ).height( H - 75 );
			tbWindow.css({'margin-left': '-' + parseInt((( W - 50 ) / 2),10) + 'px'});
			if ( typeof document.body.style.maxWidth != 'undefined' )
				tbWindow.css({'top':'20px','margin-top':'0'});
			$('#TB_title').css({'background-color':'#222','color':'#cfcfcf'});
		};

		return $('a.thickbox').each( function() {
			var href = $(this).attr('href');
			if ( ! href ) return;
			href = href.replace(/&width=[0-9]+/g, '');
			href = href.replace(/&height=[0-9]+/g, '');
			$(this).attr( 'href', href + '&width=' + ( W - 80 ) + '&height=' + ( H - 85 ) );
		});
	};
	
	jQuery('a.thickbox').click(function(){
		var oEditor = FCKeditorAPI.GetInstance('content') ;
		if (typeof oEditor != 'undefined')
		{
			oEditor.Focus();
		}
/*		if ( typeof tinyMCE != 'undefined' &&  tinyMCE.activeEditor ) {
			tinyMCE.get('content').focus();
			tinyMCE.activeEditor.windowManager.bookmark = tinyMCE.activeEditor.selection.getBookmark('simple');
		}*/
	});

	$(window).resize( function() { tb_position() } );
});
