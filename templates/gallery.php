<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$nthumb = 6;
$images = array();

if(!empty($bfiImageData)) {
	$imageData = preg_replace('/\s+/', '', $bfiImageData);
	foreach(explode(',', $imageData) as $image) {
		if (!empty($image)){
			$images[] = array('type' => 'image', 'data' => $image, 'index' => count ($images));
		}
	}
}
$firstVideo = 0;

if(!empty($bfiVideoData)) {	
	$videoData = preg_replace('/\s+/', '', $bfiVideoData);
	foreach(explode(',', $videoData) as $image) {
		if (!empty($image)){
			if ($firstVideo == 0) {
			    $firstVideo = count ($images);
			}
			$images[] =  array('type' => 'video', 'data' => $image, 'index' =>count ($images));
		}
	}
}
?>

<?php if (count ($images)>0){ 
	$main_img = $images[0];
	$sub_images = array_slice($images, 1, $nthumb);
	if ($firstVideo>($nthumb)) {
		$sub_images = array_slice($sub_images, 0,($nthumb-1));
		$sub_images[] =$images[$firstVideo];
	}
?>
<div class="bfi-launch-fullscreen">
	<img src="<?php echo BFCHelper::getImageUrlResized($bfiSourceData, $main_img['data'],'big')?>" alt="">
</div>
<div class="bfi-table-responsive">
<?php 
	$widthtable = "";
	$totalsub_images= count($sub_images);
	if($totalsub_images<3){
		$widthtable = "width:auto;";
	}
	$tdWidth = 100;

	if(!empty($totalsub_images)){
		$tdWidth = 100/$totalsub_images;
	}

?>	
	<table class="bfi-table bfi-imgsmallgallery" style="<?php echo $widthtable ?>"> 
		<tr>
<?php
	foreach($sub_images as $sub_img) {
		$srcImage = "";
		if($sub_img['type'] == 'image' || $sub_img['type'] == 'planimetry') {
			$srcImage = BFCHelper::getImageUrlResized($bfiSourceData, $sub_img['data'],'small');
		}else{
			$url = $sub_img["data"];
			if (strpos($url,'www.google.com/maps') !== false) {			    
				$srcImage = BFI()->plugin_url() . "/assets/images/street-view.jpg";
			}else{
				parse_str( parse_url( $url, PHP_URL_QUERY ), $arrUrl );
				if (array_key_exists('v',$arrUrl)) {
					$idyoutube = $arrUrl['v'];
					$srcImage = "//img.youtube.com/vi/" . $idyoutube ."/mqdefault.jpg";
				}
			}
		}
?>
			<td style="width:<?php echo $tdWidth ?>%;">
				<img src="<?php echo $srcImage?>" alt="">
				<div class="bfi-showall" data-index="<?php echo $sub_img['index'] ?>">
					<i class="fa fa-search tour-search"></i><br />
					<?php _e('Show All', 'bfi') ?>
				</div>
			</td>
<?php } ?>
		</tr>
	</table>
</div>
<script type="text/javascript">
<!--
jQuery(document).ready(function() {

	jQuery('.bfi-showall, .bfi-launch-fullscreen').magnificPopup({
		mainClass: 'bfi-gallery',
		items: [
		<?php foreach ($images as $image):?>
		<?php if($image['type'] != 'video') { ?>
		  {
			src: '<?php echo BFCHelper::getImageUrlResized($bfiSourceData, $image['data'], '')?>'
		  },
		<?php  } else { ?>
		<?php
		$url='';
	   if(is_array($image['data'])){
		  $url = $image['data']["url"];
	   }else{
		  $url = $image['data'];
	   }
	   parse_str( parse_url( $url, PHP_URL_QUERY ), $arrUrl );
//	   $idyoutube = $arrUrl['v'];	
		?>
		  {
			src: '<?php echo $url ?>',
			type: 'iframe' // this overrides default type
		  },
		<?php } ?>	
		<?php endforeach?>
		],
		gallery: {
		  enabled: true
		},
		type: 'image' // this is default type,
	});
	

	jQuery('.bfi-showall, .bfi-launch-fullscreen').click(function() {
		openAt =  jQuery(this).attr('data-index') || 0;
//		console.log(openAt);
		jQuery(this).magnificPopup('goTo', Number(openAt));
	});

});
 //-->
</script>


<?php } elseif ($merchant!= null && $merchant->LogoUrl != '') { ?>
	<img src="<?php echo BFCHelper::getImageUrlResized('merchant', $merchant->LogoUrl , 'resource_mono_full')?>" onerror="this.onerror=null;this.src='<?php echo BFCHelper::getImageUrl('merchant', $merchant->LogoUrl, 'resource_mono_full')?>'" />
<?php } ?>