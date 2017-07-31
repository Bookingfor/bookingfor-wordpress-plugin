<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$images = array();

if(!empty($merchant->ImageData)) {
	foreach(explode(', ', $merchant->ImageData) as $image) {
		if (!empty($image)){
			$images[] = array('type' => 'image', 'data' => $image);
		}
	}
}
if(!empty($merchant->PlanimetryData)) {
	foreach(explode(',', $merchant->PlanimetryData) as $image) {
		if (!empty($image)){
			$images[] =  array('type' => 'planimetry', 'data' => $image);
		}
	}
}
if(!empty($merchant->VideoData)) {	
	foreach(explode(',', $merchant->VideoData) as $image) {
		if (!empty($image)){
			$images[] =  array('type' => 'video', 'data' => $image);
		}
	}
}
?>
<?php if (count ($images)>0){ ?>
<?php
$main_img = $images[0];
$sub_images = array_slice($images, 1, 4);
$rating = $merchant->Rating;
if ($rating>9 )
{
	$rating = $rating/10;
} 
$reviewavg = isset($merchant->Avg) ? $merchant->Avg->Average : 0;
$reviewcount = isset($merchant->Avg) ? $merchant->Avg->Count : 0;
?>
<div class="bfi-launch-fullscreen">
	<img src="<?php echo BFCHelper::getImageUrlResized('merchant', $main_img['data'],'big')?>" alt="">
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
			$srcImage = BFCHelper::getImageUrlResized('merchant', $sub_img['data'],'small');
		}else{
			$url = $sub_img["data"];
			parse_str( parse_url( $url, PHP_URL_QUERY ), $arrUrl );
			$idyoutube = $arrUrl['v'];
			$srcImage = "http://img.youtube.com/vi/" . $idyoutube ."/mqdefault.jpg";
		}
?>
			<td style="width:<?php echo $tdWidth ?>%;">
				<img src="<?php echo $srcImage?>" alt="">
				<div class="bfi-showall">
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
			src: '<?php echo BFCHelper::getImageUrlResized('merchant', $image['data'], '')?>'
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
	   $idyoutube = $arrUrl['v'];	
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
		type: 'image' // this is default type
	});

});
 //-->
</script>

<?php } elseif ($merchant!= null && $merchant->LogoUrl != '') { ?>
	<img src="<?php echo BFCHelper::getImageUrlResized('merchant', $merchant->LogoUrl , 'resource_mono_full')?>" onerror="this.onerror=null;this.src='<?php echo BFCHelper::getImageUrl('merchant', $merchant->LogoUrl, 'resource_mono_full')?>'" />
<?php } ?>