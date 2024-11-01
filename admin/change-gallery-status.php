<?php
if ( ! defined( 'ABSPATH' ) )
	 exit;
	 
 $id=$_GET['id'];

global $wpdb;


if (! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'gallery-stat_'.$id )) {
	wp_nonce_ays( 'gallery-stat_'.$id );
	exit;
} 
else{
if($id=="" || !is_numeric($id))
{
	header("Location:".admin_url('admin.php?page=wp-gallery-manager'));
	exit();
	
}

$re=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_gal_gallery where id=%d",$id));
$resultno=count($re);

if($resultno==0)
{
	
	header("Location:".admin_url('admin.php?page=wp-gallery-manager'));
	exit();
}

//$result=$wpdb->get_results($re);
foreach($re as $result)
$st=$result->status;
if($st==0)	
{
	
	$acq=$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."xyz_gal_gallery SET status=%d where id=%d",1,$id));
}
else
{
	
	$acq=$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."xyz_gal_gallery SET status=%d where id=%d",0,$id));
}	

header("Location:".admin_url('admin.php?page=wp-gallery-manager&status='.$st));
exit();
}
?>