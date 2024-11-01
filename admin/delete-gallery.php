<?php
if ( ! defined( 'ABSPATH' ) )
	 exit;
	 
$id=$_GET['id'];

global $wpdb;


if (! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'gallery-del_'.$id )) {
	wp_nonce_ays( 'gallery-del_'.$id );
	exit;
} 
else{
if($id=="" || !is_numeric($id))
{
	header("Location:".admin_url('admin.php?page=wp-gallery-manager'));
	exit();

}
$re=$wpdb->query($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_gal_gallery where id=%d ",$id));
$resultno=count($re);


if($resultno==0)
{

	header("Location:".admin_url('admin.php?page=wp-gallery-manager'));
	exit();
}
else
{
	$deq=$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."xyz_gal_gallery where id=%d",$id));
	
	$delq=$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."xyz_gal_mapping where galid=%d",$id));
	header("Location:".admin_url('admin.php?page=wp-gallery-manager&delete=1'));
	exit();
}
}
?>