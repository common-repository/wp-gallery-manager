<?php
/*
Plugin Name:WP Gallery Manager
Plugin URI: http://xyzscripts.com/wordpress-plugins/wp-gallery-manager/
Description:  This plugin allow you to create any number of image galleries and render in any page by simply inserting shortcodes. You can upload any number of local images and tag with respective galleries. Multiple image uploading making this process easy. Gallery can be easily created by choosing from uploaded images. Drag and drop feature make this process easy and much user friendly. Uploaded images can be sorted using drag and drop feature. 
Version: 1.0
Author: xyzscripts.com
Author URI: http://xyzscripts.com/
License: GPLv2 or later
*/

if ( ! defined( 'ABSPATH' ) )
	 exit;

if ( !function_exists( 'add_action' ) )
{
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

ob_start();
//error_reporting(E_ALL);


define('XYZ_GALLERY_MANAGER_PLUGIN_FILE',__FILE__);
define('XYZ_GALLERY_MANAGER_DIR',dirname(plugin_basename(__FILE__)));


require( dirname( __FILE__ ) . '/admin/install.php' );
require( dirname( __FILE__ ) . '/admin/menu.php' );
require( dirname( __FILE__ ) . '/shortcode-handler.php' );
require( dirname( __FILE__ ) . '/xyz-functions.php' );

require( dirname( __FILE__ ) . '/admin/ajax-handler.php' );
require( dirname( __FILE__ ) . '/admin/uninstall.php' );


if(get_option('xyz_credit_link')=="gal"){

	add_action('wp_footer', 'xyz_gallery_credit');

}
if(!function_exists('xyz_gallery_credit'))
{
function xyz_gallery_credit() {
	$content = '<div style="clear:both;width:100%;text-align:center; font-size:11px; "><a target="_blank" href="#">Gallery</a> Powered By : <a target="_blank" title="PHP Scripts & Wordpress Plugins" href="http://www.xyzscripts.com" >XYZScripts.com</a></div>';
	echo $content;
}
}
?>