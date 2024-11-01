<?php
if ( ! defined( 'ABSPATH' ) )
	 exit;
	 
global $wpdb;
$id=$_GET['id'];
$upload_dir = wp_upload_dir();
//$plugin_dir_path = dirname(__FILE__);

$imgpath=$upload_dir['baseurl']."/xyz_gal/xyz_gimg/";
$imgpathde=plugins_url(XYZ_GALLERY_MANAGER_DIR.'/images/');

$actualpath=$upload_dir['basedir'].'/xyz_gal/xyz_gimg/';
$load_image=$imgpathde."loading.gif";
$expr = '/^[1-9][0-9]*$/';
$error_galname="";
$error_galheight="";
$error_galwidth="";



if($_POST)
{
if (! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'gallery-edit_'.$id )) {
	wp_nonce_ays( 'gallery-edit_'.$id );
	exit;
	} 
	else{
	
	$_POST=stripslashes_deep($_POST);
	
	$img=$_POST["hid_gal_images"];
	$img_id=explode(',',$img);
	
	if(isset($_POST["gal_responsive"]))
	   $gal_responsive=$_POST["gal_responsive"];
	else
		$gal_responsive=0;
	
	$gal_height=$_POST["gal_height"];
	$gal_width=$_POST["gal_width"];
			
	$img_count=count($img_id);
	$gal_name=$_POST["galleryName"];
	
	if($gal_name=="")
	{
		$f=1;
		$error_galname="Enter a gallery name";
	}
	else
	{	
		if(preg_match("/[^\w-.]/", $gal_name)){
			$f=1;
			$error_galname="Name should not contain any special characters";
		}
		else{
			$error_galname="";
		}
	}
	if($gal_height=="")
	{
		$f=1;
		$error_galheight="Enter gallery height";
	}
	else
	{
		if (preg_match($expr, $gal_height) && filter_var($gal_height, FILTER_VALIDATE_INT)) {
			$error_galheight="";
		}
		else{
			
			$f=1;
			$error_galheight="Invalid format";
		}
	}
	if($gal_width=="")
	{
		$f=1;
		$error_galwidth="Enter gallery width";
	}
	else
	{
		if(preg_match($expr, $gal_width) && filter_var($gal_width, FILTER_VALIDATE_INT)) {
			$error_galwidth="";
		}
		else{
			
			$f=1;
			$error_galwidth="Invalid format";
		}
	}
	if($gal_name!="" && $gal_height!="" && $gal_width!="")
		{	
		if($f!=1)
		{$edt=$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."xyz_gal_gallery SET name=%s,preview_image=%d,responsive=%d,gallery_height=%d,gallery_width=%d where id=%d",$gal_name,$img_id[0],$gal_responsive,$gal_height,$gal_width,$id));
				$delold=$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."xyz_gal_mapping WHERE galid=%d",$id));
				
				if($img!=0)
				{	
				   for($i=0;$i<$img_count;$i++)
				   {
				   	
				   	   
					        $re1=$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."xyz_gal_mapping(`id`,`galid`,`imgid`)values(%d,%d,%d)",0,$id,$img_id[$i]));
					        $id2=$wpdb->insert_id;
				   	   
				   }
				}
				
				echo '<br><div class="system_notice_area_style1" id="system_notice_area">Gallery updated successfully.<span id="system_notice_area_dismiss">Dismiss</span></div>';
				}
			}
	
}
	
  } 
?>
    
   <style type="text/css">

  .image_gallery
  {
  width: 60% !important;
  }
  #preview_image
  {
  border: 0px !important;
  }
       
 </style>

  
  <script type="text/javascript">

  if(typeof xyz_gallery_validation == 'undefined')
  {
	  function xyz_gallery_validation()
	  {
			var errors=0;
			
			if(document.getElementById('galleryName').value==""){				
				jQuery("#galleryName").css({"border":"1px solid red"});	
				document.getElementById('error_galname').innerHTML = "Enter a gallery name";
				errors=1;
			}
			else
			{
				jQuery("#galleryName").css({"border":"1px solid green"});
				document.getElementById('error_galname').innerHTML = "";
			}

			if(document.getElementById('gal_height').value==""){
				jQuery("#gal_height").css({"border":"1px solid red"});	
				document.getElementById('error_galheight').innerHTML = "Enter gallery height";
				errors=1;
			}
			else
			{
				jQuery("#gal_height").css({"border":"1px solid green"});
				document.getElementById('error_galheight').innerHTML = "";
			}

			if(document.getElementById('gal_width').value==""){
				jQuery("#gal_width").css({"border":"1px solid red"});	
				document.getElementById('error_galwidth').innerHTML = "Enter gallery width";
				errors=1;
			}
			else
			{
				jQuery("#gal_width").css({"border":"1px solid green"});
				document.getElementById('error_galwidth').innerHTML = "";
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
  
  jQuery(document).ready(function(){


	    if(typeof xyz_gallery_loading_show == 'undefined')
	 	{
	 	    function xyz_gallery_loading_show()
	 	    {
	 		    jQuery('#loading').html("<img src='<?php echo $load_image;?>'/>").fadeIn('fast');
	 	    }
	     }
	     
	 	if(typeof xyz_gallery_loading_hide == 'undefined')
	 	{ 	
	 	    function xyz_gallery_loading_hide()
	 	    {
	 		    jQuery('#loading').fadeOut('fast');
	 	    }  
	     }

	    if(typeof xyz_gallery_loadData == 'undefined')
	 	{           
	 	    function xyz_gallery_loadData(page)
	 	    {
	 	    	xyz_gallery_loading_show();             

	 		    var dataString = {
	 					action: 'xyz_gallery_load_images',
	 					page:+page,
	 	        };

	 		    jQuery.post(ajaxurl, dataString, function(response) {
	 			   
	 		    	xyz_gallery_loading_hide();
	 			   jQuery("#container").html(response);
	                       
	 		   });

	 			
	 	    }
	     }

	    xyz_gallery_loadData(1);  // For first time page load default results
	 	
	 	jQuery('#container .pagination li.active').live('click',function(){
	 	    var page = jQuery(this).attr('p');
	 	   xyz_gallery_loadData(page);
	 	});
	 	           
	 	jQuery('#go_btn').live('click',function(){
	 	    var page = parseInt(jQuery('.goto').val());
	 	    var no_of_pages = parseInt(jQuery('.total').attr('a'));
	 	    if(page != 0 && page <= no_of_pages){
	 	    	xyz_gallery_loadData(page);
	 	    }else{
	 	        alert('Enter a PAGE between 1 and '+no_of_pages);
	 	        jQuery('.goto').val("").focus();
	 	        return false;
	 	    }
	 	    
	 	});
	     
		     
		

	  jQuery('#form').submit(function()
	  {
		   var  xyz_gal_sel_gimg=0;

		   if(jQuery("#sel_images").length>0)
		   {	
				xyz_gal_sel_gimg = jQuery.map(jQuery("#sel_images ul li img"), function(n, i)
				{
				      return n.id;
				});
				          
		   }
				    	
		   jQuery("#hid_gal_images").val(xyz_gal_sel_gimg);
				    	
	  });


  });


  
  if(typeof xyz_admngr_remove_img == 'undefined')
  {
      function xyz_admngr_remove_img(current_id)
      {	   	
            jQuery("#sel_images #xyzgalimg_"+current_id).hide();
            jQuery("#sel_images #xyzgalimg_"+current_id).remove();
                    
      }
  }


 
    

 </script>

 
 <div class='wrap' >
    <h2>
       XYZ Gallery - Edit Gallery
       <a href="<?php echo admin_url('admin.php?page=wp-gallery-manager&action=add')?>" class="add-new-h2" >Add New</a> 
    </h2>
 
    <form name="add_gallery_form" action="" method="post"  id="form">
    <?php wp_nonce_field( 'gallery-edit_'.$id ); ?>
    <!-- <input type="hidden" name="add_gallery" value="true" /> -->
    <table class="widefat post fixed" cellspacing="0" style="width:100%;">

       <thead>
          <tr>
             <th width="250">Attribute</th>
             <th>Value</th>
             <!-- <th>Description</th> -->
         </tr>
      </thead>

      <tfoot>
         <tr>
            <th width="250">Attribute</th>
            <th>Value</th>
            <!--  <th>Description</th> -->
         </tr>
      </tfoot>

     <tbody>
     
     <?php 
     $pimgpath='';
       $resl=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_gal_gallery JOIN ".$wpdb->prefix."xyz_gal_images ON ".$wpdb->prefix."xyz_gal_gallery.preview_image=".$wpdb->prefix."xyz_gal_images.id WHERE ".$wpdb->prefix."xyz_gal_gallery.id=%d",$id));
       if(empty($resl))
       {
	      $resl=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_gal_gallery where id=%d",$id));
	      foreach($resl as $gal)
	      {
		     $gal_name=$gal->name;
		     $pimgurlc="";
		     $preview_img_id="";
		     
		     $gal_responsive=$gal->responsive;
		     $gal_height=$gal->gallery_height;
		     $gal_width=$gal->gallery_width;
		     
		     $gal_status=$gal->status;
	      }
       }
       else if($resl)
       {
             foreach($resl as $gal)
             {
	             $gal_name=$gal->name;
	             $preview_img=$gal->image;
	             $preview_img_id=$gal->preview_image;
	             $primgnam=$preview_img_id."_".$preview_img;
	             
	             $gal_responsive=$gal->responsive;
	             $gal_height=$gal->gallery_height;
	             $gal_width=$gal->gallery_width;
	             
	             $gal_status=$gal->status;
	             
	             $img_alt_text=$gal->alt_text;
	             $img_title=$gal->title;
	  
	             //$thumb=explode(".",$primgnam);
	             //$primgnam=$thumb[0]."_thumb.".$thumb[1];
	             
	             $ext = substr( $primgnam, strrpos( $primgnam, '.' )+1 );
	             $lastdot=strrpos($primgnam,'.');
	             $imgnam=substr($primgnam,0,$lastdot);
	             $primgnam=$imgnam."_thumb.".$ext;
	  
	             $prev_img_name=$gal->title;
	             $pimgurlc=$imgpath.$primgnam;
	             
	             $pimgpath=$actualpath.$primgnam;
              }
       }
       else
       {
	        $gal_name="";
	
       }
    ?>
    
    <tr>
       <!--  <input type="hidden" id="hid_id" value="<?php echo $id;?>" name="hid_id">-->
       <td><br><strong>Gallery Name<span style="color:red;"> *</span></strong></td>
       <td><br><input type="text" size="20%" name="galleryName" value="<?php echo $gal_name;?>" id="galleryName"/><p style="color:red;" id="error_galname"><?php echo $error_galname;?></p></td>
       <!--<td><br>This name is the internal name for the gallery.</td>-->
    </tr>
    
    
    <tr>
                <td><br><strong>Make gallery responsive</strong></td>
                <td><br>
                    <Input type = 'Checkbox' name ="gal_responsive" value ="1" <?php if($gal_responsive==1){echo "checked";} ?>>
                </td>
             </tr>

             <tr>
                <td><br><strong>Maximum height<span style="color:red;"> *</span></strong></td>
                <td><input type="text" size="25" id="gal_height" name="gal_height" value="<?php echo $gal_height;?>" onkeypress="return xyz_gallery_isNumber(event)" /> px <p style="color:red;" id="error_galheight"><?php echo $error_galheight;?></p></td>
             </tr>

             <tr>
                <td><br><strong>Maximum width<span style="color:red;"> *</span></strong></td>
                <td><input type="text" size="25" id="gal_width" name="gal_width" value="<?php echo $gal_width;?>" onkeypress="return xyz_gallery_isNumber(event)" /> px <p style="color:red;" id="error_galwidth"><?php echo $error_galwidth;?></p></td>
             </tr>
 
    <tr>
       <td><br><strong>Preview Image</strong><p>(This will be the first image in the slider.)</p></td>
       <td scope="row"><br>
           <div id="preview_image" class="preview_image">
              <?php if($pimgurlc!=""){?>
                  <!--  <input type="hidden" id="db_image" value="<?php echo $preview_img_id;?>" />-->
                  
                  
                   <?php 
                                   list($widthimage,$heightimage) = @getimagesize($pimgpath);
        	
        	                       $dimension=xyz_gallery_get_image_dimension($widthimage,$heightimage,2);
        	                       $dimensionarray=explode('_',$dimension);
?>
                  
                   <div class="xyz_thumb_preview_gal_div">
                  <img src="<?php echo $pimgurlc;?>" style="width:<?php echo $dimensionarray[0];?>px;height:<?php echo $dimensionarray[1];?>px;" alt="<?php echo $img_alt_text;?>" title="<?php echo $img_title;?>" value="<?php echo $preview_img_id;?>" >
             </div>
             
              <?php }?>
           </div>
       </td>
    </tr> 
 
    <tr>
       <td><br><strong>Gallery Images</strong><p>(Drag images from available images.)</p></td>
       <td scope="row"><br>
           <div id="sel_images" class="gallery_new">
               <ul class="sortable-list" id="sel_images_ul">
               <?php 
               $pimgpath='';
               $resgimg=$wpdb->get_results($wpdb->prepare("SELECT imgid FROM ".$wpdb->prefix."xyz_gal_mapping WHERE galid=%d",$id));
               if($resgimg)
               {
                  foreach($resgimg as $galimg)
                  {
	                 $galimage[]=$galimg->imgid;
                 
		             $img_id=$galimg->imgid;
		             $query=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_gal_images where id=%d",$img_id));
		             foreach($query as $gal_images)
		             {	
		                $gal_img_name=$gal_images->image;
		                $primgalnam1=$img_id."_".$gal_img_name;
		                
		                $ext = substr( $primgalnam1, strrpos( $primgalnam1, '.' )+1 );
		                $lastdot=strrpos($primgalnam1,'.');
		                $imgnam=substr($primgalnam1,0,$lastdot);
		                $primgalnam=$imgnam."_thumb.".$ext;
		
		                //$thumb=explode(".",$primgalnam1);
		                //$primgalnam=$thumb[0]."_thumb.".$thumb[1];
		
		                $pgimgurlc=$imgpath.$primgalnam;
		                $img_alt_text=$gal_images->alt_text;
		                $title=$gal_images->title;
		                
		                  $pimgpath=$actualpath.$primgalnam;
		       ?>
	                    <li class="sortable-item" style="display:list-item;width:96px;" id="xyzgalimg_<?php echo $img_id;?>" >	
                       
	                        <?php 
                                   list($widthimage,$heightimage) = @getimagesize($pimgpath);
        	
        	                       $dimension=xyz_gallery_get_image_dimension($widthimage,$heightimage,2);
        	                       $dimensionarray=explode('_',$dimension);
        	                ?>
                           <div class="xyz_thumb_preview_gal_div" id="xyz_thumb_preview_gal_div_<?php echo $gal_images->id;?>">
	                       <div  id="xyz_gal_imag_close_<?php echo $gal_images->id;?>" class='xyz_gal_imag_close' onclick='return xyz_admngr_remove_img(<?php echo $gal_images->id;?>);'></div>
	                       <img src="<?php echo $pgimgurlc;?>" style="width:<?php echo $dimensionarray[0];?>px;height:<?php echo $dimensionarray[1];?>px;" alt="<?php echo $img_alt_text;?>" title="<?php echo $title;?>" value="<?php echo $gal_images->id;?>"  class="change" id="<?php echo $gal_images->id;?>">
                           </div>
                                                      
                        </li>
             <?php 
	                }	
	            }
             }
            else 
            {
	           $galimage[]=0;
            }	
            ?>
            </ul>
         </div>
      </td>
   </tr>
   
   <input type="hidden" id="hid_gal_images" name="hid_gal_images" />

   <tr>
      <td><br><strong>Available Images</strong></td>
      <td scope="row"><br>
        <?php
         $res=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."xyz_gal_images order by id desc");
         $no=0;
         if($res)
	       $no=count($res);
         if($no==0)
         { ?>
          <div >
             <h3> No images are created yet</h3>
          </div>
      <?php }
      else
      {  ?>
        <div id="loading"></div>
        <div id="container">
            <div class="data"></div>
            <div class="pagination"></div>
        </div>
      <?php }?>
     </td>
  </tr>
  
 <tr><td></td></tr> <tr><td></td></tr>
 <?php if($gal_status==1){?>
 <tr>
    <td><br><strong>Gallery Shortcode</strong></td> 
    <td scope="row"><br>[xyz_gallery id=<?php echo $id; ?>]</td>
 </tr> 
 <?php }?>    
  <tr>
  
    <td></td>
    <td class="major-publishing-actions">
      <input type="submit" name="Submit" class="button-primary" value="Save Changes" id="submitgal" onclick="return xyz_gallery_validation()" />
    </td>
  </tr>
</tbody>
</table>
</form>
</div>

<?php ?>