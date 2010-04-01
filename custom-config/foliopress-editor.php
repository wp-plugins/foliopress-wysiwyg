/*
 * Foliopress FCKeditor Styles - http://www.foliovison.com
 * Copyright (C) 2007 Foliovision
 *
 * == BEGIN LICENSE ==
 *
 * Licensed under the Creative Commons: Attribution and Non-distribution
 *
 * == END LICENSE ==
 *
 * This is the replacement for the default CSS file used by the editor area. 
 * With our version it should be much easier to achieve real WYSIWGY.
 * t defines the
 * initial font of the editor and background color.
 *
 *Conventionally a user can configure the editor to use another CSS file via
*  FCKConfig.EditorAreaCSS key in the configuration file.
*  It's easier to use @import as we have below here as you can add any 
*  modifications below the @import command.
*  You may want to change the "body" styles to match your editor web site, for
*  background color and font family and size.
*  
*  Our default font size is 16 as we like to see and read what we
*  our editing.
*/

<?php require_once( dirname( __FILE__ ) . '/../foliopress-wysiwyg-class.php' ); ?>

<?php $options = get_option( FV_FCK_OPTIONS ); ?>

<?php if( $options['bodyclass'] || $options['bodyid'] ) : ?>
	@import url("<?php bloginfo('stylesheet_url'); ?>");
<?php endif; ?>

<?php if( trim( $options['bodyid'] ) != '' || trim( $options['bodyclass'] ) != ''  ) : ?>
<?php echo $options['wysiwygstyles']; ?>
<?php else : ?>
body {
	background-color: #FFFFFF;
	padding: 5px 5px 5px 5px;
	margin: 0px;
	width: 600px;
}

body, td {
	font-family: Arial, Verdana, Sans-Serif;
	font-size: 16px;
}

a[href] {
	color: #0000FF !important;
}

.Bold {
	font-weight: bold;
}

.Title {
	font-weight: bold;
	font-size: 20px;
	color: #cc3300;
}

.Code {
	border: #8b4513 1px solid;
	padding-right: 5px;
	padding-left: 5px;
	color: #000066;
	font-family: 'Courier New' , Monospace;
	background-color: #ff9933;
}

/* Images in h5 */

a img {
	border: none;
}

h5 {
	margin: 10px;
	padding: 0;
	line-height: 1.3em;
	font-size: 0.85em;
	font-weight: normal;
	text-align: center;
}

h5 img {
	padding: 5px;
	background-color: white;
	border: 1px solid silver;
}
	
h5.right {
	margin: 2px 0 2px 10px;
	text-align: center;
	font-weight: normal;
	float: right;
	}
	
h5.left {
	margin: 2px 10px 2px 0;
	text-align: center;
	font-weight: normal;
	float: left;
	}
	
h5.center {
	text-align: center;
	font-weight: normal;
	}
	
h5.center img {
	margin: 0 auto;
	}
	
h5 a {
	text-decoration: none !important;
	color: #696969;
	}

h5 a:link {
	text-decoration: none !important;
	color: #696969;
	}
	
h5 a.hide-link:hover, h5 a.hide-link:focus, h5 a.hide-link:active, h5 a.hide-link:visited {
	text-decoration: none !important;
	color: #696969;
}

h5.noborder img {
	border: none;
}

img.noborder {
	border: none;
}

.noborder {
	border: none;
} 

/*  colorful text   */

.red {
    color: red;
}
<?php endif; ?>