<?php 
if ( ! defined( 'ABSPATH' ) )
	 exit;
	 
if(!function_exists('xyz_gallery_network_install'))
{
function xyz_gallery_network_install($networkwide) {
	global $wpdb;

	if (function_exists('is_multisite') && is_multisite()) {
		// check if it is a network activation - if so, run the activation function for each blog id
		if ($networkwide) {
			$old_blog = $wpdb->blogid;
			// Get all blog ids
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				xyz_gallery_install();
			}
			switch_to_blog($old_blog);
			return;
		}
	}
	xyz_gallery_install();
}
}

if(!function_exists('xyz_gallery_install'))
{
  function xyz_gallery_install()
  {
	global $wpdb;
	
	
	
	add_option("xyz_gal_thumb_height", '120');
	add_option("xyz_gal_thumb_width", '160');
	add_option("xyz_gal_img_height", '768');
	add_option("xyz_gal_img_width", '1024');
	
	add_option("xyz_gal_gallery_height", '768');
	add_option("xyz_gal_gallery_width", '1024');
	add_option("xyz_gal_wpgal_responsive", '1');
	
	add_option("xyz_gal_slideshow", '0');	
	add_option("xyz_gal_slideshow_interval", '1');
	
	if(get_option('xyz_credit_link')=="")
	{
		add_option("xyz_credit_link", '0');
	}
	
	add_option("xyz_gal_credit_dismiss", '0');
	add_option("xyz_gal_wp_gallery_override", '0');
	add_option("xyz_gal_page_limit", '20');
	
	
	
	$wpdb->query("CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."xyz_gal_gallery (
			`id` int NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
            `preview_image` int NOT NULL,  
			`responsive` int NOT NULL, 
			`gallery_height` int NOT NULL,
			`gallery_width` int NOT NULL,       
            `status` int NOT NULL		
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
		
	
	/*$wpdb->query("ALTER TABLE ".$wpdb->prefix."xyz_gal_gallery 
			ADD COLUMN responsive int NOT NULL AFTER preview_image,
			ADD COLUMN gallery_height int NOT NULL AFTER responsive,
			ADD COLUMN gallery_width int NOT NULL AFTER gallery_height");*/
		
	
		$wpdb->query("CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."xyz_gal_images (
				`id` int NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`title` varchar(230) COLLATE utf8_unicode_ci NOT NULL,
				`description` longtext COLLATE utf8_unicode_ci NOT NULL,
				`alt_text` varchar(230) COLLATE utf8_unicode_ci NOT NULL,
				`url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`image` varchar(230) COLLATE utf8_unicode_ci NOT NULL	
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
		
	
		$wpdb->query("CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."xyz_gal_mapping (
				`id` int NOT NULL AUTO_INCREMENT  PRIMARY KEY ,
				`galid` int NOT NULL,
				`imgid` int NOT NULL									
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
	
			

   
    $upload_dir = wp_upload_dir();

    if(!is_dir($upload_dir['basedir']))
    {
	    mkdir($upload_dir['basedir'],0777);
    }
    if(!is_dir($upload_dir['basedir']."/xyz_gal"))
    {
	    mkdir($upload_dir['basedir']."/xyz_gal",0777);
    }
    if(!is_dir($upload_dir['basedir']."/xyz_gal/xyz_gimg"))
    {
	    mkdir($upload_dir['basedir']."/xyz_gal/xyz_gimg",0777);
    }

  }
}

register_activation_hook( XYZ_GALLERY_MANAGER_PLUGIN_FILE ,'xyz_gallery_network_install');
?>