<?php
if ( ! defined( 'ABSPATH' ) )
	 exit;

if($_POST && isset($_POST['xyz_gal_credit_link']))
{
	$xyz_credit_link=$_POST['xyz_gal_credit_link'];
	update_option('xyz_credit_link', $xyz_credit_link);
}
?>

<div style="margin-top: 10px">
<table style="float:right; ">
<tr>
<td style="float:right;">
<a class="xyz_gal_link" style="margin-left:8px;margin-right:12px;" target="_blank" href="http://help.xyzscripts.com/docs/wp-gallery-manager/faq"><b>FAQ</b></a>
</td>
<td style="float:right;">
<a class="xyz_gal_link" style="margin-left:8px;" target="_blank" href="http://help.xyzscripts.com/docs/wp-gallery-manager/"><b>Readme</b></a> |
</td>
<td style="float:right;">
<a class="xyz_gal_link" style="margin-left:8px;" target="_blank" href="http://xyzscripts.com/wordpress-plugins/wp-gallery-manager/details"><b>About</b></a> |
</td>
<td style="float:right;">
<a class="xyz_gal_link" target="_blank" href="http://xyzscripts.com"><b>XYZScripts</b></a> |
</td>

</tr>
</table>
</div>

<div style="clear: both"></div>

<?php
if((get_option('xyz_credit_link')=="0")&&(get_option('xyz_gal_credit_dismiss')=="0")){
?>
<div style="float:left;background-color: #FFECB3;border-radius:5px;padding: 0px 5px;border: 1px solid #E0AB1B" id="xyz_gal_backlink_div">

	Please do a favour by enabling backlink to our site. <a id="xyz_gal_backlink" style="cursor: pointer;" >Okay, Enable</a>.
	<a id="xyz_gal_backlink1" style="cursor: pointer;" >Dismiss</a>
<script type="text/javascript">
	var stat = 0;
	jQuery(document).ready(function() {
		jQuery('#xyz_gal_backlink').click(function() {

			xyz_gallery_blink(1)
		});

		jQuery('#xyz_gal_backlink1').click(function() {

			xyz_gallery_blink(-1)
		});

			function xyz_gallery_blink(stat){
				<?php $ajax_gllry_nonce = wp_create_nonce( "xyz-gallery-mngr" );?>
				var dataString = {
					action: 'xyz_gallery_ajax_backlink',
					security:'<?php echo $ajax_gllry_nonce; ?>',
					enable: stat
				};
				jQuery.post(ajaxurl, dataString, function(response) {

					if(response==1){
						jQuery("#xyz_gal_backlink_div").html('Thank you for enabling backlink!');
					 	jQuery("#xyz_gal_backlink_div").css('background-color', '#D8E8DA');
						jQuery("#xyz_gal_backlink_div").css('border', '1px solid #0F801C');
						jQuery("select[id=xyz_gal_credit_link] option[value=gal]").attr("selected", true);
					}
					if(response==-1){
						jQuery("#xyz_gal_backlink_div").remove();

					}

					/*if(window.rcheck)
					{
						document.location.reload();
					}*/
				});
			}
	});
</script>
</div><br />
	<?php
}
?>
