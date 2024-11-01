<?php 
if ( ! defined( 'ABSPATH' ) )
   exit;
   
add_shortcode( 'xyz_gallery', 'xyz_gallery_shortcode' );

function xyz_gallery_shortcode($id)
{

	global $wpdb;
	
	global $gal_preview_height;
	$gal_preview_height=0;
	
	$wpgal_responsive=get_option("xyz_gal_wpgal_responsive");
	$def_gal_height=get_option("xyz_gal_gallery_height");
	$def_gal_width=get_option("xyz_gal_gallery_width");
	
	if(isset($id['id']))
	{
	   $id=$id['id'];
		 $upload_dir = wp_upload_dir();
	   $imgpath=$upload_dir['baseurl']."/xyz_gal/xyz_gimg/";
       
	   $actualpath=$upload_dir['basedir'].'/xyz_gal/xyz_gimg/';

	   $thumb_height=get_option("xyz_gal_thumb_height");
	   $thumb_width=get_option("xyz_gal_thumb_width");
	   $img_height=get_option("xyz_gal_img_height");
	   $img_width=get_option("xyz_gal_img_width");

       $pimgpath='';
	   $thumbimgpath='';
	   $slideshow=get_option("xyz_gal_slideshow");
	
	   if($slideshow==1)
	      $slideshow_interval=get_option("xyz_gal_slideshow_interval")*1000;
       else
	      $slideshow_interval=0;
       
	   $resgal=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_gal_gallery WHERE ".$wpdb->prefix."xyz_gal_gallery.id=%d",$id));
	   
	   $gal_responsive=$resgal->responsive;
	   $gal_height=$resgal->gallery_height;
	   $gal_width=$resgal->gallery_width;	   	      
	   $gal_status=$resgal->status;
	   
	   if($gal_status==0)
	   	return;
	   	   

	   $resgimg=$wpdb->get_results($wpdb->prepare("SELECT imgid FROM ".$wpdb->prefix."xyz_gal_mapping WHERE galid=%d",$id));
	   foreach($resgimg as $galimg)
	   {
		   $galimage[]=$galimg->imgid;
	   }
	   $img_count=count($resgimg);
	
	   //echo '<h2>'.$gal_name.'</h2>';

           $imagestring='';
           $thumbstring='';
           $detailstring='';
           $thumb_detailstring='';
           $iii=0;
           for($i=0;$i<$img_count;$i++)
           {
           	  $img_id=$galimage[$i];
           	  $query=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_gal_images where id=%d",$img_id));
           	  foreach($query as $gal_images)
           	  {
           		  $gal_img_name=$gal_images->image;
           		  $img_title=$gal_images->title;
           		  $alt_text=$gal_images->alt_text;
           		  $primgalnam=$img_id."_".$gal_img_name;
           		  $url=$gal_images->url;
           
           		  $pgimgurlc=$imgpath.$primgalnam;
			  
			      $pimgpath=$actualpath.$primgalnam;
           		  
           		  $ext = substr( $primgalnam, strrpos( $primgalnam, '.' )+1 );
           		  $lastdot=strrpos($primgalnam,'.');
           		  $imgnam=substr($primgalnam,0,$lastdot);
           		  $bgimgnam=$imgnam."_thumb.".$ext;
           		  
           		  //$thumb=explode(".",$primgalnam);
           		  //$bgimgnam=$thumb[0]."_thumb.".$thumb[1];
           		  $thumbimg=$imgpath.$bgimgnam;
           		  
                   $thumbimgpath=$actualpath.$bgimgnam;
           		  list($widthimage,$heightimage) = @getimagesize($pimgpath);
           		  
           		  $dimension=xyz_gallery_get_image_dimension($widthimage,$heightimage);
           		  $dimensionarray=explode('_',$dimension);
           		  
           		  
           		  if($detailstring !='')
           		  $detailstring.='_'.$img_id.'-'.$dimensionarray[0].'-'.$dimensionarray[1];
           		  else
           		  $detailstring.=$img_id.'-'.$dimensionarray[0].'-'.$dimensionarray[1];
           		  
           		  if($dimensionarray[1]>$gal_preview_height)
           		      $gal_preview_height=$dimensionarray[1];
           		  	
           		  
           		  list($twidthimage,$theightimage) = @getimagesize($thumbimgpath);
           		  
           		  $tdimension=xyz_gallery_get_image_dimension($twidthimage,$theightimage);
           		  $tdimensionarray=explode('_',$tdimension);
           		  
           		  
           		  if($thumb_detailstring !='')
           		  $thumb_detailstring.='_'.$img_id.'-'.$tdimensionarray[0].'-'.$tdimensionarray[1];
           		  else
           		  $thumb_detailstring.=$img_id.'-'.$tdimensionarray[0].'-'.$tdimensionarray[1];
           		  
           		
                if($iii ==0)
                   $imagestring.='<a href="'.$url.'"><img class="xyz_gal_bigimage xyz_gal_active" id="xyz_gal_image_'.$img_id.'" src="'.$pgimgurlc.'" style="max-width:100%;height:auto;max-height:'.($gal_height-($thumb_height+20)-10).'px;width:auto;" title="'.$img_title.'" alt="'.$alt_text.'" /></a>';
                else
                   $imagestring.='<a href="'.$url.'"><img class="xyz_gal_bigimage " id="xyz_gal_image_'.$img_id.'" src="'.$pgimgurlc.'" style="max-width:100%;height:auto;max-height:'.($gal_height-($thumb_height+20)-10).'px;width:auto;display: none;" title="'.$img_title.'" alt="'.$alt_text.'"/></a>';

           
                if($thumbstring !='')
                   $thumbstring.="(#*&%$$%&*#)"."<div onclick='xyz_gallery_LoadImage(".$img_id.",".$id.")' class='xyz_gal_thump_img' id='xyz_gal_thump_img_".$img_id."' style='width:".$thumb_width."px;height:".$thumb_height."px;'><div style='line-height:".$thumb_height."px;'><img id='xyz_gal_thumb_img_new_".$img_id."' class='xyz_gal_thumb_image' src='".$thumbimg."' title='".$img_title."' alt='".$alt_text."' /></div></div>";
                else 
                   $thumbstring.="<div onclick='xyz_gallery_LoadImage(".$img_id.",".$id.")' class='xyz_gal_thump_img xyz_gal_active_thumb' id='xyz_gal_thump_img_".$img_id."' style='width:".$thumb_width."px;height:".$thumb_height."px;'><div style='line-height:".$thumb_height."px;'><img id='xyz_gal_thumb_img_new_".$img_id."' class='xyz_gal_thumb_image' src='".$thumbimg."' title='".$img_title."' alt='".$alt_text."'/></div></div>";
           	
             
           		  $iii=$iii+1;
           	  }
           }
           
           $totalwidth=($img_count*($thumb_width+35));
           $img_thumb_gap=20;
	
	    }
        else
        {   
        	global $wp_query;
        	  $upload_dir = wp_upload_dir();
           
           $imgpath=$upload_dir['baseurl'];
           $gal_ids=$id['ids'];
           
           if(isset($id['xyz_cls_listing_id']))
           	  $id=$id['xyz_cls_listing_id'];
           else
           	  $id=abs(intval(hexdec(uniqid())));
           //echo $xyz_cls_id;
           
           $gal_id=explode(',',$gal_ids);
           $img_count=count($gal_id);
                      
           $thumb_width=get_option('thumbnail_size_w');
           $thumb_height=get_option('thumbnail_size_h');
           $img_height=get_option("large_size_h");
           $img_width=get_option("large_size_w");
           $img_med_height=get_option("medium_size_h");
           $img_med_width=get_option("medium_size_w");
           
           $gal_responsive=get_option("xyz_gal_wpgal_responsive");
           $gal_height=get_option("xyz_gal_gallery_height");
           $gal_width=get_option("xyz_gal_gallery_width");
                      
           
           $slideshow=get_option("xyz_gal_slideshow");
           
           if($slideshow==1)
           	$slideshow_interval=get_option("xyz_gal_slideshow_interval")*1000;
           else
           	$slideshow_interval=0;
           
           $imagestring='';
           $thumbstring='';
           $detailstring='';
           $thumb_detailstring='';
           $iii=0;
           
           $pid = $wp_query->get_queried_object_id();
           
           $post_status=get_post_status( $pid );
           if($post_status!="publish")
           	return;
           
           
           
           for($i=0;$i<$img_count;$i++)
           {
           	$img_id=$gal_id[$i];
           	$query=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."postmeta where post_id=%d AND meta_key=%s",$img_id,"_wp_attached_file"));
           	foreach($query as $gal_images)
           	{           		           		           		                      		
           		$img_title = get_the_title($img_id);
           		$alt_text = get_post_meta($img_id, '_wp_attachment_image_alt', true);
           		
           		$thumb_url=wp_get_attachment_thumb_url( $img_id );  
           		$thumb_name = substr( $thumb_url, strrpos( $thumb_url, '/' )+1 );
           		$thumb_ext = substr( $thumb_name, strrpos( $thumb_name, '.' )+1 );
           		$thumb_dot=strrpos($thumb_name,'.');
           		$thumbnam=substr($thumb_name,0,$thumb_dot);
           		$thumb=$thumbnam.".".$thumb_ext;
           		
           		if(get_option("uploads_use_yearmonth_folders")==1)
           		{           			           			           			
           		    $image=explode("/",$gal_images->meta_value);
           		    $img_name=$image[2];
           		    $bgimgurlc=$imgpath.$gal_images->meta_value;
           		           		   
           		    $actualpath1=$upload_dir['basedir'].'/';  
           		    $bgimgurlc1=$actualpath1.$gal_images->meta_value;
           		           		
           		    $thumb_path=$upload_dir['baseurl'].$image[0]."/".$image[1]."/";
           		    $thumb_path1=$upload_dir['basedir']."/".$image[0]."/".$image[1]."/";
           		              
           		    $ext = substr( $img_name, strrpos( $img_name, '.' )+1 );
           		    $lastdot=strrpos($img_name,'.');
           		    $imgnam=substr($img_name,0,$lastdot);
           		    $bgimgnam=$imgnam."-".$thumb_width."x".$thumb_height.".".$ext;  
           		            		           		              		               		    
           		    $thumbimg=$thumb_path.$thumb;
           		    $thumbimg1=$thumb_path1.$thumb;
           		    
           		}
           		else if(get_option("uploads_use_yearmonth_folders")=="")
           		{           			           			                                                           
                    $img_name=$gal_images->meta_value;
                    $bgimgurlc=$imgpath.$gal_images->meta_value;
                                        
                    $actualpath1=$upload_dir['basedir'].'/';
                    $bgimgurlc1=$actualpath1.$gal_images->meta_value;
                    
                    $thumb_path=$imgpath;
                                                           
                    $ext = substr( $img_name, strrpos( $img_name, '.' )+1 );
                    $lastdot=strrpos($img_name,'.');
                    $imgnam=substr($img_name,0,$lastdot);
                    $bgimgnam=$imgnam."-".$thumb_width."x".$thumb_height.".".$ext; 
                                                           
                    $thumbimg=$thumb_path.$thumb;
                    $thumbimg1=$actualpath1.$thumb;
                    
           		}	
           
           		list($widthimage,$heightimage) = @getimagesize($bgimgurlc1);
           
           		$dimension=xyz_gallery_get_image_dimension($widthimage,$heightimage,0,1);
           		$dimensionarray=explode('_',$dimension);
           
           
           		if($detailstring !='')
           			$detailstring.='_'.$img_id.'-'.$dimensionarray[0].'-'.$dimensionarray[1];
           		else
           			$detailstring.=$img_id.'-'.$dimensionarray[0].'-'.$dimensionarray[1];
           		
           		if($dimensionarray[1]>$gal_preview_height)
           			$gal_preview_height=$dimensionarray[1];
           		
           		//echo $dimensionarray[1]."-";
                                 
           
           		list($twidthimage,$theightimage) = @getimagesize($thumbimg1);
           
           		$tdimension=xyz_gallery_get_image_dimension($twidthimage,$theightimage,0,1);
           		$tdimensionarray=explode('_',$tdimension);
           
           
           		if($thumb_detailstring !='')
           			$thumb_detailstring.='_'.$img_id.'-'.$tdimensionarray[0].'-'.$tdimensionarray[1];
           		else
           			$thumb_detailstring.=$img_id.'-'.$tdimensionarray[0].'-'.$tdimensionarray[1];
           
           
           		if($iii ==0)
           			$imagestring.='<img class="xyz_gal_bigimage xyz_gal_active" id="xyz_gal_image_'.$img_id.'" src="'.$bgimgurlc.'" style="max-width:100%;height:auto;max-height:'.($gal_height-($thumb_height+20)-10).'px;width:auto;" title="'.$img_title.'" alt="'.$alt_text.'"/>';
           		else
           			$imagestring.='<img class="xyz_gal_bigimage " id="xyz_gal_image_'.$img_id.'" src="'.$bgimgurlc.'" style="max-width:100%;height:auto;max-height:'.($gal_height-($thumb_height+20)-10).'px;width:auto;display:none;" title="'.$img_title.'" alt="'.$alt_text.'"/>';
           
           
           
           		if($thumbstring !='')
           			$thumbstring.="(#*&%$$%&*#)"."<div onclick='xyz_gallery_LoadImage(".$img_id.",".$id.")' class='xyz_gal_thump_img' id='xyz_gal_thump_img_".$img_id."' style='width:".$thumb_width."px;height:".$thumb_height."px;'><div style='line-height:".$thumb_height."px;'><img id='xyz_gal_thumb_img_new_".$img_id."' class='xyz_gal_thumb_image' src='".$thumbimg."' title='".$img_title."' alt='".$alt_text."'/></div></div>";
           		else
           			$thumbstring.="<div onclick='xyz_gallery_LoadImage(".$img_id.",".$id.")' class='xyz_gal_thump_img xyz_gal_active_thumb' id='xyz_gal_thump_img_".$img_id."' style='width:".$thumb_width."px;height:".$thumb_height."px;'><div style='line-height:".$thumb_height."px;'><img id='xyz_gal_thumb_img_new_".$img_id."' class='xyz_gal_thumb_image' src='".$thumbimg."' title='".$img_title."' alt='".$alt_text."'/></div></div>";
           
           
           		$iii=$iii+1;
           	}
           }
           
           $totalwidth=($img_count*($thumb_width+35));
           $img_thumb_gap=20;
        }
        
        
        $tmp='';
        if(is_numeric(ini_get('output_buffering'))){
        	$tmp=ob_get_contents();
        	ob_clean();
        	ob_start();
        }
?>


<div id="xyz_gallery_<?php echo $id;?>"  style="width:<?php echo $gal_width;?>px;height:<?php echo $gal_height;?>px;" class="xyz_gal_container">

<div style="max-height:<?php echo (($gal_height-($thumb_height+20)));?>px;height:<?php echo ($gal_preview_height+20);?>px;" class="xyz_gal_preview">
<?php echo $imagestring;?>
</div>

<div class="xyz_gal_thumb_container" style="height:<?php echo ($thumb_height+20);?>px;">
<table class="xyz_gal_thumb_table">
<tr>
<td class="xyz_gal_leftarrow_td">
<div class="xyz_gal_leftarrow"></div>
</td>
<td class="xyz_gal_thumb_td"> 
<div class="xyz_gal_thumb_outer_div">
<div class="xyz_gal_thumb_div">
<div class="xyz_gal_thumb_innerdiv" id="xyz_gal_thumb_innerdiv_<?php echo $id;?>" style="height:<?php echo ($thumb_height+20);?>px;width:<?php echo $totalwidth;?>px;"></div>
</div>
</div>
</td>
<td class="xyz_gal_rightarrow_td">
<div class="xyz_gal_rightarrow"></div>
</td>
</table>

<input type="hidden" class="xyz_gal_current_divset" name="xyz_gal_currentslider_<?php echo $id;?>" id="xyz_gal_currentslider_<?php echo $id;?>" value="1" />
<input type="hidden" name="xyz_gal_divcount_<?php echo $id;?>" id="xyz_gal_divcount_<?php echo $id;?>" value="1" />
<input type="hidden" name="xyz_gal_detailstring_<?php echo $id;?>" id="xyz_gal_detailstring_<?php echo $id;?>" value="<?php echo $detailstring;?>" />
<input type="hidden" name="xyz_gal_thumb_detailstring_<?php echo $id;?>" id="xyz_gal_thumb_detailstring_<?php echo $id;?>" value="<?php echo $thumb_detailstring;?>" />
<input type="hidden" name="xyz_gal_thumbstring_<?php echo $id;?>" id="xyz_gal_thumbstring_<?php echo $id;?>" value="<?php echo $thumbstring;?>" />
<input type="hidden" name="xyz_gal_imagecount_<?php echo $id;?>" id="xyz_gal_imagecount_<?php echo $id;?>" value="<?php echo $img_count;?>" />

</div>
</div>
       

<script type="text/javascript">

if(typeof xyz_gallery_LoadImage == 'undefined')
{
function xyz_gallery_LoadImage(id,galid)
{
	<?php if($slideshow==1){?>
	clearInterval(window['xyz_gal_interval_'+galid]);
	<?php }?>	
	
	jQuery('#xyz_gallery_'+galid+' .xyz_gal_bigimage').hide();
	jQuery('#xyz_gallery_'+galid+' #xyz_gal_image_'+id).show();

	jQuery('#xyz_gallery_'+galid+' .xyz_gal_bigimage').removeClass('xyz_gal_active');
	jQuery('#xyz_gallery_'+galid+' #xyz_gal_image_'+id).addClass('xyz_gal_active');

	jQuery('.xyz_gal_thumb_divset'+' .xyz_gal_thump_img').removeClass('xyz_gal_active_thumb');
    jQuery('#xyz_gal_thump_img_'+id).addClass('xyz_gal_active_thumb');

	<?php if($slideshow==1){?>
	window['xyz_gal_interval_'+galid]=setInterval('xyz_gallery_slide_show('+galid+')',<?php echo $slideshow_interval;?>);
	<?php }?>	
}
}

var gal_responsive=<?php echo $gal_responsive;?>;
var gal_width=<?php echo $gal_width;?>;
var gal_height=<?php echo $gal_height;?>;

var img_thumb_gap=<?php echo $img_thumb_gap?>;
var imgheight123=<?php echo $img_height;?>;
var imgwidth123=<?php echo $img_width;?>;
var thumbwidth=<?php echo ($thumb_width+$img_thumb_gap);?>;
var firstheightouter=<?php echo ($img_height+$thumb_height+100);?>;
var firstheightinner=<?php echo (($gal_height-($thumb_height+20)));?>;
var thumbheight=<?php echo $thumb_height;?>;


var xyznext_<?php echo $id;?>=1;
var xyzprevious_<?php echo $id;?>=0;

<?php if($slideshow==1){?>

if(typeof xyz_gal_interval_<?php echo $id;?> == 'undefined')
	var xyz_gal_interval_<?php echo $id;?>=setInterval('xyz_gallery_slide_show(<?php echo $id;?>)',<?php echo $slideshow_interval;?>);
<?php }?>



if(typeof xyz_gallery_resize == 'undefined')
{
function xyz_gallery_resize(id)
{

    
	thumbwidth=<?php echo ($thumb_width+$img_thumb_gap);?>;
	    
	var thumpstring=jQuery('#xyz_gal_thumbstring_'+id).val();
	var thumpcount=jQuery('#xyz_gal_imagecount_'+id).val();
	
    var gal_responsive=<?php echo $gal_responsive;?>;
	var gal_width=<?php echo $gal_width;?>;
	var gal_height=<?php echo $gal_height;?>;

	var slide_show=<?php echo $slideshow;?>;


	if(gal_responsive==1)
	{
	    jQuery('#xyz_gallery_'+id).css("width","100%");
	    var container_totalwidth=jQuery('#xyz_gallery_'+id).outerWidth();

	    if(container_totalwidth>gal_width)
	    	container_totalwidth=gal_width;
    	
	    container_total_height=((container_totalwidth/gal_width) * gal_height);	    
		newheight123=parseFloat(container_total_height)-20-thumbheight;
		
		
		if(newheight123<(thumbheight*2))
	    {		    
		    jQuery('#xyz_gallery_'+id).css('height',(thumbheight+40)+'px');
		    jQuery('#xyz_gallery_'+id+' .xyz_gal_preview').css('max-height','0px');
		    jQuery('#xyz_gallery_'+id+' .xyz_gal_bigimage').css('max-height','0px');
	    }
		else
		{		
			jQuery('#xyz_gallery_'+id+' .xyz_gal_preview').css('max-height',newheight123+'px');		
			jQuery('#xyz_gallery_'+id+' .xyz_gal_bigimage').css('max-height',(newheight123-10)+'px'); 
						    
	     	    		   
		    jQuery('#xyz_gallery_'+id).css("width",container_totalwidth+"px");	   
		    jQuery('#xyz_gallery_'+id).css("height",container_total_height+"px");
	      
		}
	   
	}
		
		
	
var totalwidth=jQuery('#xyz_gallery_'+id+' .xyz_gal_preview').outerWidth();
	totalwidth=parseFloat(totalwidth-60);
var singledisplaycount=Math.floor(totalwidth/thumbwidth);
if(singledisplaycount<1)
	singledisplaycount=1;
jQuery('#xyz_gallery_'+id+' .xyz_gal_thumb_div').css('width',(singledisplaycount*thumbwidth)+'px');


var thumpstringarray=[];
thumpstringarray=thumpstring.split('(#*&%$$%&*#)');

var thumppart='';
var p=1;
var q=1;
for(i=0;i<thumpstringarray.length;i++)
{
	if(p >singledisplaycount)
	{

		q=q+1;
		//jQuery('#xyz_gallery_'+id+' .xyz_gal_rightarrow').show();
		
		thumppart=thumppart+'</div>';
		p=1;

	}
	
if(p ==1)
{
	if(i >0)
	thumppart=thumppart+'<div class="xyz_gal_thumb_divset" style="width:'+(singledisplaycount*thumbwidth)+'px">';
	else
	thumppart=thumppart+'<div class="xyz_gal_thumb_divset xyz_thumb_active" style="width:'+(singledisplaycount*thumbwidth)+'px">';
}


thumppart=thumppart+thumpstringarray[i];


p=p+1;
}
	
if(singledisplaycount>1)
    jQuery('#xyz_gal_divcount_'+id).val((thumpcount-singledisplaycount)+1);
else if(singledisplaycount==1)
	jQuery('#xyz_gal_divcount_'+id).val(q);

totthumbwidth=(singledisplaycount*thumbwidth)*q;



jQuery('#xyz_gal_thumb_innerdiv_'+id).css('width',totthumbwidth+'px');

/*if(q >1)
{
	jQuery('#xyz_gallery_'+id+' .xyz_gal_rightarrow').show();
	jQuery('#xyz_gallery_'+id+' .xyz_gal_leftarrow').hide();
}*/

if(thumppart !='')
thumppart=thumppart+'</div>';

jQuery('#xyz_gal_thumb_innerdiv_'+id).html(thumppart);





}
}


if(typeof xyz_gallery_slide_show == 'undefined')
{
	function xyz_gallery_slide_show(id)
	{

		  var xyzimage = jQuery('#xyz_gallery_'+id+' .xyz_gal_preview img');
		  var xyzcurrentimage=jQuery('#xyz_gallery_'+id+' .xyz_gal_active');
	      xyzimagecount = xyzimage.length;
	      xyzcurrentindex=xyzimage.index(xyzcurrentimage);

	      var xyzthumbset = jQuery('#xyz_gallery_'+id+' .xyz_thumb_active .xyz_gal_thump_img');
		      xyzthumbsetlength=xyzthumbset.length;



		   


		      

			  
	      if(window['xyznext_'+id] ==1)
	      {

	    	//  alert(xyzcurrentindex+'==='+xyzthumbsetlength);

	    	  
			if(parseInt(xyzimagecount)-1 ==xyzcurrentindex)
			{

				
				window['xyznext_'+id] = 0;
				window['xyzprevious_'+id]=1;
				var xyznewindex=parseInt(xyzcurrentindex)-1;

				if(xyznewindex <0)
				xyznewindex=0;

			    xyzmodindex=parseInt(xyzcurrentindex);
				xyzmod=xyzmodindex % xyzthumbsetlength;

				//if(xyzmod ==0)
					xyz_gallery_goleft(id);
				
			}
			else		      
			{
				var xyznewindex=parseInt(xyzcurrentindex)+1;

			    xyzmodindex=parseInt(xyzcurrentindex)+1;
				xyzmod=xyzmodindex % xyzthumbsetlength;

				
				//if(xyzmod ==0)
					xyz_gallery_goright(id);
					
					

			}

	      }
	      else
	      {

	    	  if(xyzcurrentindex ==0)
	    	  {
	    		  window['xyznext_'+id] =1;
	    		  window['xyzprevious_'+id]=0;
	    		  var xyznewindex=parseInt(xyzcurrentindex)+1;

			      xyzmodindex=parseInt(xyzcurrentindex)+1;
	    		  xyzmod=xyzmodindex % xyzthumbsetlength;
	    		  
	    		  //if(xyzmod ==0)
	    			  xyz_gallery_goright(id);
	    	  }
	    	  else
	    	  {
			 	 var xyznewindex=parseInt(xyzcurrentindex)-1;

			 	 if(xyznewindex <0)
				 xyznewindex=0;
			 	 
			     xyzmodindex=parseInt(xyzcurrentindex);
			 	 
			 	 xyzmod=xyzmodindex % xyzthumbsetlength;

			 	 //if(xyzmod ==0)
			 		xyz_gallery_goleft(id);
	    	  }
	      
	      }


	      
		      

	      jQuery('#xyz_gallery_'+id+' .xyz_gal_bigimage').hide();
	      jQuery('#xyz_gallery_'+id+' .xyz_gal_bigimage').removeClass('xyz_gal_active');


	    xyzdispalyid=xyzimage[xyznewindex].id;

	   


	    jQuery('#xyz_gallery_'+id+' '+'#'+xyzdispalyid).show();
	    jQuery('#xyz_gallery_'+id+' '+'#'+xyzdispalyid).addClass('xyz_gal_active');

	    
	  	imgid1=xyzdispalyid.split("_");
	  	imgid11=imgid1[3];
	  	
	  	 jQuery('.xyz_gal_thumb_divset'+' .xyz_gal_thump_img').removeClass('xyz_gal_active_thumb');
	  	 jQuery('#xyz_gal_thump_img_'+imgid11).addClass('xyz_gal_active_thumb');

	    
	    
	      
	}
}


if(typeof xyz_gallery_goright == 'undefined')
{
function xyz_gallery_goright(id)
{

	currentslider=jQuery('#xyz_gal_currentslider_'+id).val();
	divcount=jQuery('#xyz_gal_divcount_'+id).val();
	
	currentslider=parseInt(currentslider)+1;

	if(currentslider >divcount)
	currentslider=divcount;


	/*width=jQuery('#xyz_gallery_'+id+' .xyz_gal_thumb_divset').width();*/
    width=thumbwidth;
    

	jQuery('#xyz_gallery_'+id+' .xyz_gal_thumb_innerdiv').animate({"left": "-="+width+'px'}, "slow");
				

	if(divcount >1 && currentslider ==divcount)
	{
		jQuery('#xyz_gallery_'+id+' .xyz_gal_rightarrow').hide();
		jQuery('#xyz_gallery_'+id+' .xyz_gal_leftarrow').show();
	}
	else if(divcount >1 && currentslider ==1)
	{
		jQuery('#xyz_gallery_'+id+' .xyz_gal_rightarrow').show();
		jQuery('#xyz_gallery_'+id+' .xyz_gal_leftarrow').hide();
	}
	else if(divcount >1 && currentslider >1)
	{
		jQuery('#xyz_gallery_'+id+' .xyz_gal_rightarrow').show();
		jQuery('#xyz_gallery_'+id+' .xyz_gal_leftarrow').show();
	}
	else
	{
		jQuery('#xyz_gallery_'+id+' .xyz_gal_rightarrow').hide();
		jQuery('#xyz_gallery_'+id+' .xyz_gal_leftarrow').hide();
	}


	jQuery('#xyz_gal_currentslider_'+id).val(currentslider);




	jQuery('#xyz_gallery_'+id+' .xyz_gal_thumb_divset').removeClass('xyz_thumb_active');
	jQuery('#xyz_gallery_'+id+' .xyz_gal_thumb_divset').eq(parseInt(currentslider)-1).addClass('xyz_thumb_active');
	
	

}
}

if(typeof xyz_gallery_goleft == 'undefined')
{
function xyz_gallery_goleft(id)
{
	currentslider=jQuery('#xyz_gal_currentslider_'+id).val();
	divcount=jQuery('#xyz_gal_divcount_'+id).val();

	var thumbcount=jQuery('#xyz_gal_imagecount_'+id).val();
	
	
	currentslider=parseInt(currentslider)-1;

	if(currentslider <1)
	currentslider=1;	

	/*width=jQuery('#xyz_gallery_'+id+' .xyz_gal_thumb_divset').width();*/
	width=thumbwidth;




	 var xyzimage = jQuery('#xyz_gallery_'+id+' .xyz_gal_preview img');
	 var xyzcurrentimage=jQuery('#xyz_gallery_'+id+' .xyz_gal_active');
     xyzimagecount = xyzimage.length;
     xyzcurrentindex=xyzimage.index(xyzcurrentimage);
	

     var totalwidth1=jQuery('#xyz_gallery_'+id+' .xyz_gal_preview').outerWidth();
	 totalwidth1=parseFloat(totalwidth1-60);
     var singledisplaycount1=Math.floor(totalwidth1/thumbwidth);
     if(singledisplaycount1<1)
	   singledisplaycount1=1;


	
	
	jQuery('#xyz_gallery_'+id+' .xyz_gal_thumb_innerdiv').animate({"left": "+="+width+'px'}, "slow");

	
	
	if( xyzcurrentindex==1 && divcount >1)
	{
		jQuery('#xyz_gallery_'+id+' .xyz_gal_leftarrow').hide();
		jQuery('#xyz_gallery_'+id+' .xyz_gal_rightarrow').show();
	}
	else if(xyzcurrentindex >1 && divcount >1 )
	{
		jQuery('#xyz_gallery_'+id+' .xyz_gal_leftarrow').show();
		if(xyzcurrentindex<=(thumbcount-singledisplaycount1))
		jQuery('#xyz_gallery_'+id+' .xyz_gal_rightarrow').show();
	}
		
	else
	{
		jQuery('#xyz_gallery_'+id+' .xyz_gal_rightarrow').hide();
		jQuery('#xyz_gallery_'+id+' .xyz_gal_leftarrow').hide();
	}
	


	jQuery('#xyz_gal_currentslider_'+id).val(currentslider);

	jQuery('#xyz_gallery_'+id+' .xyz_gal_thumb_divset').removeClass('xyz_thumb_active');
	jQuery('#xyz_gallery_'+id+' .xyz_gal_thumb_divset').eq(parseInt(currentslider)-1).addClass('xyz_thumb_active');

}
}

jQuery(document).ready(function() {


	xyz_gallery_resize(<?php echo $id;?>);


	
	jQuery(window).resize(function() 
	{
		<?php if($slideshow==1){?>
		clearInterval(window.xyz_gal_interval_<?php echo $id;?>);
		<?php }?>
		jQuery(".xyz_gal_thumb_innerdiv").animate({"left":"0px"}, "slow");
		jQuery('.xyz_gal_current_divset').val(1);


		xyzimage = jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_preview img');


		jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_bigimage').hide();
		jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_bigimage').removeClass('xyz_gal_active');
		xyz_gallery_resize(<?php echo $id;?>);
		

		xyzdispalyid=xyzimage[0].id;
		

		jQuery('#xyz_gallery_'+<?php echo $id;?>+' '+'#'+xyzdispalyid).show();
		jQuery('#xyz_gallery_'+<?php echo $id;?>+' '+'#'+xyzdispalyid).addClass('xyz_gal_active');


		<?php if($slideshow==1){?>
		
		xyz_gal_interval_<?php echo $id;?>=setInterval('xyz_gallery_slide_show(<?php echo $id;?>)',<?php echo $slideshow_interval;?>);
		<?php }?>
	});


	jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_rightarrow').click(function() {


		<?php if($slideshow==1){?>
		clearInterval(window.xyz_gal_interval_<?php echo $id;?>);
<?php }?>
		currentslider=jQuery('#xyz_gal_currentslider_'+<?php echo $id;?>).val();
		divcount=jQuery('#xyz_gal_divcount_'+<?php echo $id;?>).val();
		
		currentslider=parseInt(currentslider)+1;

		if(currentslider >divcount)
		currentslider=divcount;
		

		/*width=jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_thumb_divset').width();*/
		width=thumbwidth;
	
		jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_thumb_innerdiv').animate({"left": "-="+width+'px'}, "slow");
		

		if(divcount >1 && currentslider ==divcount)
		{
			jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_rightarrow').hide();
			jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_leftarrow').show();
		}
		else if(divcount >1 && currentslider ==1)
		{
			jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_rightarrow').show();
			jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_leftarrow').hide();
		}
		else if(divcount >1 && currentslider >1)
		{
			jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_rightarrow').show();
			jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_leftarrow').show();
		}
		else
		{
			jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_rightarrow').hide();
			jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_leftarrow').hide();
		}


		jQuery('#xyz_gal_currentslider_'+<?php echo $id;?>).val(currentslider);


		jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_thumb_divset').removeClass('xyz_thumb_active');
		jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_thumb_divset').eq(parseInt(currentslider)-1).addClass('xyz_thumb_active');

		
	    /*imgdivid=jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_thumb_active .xyz_gal_thump_img');*/	    
	   imgdivid=jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_thumb_divset .xyz_gal_thump_img').eq(parseInt(currentslider)-1);
	    
	    
	    imgdiv123=imgdivid[0].id;
	    imgdiv123array=imgdiv123.split('_');
	    imgdiv123arraylength=imgdiv123array.length;
	    imgdiv123minus=parseInt(imgdiv123arraylength)-1;

	    xyz_gallery_LoadImage(imgdiv123array[imgdiv123minus],<?php echo $id;?>);

	    jQuery('.xyz_gal_thumb_divset'+' .xyz_gal_thump_img').removeClass('xyz_gal_active_thumb');
	    jQuery('#xyz_gal_thump_img_'+imgdiv123array[imgdiv123minus]).addClass('xyz_gal_active_thumb');

	    

	});
	
	jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_leftarrow').click(function() {
		<?php if($slideshow==1){?>
		clearInterval(window.xyz_gal_interval_<?php echo $id;?>);
<?php }?>
		currentslider=jQuery('#xyz_gal_currentslider_'+<?php echo $id;?>).val();
		divcount=jQuery('#xyz_gal_divcount_'+<?php echo $id;?>).val();
		
		currentslider=parseInt(currentslider)-1;

		if(currentslider <1)
		currentslider=1;	
		
		/*width=jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_thumb_divset').width();*/
		width=thumbwidth;

		jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_thumb_innerdiv').animate({"left": "+="+width+'px'}, "slow");
		

		if(currentslider ==1 && divcount >1)
		{
			jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_leftarrow').hide();
			jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_rightarrow').show();
		}
		else if(currentslider >1 && divcount >1)
		{
			jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_leftarrow').show();
			jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_rightarrow').show();
		}
		else
		{
			jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_rightarrow').hide();
			jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_leftarrow').hide();
		}
		


		jQuery('#xyz_gal_currentslider_'+<?php echo $id;?>).val(currentslider);

		jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_thumb_divset').removeClass('xyz_thumb_active');
		jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_thumb_divset').eq(parseInt(currentslider)-1).addClass('xyz_thumb_active');


		
	    /*imgdivid=jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_thumb_active .xyz_gal_thump_img');*/	    
	    imgdivid=jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_thumb_divset .xyz_gal_thump_img').eq(parseInt(currentslider)-1);
	    

	    imglen=imgdivid.length;
	    imglenminus=parseInt(imglen)-1;
	    
	    imgdiv123=imgdivid[imglenminus].id;
	    imgdiv123array=imgdiv123.split('_');
	    imgdiv123arraylength=imgdiv123array.length;
	    imgdiv123minus=parseInt(imgdiv123arraylength)-1;

	    xyz_gallery_LoadImage(imgdiv123array[imgdiv123minus],<?php echo $id;?>);

	    jQuery('.xyz_gal_thumb_divset'+' .xyz_gal_thump_img').removeClass('xyz_gal_active_thumb');
	    jQuery('#xyz_gal_thump_img_'+imgdiv123array[imgdiv123minus]).addClass('xyz_gal_active_thumb');

	
	});


	divcount=jQuery('#xyz_gal_divcount_'+<?php echo $id;?>).val();
if(divcount >1)
{

	jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_rightarrow').show();
	jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_leftarrow').hide();
}
else
{
	jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_rightarrow').hide();
	jQuery('#xyz_gallery_'+<?php echo $id;?>+' .xyz_gal_leftarrow').hide();
}
	
	
});

jQuery(window).load(function() {

	xyz_gallery_resize(<?php echo $id;?>);
});	

</script>      
         
<?php
if(is_numeric(ini_get('output_buffering'))){
 $xyz_wp_gallery_content = ob_get_contents();
ob_clean();
echo $tmp;
$xyz_wp_gallery_content=str_replace(array("\r\n","\r","\t"),"\n",$xyz_wp_gallery_content);
do{		$xyz_wp_gallery_content=str_replace("\n\n","\n",$xyz_wp_gallery_content);
}while(strpos($xyz_wp_gallery_content,"\n\n") !== false);
return $xyz_wp_gallery_content;
}


}   


$override=get_option("xyz_gal_wp_gallery_override");

if($override==1)
{
	remove_shortcode('gallery');
	add_shortcode( 'gallery', 'xyz_gallery_shortcode' );
	
}	



	
if ( ! function_exists( 'is_plugin_active' ) )
	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );	

	
if (is_plugin_active( 'xyz-shopping-cart/xyz-shopping-cart.php' ) ) 
{	
	$xyz_shc_setting=get_option("xyz_cart_product_gallery");
	
	if($xyz_shc_setting==2)
		add_shortcode( 'xyz_gallery_shc', 'xyz_gallery_shortcode' );
	
}	

if (is_plugin_active( 'wp-classifieds/wp-classifieds.php' ) )
{
	$xyz_cls_setting=get_option("xyz_cls_gallery");

	if($xyz_cls_setting==2)
		add_shortcode( 'xyz_gallery_cls', 'xyz_gallery_shortcode' );

}
	
	
	
?>