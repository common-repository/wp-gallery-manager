<?php
if ( ! defined( 'ABSPATH' ) )
	 exit;
	 
global $wpdb;

$upload_dir = wp_upload_dir();

$max_thumb_height=get_option("xyz_gal_thumb_height");
$max_thumb_width=get_option("xyz_gal_thumb_width");
$max_img_height=get_option("xyz_gal_img_height");
$max_img_width=get_option("xyz_gal_img_width");

$imgpathde=plugins_url(XYZ_GALLERY_MANAGER_DIR.'/images/');

$noimage=$imgpathde."noimage.jpg";
//$noimage="";

$gallery_res=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."xyz_gal_gallery");
$f=0;$u=0;


if(isset($_GET['create'])&& $_GET['create']==1 && isset($_GET['count']))
{
	echo '<br><div class="system_notice_area_style1" id="system_notice_area">'.$_GET['count'].'images added .<span id="system_notice_area_dismiss">Dismiss</span></div>';
}

if($_POST)
{
	if (! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'add-img_' )) {
			wp_nonce_ays( 'add-img_' );
			exit;
		}

	$_POST=stripslashes_deep($_POST);
	
	$error_single="";
	$error_multiple="";
	
	if(isset($_POST["multiple_files_upload"]) && $_POST["multiple_files_upload"]==1)
	{
		$name="";
		$alt_text="";
		$desc="";
		$url="";
		$error_url="";
		$error_multiple="";
		
		$multiple_files_upload= $_POST["multiple_files_upload"];
		$img_cnt=0;
				
		for($i=0; $i<count($_FILES['multiple_image']['tmp_name']); $i++)
		{
			
			$img = $_FILES['multiple_image']['name'][$i];
	        $img_namesubstrcl = substr( $img, strrpos( $img, '.' )+1 );
	        
	        if($img=="")
	        {
	        	$f=1;
	        	$error_multiple="Invalid image!";
	        }
	        else if($img!="")
	        {
		      if (($img_namesubstrcl == "png")||($img_namesubstrcl=="gif") || ($img_namesubstrcl=="jpeg")|| ($img_namesubstrcl=="jpg") || ($img_namesubstrcl=="bmp") || ($img_namesubstrcl == "PNG")||($img_namesubstrcl=="GIF") || ($img_namesubstrcl=="JPEG")|| ($img_namesubstrcl=="JPG") )
		      {

			    $image_type=$_FILES['multiple_image']['type'][$i];
			    $img_tmp_nam=$_FILES['multiple_image']['tmp_name'][$i];
			
			    $thumb_height=get_option("xyz_gal_thumb_height");
			    $thumb_width=get_option("xyz_gal_thumb_width");
			
			    $image_info = getimagesize($_FILES['multiple_image']['tmp_name'][$i]);
			    $minimum = array('width' => $thumb_width, 'height' => $thumb_height);
			    $image_width = $image_info[0];
			    $image_height = $image_info[1];
			
			
			    if ($image_width < $minimum['width'] || $image_height <  $minimum['height'])
			    {
				    $f=1;
				    $error_multiple="Failed to upload ".$img;
			    }
			
			    else
			    {
				
				    $re=$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."xyz_gal_images(`id`,`image`) values(%d,%s)",0,$img));
				    $id1=$wpdb->insert_id;
				
				    if(isset($_POST["gallery"]))
				    {
					    foreach($_POST["gallery"] as $val)
					    {
						    $gal_id=$val;
				
						    $re1=$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."xyz_gal_mapping(`id`,`galid`,`imgid`)values(%d,%d,%d)",0,$gal_id,$id1));
						    $id2=$wpdb->insert_id;
					    }
				    }
				
				    $tarray=explode('.',$img);
				
				    $ext = substr( $img, strrpos( $img, '.' )+1 );
				    $lastdot=strrpos($img,'.');
				    $imgnam=substr($img,0,$lastdot);
				    $bgimgnam=$imgnam.".".$ext;
				
				    $file=$upload_dir['basedir']."/xyz_gal/xyz_gimg/".$id1."_".$img;
				    $filethumb=$upload_dir['basedir']."/xyz_gal/xyz_gimg/".$id1."_".$imgnam.'_thumb.'.$ext;
				
				
				    if(move_uploaded_file($_FILES['multiple_image']['tmp_name'][$i],$file))
				    {
					    if(copy($file,$filethumb))
					    {
						    xyz_gallery_resize_custom_limit($max_thumb_width, $max_thumb_height,$image_type,$filethumb);
					    }
					    xyz_gallery_resize_custom_limit($max_img_width, $max_img_height,$image_type,$file);
					    $img_cnt++;
				    }
			
			    }
		      }
		      else
		      {
		      	$f=1;
		      	$error_multiple="Failed to upload ".$img;
		      
		      }
	        }
		    
		
		
		}
		if($f==0)
		{
		header("Location:".admin_url('admin.php?page=wp-gallery-manager-images&create=2'));
		exit();
		}
    }
								
	else
	{	
	    $name=$_POST["img_name"];
	    $alt_text=$_POST["img_alt_text"];
	    //$desc=$_POST["img_desc"];
	    $desc=$img_error=$alt_error="";
		$u=0;
	   	if(preg_match("/[^\w\s-.]/", $name)){
			$f=1;$u=1;
			$img_error ="Name should contain alphabets only";
		}

		if(preg_match("/[^\w\s-.]/", $alt_text)){
			$f=1;$u=1;
			$alt_error ="Name should contain alphabets only";
		}

	    $multiple_files_upload=0;
	    $error_single="";
		
		if(!empty($_POST["img_url"])){
			if(!filter_var($_POST["img_url"], FILTER_VALIDATE_URL) === false) {
				 $url=$_POST["img_url"];
			}
			else{
				$f=1;$u=1;
				$error_url = "Enter valid Url";
			}
		}

	    // $url=$_POST["img_url"];
	    $img=$_FILES["image"]["name"];
	
	    $img_namesubstrcl = substr( $img, strrpos( $img, '.' )+1 );
	
	    if($url !="")
	    {
		    if(substr($url,0,7) !="http://")
		    {
			    if(substr($url,0,8) !="https://")
			    {
				    $url="http://".$url;
			    }
		    }
	    }
	    if($u!=1){
	    	    if($img=="")
	    	    {
	    		    $f=1;
	    		    $error_single="Invalid image!";
	    	    }
	    	    else
	    	    {				
	    	        if (($img_namesubstrcl == "png")||($img_namesubstrcl=="gif") || ($img_namesubstrcl=="jpeg")|| ($img_namesubstrcl=="jpg") || ($img_namesubstrcl=="bmp") || ($img_namesubstrcl == "PNG")||($img_namesubstrcl=="GIF") || ($img_namesubstrcl=="JPEG")|| ($img_namesubstrcl=="JPG") )
	    		    {
	    			    $image_type=$_FILES["image"]["type"];
	    			    $img_tmp_nam=$_FILES["image"]["tmp_name"];
	    			
	    			    $thumb_height=get_option("xyz_gal_thumb_height");
	    			    $thumb_width=get_option("xyz_gal_thumb_width");
	    			
	    			    $image_info = getimagesize($_FILES["image"]["tmp_name"]);
	    			    $minimum = array('width' => $thumb_width, 'height' => $thumb_height);
	    			    $image_width = $image_info[0];
	    			    $image_height = $image_info[1];
	    			
	    			    if ($image_width < $minimum['width'] || $image_height <  $minimum['height'])
	    			    {
	    				    $f=1;
	    				    $error_single="Image should have minimum thumb dimension";
	    			    }
	    			
	    			    else
	    			    {
	    				    $re=$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."xyz_gal_images(`id`,`title`,`description`,`alt_text`,`url`) values(%d,%s,%s,%s,%s)",0,$name,$desc,$alt_text,$url));
	    				    $id1=$wpdb->insert_id;
	    				
	    				    if(isset($_POST["gallery"]))
	    				    {
	    					    foreach($_POST["gallery"] as $val)
	    					    {
	    						    $gal_id=$val;
	    				
	    						    $re1=$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."xyz_gal_mapping(`id`,`galid`,`imgid`)values(%d,%d,%d)",0,$gal_id,$id1));
	    						    $id2=$wpdb->insert_id;
	    					    }
	    				    }
	    				
	    				    $tarray=explode('.',$img);
	    				
	    				    $ext = substr( $img, strrpos( $img, '.' )+1 );
	    				    $lastdot=strrpos($img,'.');
	    				    $imgnam=substr($img,0,$lastdot);
	    				    $bgimgnam=$imgnam.".".$ext;
	    		
	    				    $file=$upload_dir['basedir']."/xyz_gal/xyz_gimg/".$id1."_".$img;
	    				    $filethumb=$upload_dir['basedir']."/xyz_gal/xyz_gimg/".$id1."_".$imgnam.'_thumb.'.$ext;
	    				
	    				    
	    				    if(move_uploaded_file($_FILES["image"]["tmp_name"],$file))
	    				    {
	    				    	
	    					    if(copy($file,$filethumb))
	    					    {	
	    						    xyz_gallery_resize_custom_limit($max_thumb_width, $max_thumb_height,$image_type,$filethumb);
	    					 		
	    					    }

	    					    xyz_gallery_resize_custom_limit($max_img_width, $max_img_height,$image_type,$file);
	    				    }
	    					
	    			       $re=$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."xyz_gal_images SET image=%s where id=%d",$img,$id1));
	    			
	    			    }
	    		    }
	    		
	    		    else 
	    		    { 
	    			    $f=1;
	    			    $error_single='Invalid file type '.$img_namesubstrcl;
	    			
	    		    }	
	    	    }
	    	}
	    
	    if($f==0)
	    {
	    
	    	header("Location:".admin_url('admin.php?page=wp-gallery-manager-images&create=1'));
	    	exit();
	    }
    }
	/*if($f==0)
	{
				
	  header("Location:".admin_url('admin.php?page=wp-gallery-manager-images&create=1'));
	  exit();
	}*/
	
}	
else 
{
	$multiple_files_upload="";
	
	$name="";
	$alt_text="";
	$desc="";
	$url="";
	$error_url="";
	$img="";
	$error_single="";
	$error_multiple="";
}
?>

<style>
select
{
  font-size:1em;
  
}
select option
{
padding:0.2em;
}

</style>


<script type="text/javascript">

if(typeof xyz_gallery_validation == 'undefined')
{
  function xyz_gallery_validation()
  {
		var errors=0;

		if (jQuery("#multiple_files_upload").is(':checked')) 
		{
			  if(document.getElementById('multiple_image').value=="")
			  {
				  jQuery("#multiple_image").css({"border":"1px solid red"});
				  document.getElementById('error_multiple').innerHTML = "Invalid image";
				  errors=1;
			  }
			  else
			  {
				  jQuery("#multiple_image").css({"border":"1px solid green"});
			  }
		}
		else
		{			
		      if(document.getElementById('image').value=="")
			  {
			      jQuery("#image").css({"border":"1px solid red"});
			      document.getElementById('error').innerHTML = "Invalid image";
			      errors=1;
		      }
		      else
		      {
			      jQuery("#image").css({"border":"1px solid green"});
		      }
		}
		if(errors==1)
		{
			return false;
		}
		else
		{
			return true;
		
		}
		
  }	
}

if(typeof xyz_gallery_readURL == 'undefined')
{
  function xyz_gallery_readURL(input) 
  {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            jQuery('#blah')
                .attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
    }
  }
}


if(typeof xyz_gal_bgchange == 'undefined')
{
  function xyz_gal_bgchange()
  {
		if (jQuery("#multiple_files_upload").is(':checked')) {

		   jQuery("#multiple_img_browse_tr").show();
		   		   
		   jQuery("#title_tr").hide();
		   jQuery("#alttext_tr").hide();
		   jQuery("#url_tr").hide();
		   jQuery("#single_img_browse_tr").hide();
		}
		else
		{
			jQuery("#multiple_img_browse_tr").hide();
						
			jQuery("#title_tr").show();
			jQuery("#alttext_tr").show();
			jQuery("#url_tr").show();
			jQuery("#single_img_browse_tr").show();
		}
		
  }
}

</script>


<div class='wrap'>
   <h2>Add New Image</h2>
   <form name="add_gallery_form" action="" method="post" enctype="multipart/form-data">
   		<?php wp_nonce_field('add-img_');?>
     <!-- <input type="hidden" name="add_gallery" value="true" /> -->
     <table class="widefat fixed" cellspacing="0" >
      
      <thead>
        <tr>
          <th width="250">Attribute</th>
          <th>Value</th>
          <th></th>
        </tr>
      </thead>

      <tbody>
      
      
      <tr>
        <td><strong>Upload Multiple Files</strong></td>
        <td>
           <Input type = 'Checkbox' name ="multiple_files_upload" id="multiple_files_upload" value ="1" <?php if($multiple_files_upload==1){echo "checked";} ?> onchange="return xyz_gal_bgchange()">
       </td>
      </tr>
      
        <tr id="title_tr">
           <td><strong>Image Title</strong></td>
           <td><input type="text" size="20" name="img_name" value="<?php echo $name;?>" /><p style="color:red;" id="error"><?php echo $img_error;?></p></td>
        </tr>

        <tr id="alttext_tr">
           <td><strong>Alt Text</strong></td>
           <td><input type="text" size="20" name="img_alt_text" value="<?php echo $alt_text;?>" /><p style="color:red;" id="error"><?php echo $alt_error;?></p></td>
        </tr>

        <!--  <tr>
           <td><strong>Description</strong></td>
           <td><textarea  name="img_desc"><?php echo $desc;?></textarea></td>
        </tr>-->

        <tr id="url_tr">
           <td><strong>Url</strong></td><td><input type="text" size="20" name="img_url" value="<?php echo $url;?>" /><p style="color:red;" id="error"><?php echo $error_url;?></p></td>
        </tr>

        <tr>
           <td><strong>Select Galleries</strong></td>
           <td>
              <select multiple="multiple" style="width:300px;" class="widefat" name="gallery[]">
              <?php foreach($gallery_res as $gallery) {?>
                    <option value="<?php echo $gallery->id;?>" <?php //if($gal_id==$gallery->id)echo "selected;"?>><?php echo $gallery->name;?></option>
              <?php }?>
              </select>
           </td>
        </tr>

        <tr id="single_img_browse_tr">
           <td><strong>Image</strong></td>
           <td scope="row">
               <input type="file" name="image" id="image"  onchange="xyz_gallery_readURL(this);"/>
               <div class="xyz_gal_msg">jpeg,jpg,png,gif images are supported<br>Minimum image dimension - <?php echo $max_thumb_width;?> x <?php echo $max_thumb_height;?> </div>
               <p style="color:red;" id="error"><?php echo $error_single;?></p>
               
               <?php /*list($widthimage,$heightimage) = @getimagesize($noimage);
	                                 
	                                 $dimension=xyz_gallery_get_image_dimension($widthimage,$heightimage,3);
	                                 $dimensionarray=explode('_',$dimension); */?>
               
               <p><img id="blah" src="<?php echo $noimage;?>" style="max-height:90px;max-width:120px;" alt="your image" /></p>
           </td>
        </tr>
        
        <tr id="multiple_img_browse_tr" style="display:none;">
           <td><strong>Images</strong></td>
           <td scope="row">
               <input type="file"  multiple="multiple" name="multiple_image[]" id="multiple_image"  />
               <div class="xyz_gal_msg">jpeg,jpg,png,gif images are supported<br>Minimum image dimension - <?php echo $max_thumb_width;?> x <?php echo $max_thumb_height;?> </div>
               <p style="color:red;" id="error_multiple"><?php echo $error_multiple;?></p>
           </td>
        </tr>       

        <tr>
           <td></td>
           <td><input type="submit" name="add_image" class="button-primary" value="Add Image"  onclick="return xyz_gallery_validation()" /></td>
        </tr> 

     </tbody>
   </table>
  </form>
</div>

<script type="text/javascript">
xyz_gal_bgchange();
</script>

<?php ?>
