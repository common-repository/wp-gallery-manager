<?php 
if ( ! defined( 'ABSPATH' ) )
	 exit;
	 
if(!function_exists('xyz_gallery_resize_custom_limit'))
{
function xyz_gallery_resize_custom_limit($imgwidth,$imgheight,$image_type,$newfile=NULL)
{
	list($width, $height, $type, $attr) = @getimagesize($newfile);

	$new_w=$width;
	$new_h=$height;

	if($width > $imgwidth)
	{
		$new_w=$imgwidth;
		$new_h=($imgwidth/$width) * $height;
	}

	if($new_h > $imgheight)
	{
		$new_w=($imgheight/$new_h) * $new_w;
		$new_h=$imgheight;
	}

	if($new_h!=$height || $new_w!=$width)
	xyz_gallery_resize($new_w,$new_h,$image_type,$newfile);
}
}


if(!function_exists('xyz_gallery_resize'))
{
function xyz_gallery_resize($width,$height,$image_type,$newfile=NULL)
{
	list($w, $h) = @getimagesize($newfile);
	

	if($w<=0 || $h<=0)
	{
		//"Could not resize given image";
		return false;
	}

	if($width<=0)
	$width=$w;

	if($height<=0)
	$height=$h;
	
	
	

	return xyz_gallery_resized($width,$height,$image_type,$newfile);
}
}


if(!function_exists('xyz_gallery_resized'))
{
function xyz_gallery_resized($width,$height,$image_type,$newfile=NULL)
{
	$image_type=strtolower($image_type);
	
	list($w, $h) = @getimagesize($newfile);
	
	if($w !=$width || $h !=$height)
	{
		if (!function_exists("imagecreate"))
		{
			//"Error: GD Library is not available.";
			return false;
		}

		$newimg=@imagecreatetruecolor($width,$height);
		
		//echo $image_type.stripos($image_type,'jpeg');die;
		if(stripos($image_type,'png')!==false)
		{
			$transperant=imagecolorallocatealpha($newimg, 0, 255, 0, 127);
			imagefill($newimg, 0, 0, $transperant);
		}
		
				
		if(stripos($image_type,'gif')!==false)
		$create=@imagecreatefromgif($newfile);
		elseif((stripos($image_type,'jpg')!==false) || (stripos($image_type,'jpeg')!==false))
		$create=@imagecreatefromjpeg($newfile);
		elseif(stripos($image_type,'png')!==false)
		$create=@imagecreatefrompng($newfile);

		
		
		
		
		
		
		
				
		
		

		@imagecopyresampled ( $newimg, $create, 0,0,0,0, $width, $height, $w,$h);

		
		
		
		if(!empty($newfile))
		{
			if(!@preg_match("/\..*+$/",@basename($newfile)))
			{
				if(@preg_match("/\..*+$/",@basename($newfile),$matches))
				$newfile=$newfile.$matches[0];
			}
		}

		
		if(stripos($image_type,'gif')!==false)
		{
			if(!empty($newfile))
			@imagegif($newimg,$newfile);
			else
			{
				@header("Content-type: image/gif");
				@imagegif($newimg);
			}
		}
		elseif((stripos($image_type,'jpg')!==false) || (stripos($image_type,'jpeg')!==false))
		{
			
			
			if(!empty($newfile))
			@imagejpeg($newimg,$newfile);
			else
			{
				@header("Content-type: image/jpeg");
				@imagejpeg($newimg);
			}
			
			
			
		}
		elseif(stripos($image_type,'png')!==false)
		{
			imagealphablending($newimg, false);
			imagesavealpha($newimg, true);
			if(!empty($newfile))
			@imagepng($newimg,$newfile);
			else
			{
				@header("Content-type: image/png");
				@imagepng($newimg);
			}
		}

		@imagedestroy($newimg);
	}
}
}


if(!function_exists('xyz_gallery_get_image_dimension'))
{
function xyz_gallery_get_image_dimension($width,$height,$type=0,$gallery=0)
{
	$newwidth=$width;
	$newheight=$height;
	if($gallery==1){
		
		
   $thumb_width=get_option('thumbnail_size_w');
	$thumb_height=get_option('thumbnail_size_h');
    $img_height=get_option("large_size_h");
	$img_width=get_option("large_size_w");
	
	}
	else {
	
	$thumb_height=get_option("xyz_gal_thumb_height");
	$thumb_width=get_option("xyz_gal_thumb_width");
	$img_height=get_option("xyz_gal_img_height");
	$img_width=get_option("xyz_gal_img_width");
	}

	if($type ==0)
	{
		$imgwidth=$img_width;
		$imgheight=$img_height;
	}
	else if($type ==1)
	{
		
		$imgwidth=50;
		$imgheight=40;
	}

	else if($type ==2)
	{
	
		$imgwidth=96;
		$imgheight=72;
	}
	
	

	if($width > $imgwidth)
	{
		$newwidth=$imgwidth;
		$newheight=($imgwidth/$width) * $height;
	}

	if($newheight > $imgheight)
	{
		$newwidth=($imgheight/$newheight) * $newwidth;

		$newheight=$imgheight;
	}

	return $newwidth.'_'.$newheight;
}
}


if(!function_exists('xyz_gallery_plugin_get_version'))
{
	function xyz_gallery_plugin_get_version()
	{
		if ( ! function_exists( 'get_plugins' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$plugin_folder = get_plugins( '/' . plugin_basename( dirname( WP_GALLERY_MANAGER_PLUGIN_FILE ) ) );
		 	
		return $plugin_folder['wp-gallery-manager.php']['Version'];
	}
}



add_filter( 'plugin_row_meta','xyz_gallery_links',10,2);

if(!function_exists('xyz_gallery_links'))
{
function xyz_gallery_links($links, $file) 
{
	$base = plugin_basename(XYZ_GALLERY_MANAGER_PLUGIN_FILE);
	if ($file == $base) 
	{
		

		$links[] = '<a href="http://kb.xyzscripts.com/wordpress-plugins/wp-gallery-manager"  title="FAQ">FAQ</a>';
		$links[] = '<a href="http://docs.xyzscripts.com/wordpress-plugins/wp-gallery-manager/"  title="Read Me">README</a>';
		$links[] = '<a href="http://xyzscripts.com/donate/1" title="Donate">DONATE</a>';

		$links[] = '<a href="http://xyzscripts.com/support/" class="xyz_support" title="Support"></a>';
		$links[] = '<a href="http://twitter.com/xyzscripts" class="xyz_twitt" title="Follow us on twitter"></a>';
		$links[] = '<a href="https://www.facebook.com/xyzscripts" class="xyz_fbook" title="Facebook"></a>';
		$links[] = '<a href="https://plus.google.com/+Xyzscripts/" class="xyz_gplus" title="+1 us on Google+"></a>';
		$links[] = '<a href="http://www.linkedin.com/company/xyzscripts" class="xyz_linkedin" title="Follow us on linkedin"></a>';
		
	}
	return $links;
}
}

?>
