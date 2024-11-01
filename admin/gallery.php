<?php
if ( ! defined( 'ABSPATH' ) )
   exit; 
   
global $wpdb;
$upload_dir = wp_upload_dir();
$imgpath=$upload_dir['baseurl']."/xyz_gal/xyz_gimg/";

  
$imgpathde=plugins_url(XYZ_GALLERY_MANAGER_DIR.'/images/');

$editimage=$imgpathde."edit.png";
$deleteimage=$imgpathde."delete.png";
$activate_img=$imgpathde."activate.gif";
$block_img=$imgpathde."block.png";

$limit =get_option('xyz_gal_page_limit');
$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
$offset = ( $pagenum - 1 ) * $limit;
$re=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."xyz_gal_gallery order by id desc LIMIT $offset, $limit");

$no=0;
if($re)
$no=count($re);

if(isset($_GET['delete'])&& $_GET['delete']==1)
{
	echo '<br><div class="system_notice_area_style1" id="system_notice_area">Gallery is deleted successfully.<span id="system_notice_area_dismiss">Dismiss</span></div>';
}
if(isset($_GET['create'])&& $_GET['create']==1)
{
	echo '<br><div class="system_notice_area_style1" id="system_notice_area">New gallery created .<span id="system_notice_area_dismiss">Dismiss</span></div>';
}
if(isset($_GET['edit'])&& $_GET['edit']==1)
{
	echo '<br><div class="system_notice_area_style1" id="system_notice_area">Gallery edited successfully .<span id="system_notice_area_dismiss">Dismiss</span></div>';
}
if(isset($_GET['status'])&& $_GET['status']==1)
{
	echo '<br><div class="system_notice_area_style1" id="system_notice_area">Gallery is deactivated successfully.<span id="system_notice_area_dismiss">Dismiss</span></div>';
}
if(isset($_GET['status'])&& $_GET['status']==0)
{
	echo '<br><div class="system_notice_area_style1" id="system_notice_area">Gallery is activated successfully.<span id="system_notice_area_dismiss">Dismiss</span></div>';
}

?>



<div class='wrap'>
   <h2>
   Galleries
   <a href="<?php echo admin_url('admin.php?page=wp-gallery-manager&action=add')?>" class="add-new-h2" >Add New</a> 
   </h2>
   <p>This is a listing of all galleries.</p>

   <table class="widefat post fixed" id="galleryResults" cellspacing="0">
      <thead>
         <tr class="xyz_gallery_alternate">
        	<th width="25%" scope="row">Gallery Name</th>
        	<th>Preview Image</th>
            <th width="10%" scope="row">Status</th>
            <th width="25%" scope="row" >Action</th>
            <th width="25%" scope="row" >Gallery Shortcode</th>
            
         </tr>
       </thead>
       
       <tfoot>
         <tr class="xyz_gallery_alternate">
        	<th width="25%" scope="row" >Gallery Name</th>
        	<th>Preview Image</th>
            <th width="10%" scope="row" >Status</th>
            <th width="25%" scope="row" >Action</th>
            <th width="25%" scope="row" >Gallery Short Code</th>
            
            
        </tr>
       </tfoot>
       
       <tbody>
       <?php if($no==0){?>
	     <tr valign="top" >
	         <td scope="row" colspan="5" ><h3> No galleries are created yet</h3></td>
         </tr>
       <?php } 
       else { 
       
       	$count = 0;
       	$class = '';
       
          foreach($re as $gallery) { 
          	$class = ( $count % 2 == 1 ) ? ' class="xyz_gallery_alternate"' : '';
          	$count++;
          	$resl=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_gal_images where id=%d",$gallery->preview_image));
          	if($resl){
          	foreach($resl as $gal)
          	{
          		
          		
          		$bgimgc=$gal->image;
          		$id=$gal->id;
          		          		          		
          		$bgimgnam=$id."_".$bgimgc;
          		
          		$ext = substr( $bgimgnam, strrpos( $bgimgnam, '.' )+1 );
          		$lastdot=strrpos($bgimgnam,'.');
          		$imgnam=substr($bgimgnam,0,$lastdot);
          		$bgimgnam=$imgnam."_thumb.".$ext;
          		
          		//$thumb=explode(".",$bgimgnam);
          		//$bgimgnam=$thumb[0]."_thumb.".$thumb[1];
          		
          		$bgimgurlc=$imgpath.$bgimgnam;
          	}}
          	else 
          		$bgimgc="";
          ?>				
          <tr <?php echo $class; ?>>
              <td><?php echo $gallery->name;?></td>
              <td>
                   <div class="xyz_gal_image">
                       <div>
                           <?php if($bgimgc!=""){
                           	
                           	$upload_dir = wp_upload_dir();
                           	$actualpath=$upload_dir['basedir'].'/xyz_gal/xyz_gimg/';
                           	$pimgpath=$actualpath.$bgimgnam;
              
              	                list($widthimage,$heightimage) = @getimagesize($pimgpath);
              	                $dimension=xyz_gallery_get_image_dimension($widthimage,$heightimage,1);
              	                $dimensionarray=explode('_',$dimension);
              	
              	                ?><img src="<?php echo $bgimgurlc;?>" url="<?php echo $bgimgurlc;?>" title="<?php echo $gal->title;?>"style="height:<?php echo $dimensionarray[1];?>px;width:<?php echo $dimensionarray[0];?>px;" alt="<?php echo $gal->alt_text;?>">
              	            <?php }?>
              	        </div>
                    </div>
              	</td>
              	
              
              <td>
              <?php if($gallery->status==1){ echo "<b>Active</b>";}if($gallery->status==0){ echo "Inactive";}?>
              
              </td>
              <td>
                 <input type="hidden" name="galleryId" value="<?php echo $gallery->id;?>" />
                 <?php if($gallery->status==1){
                        $staturl= admin_url('admin.php?page=wp-gallery-manager&action=changestatus&id='.$gallery->id);
              	 ?>
                  <a href="<?php echo wp_nonce_url($staturl,'gallery-stat_'.$gallery->id); ?>"><img src="<?php echo $block_img;?>" title="Deactivate" style="height:15px;width:15px;"></a>&nbsp;&nbsp;&nbsp;&nbsp;  
                  <a href="<?php echo admin_url('admin.php?page=wp-gallery-manager&action=edit&id='.$gallery->id); ?>"><img src="<?php echo $editimage;?>" title="Edit" style="height:15px;width:15px;"></a>  
                 <?php }?>
                 <?php if($gallery->status==0){
                    $staturl= admin_url('admin.php?page=wp-gallery-manager&action=changestatus&id='.$gallery->id);
                    $delurl = admin_url('admin.php?page=wp-gallery-manager&action=delete&id='.$gallery->id);
                    
                  ?>
                 <a href="<?php echo wp_nonce_url($staturl,'gallery-stat_'.$gallery->id); ?>"><img src="<?php echo $activate_img;?>" title="Activate" style="height:15px;width:15px;"></a>&nbsp;&nbsp;&nbsp;&nbsp; 
                 <a href="<?php echo admin_url('admin.php?page=wp-gallery-manager&action=edit&id='.$gallery->id); ?>"><img src="<?php echo $editimage;?>" title="Edit" style="height:15px;width:15px;"></a>&nbsp;&nbsp;&nbsp;&nbsp; 
                 <a href="<?php echo wp_nonce_url($delurl,'gallery-del_'.$gallery->id); ?>" onclick="javascript: return confirm( 'Do you really want to delete this gallery?')"><img src="<?php echo $deleteimage;?>" title="Delete" style="height:15px;width:15px;"></a>
                <?php }?>
              
              </td>
              
              <td>
              <?php if($gallery->status==1){?>
              [xyz_gallery id=<?php echo $gallery->id; ?>]
              <?php }?>
              <?php if($gallery->status==0){?>
              NA
              <?php }?>
              </td>
              
          </tr>
		  <?php } ?>
       </tbody>
   </table>
     
   <?php 
       $re2=$wpdb->get_var("SELECT count(*) FROM ".$wpdb->prefix."xyz_gal_gallery");
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
     }?>
 
 <table  style="display:none;"></table>
     
</div>
     
<?php ?>