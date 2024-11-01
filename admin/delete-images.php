<?php
if ( ! defined( 'ABSPATH' ) )
	 exit;
	 
$id=$_GET['id'];

global $wpdb;
$upload_dir = wp_upload_dir();


if (! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'image-del_'.$id )) {
	wp_nonce_ays( 'image-del_'.$id );
	exit;
} 
else{
if($id=="" || !is_numeric($id))
{
	header("Location:".admin_url('admin.php?page=wp-gallery-manager-images'));
	exit();

}
$re=$wpdb->query($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_gal_images where id=%d ",$id));
$resultno=count($re);


if($resultno==0)
{

	header("Location:".admin_url('admin.php?page=wp-gallery-manager-images'));
	exit();
}
else
{
	$re1=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_gal_images where id=%d",$id));
	foreach($re1 as $re11)
		$image=$re11->image;
	if($image!="")
	{
	         	unlink($upload_dir['basedir']."/xyz_gal/xyz_gimg/".$id."_".$image);
	         	
	         	$ext = substr( $image, strrpos( $image, '.' )+1 );
	         	$lastdot=strrpos($image,'.');
	         	$imgnam=substr($image,0,$lastdot);
	         	$timage=$imgnam."_thumb.".$ext;
	         	
	         	//$thumb=explode(".",$image);
	         	//$image=$thumb[0]."_thumb.".$thumb[1];
	         	
	         	unlink($upload_dir['basedir']."/xyz_gal/xyz_gimg/".$id."_".$timage);
	}
	
	$deq=$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."xyz_gal_images where id=%d",$id));
	
	$delq=$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."xyz_gal_mapping where imgid=%d",$id));
	header("Location:".admin_url('admin.php?page=wp-gallery-manager-images&delete=1'));
	exit();
	
}
}
?>