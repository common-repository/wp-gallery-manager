<?php
if ( ! defined( 'ABSPATH' ) )
   exit;
   
global $wpdb;
$id=$_GET['id'];
$upload_dir = wp_upload_dir();

$max_thumb_height=get_option("xyz_gal_thumb_height");
$max_thumb_width=get_option("xyz_gal_thumb_width");
$max_img_height=get_option("xyz_gal_img_height");
$max_img_width=get_option("xyz_gal_img_width");
$imgpath=plugins_url(XYZ_GALLERY_MANAGER_DIR.'/images/');

$imgpathde = $upload_dir['baseurl']."/xyz_gal/xyz_gimg/";

$resel=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."xyz_gal_gallery");
$f=0;
$error="";
if($_POST)
{
    if (! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'image-edit_'.$id )) {
        wp_nonce_ays( 'image-edit_'.$id );
        exit;
    } 
    else{
        $u=0;$img_error = $alt_error = $error_url = "";
        $_POST=stripslashes_deep($_POST);
        $name=$_POST["img_name"];
        $alt_text=$_POST["img_alt_text"];

        if(preg_match("/[^\w\s-.]/", $name)){
            $f=1;$u=1;
            $img_error ="Name should contain alphabets only";
        }

        if(preg_match("/[^\w\s-.]/", $alt_text)){
            $f=1;$u=1;
            $alt_error ="Name should contain alphabets only";
        }

        if(!empty($_POST["img_url"])){
            if(!filter_var($_POST["img_url"], FILTER_VALIDATE_URL) === false) {
                 $url=$_POST["img_url"];
            }
            else{
                $f=1;$u=1;
                $error_url = "Enter valid Url";
            }
        }
        $desc="";
        //$url=$_POST["img_url"];
        $img=$_FILES["image"]["name"];
        $image_type=$_FILES["image"]["type"];
        $img_tmp_nam=$_FILES["image"]["tmp_name"];
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

        if($u!=1)
       { $re1=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_gal_images where id=%d",$id));
               foreach($re1 as $re11)
                   $imageold=$re11->image;
               $re=$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."xyz_gal_images SET title=%s,description=%s,alt_text=%s,url=%s WHERE id=%d",$name,$desc,$alt_text,$url,$id));    
               if(isset($_POST["gallery"]))
               {
                   $deq=$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."xyz_gal_mapping WHERE imgid=%d",$id));
                   foreach($_POST["gallery"] as $val)
                   {
                       $gal_id=$val;
                       $re1q=$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."xyz_gal_mapping(`id`,`galid`,`imgid`)values(%d,%d,%d)",0,$gal_id,$id));
                       $wpdb->insert_id;
                   }
               }
               if($img!="")
               {
                   if (($img_namesubstrcl == "png")||($img_namesubstrcl=="gif") || ($img_namesubstrcl=="jpeg")|| ($img_namesubstrcl=="jpg") || ($img_namesubstrcl=="bmp") || ($img_namesubstrcl == "PNG")||($img_namesubstrcl=="GIF") || ($img_namesubstrcl=="JPEG")|| ($img_namesubstrcl=="JPG") )
                   {
                       $image_info = getimagesize($_FILES["image"]["tmp_name"]);
                       $thumb_height=get_option("xyz_gal_thumb_height");
                       $thumb_width=get_option("xyz_gal_thumb_width");
                       $minimum = array('width' => $thumb_width, 'height' => $thumb_height);
                       $image_width = $image_info[0];
                       $image_height = $image_info[1];
              
                       $tarray=explode('.',$img);
                       $ext = substr( $img, strrpos( $img, '.' )+1 );
                       $lastdot=strrpos($img,'.');
                       $imgnam=substr($img,0,$lastdot);
                       $bgimgnam=$imgnam.".".$ext;
                       $file=$upload_dir['baseurl']."/xyz_gal/xyz_gimg/".$id."_".$img;
                       $filethumb=$upload_dir['baseurl']."/xyz_gal/xyz_gimg/".$id."_".$imgnam.'_thumb.'.$ext;
                       if ($image_width < $minimum['width'] || $image_height <  $minimum['height'])
                       {
                           $f=1;
                           $error="Image should have minimum thumb size";
                       }
                       else 
                       {    
                           if($imageold!="")
                           {
                               unlink($upload_dir['basedir']."/xyz_gal/xyz_gimg/".$id."_".$imageold);
                               $ext = substr( $imageold, strrpos( $imageold, '.' )+1 );
                               $lastdot=strrpos($imageold,'.');
                               $imgnam=substr($imageold,0,$lastdot);
                               $imageold=$imgnam."_thumb.".$ext;
                               unlink($upload_dir['basedir']."/xyz_gal/xyz_gimg/".$id."_".$imageold);
                           }
                           if(move_uploaded_file($_FILES["image"]["tmp_name"],$file))
                           {
                               if(copy($file,$filethumb))
                               {
                                   xyz_gallery_resize_custom_limit($max_thumb_width, $max_thumb_height,$image_type,$filethumb);
                               }
                               xyz_gallery_resize_custom_limit($max_img_width, $max_img_height,$image_type,$file);
                           }
                           $re=$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."xyz_gal_images SET image=%s where id=%d",$img,$id));
                       }
                   }
                   else 
                   { 
                       $f=1;
                       $error='Invalid file!';
                   }    
               }
       
               if($f==0)
               {
                   echo '<br><div class="system_notice_area_style1" id="system_notice_area">Image edited successfully.<span id="system_notice_area_dismiss">Dismiss</span></div>';
               }
       }

    }
}	
$reimg=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_gal_images where id=%d",$id));
foreach($reimg as $image)
{
    $name=$image->title;
    $desc=$image->description;
    $alt_text=$image->alt_text;
    $url=$image->url;
    $img=$image->image;
    $bgimgnam1=$id."_".$img;
    $ext = substr( $bgimgnam1, strrpos( $bgimgnam1, '.' )+1 );
    $lastdot=strrpos($bgimgnam1,'.');
    $imgnam=substr($bgimgnam1,0,$lastdot);
    $bgimgnam=$imgnam."_thumb.".$ext;
    $bgimgurlc=$imgpathde.$bgimgnam;
    $upload_dir = wp_upload_dir();
    $actualpath=$upload_dir['basedir'].'/xyz_gal/xyz_gimg/';
    $pimgpath=$actualpath.$bgimgnam;
}
//$error="";
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
    if(typeof xyz_gallery_readURL == 'undefined')
    {
        function xyz_gallery_readURL(input) {
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
</script>
<div class='wrap'>
    <h2>
        XYZ Gallery - Edit Images
        <a  href="
<?php echo admin_url('admin.php?page=wp-gallery-manager-images&action=add')?>" class="add-new-h2">Add New</a>
    </h2>
    <br>
    <br>
    <form name="add_gallery_form" action="" method="post" enctype="multipart/form-data">
        <?php wp_nonce_field( 'image-edit_'.$id); ?>
        <table class="widefat fixed" cellspacing="0" >
            <thead>
                <tr>
                    <th width="250">
                        Attribute
                    </th>
                    <th>
                        Value
                    </th>
                    <th>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Image Title</strong>
                    </td>
                    <td>
                        <input type="text" size="20" name="img_name" value="
<?php echo esc_attr($name);?>" /><p style="color:red;" id="error"><?php echo $img_error;?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Alt Text</strong>
                    </td>
                    <td>
                        <input type="text" size="20" name="img_alt_text" value="
<?php echo esc_attr($alt_text);?>" /><p style="color:red;" id="error"><?php echo $alt_error;?></p>
                    </td>
                </tr>
                <!-- <tr>
<td><strong>Description</strong></td>
<td><textarea  name="img_desc">
<?php echo esc_attr($desc);?></textarea></td>
</tr> -->
                <tr>
                    <td>
                        <strong>Url</strong>
                    </td>
                    <td>
                        <input type="text" size="20" name="img_url" value="
<?php echo esc_attr($url);?>" /><p style="color:red;" id="error"><?php echo $error_url;?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Galleries</strong>
                    </td>
                    <td>
                        <select multiple="multiple" style="width:300px;" class="widefat" name="gallery[]">
                            <?php 
$selected_gal=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_gal_gallery JOIN ".$wpdb->prefix."xyz_gal_mapping ON ".$wpdb->prefix."xyz_gal_gallery.id=".$wpdb->prefix."xyz_gal_mapping.galid WHERE ".$wpdb->prefix."xyz_gal_mapping.imgid=%d",$id));
foreach($resel as $gallery) {?>
                            <option  value="<?php echo $gallery->id;?>" <?php foreach($selected_gal as $gal){if($gallery->id==$gal->galid){ echo "selected"; }}?>><?php echo $gallery->name;?>
                            </option> 
                            <?php }?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Image</strong>
                    </td>
                    <td scope="row">
                        <input type="file" name="image" id="image" onchange="xyz_gallery_readURL(this);" />
                        <div class="xyz_gal_msg">
                            jpeg,jpg,png,gif images are supported
                            <br>
                            Minimum image dimension - 
                            <?php echo $max_thumb_width;?> x 
                            <?php echo $max_thumb_height;?> 
                        </div>
                        <p style="color:red;">
                            <?php echo $error;?></p>
                        <?php 
list($widthimage,$heightimage) = @getimagesize($pimgpath);
$dimension=xyz_gallery_get_image_dimension($widthimage,$heightimage,1);
$dimensionarray=explode('_',$dimension);
                        ?>
                        <p>
                            <img id="blah" src="
<?php echo $bgimgurlc;?>" style="height:
<?php echo $dimensionarray[1];?>px;width:
<?php echo $dimensionarray[0];?>px;" alt="your image" />
                        </p>
                    </td>
                </tr>
                <tr>
                    <td>
                    </td>
                    <td>
                        <input type="submit" name="add_image" class="button-primary" value="Save Changes" />
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
<?php ?>