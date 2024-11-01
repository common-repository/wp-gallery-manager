<?php 
if ( ! defined( 'ABSPATH' ) )
	 exit;
	 
add_action('admin_menu', 'xyz_gallery_menu');

function xyz_gallery_menu()
{
	add_menu_page('WP Gallery Manager', 'WP Gallery Manager', 'manage_options', 'wp-gallery-manager-settings', 'xyz_gallery_settings');
	
	// Add a submenu to the Dashboard:
	add_submenu_page('wp-gallery-manager-settings', 'WP Gallery Manager - Settings', 'Settings', 'manage_options', 'wp-gallery-manager-settings' ,'xyz_gallery_settings');
	add_submenu_page('wp-gallery-manager-settings', 'WP Gallery Manager - Images', 'Images', 'manage_options', 'wp-gallery-manager-images' ,'xyz_gallery_images'); 
	add_submenu_page('wp-gallery-manager-settings','WP Gallery Manager - Gallery', 'Gallery', 'manage_options', 'wp-gallery-manager', 'xyz_gallery_overview');
	
	
}

function xyz_gallery_overview()
{
	
	require( dirname( __FILE__ ) . '/header.php' );
	$fl=0;
	
	if(isset($_GET['action']) && $_GET['action']=='changestatus' )
	{
		include(dirname( __FILE__ ) . '/change-gallery-status.php');
		$fl=1;
	}
	
	if(isset($_GET['action']) && $_GET['action']=='add' )
	{
		include(dirname( __FILE__ ) . '/add-gallery.php');
		$fl=1;
	
	}
	
	if(isset($_GET['action']) && $_GET['action']=='edit' )
	{
		include(dirname( __FILE__ ) . '/edit-gallery.php');
		$fl=1;
	
	}
	if(isset($_GET['action']) && $_GET['action']=='delete' )
	{
		include(dirname( __FILE__ ) . '/delete-gallery.php');
		$fl=1;
	}
	if($fl==0)
	{
	require( dirname( __FILE__ ) . '/gallery.php' );
	}
	require( dirname( __FILE__ ) . '/footer.php' );
	
}

function xyz_gallery_settings()
{
	require( dirname( __FILE__ ) . '/header.php' );
	require( dirname( __FILE__ ) . '/settings.php' );
	require( dirname( __FILE__ ) . '/footer.php' );
}




function xyz_gallery_images()
{
	$im=0;
	require( dirname( __FILE__ ) . '/header.php' );
	if(isset($_GET['action']) && $_GET['action']=='add' )
	{
		include(dirname( __FILE__ ) . '/add-images.php');
		$im=1;
	
	}
	if(isset($_GET['action']) && $_GET['action']=='edit' )
	{
		include(dirname( __FILE__ ) . '/edit-images.php');
		$im=1;
	
	}
	if(isset($_GET['action']) && $_GET['action']=='delete' )
	{
		include(dirname( __FILE__ ) . '/delete-images.php');
		$im=1;
	}
	if($im==0)
	{	
	require( dirname( __FILE__ ) . '/images.php' );
	}
	require( dirname( __FILE__ ) . '/footer.php' );
	
}

function xyz_gallery_add_style_script_admin()
{

	// Register scripts
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	//wp_enqueue_script( 'jquery-ui-widget' );
	wp_enqueue_script('jquery-ui-draggable');
	wp_enqueue_script('jquery-ui-droppable');
	wp_enqueue_script('jquery-ui-sortable');
	
	
	wp_register_script( 'xyz_gal_notice_script', plugins_url('wp-gallery-manager/js/notice.js') );
	wp_enqueue_script( 'xyz_gal_notice_script' );
	
	
		
	// Register stylesheets
	wp_register_style('xyz_gal_style', plugins_url('wp-gallery-manager/css/style.css'));
	wp_enqueue_style('xyz_gal_style');

}

add_action('admin_enqueue_scripts', 'xyz_gallery_add_style_script_admin');

function xyz_gallery_add_style_script()
{
	// Register scripts
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-slider');
			
	// Register stylesheets
	wp_register_style('xyz_gal_slider_style', plugins_url('wp-gallery-manager/css/slider.css'));
	wp_enqueue_style('xyz_gal_slider_style');
	
}

add_action('wp','xyz_gallery_add_style_script');
?>