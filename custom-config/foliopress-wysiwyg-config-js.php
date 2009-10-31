<?php require_once( dirname( __FILE__ ) . '/../foliopress-wysiwyg-class.php' ); ?>

<?php $options = get_option( FV_FCK_OPTIONS ); ?>

/// PLEASE CHANGE THIS FILE ONLY IF YOU KNOW WHAT YOU ARE DOING !!!!!

/*
You can add any ToolbarSet in here and it will load automaticaly into Options
page of FolioPress WYSIWYG. Just make sure that code you add is correct, because
it may cause your Wordpress page to crash.
*/

/*
If you want to add skin, just copy it into default skins directory, it will
automatically load into Options page. Again make sure skin you are adding is
correct.
*/

FCKConfig.ToolbarSets["Default"] = [ 
	['Source','DocProps','-','Save','NewPage','Preview','-','Templates'], 
	['Cut','Copy','Paste','PasteText','PasteWord','-','Print','SpellCheck'], 
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'], 
	['Form','Checkbox','Radio','TextField','Textarea','Select','Button','ImageButton','HiddenField'], 
	'/', 
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'], 
	['OrderedList','UnorderedList','-','Outdent','Indent'], 
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'], 
	['Link','Unlink','Anchor'], 
	['Image','Flash','Table','Rule','Smiley','SpecialChar','PageBreak'], 
	'/', 
	['Style','FontFormat','FontName','FontSize'], 
	['TextColor','BGColor'], 
	['FitWindow','-','About'] 
];

FCKConfig.ToolbarSets["Basic"] = [ 
	['Source', 'Bold','Italic','-','OrderedList','UnorderedList','-','Link','Unlink','-','About'] 
];

FCKConfig.ToolbarSets["Foliovision"] = [
	['Cut','Copy','Paste','foliopress-paste','-','Bold','Italic','-','RemoveFormat','-','OrderedList','UnorderedList','-','Outdent','Indent','Blockquote','-','Link','Unlink','Anchor','-','foliopress-more','-','kfmBridge','-','Source','-','FitWindow']
	//wp_buttons,
];

FCKConfig.ToolbarSets["Foliovision-Full"] = [ 
	['Cut','Copy','Paste','-','Undo','Redo','-','Bold','Italic','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyFull','-','OrderedList','UnorderedList','-','Outdent','Indent','-','Link','Unlink','Anchor','-','kfmBridge'], 
	//['Subscript','Superscript','-', 
	//media_buttons, 
	'/', 
	//['Style'], 
	//['Find','Replace','-', , 
	//['FontName','FontSize'], 
	//['TextColor','BGColor'], 
	//['GoogleSpellCheck'], 
	['FontFormat','RemoveFormat','-','Replace','Table','Rule','SpecialChar','-','foliopress-more','foliopress-next','-','Source','-','FitWindow']
];

FCKConfig.ToolbarSets["Custom"] = [ <?php echo stripslashes($options['customtoolbar']); ?> ];


/* This will be applied to the body element of the editor
This is the right way to make your textarea look the same way it does in the browser.
You need to have a unique identifier on your actual final content enclosing element.
You need to configure this with a custom stylesheet - 
use @import to bring in your own stylesheet.
It's quite possible to make this work you will need to rewrite your css file somewhat 
to make all the content area elements use only #content element instead of
div#content element. Good luck! Hopefully future editions of FCK will make
true WYSIWYG easier.
*/
FCKConfig.BodyId = '<?php echo $options['bodyid']; ?>' ;
FCKConfig.BodyClass = '<?php echo $options['bodyclass']; ?>' ;

/// Added for version 0.9.6


/* These are paths you don't want to change unless you really know what you are doing.
You've been warned. */

FCKConfig.ImageBrowserURL = FCKConfig.BasePath+'plugins/kfm/?lang='+FCKConfig.DefaultLanguage+'&kfm_caller_type=fck&type=Image';
FCKConfig.ImageUploadURL = FCKConfig.BasePath+'plugins/kfm/?lang='+FCKConfig.DefaultLanguage+'&kfm_caller_type=fck&type=Image';

FCKConfig.SkinPath = FCKConfig.BasePath + 'skins/<?php print( $fp_wysiwyg->aOptions[fp_wysiwyg_class::FVC_SKIN] ); ?>/';

FCKConfig.Plugins.Add( 'kfm' );
FCKConfig.Plugins.Add( 'kfmBridge' );
FCKConfig.Plugins.Add( 'foliopress-wp' );
FCKConfig.EditorAreaCSS = FCKConfig.BasePath + '../../custom-config/foliopress-editor.css';
if( FCKConfig.BodyId || FCKConfig.BodyClass ) FCKConfig.EditorAreaCSS = '<?php bloginfo('stylesheet_url'); ?>';

FCKConfig.Plugins.Add( 'foliopress-clean' );
<?php  
	if( count( $fp_wysiwyg->aOptions[fp_wysiwyg_class::FVC_FPC_TEXTS] ) ){
		print( 'FCKConfig.FPClean_SpecialText = [' );
		$aFP = $fp_wysiwyg->aOptions[fp_wysiwyg_class::FVC_FPC_TEXTS];
		$iFP = count( $aFP );
		for( $i=0; $i<$iFP; $i++ ){

			if( $i < $iFP - 1 ) print( " '".$aFP[$i]."'," );
			else print( " '".$aFP[$i]."' " );
		}
		print( "];\n" );
	}
?>
FCKConfig.FPClean_Tags = 'p|div';

FCKConfig.RemoveFormatTags = 'b,big,del,dfn,em,font,i,ins,kbd,q,samp,small,span,strike,strong,sub,sup,tt,u,var' ;

/// Added for version 0.4
FCKConfig.Plugins.Add( 'foliopress-preformated' );
//FCKConfig.Plugins.Add( 'foliopress-table-cleanup' );
//FCKConfig.Plugins.Add( 'foliopress-word-cleanup' );