<?php
if ( ! defined( 'ABSPATH' ) )
	 exit;
	
global $wpdb;

$imgpathde=plugins_url(WP_GALLERY_MANAGER_DIR.'/images/');

$load_image=$imgpathde."loading.gif";

$res=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."xyz_gal_images order by id desc ");

$error_galname="";
$error_galheight="";
$error_galwidth="";
$expr = '/^[1-9][0-9]*$/';
$gal_name="";
$gal_responsive="";
$gal_height="";
$gal_width="";

$no=0;
if($res)
	$no=count($res);

if($_POST)
{
	if (! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'add-glry_' )) {
			wp_nonce_ays( 'add-glry_' );
			exit;
		}

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
			$error_galname="Name should contain alphabets only";
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
		
		if(preg_match($expr, $gal_height) && filter_var($gal_height, FILTER_VALIDATE_INT)) {
			$error_galheight="";
		}
		else{
			$f=1;
			$error_galheight="Gallery height should contain integers";
		}
	}
	if($gal_width=="")
	{
		$f=1;
		$error_galwidth="Enter gallery width";
	}
	else
	{
		if(preg_match($expr, $gal_height) && filter_var($gal_width, FILTER_VALIDATE_INT)) {
			$error_galwidth="";
		}
		else{
			$f=1;
			$error_galwidth="Gallery width should contain integers.";
		}
	}
	
	if($gal_name!="" && $gal_height!="" && $gal_width!="")
	{	
   if($f!=1)
   { $re=$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."xyz_gal_gallery values(%d,%s,%d,%d,%d,%d,%d)",0,$gal_name,$img_id[0],$gal_responsive,$gal_height,$gal_width,0));
   	$id=$wpdb->insert_id;
   	
   	for($i=0;$i<$img_count;$i++)
   	{
   		$re1=$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."xyz_gal_mapping(`id`,`galid`,`imgid`)values(%d,%d,%d)",0,$id,$img_id[$i]));
   		$id2=$wpdb->insert_id;
   	}
   
       header("Location:".admin_url('admin.php?page=wp-gallery-manager&create=1'));
       exit();
   }
	}
}
?>


 
<style type="text/css">
.image_gallery
{
  width: 60% !important;
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

   <h2>Add New Gallery</h2>
   <p>This is where you can create new galleries. Once the new gallery has been added, a short code will be provided for use in posts/pages.</p>

   <form name="add_gallery_form" action="" method="post" enctype="multipart/form-data" id="form">
      	<?php wp_nonce_field('add-glry_');?>
      <table class="widefat post fixed" cellspacing="0">
       
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

             <tr>
                <td><br><strong>Gallery Name<span style="color:red;"> *</span></strong></td>
                <td><br><input type="text" name="galleryName" value="<?php echo $gal_name;?>" id="galleryName"/><p style="color:red;" id="error_galname"><?php echo $error_galname;?></p></td>
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
                <td><br><strong>Gallery Images</strong><p>(Drag images from available images.)</p></td>
                <td scope="row"><br>
                     <div id="sel_images" class="gallery_new">
                     <ul class="sortable-list">
                     
                     </ul></div>
                </td>
             </tr>


             <input type="hidden" id="hid_gal_images" name="hid_gal_images" />

             <tr>
                <td><br><strong>Available Images</strong></td>
                <td scope="row"><br>
                   <?php
                   $pimgpath='';
                    if($no==0){?>
                   
                        <div>
                             <h3> No images are created yet</h3>
                        </div>
                        
                   <?php }
                   else{?>
                   
                      <div id="loading"></div>
                      <div id="container">
                           <div class="data"></div>
                           <div class="pagination"></div> 
                      </div>
                                                           
                  <?php } ?>
                </td>
             </tr>
             
               <tr><td></td></tr> <tr><td></td></tr>  
                                   
             <tr>
                 <td></td>
                 <td class="major-publishing-actions">
                 <!--  <a id="select_gallery"  name="select_gallery" class="button-primary" onclick="showDiv()">Show Images</a>-->
                 <input type="submit" name="Submit" class="button-primary" value="Add Gallery" id="submitgal"  onclick="return xyz_gallery_validation()" /></td>
           </tr>

        </tbody>                
    </table>
                 
  </form>
</div>


<?php ?>