<?php
if ( ! defined( 'ABSPATH' ) )
   exit;

if(!function_exists('xyz_gallery_load_images'))
{
  function xyz_gallery_load_images()
  {
	 global $wpdb;
	   if(current_user_can('administrator')){
     if($_POST['page'])
     {
       $page = $_POST['page'];
       $cur_page = $page;
       $page -= 1;
       $per_page =20;
       $previous_btn = true;
       $next_btn = true;
       $first_btn = true;
       $last_btn = true;
       $start = $page * $per_page;

       $plugin_dir_path = dirname(__FILE__);
       $upload_dir = wp_upload_dir();
       $imgpath=$upload_dir['baseurl']."/xyz_gal/xyz_gimg/";
       $actualpath=$upload_dir['basedir'].'/xyz_gal/xyz_gimg/';
       $pimgpath='';

       $result_pag_data = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_gal_images order by id desc LIMIT %d,%d",$start, $per_page));
       $msg = "";

       foreach($result_pag_data as $row)
       {
	      $bgimgc=$row->image;
	      $id=$row->id;
	      $bgimgnam1=$id."_".$bgimgc;

	      $img_alt_text=$row->alt_text;
	      $img_title=$row->title;

	      $ext = substr( $bgimgnam1, strrpos( $bgimgnam1, '.' )+1 );
	      $lastdot=strrpos($bgimgnam1,'.');
	      $imgnam=substr($bgimgnam1,0,$lastdot);
	      $bgimgnam=$imgnam."_thumb.".$ext;

	      $bgimgurlc=$imgpath.$bgimgnam;
	      $pimgpath=$actualpath.$bgimgnam;
	      list($widthimage,$heightimage) = @getimagesize($pimgpath);

	      $dimension=xyz_gallery_get_image_dimension($widthimage,$heightimage,2);
	      $dimensionarray=explode('_',$dimension);

          $htmlmsg=htmlentities($row->image);


          $msg .= "<li class='sortable-item' id='xyzgalimg_".$id."'  ><div class='xyz_thumb_preview_gal_div' id='xyz_thumb_preview_gal_div_".$id."'><div style='display:none;' id='xyz_gal_imag_close_".$id."' class='xyz_gal_imag_close' onclick='return xyz_admngr_remove_img($id);'></div><img style=width:".$dimensionarray[0]."px;height:".$dimensionarray[1]."px; src='".$bgimgurlc."' alt='".$img_alt_text."' title='".$img_title."' value='".$id."'  class='change' id='$id'></div></li>";

      }
      $msg = "<div class='data' id='data'><div id='image_gallery' class='image_gallery'><ul class='sortable-list'>" . $msg . "</ul></div></div>"; // Content for Data


/* --------------------------------------------- */

      $count =$wpdb->get_var("SELECT count(*) FROM ".$wpdb->prefix."xyz_gal_images");
      $no_of_paginations = ceil($count / $per_page);


/* ---------------Calculating the starting and ending values for the loop----------------------------------- */

      if ($cur_page >= 7)
      {
          $start_loop = $cur_page - 3;
          if ($no_of_paginations > $cur_page + 3)
              $end_loop = $cur_page + 3;
          else if ($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6)
          {
              $start_loop = $no_of_paginations - 6;
              $end_loop = $no_of_paginations;
          }
          else
          {
              $end_loop = $no_of_paginations;
          }
      }
      else
      {
          $start_loop = 1;
          if ($no_of_paginations > 7)
             $end_loop = 7;
          else
             $end_loop = $no_of_paginations;
      }

/* ----------------------------------------------------------------------------------------------------------- */

      $msg .= "<div class='pagination'><ul>";

      // FOR ENABLING THE FIRST BUTTON
      if ($first_btn && $cur_page > 1)
      {
         $msg .= "<li p='1' class='active'>First</li>";
      }
      else if ($first_btn)
      {
         $msg .= "<li p='1' class='inactive'>First</li>";
      }

     // FOR ENABLING THE PREVIOUS BUTTON
     if ($previous_btn && $cur_page > 1)
     {
        $pre = $cur_page - 1;
        $msg .= "<li p='$pre' class='active'>Previous</li>";
     }
     else if ($previous_btn)
     {
        $msg .= "<li class='inactive'>Previous</li>";
     }
     for ($i = $start_loop; $i <= $end_loop; $i++)
     {
        if ($cur_page == $i)
            $msg .= "<li p='$i' style='color:#fff;background-color:#006699;' class='active'>{$i}</li>";
        else
            $msg .= "<li p='$i' class='active'>{$i}</li>";
     }

     // TO ENABLE THE NEXT BUTTON
     if ($next_btn && $cur_page < $no_of_paginations)
     {
        $nex = $cur_page + 1;
        $msg .= "<li p='$nex' class='active'>Next</li>";
     }
     else if ($next_btn)
     {
        $msg .= "<li class='inactive'>Next</li>";
     }

     // TO ENABLE THE END BUTTON
     if ($last_btn && $cur_page < $no_of_paginations)
     {
        $msg .= "<li p='$no_of_paginations' class='active'>Last</li>";
     }
     else if ($last_btn)
     {
        $msg .= "<li p='$no_of_paginations' class='inactive'>Last</li>";
     }

     $goto = "<input type='text' class='goto' size='1' style='margin-top:-1px;margin-left:60px;'/><input type='button' id='go_btn' class='go_button' value='Go'/>";
     $total_string = "<span class='total' a='$no_of_paginations'>Page <b>" . $cur_page . "</b> of <b>$no_of_paginations</b></span>";
     $msg = $msg . "</ul>" . $goto . $total_string . "</div>";  // Content for pagination
     echo $msg;

    }

?>
	<script type="text/javascript">

	jQuery(document).ready(function(){

		jQuery('#image_gallery .sortable-item').draggable({

		    helper: "clone"

		});

		jQuery('#sel_images .sortable-list').droppable({

		    /*accept: "#image_gallery .sortable-item",*/
		    drop: function( event, ui ) {
		            tag=ui.draggable;
		            var img=tag.attr("id");
		            var img_id=img.split("_");
		            tag.clone().attr("id", tag.attr("id")).appendTo( this );
				    jQuery("#sel_images #xyz_thumb_preview_gal_div_"+img_id[1]+" #xyz_gal_imag_close_"+img_id[1]).show();

		          },
		    accept: function(draggable) {
		              return jQuery(this).find("#" + draggable.attr("id")).length == 0;

		            }

		}).sortable({

		     connectWith: '.sortable-list',
		     helper: function (e, li) {
		               this.copyHelper = li.clone().insertAfter(li);
		               jQuery(this).data('copied', false);
		               return li.clone();
		             },

		     stop: function () {
		             var copied = jQuery(this).data('copied');
		             if (!copied) {
		               this.copyHelper.remove();
		             }
		             this.copyHelper = null;
		          }
		});
   });

	</script>
<?php

	die;
	}
  }

}

add_action('wp_ajax_xyz_gallery_load_images','xyz_gallery_load_images');


if(!function_exists('xyz_gallery_ajax_backlink'))
{
    function xyz_gallery_ajax_backlink(){
        if(current_user_can('administrator')){
            check_ajax_referer( 'xyz-gallery-mngr','security' );
            global $wpdb;
            if(isset($_POST)){
                if($_POST['enable']==1){
                    update_option('xyz_credit_link','gal');
                    echo 1;
                }
                if($_POST['enable']==-1){
                    update_option('xyz_gal_credit_dismiss','1');
                    echo -1;
                }
            }
        }
        die;
    }
}

add_action('wp_ajax_xyz_gallery_ajax_backlink', 'xyz_gallery_ajax_backlink');
?>
