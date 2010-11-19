/*
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2005 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 *    http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 *    http://www.fckeditor.net/
 * 
 * File Name: fckplugin.js
 * 
 */
FCKCommands.RegisterCommand('FVWPFlowplayer', new FCKDialogCommand( 'FVWPFlowplayer', 'FV WP Flowplayer', FCKPlugins.Items['FVWPFlowplayer'].Path + 'fv_wp_flowplayer.php', 500, 300 ) ) ;

// Create the "Abbr" toolbar button.
var oAbbrItem = new FCKToolbarButton( 'FVWPFlowplayer', 'Insert Flowplayer' ) ;
oAbbrItem.IconPath = FCKPlugins.Items['FVWPFlowplayer'].Path + 'icon.png' ;
FCKToolbarItems.RegisterItem( 'FVWPFlowplayer', oAbbrItem ) ;

// The object used for all Abbr operations.
var FCKFVWPFlowplayer = new Object() ;

// Insert a new Abbr
FCKFVWPFlowplayer.Insert = function(val) {
	FCK.InsertHtml(val);
}