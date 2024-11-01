<?php
if ( ! defined( 'ABSPATH' ) )
	 exit;
	
if(!function_exists('xyz_gallery_destroy'))
{
function xyz_gallery_destroy()
{
    global $wpdb;
    $upload_dir = wp_upload_dir();
    
    $re=$wpdb->get_results("SELECT * FROM  ".$wpdb->prefix."xyz_gal_images ");
    $renum=count($re);
    if($renum!=0)
    {
	    foreach($re as $result)
	    {
	
		    $image_id=$result->id;
		    $image=$result->image;
		    if($image!="")
		    {
		    	unlink($upload_dir['basedir']."/xyz_gal/xyz_gimg/".$image_id."_".$image);
		    	
		    	$ext = substr( $image, strrpos( $image, '.' )+1 );
		    	$lastdot=strrpos($image,'.');
		    	$imgnam=substr($image,0,$lastdot);
		    	$image_thumb=$imgnam."_thumb.".$ext;
		    	
		    	//$thumb=explode(".",$image);
		    	//$image_thumb=$thumb[0]."_thumb.".$thumb[1];
		    	unlink($upload_dir['basedir']."/xyz_gal/xyz_gimg/".$image_id."_".$image_thumb);
		    	
		    	
		    }
		
		
	    }	
	    
	   rmdir($upload_dir['basedir']."/xyz_gal/xyz_gimg");
	   rmdir($upload_dir['basedir']."/xyz_gal");
    }
    
    
    $wpdb->query("DROP TABLE ".$wpdb->prefix."xyz_gal_gallery");
    $wpdb->query("DROP TABLE ".$wpdb->prefix."xyz_gal_images");
    $wpdb->query("DROP TABLE ".$wpdb->prefix."xyz_gal_mapping");
    
    delete_option("xyz_gal_thumb_height");
    delete_option("xyz_gal_thumb_width");
    delete_option("xyz_gal_img_height");
    delete_option("xyz_gal_img_width");
    
    delete_option("xyz_gal_gallery_height");
    delete_option("xyz_gal_gallery_width");
    delete_option("xyz_gal_wpgal_responsive");
    
    delete_option("xyz_gal_slideshow");   
    delete_option("xyz_gal_slideshow_interval");
    
    if(get_option('xyz_credit_link')=="gal")
    {
    	update_option("xyz_credit_link", '0');
    }

    delete_option("xyz_gal_credit_dismiss");
    delete_option("xyz_gal_wp_gallery_override");
    delete_option("xyz_gal_page_limit");

}
}

if(!function_exists('xyz_gallery_network_destroy'))
{
function xyz_gallery_network_destroy($networkwide) {
	
	global $wpdb;

	if (function_exists('is_multisite') && is_multisite()) {
		// check if it is a network activation - if so, run the activation function for each blog id
		if ($networkwide) {
			$old_blog = $wpdb->blogid;
			// Get all blog ids
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				xyz_gallery_destroy();
			}
			switch_to_blog($old_blog);
			return;
		}
	}
	xyz_gallery_destroy();
}
}

register_uninstall_hook(XYZ_GALLERY_MANAGER_PLUGIN_FILE,' xyz_gallery_network_destroy');
?>