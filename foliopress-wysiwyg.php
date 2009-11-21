<?php
/*
Plugin Name: FolioPress WYSIWYG
Plugin URI: http://www.foliovision.com
Description: WYSIWYG FCKEditor with custom Image Management and nice skin.
Version: 0.9.7
Author: Foliovision s r.o.
Author URI: http://www.foliovision.com
*/

define( 'FV_FCK_NAME', 'Foliopress WYSIWYG' );
define( 'FV_FCK_OPTIONS', 'fp_wysiwyg' );

require_once( 'foliopress-wysiwyg-class.php' );

add_action( 'admin_head', array( &$fp_wysiwyg, 'FckLoadAdminHead' ) );
add_action( 'admin_menu', array( &$fp_wysiwyg, 'AddOptionPage' ) );

add_action( 'edit_form_advanced', array( &$fp_wysiwyg, 'LoadFCKEditor' ) );
add_action( 'edit_page_form', array( &$fp_wysiwyg, 'LoadFCKEditor' ) );
add_action( 'simple_edit_form', array( &$fp_wysiwyg, 'LoadFCKEditor' ) );

add_action( 'option_posts_per_page', array( &$fp_wysiwyg, 'KillTinyMCE' ) );
//add_action( 'personal_options_update', array( &$fp_wysiwyg, 'PersonalOptionsUpdate' ) );
//register_activation_hook( __FILE__, array( &$fp_wysiwyg, 'PluginActivate' ) );

///   Addition 20/03/09 mVicenik Foliovision
$aOptions = get_option( FV_FCK_OPTIONS );
if( isset( $aOptions['HideMediaButtons'] ) && $aOptions['HideMediaButtons'] == true)
   add_filter( 'media_buttons_context', array(&$fp_wysiwyg, 'fv_remove_mediabuttons') );
add_action('admin_print_scripts', array(&$fp_wysiwyg, 'add_admin_js'));
add_action('content_edit_pre', array(&$fp_wysiwyg, 'do_wpautop'));
///   End of addition

///   Addition 29/06/09 mVicenik Foliovision
add_filter('content_save_pre', array(&$fp_wysiwyg, 'remove_blank_p'));
///   End of addition
?>