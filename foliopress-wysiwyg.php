<?php
/*
Plugin Name: FolioPress WYSIWYG
Plugin URI: http://foliovision.com/seo-tools/wordpress/plugins/wysiwyg
Description: WYSIWYG FCKEditor with custom Image Management and nice skin.
Version: 0.9.12
Author: Foliovision s r.o.
Author URI: http://www.foliovision.com
*/

///	Addition	12/02/2010
register_activation_hook(__FILE__,'fp_wysiwyg_activate');

function fp_wysiwyg_activate() {
	if( get_option( 'default_post_edit_rows' ) < 20 )
		update_option( 'default_post_edit_rows', 20 );
}
///	End of addition

define( 'FV_FCK_NAME', 'Foliopress WYSIWYG' );
define( 'FV_FCK_OPTIONS', 'fp_wysiwyg' );

require_once( 'foliopress-wysiwyg-class.php' );

if( $GLOBALS['wp_version'] < 2.5 ) {
  add_action( 'admin_head', array( &$fp_wysiwyg, 'admin_init' ) );
}
else {
  add_action( 'admin_init', array( &$fp_wysiwyg, 'admin_init' ) );
}

add_action( 'admin_head', array( &$fp_wysiwyg, 'FckLoadAdminHead' ) );
add_action( 'admin_menu', array( &$fp_wysiwyg, 'AddOptionPage' ) );

add_action( 'edit_form_advanced', array( &$fp_wysiwyg, 'LoadFCKEditor' ) );
add_action( 'edit_page_form', array( &$fp_wysiwyg, 'LoadFCKEditor' ) );
add_action( 'simple_edit_form', array( &$fp_wysiwyg, 'LoadFCKEditor' ) );

if( $GLOBALS['wp_version'] >= 3.0 ) {
  add_action( 'admin_head', array( &$fp_wysiwyg, 'KillTinyMCE' ) );
}
else {
  add_action( 'option_posts_per_page', array( &$fp_wysiwyg, 'KillTinyMCE' ) );
}

if( $GLOBALS['wp_version'] >= 3.0 ) {
  add_filter('user_can_richedit', array(&$fp_wysiwyg, 'user_can_richedit') );
}
//add_action( 'personal_options_update', array( &$fp_wysiwyg, 'PersonalOptionsUpdate' ) );
//register_activation_hook( __FILE__, array( &$fp_wysiwyg, 'PluginActivate' ) );

///   Addition 20/03/09 mVicenik Foliovision
add_filter( 'media_buttons_context', array(&$fp_wysiwyg, 'fv_remove_mediabuttons') );
add_action('admin_print_scripts', array(&$fp_wysiwyg, 'add_admin_js'));
add_action('content_edit_pre', array(&$fp_wysiwyg, 'do_wpautop'));
///   End of addition

///   Addition 29/06/09 mVicenik Foliovision
add_filter('content_save_pre', array(&$fp_wysiwyg, 'remove_blank_p'));
///   End of addition

///	Addition	1/03/10	Foliovision
/// /// commented on 2010/06/21 for compatibilityß
if( $GLOBALS['wp_version'] >= 2.7 ) {
  add_action('admin_menu', array(&$fp_wysiwyg, 'meta_box_add') );
  add_action('admin_menu', array(&$fp_wysiwyg, 'remove_meta_boxes'), 99, 3 );
}
add_filter('wp_insert_post', array(&$fp_wysiwyg, 'wp_insert_post'));
add_filter('the_content', array(&$fp_wysiwyg, 'the_content'), 0);
///	End of addition

?>
