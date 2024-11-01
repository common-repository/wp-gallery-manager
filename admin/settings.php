<?php
if ( ! defined( 'ABSPATH' ) )
	 exit; 
	 
global $wpdb;

$upload_dir = wp_upload_dir();


$imgpath=$upload_dir['basedir']."/xyz_gal/xyz_gimg/";
$res=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."xyz_gal_images");


if($_POST)
{
	if (! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'gallery-setting_' )) {
			wp_nonce_ays( 'gallery-setting_' );
			exit;
		}
		
	$_POST=stripslashes_deep($_POST);
	
	$thumb_height=abs(intval($_POST["thumb_height"]));
	$thumb_width=abs(intval($_POST["thumb_width"]));
	$img_height=abs(intval($_POST["img_height"]));
	$img_width=abs(intval($_POST["img_width"]));
	
	if(isset($_POST["gal_responsive"]))
	   $gal_responsive=$_POST["gal_responsive"];
	else 
		$gal_responsive=0;
	
	$gal_height=abs(intval($_POST["gal_height"]));
	$gal_width=abs(intval($_POST["gal_width"]));
	
	
	$slideshow=$_POST["slideshow"];
	$slideshow_interval=abs(intval($_POST["slideshow_interval"]));
	
	$override=$_POST["override"];
	$page_limit=abs(intval($_POST["page_limit"]));
	
	update_option('xyz_gal_thumb_height', $thumb_height);
	update_option('xyz_gal_thumb_width', $thumb_width);
	update_option('xyz_gal_img_height', $img_height);
	update_option('xyz_gal_img_width', $img_width);
	
	update_option('xyz_gal_wpgal_responsive', $gal_responsive);	
	update_option('xyz_gal_gallery_height', $gal_height);
	update_option('xyz_gal_gallery_width', $gal_width);
	
	update_option('xyz_gal_slideshow', $slideshow);			
	update_option('xyz_gal_slideshow_interval', $slideshow_interval);
	
	update_option('xyz_gal_wp_gallery_override', $override);
	update_option('xyz_gal_page_limit', $page_limit);
		
	
echo '<br><div class="system_notice_area_style1" id="system_notice_area">Settings updated successfully.<span id="system_notice_area_dismiss">Dismiss</span></div>';
}
else 
{
	$thumb_height=get_option("xyz_gal_thumb_height");
	$thumb_width=get_option("xyz_gal_thumb_width");
	$img_height=get_option("xyz_gal_img_height");
	$img_width=get_option("xyz_gal_img_width");
		
	$gal_responsive=get_option("xyz_gal_wpgal_responsive");
	$gal_height=get_option("xyz_gal_gallery_height");
	$gal_width=get_option("xyz_gal_gallery_width");
	
	$slideshow=get_option("xyz_gal_slideshow");	
	$slideshow_interval=get_option("xyz_gal_slideshow_interval");
	
	$xyz_credit_link=get_option('xyz_credit_link');
	
	$override=get_option("xyz_gal_wp_gallery_override");
	$page_limit=get_option("xyz_gal_page_limit");
}
?>


 

<script type="text/javascript">

if(typeof xyz_gallery_isNumber == 'undefined')
{
    function xyz_gallery_isNumber(evt) 
    {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
           return false;
        }
        return true;
    }
}	 

jQuery(document).ready(function() {


    if (jQuery("#slideshow").is(':checked')) {
		jQuery("#slideshow_interval").show();
		
		
		
	}else{
		jQuery("#noslideshow").attr("checked",true);
		jQuery("#slideshow_interval").hide();
		
		
	}
	
	jQuery("#noslideshow").click(function() {
		jQuery("#slideshow_interval").hide();
		
	});
	jQuery("#slideshow").click(function() {
		jQuery("#slideshow_interval").show();
		
		
		
	});	

});
	  
</script>



<div class='wrap'>
<h2>Settings</h2>

<form name="add_gallery_form" action="" method="post" enctype="multipart/form-data">
<?php wp_nonce_field('gallery-setting_');?>
<input type="hidden" name="add_gallery" value="true" />
<table class="widefat post fixed" cellspacing="0" >

<tbody>

<tr valign="top"><td scope="row" colspan="2"><h3> Thumbnail Settings</h3></td></tr>

<tr>
<td scope="row">Maximum height</td>
<td><input type="text" size="25" name="thumb_height" value="<?php echo $thumb_height;?>" onkeypress="return xyz_gallery_isNumber(event)"/> px </td>
</tr>

<tr>
<td scope="row">Maximum width</td>
<td><input type="text" size="25" name="thumb_width" value="<?php echo $thumb_width;?>" onkeypress="return xyz_gallery_isNumber(event)"/> px </td>
</tr>

<tr valign="top"><td scope="row" colspan="2"><h3> Image Settings</h3></td></tr>

<tr>
<td scope="row">Maximum height</td>
<td><input type="text" size="25" name="img_height" value="<?php echo $img_height;?>" onkeypress="return xyz_gallery_isNumber(event)"/> px </td>
</tr>

<tr>
<td scope="row">Maximum width</td>
<td><input type="text" size="25" name="img_width" value="<?php echo $img_width;?>" onkeypress="return xyz_gallery_isNumber(event)" /> px </td>
</tr>


<tr valign="top"><td scope="row" colspan="2"><h3> Wordpress Gallery Settings</h3></td></tr>

<tr>
<td scope="row">Override wordpress gallery ?<p>( Supported shortcode format - [gallery ids="id1,id2,....,idn"] )</p></td>
<td>
<input type="radio" id="override" name="override" value="1" <?php if(isset($_POST['override']) && $_POST['override']==1){?>checked="checked"<?php }else if($override==1){?>checked="checked"<?php }?>/><label for="override">Yes</label>
<input type="radio" id="nooverride" name="override" value="0" <?php if(isset($_POST['override']) && $_POST['override']==0){?>checked="checked"<?php }else if($override==0){?>checked="checked"<?php }?>/><label for="nooverride">No</label>
</td>
</tr>


<tr>
<td scope="row">Make gallery responsive</td>
<td>
<Input type = 'Checkbox' name ="gal_responsive" value ="1" <?php if($gal_responsive==1){echo "checked";} ?>>

</td>
</tr>

<tr>
<td scope="row">Maximum height</td>
<td><input type="text" size="25" name="gal_height" value="<?php echo $gal_height;?>" onkeypress="return xyz_gallery_isNumber(event)" /> px </td>
</tr>

<tr>
<td scope="row">Maximum width</td>
<td><input type="text" size="25" name="gal_width" value="<?php echo $gal_width;?>" onkeypress="return xyz_gallery_isNumber(event)" /> px </td>
</tr>


<tr valign="top"><td scope="row" colspan="2"><h3> Slideshow Settings</h3></td></tr>




<tr>
<td scope="row">Enable automatic slideshow ?</td>
<td>
<input type="radio" id="slideshow" name="slideshow" value="1" <?php if(isset($_POST['slideshow']) && $_POST['slideshow']==1){?>checked="checked"<?php }else if($slideshow==1){?>checked="checked"<?php }?>/><label for="slideshow">Yes</label>
<input type="radio" id="noslideshow" name="slideshow" value="0" <?php if(isset($_POST['slideshow']) && $_POST['slideshow']==0){?>checked="checked"<?php }else if($slideshow==0){?>checked="checked"<?php }?>/><label for="noslideshow">No</label>
</td>
</tr>

<tr id="slideshow_interval">
<td scope="row">Interval</td>
<td><input type="number" step="any"  name="slideshow_interval"  min="1" value="<?php echo $slideshow_interval;?>" onkeypress="return xyz_gallery_isNumber(event)" /> sec</td>
</tr>

<tr valign="top"><td scope="row" colspan="2"><h3> Basic Settings</h3></td></tr>

<tr>
<td scope="row">Pagination limit</td>
<td><input type="text" size="25" name="page_limit" value="<?php echo $page_limit;?>" onkeypress="return xyz_gallery_isNumber(event)" /></td>
</tr>

<tr valign="top">

<td scope="row" colspan="1"><label for="xyz_gal_credit_link">Enable credit link to author ?</label>	</td><td><select name="xyz_gal_credit_link" id="xyz_gal_credit_link" >

<option value ="gal" <?php if($xyz_credit_link=='gal') echo 'selected'; ?> >Yes </option>

<option value ="<?php echo $xyz_credit_link!='gal'?$xyz_credit_link:0;?>" <?php if($xyz_credit_link!='gal') echo 'selected'; ?> >No </option>
</select>
</td></tr>

<tr><td></td>
<td class="major-publishing-actions">

<input type="submit" name="Submit" class="button-primary" value="Update Settings" /></td>
</tr>

</tbody>
</table>
</form>
</div>
<?php 
?>
