<?php
if ( ! defined( 'ABSPATH' ) )
   exit;
 
global $wpdb;

$upload_dir = wp_upload_dir();


$imgpath=$upload_dir['baseurl']."/xyz_gal/xyz_gimg/";
$imgpathde=plugins_url(XYZ_GALLERY_MANAGER_DIR.'/images/');

$editimage=$imgpathde."edit.png";
$deleteimage=$imgpathde."delete.png";

$limit =get_option('xyz_gal_page_limit');
$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
$offset = ( $pagenum - 1 ) * $limit;
$res=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."xyz_gal_images order by id desc LIMIT $offset, $limit");
$no=0;
if($res)
$no=count($res);

if(isset($_GET['delete'])&& $_GET['delete']==1)
{
	echo '<br><div class="system_notice_area_style1" id="system_notice_area">Image is deleted successfully.<span id="system_notice_area_dismiss">Dismiss</span></div>';
}
if(isset($_GET['create'])&& $_GET['create']==1)
{
	echo '<br><div class="system_notice_area_style1" id="system_notice_area">New image added .<span id="system_notice_area_dismiss">Dismiss</span></div>';
}
if(isset($_GET['create'])&& $_GET['create']==2)
{
	echo '<br><div class="system_notice_area_style1" id="system_notice_area">Images added .<span id="system_notice_area_dismiss">Dismiss</span></div>';
}
?>


<script type="text/javascript">


if(typeof xyz_gallery_loadImage == 'undefined')
{
function xyz_gallery_loadImage(id)
{
	document.getElementById("imgpop_"+id).style.display="";
	document.getElementById("behind_div").style.display="";

	var ie=document.all && !window.opera;
	var iebody=(document.compatMode=="CSS1Compat")? document.documentElement : document.body ;

	//ht=(ie)? iebody.clientHeight: window.innerHeight ;
	//wt=(ie)? iebody.clientWidth : window.innerWidth ;
	

	//document.getElementById("imgpop_"+id).style.top=ht/2-150 +'px';
	//document.getElementById("imgpop_"+id).style.left=wt/2-150 +'px';
}
}

if(typeof xyz_gallery_closeImage == 'undefined')
{
function xyz_gallery_closeImage(id)
{
	document.getElementById("imgpop_"+id).style.display="none";
	document.getElementById("behind_div").style.display="none";
}
}

</script>	

<div class='wrap'>
  
     <h2>Images 
     <a href="<?php echo admin_url('admin.php?page=wp-gallery-manager-images&action=add')?>" class="add-new-h2" >Add New</a>  
  </h2>
  <p>This is a listing of all images.</p>
  
  <table class="widefat post fixed" id="galleryResults" cellspacing="0">
  
    <thead>
      <tr class="xyz_gallery_alternate">
        <th>Image Preview</th>
        <th width="25%" scope="row">Title</th>
        <th width="25%" scope="row">Action</th>
        
      </tr>
    </thead>
       
    <tfoot>
      <tr class="xyz_gallery_alternate">
        <th>Image Preview</th>
        <th width="25%" scope="row">Title</th>
        <th width="25%" scope="row">Action</th>
        
      </tr>
    </tfoot>
        
    <tbody>
      <?php if($no==0){?>
	    <tr valign="top" >
	      <td scope="row" colspan="3" ><h3> No images are created yet</h3></td>
        </tr>
	 <?php } 
	 else 
	 {
	 	$count = 0;
	 	$class = '';
	   foreach($res as $image) 
	   {
	   	
	   	  $class = ( $count % 2 == 1 ) ? ' class="xyz_gallery_alternate"' : '';
	   	  $count++;
	   	
          $bgimgc=$image->image;
          $id=$image->id;
          if($bgimgc!="")
          $bgimgnam1=$id."_".$bgimgc;
          
          $ext = substr( $bgimgnam1, strrpos( $bgimgnam1, '.' )+1 );
          $lastdot=strrpos($bgimgnam1,'.');
          $imgnam=substr($bgimgnam1,0,$lastdot);
          $bgimgnam=$imgnam."_thumb.".$ext;
        		
          //$thumb=explode(".",$bgimgnam1);
          //$bgimgnam=$thumb[0]."_thumb.".$thumb[1];
        		
          $bgimgurlc=$imgpath.$bgimgnam;
        		
      ?>				
      <tr <?php echo $class; ?>>
        <td><?php if($bgimgc!=""){?>
          <div class="xyz_gal_image">
           <div>
               
        <?php 
        
        $upload_dir = wp_upload_dir();
        $actualpath=$upload_dir['basedir'].'/xyz_gal/xyz_gimg/';
        $pimgpath=$actualpath.$bgimgnam;
        
        
        
        	       list($widthimage,$heightimage) = @getimagesize($pimgpath);
        	       
        	       
        	
        	       $dimension=xyz_gallery_get_image_dimension($widthimage,$heightimage,1);
        	       $dimensionarray=explode('_',$dimension);?>
        	       <img src="<?php echo $bgimgurlc;?>" title="<?php echo $image->title;?>" alt="<?php echo $image->alt_text;?>" class="xyz_gal_thumb_image" style="height:<?php echo $dimensionarray[1];?>px;width:<?php echo $dimensionarray[0];?>px;cursor: pointer;" onclick="xyz_gallery_loadImage(<?php echo $id;?>)">
               
                   <div id="imgpop_<?php echo $id;?>" class="xyz_gal_image_popup" style="display: none;">
                         <?php if($image->title!=""){?><div class="xyz_gal_img_caption"><h4><?php echo $image->title;?></h4></div><?php }?>
                         <div style="min-height:100px;width:100%;">
                         <img src="<?php echo  $imgpath.$bgimgnam1; ?>" class="xyz_gal_image_pop_inner" alt="<?php echo $image->alt_text;?>" title="<?php echo $image->title;?>"/>
                         </div>
                         <div class="xyz_gal_image_pop_close" onclick="xyz_gallery_closeImage(<?php echo $id;?>)"></div>
                   </div>
               
               
            </div>
          </div><?php }?>
        </td>
        
        <td><?php echo $image->title;?></td>
        <td class="major-publishing-actions">
          <?php
        
            $imgdel = admin_url('admin.php?page=wp-gallery-manager-images&action=delete&id='.$image->id);
          ?>
           <input type="hidden" name="galleryId" value="<?php echo $image->id;?>" />
           <a href="<?php echo admin_url('admin.php?page=wp-gallery-manager-images&action=edit&id='.$image->id); ?>"><img src="<?php echo $editimage;?>" title="Edit" style="height:15px;width:15px;"></a>&nbsp;&nbsp;&nbsp;&nbsp;  
           <a href="<?php echo wp_nonce_url($imgdel,'image-del_'.$image->id); ?>" onclick="javascript: return confirm( 'Do you really want to delete this image?')"><img src="<?php echo $deleteimage;?>" title="Delete" style="height:15px;width:15px;"></a>
        </td>
      </tr>
	  <?php } ?>
     </tbody>
   </table>
     
     <?php 
     
       $re2=$wpdb->get_var("SELECT count(*) FROM ".$wpdb->prefix."xyz_gal_images");
       $no2=0;
       if($re2)
     	  $total=$re2;
       $num_of_pages = ceil( $total / $limit );
     
       $page_links = paginate_links( array(
     		'base' => add_query_arg( 'pagenum','%#%'),
     		'format' => '',
     		'prev_text' =>  '&laquo;',
     		'next_text' =>  '&raquo;',
     		'total' => $num_of_pages,
     		'current' => $pagenum
       ) );
     
     
     
       if ( $page_links ) {
     	  echo '<div class="tablenav" style="width:99%"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
        }
	} 
    ?>
 
 <table  style="display:none;"></table>
    <div class="xyz_gal_behind_div" id="behind_div" style="display: none;">	 
</div>
</div>
     
<?php ?>