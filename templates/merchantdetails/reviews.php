<?php
//$base_url = get_site_url();
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$base_url = get_site_url();
$language = $GLOBALS['bfi_lang'];
$languageForm ='';
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$languageForm = "/" .ICL_LANGUAGE_CODE;
		}
}
$merchantdetails_page = get_post( bfi_get_page_id( 'merchantdetails' ) );
$url_merchant_page = get_permalink( $merchantdetails_page->ID );
$routeMerchant = $url_merchant_page . $merchant->MerchantId.'-'.BFI()->seoUrl($merchant->Name);

$accommodationdetails_page = get_post( bfi_get_page_id( 'accommodationdetails' ) );
$url_resource_page = get_permalink( $accommodationdetails_page->ID );
$uri = $url_resource_page;

$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$showdata = COM_BOOKINGFORCONNECTOR_SHOWDATA;

//$page = isset($_GET['paged']) ? $_GET['paged'] : 1;
$page = bfi_get_current_page() ;

$pages = 0;
if($total>0){
	$pages = ceil($total / COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);
}


$rating_text = array('merchants_reviews_text_value_0' => __('Very poor', 'bfi'),
						'merchants_reviews_text_value_1' => __('Poor', 'bfi'),   
						'merchants_reviews_text_value_2' => __('Disappointing', 'bfi'),
						'merchants_reviews_text_value_3' => __('Fair', 'bfi'),
						'merchants_reviews_text_value_4' => __('Okay', 'bfi'),
						'merchants_reviews_text_value_5' => __('Pleasant', 'bfi'),  
						'merchants_reviews_text_value_6' => __('Good', 'bfi'),
						'merchants_reviews_text_value_7' => __('Very good', 'bfi'),  
						'merchants_reviews_text_value_8' => __('Fabulous', 'bfi'), 
						'merchants_reviews_text_value_9' => __('Exceptional', 'bfi'),  
						'merchants_reviews_text_value_10' => __('Exceptional', 'bfi'),                                 
					);
$merchantname = BFCHelper::getLanguage($merchant->Name, $GLOBALS['bfi_lang'], null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 

$hasSuperior = !empty($merchant->RatingSubValue);
$rating = (int)$merchant->Rating;
if ($rating>9 )
{
	$rating = $rating/10;
	$hasSuperior = ($MerchantDetail->Rating%10)>0;
} 

/*---------------IMPOSTAZIONI SEO----------------------*/
	$payload["@type"] = "Organization";
	$payload["@context"] = "http://schema.org";
	$payload["name"] = $merchantName;
	$payload["description"] = $merchantDescriptionSeo;
	$payload["url"] = $routeSeo; 
	if (!empty($merchant->LogoUrl)){
		$payload["logo"] = "https:".BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'logobig');
	}
/*--------------- FINE IMPOSTAZIONI SEO----------------------*/

?>
<script type="application/ld+json">// <![CDATA[
<?php echo json_encode($payload,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); ?>
// ]]></script>

<div class="bfi-content">
	<?php //echo JHTML::link(JRoute::_('index.php?option=com_bookingforconnector&view=merchantdetails&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name),true,-1), JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_RETURN') ,array('class' => ' bfi-pull-right'));?>
	<div class="bfi-title-name"><h1><?php echo  $merchant->Name?></h1> 
		<span class="bfi-item-rating">
			<?php for($i = 0; $i < $rating; $i++) { ?>
				<i class="fa fa-star"></i>
			<?php } ?>
			<?php if ($hasSuperior) { ?>
				&nbsp;S
			<?php } ?>
		</span>
	</div>

	<div class="clear"></div>
	<?php
   //$filters = $this->params['filters'];
   
   $list = array (__('All reviewers', 'bfi'), __('Solo travellers', 'bfi'), __('Groups', 'bfi'), __('Young couplet', 'bfi'), __('Mature couples', 'bfi'), __('Families with young children', 'bfi'), __('Family with older children', 'bfi'));

   if(isset($summaryRatings)) {
		$val1 = round($summaryRatings->AValue1 * 10);
		$val2 = round($summaryRatings->AValue2 * 10);
		$val3 = round($summaryRatings->AValue3 * 10);
		$val4 = round($summaryRatings->AValue4 * 10);
		$val5 = round($summaryRatings->AValue5 * 10);
		$total = number_format((float)$summaryRatings->Average, 1, '.', '');

		$totalInt = BFCHelper::convertTotal($total);
	?>
	<div class="bfi-rating-container">
		<div class="bfi-row">
			<div class="bfi-col-md-3 bfi-text-center">
				<div class="bfi-rating_valuation">
					<div class="bfi-rating-title"><?php echo $rating_text['merchants_reviews_text_value_'.$totalInt]; ?></div>
					<div class="bfi-rating-value"><?php echo  $total; ?></div>
					<div class="bfi-rating-based"><?php printf( __( 'Based on %1$s Reviews.'  , 'bfi'), $summaryRatings->Count ); ?></div>
				</div>
			</div>
			<div class="bfi-col-md-4">
				<br />
				<div class="bfi-rating-title_desc"><?php _e('Staff', 'bfi'); ?> <span class="bfi-pull-right"><?php echo number_format((float)$summaryRatings->AValue1, 1, '.', ''); ?></span></div>
				<div class="bfi-progress">
					<div class="bfi-progress-bar" style="width: <?php echo  $val1; ?>%"></div>
				</div>
				<div class="bfi-rating-title_desc"><?php _e('Services', 'bfi'); ?> <span class="bfi-pull-right"><?php echo number_format((float)$summaryRatings->AValue2, 1, '.', ''); ?></span></div>
				<div class="bfi-progress">
					<div class="bfi-progress-bar" style="width: <?php echo  $val2; ?>%"></div>
				</div>
				<div class="bfi-rating-title_desc"><?php _e('Clean', 'bfi'); ?> <span class="bfi-pull-right"><?php echo number_format((float)$summaryRatings->AValue3, 1, '.', ''); ?></span></div>
				<div class="bfi-progress">
					<div class="bfi-progress-bar" style="width: <?php echo  $val3; ?>%"></div>
				</div>
			</div>
			<div class="bfi-col-md-1">
			</div>
			<div class="bfi-col-md-4">
				<br />
				<div class="bfi-rating-title_desc"><?php _e('Comfort', 'bfi'); ?> <span class="bfi-pull-right"><?php echo number_format((float)$summaryRatings->AValue4, 1, '.', ''); ?></span></div>
				<div class="bfi-progress">
					<div class="bfi-progress-bar" style="width: <?php echo  $val4; ?>%"></div>
				</div>
				<div class="bfi-rating-title_desc"><?php _e('Value for Money', 'bfi'); ?> <span class="bfi-pull-right"><?php echo number_format((float)$summaryRatings->AValue5, 1, '.', ''); ?></span></div>
				<div class="bfi-progress">
					<div class="bfi-progress-bar" style="width: <?php echo  $val5; ?>%"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="bfi-rating-container">
			<?php $typologyId = isset($_SESSION['ratings']['filters']['typologyid']) ? $_SESSION['ratings']['filters']['typologyid'] : 0; ?>
			<form action="<?php echo $routeMerchant; ?>/<?php echo  _x('reviews', 'Page slug', 'bfi' ) ?>" method="post" name="adminForm" id="adminForm" class="bfi-rating-filter ratingformfilter">
				<div class="filters">
					<?php _e('Show reviews from', 'bfi') ?>
					<select id="filterstypologyid" name="filters[typologyid]" onchange="this.form.submit();">
						<option value="0"<?php if($typologyId == 0) { echo ' selected="selected"'; } else { echo ''; } ?>><?php _e('All reviewers', 'bfi'); ?></option>
						<option value="1"<?php if($typologyId == 1) { echo ' selected="selected"'; } else { echo ''; } ?>><?php _e('Solo travellers', 'bfi'); ?></option>
						<option value="2"<?php if($typologyId == 2) { echo ' selected="selected"'; } else { echo ''; } ?>><?php _e('Groups', 'bfi'); ?></option>
						<option value="3"<?php if($typologyId == 3) { echo ' selected="selected"'; } else { echo ''; } ?>><?php _e('Young couplet', 'bfi'); ?></option>
						<option value="4"<?php if($typologyId == 4) { echo ' selected="selected"'; } else { echo ''; } ?>><?php _e('Mature couples', 'bfi'); ?></option>
						<option value="5"<?php if($typologyId == 5) { echo ' selected="selected"'; } else { echo ''; } ?>><?php _e('Families with young children', 'bfi'); ?></option>
						<option value="6"<?php if($typologyId == 6) { echo ' selected="selected"'; } else { echo ''; } ?>><?php _e('Family with older children', 'bfi'); ?></option>
					</select>
					<input type="hidden" name="filter_order" value="">
					<input type="hidden" name="filter_order_Dir" value="">
					<input type="hidden" name="searchid" value="-1">
					<input type="hidden" name="limitstart" value="0">
					<a href="<?php echo $routeMerchant; ?>/<?php echo _x('review', 'Page slug', 'bfi' ) ?>" class="bfi-btn bfi-alternative bfi-pull-right"><?php _e('Write a Review', 'bfi') ?></a>
				</div>
			</form>
		<?php if ($ratings != null){ ?>
		<div class="bfi-merchantdetails-ratings">
			<br />
			<?php foreach($ratings as $rating){ ?>
			<?php 
			$creationDateLabel = "";
			if (isset($rating->CreationDate)) {
				
				
				$creationDate = BFCHelper::parseJsonDate($rating->CreationDate,'Y-m-d');
				$jdate  = new DateTime($creationDate,new DateTimeZone('UTC'));
				$creationDateLabel = __('Reviewed', 'bfi') . ' ' .$jdate->format('d/m/Y');
			}
			$checkInDateLabel = "";
			if (isset($rating->CheckInDate)) {
				$checkInDate = BFCHelper::parseJsonDate($rating->CheckInDate,'Y-m-d');
				$jdate  = new DateTime($checkInDate,new DateTimeZone('UTC'));
				$checkInDateLabel =  __('Stayed', 'bfi') . ' '.date_i18n('F Y',$jdate->getTimestamp());

			}
			
			$location = "";
			if ( $rating->City != ""){
				$location .= $rating->City . ", ";
			}
			$location .= $rating->Nation;
			
			$t = BFCHelper::convertTotal($rating->Total);
			
			//Reply=<risposte><risposta><![CDATA[Test risposta]]></risposta></risposte>
			
			$reply = ""; 
			$replydateLabel = ""; 
			if (!empty($rating->Reply)){					
				if (strpos($rating->Reply,'<replies>') !== false) {
					$replies = bfi_simpledom_load_string($rating->Reply);
					
					$reply = $replies->reply[0];
					$replydate = $replies->reply[0]["date"];


					if(!empty($replydate)){
//					$jdatereply  = new JDate(strtotime($replydate),2); // 3:20 PM, December 1st, 2012
					$jdatereply  = DateTime::createFromFormat('Ymd', $replydate,new DateTimeZone('UTC'));

					$replydateLabel =sprintf(__( 'Replied on %1s', 'bfi' ), $jdatereply->format('d/m/Y'));
					}
				} else{
					$reply =$rating->Reply;
				}
			}
			?>
			<div class="bfi-row bfi-rating-list">
				<div class="bfi-col-md-2 ">
					<strong><?php echo  $rating->Name; ?></strong><br />
					<?php echo $location; ?><br />
					<?php 
					if(!empty($rating->TypologyId)){
						echo $list[$rating->TypologyId] ;
					}
					?><br />
					<?php if (!empty($rating->Label)) {?>
						<br />
						<div class="bfi-rating-lineheight">
							<?php echo $checkInDateLabel; ?>
						</div>
					<?php } ?>
					<?php if (!empty($rating->ResourceId)) { ?>
						<br />
						<div class="bfi-rating-lineheight">
							<?php _e('Reference', 'bfi'); ?><br />
							 <?php 
								$resourceName = BFCHelper::getLanguage($rating->ResourceName, $GLOBALS['bfi_lang'], null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
								$resourceRoute = $uri.$rating->ResourceId.'-'.BFI()->seoUrl($resourceName);
							 ?>
							<a class="" href="<?php echo $resourceRoute ?>" id="nameAnchor<?php echo $rating->ResourceId?>" target="_blank"><?php echo  $resourceName ?></a>

						</div>
					<?php } ?>
						<br />
				</div>
				<div class="bfi-col-md-10 ">
					<div class=" bfi-arrow-box ">
						<div class="bfi-row">
							<div class="bfi-col-md-6">
								<div class="bfi-rating-value-small"><?php echo  $rating->Total; ?></div>
								<div class="bfi-rating-title-small"><?php echo $rating_text['merchants_reviews_text_value_'.$t]; ?></div>
							</div>
							<div class="bfi-col-md-6 com_bookingforconnector_rating_date_small ">
								<div class="bfi-pull-right" ><?php echo  $creationDateLabel?></div>
								<?php if (!empty($rating->Label) && !empty($rating->OrderId)) {?>
									<div class="com_bookingforconnector_rating_sign-check bfi-pull-right" ><?php printf( __( 'Review certified by %1$s', 'bfi' ), $rating->Label ); ?></div>
								<?php } ?>
							</div>
						</div>
					</div>
					
					<div class="bfi-rating-details">
					<?php if($rating->NotesData !="") {?>
						<p > <span class="bfi-label bfi-label-info"><b><?php _e('+', 'bfi') ?></b></span>
						<span class="expander"><?php echo  stripslashes($rating->NotesData); ?></span>
						</p>
						<br />
					<?php } ?>
					<?php if($rating->NotesData1 !="") {?>
						<p ><span class="bfi-label bfi-label-warning"><b><?php _e('-', 'bfi') ?></b></span>
						<span class="expander"><?php echo  stripslashes($rating->NotesData1); ?></span>
						</p>
					<?php } ?>
					</div>
					<?php if (!empty($reply)) { ?>
						<div class="bfi-rating-details bfi-arrow-box-top">
							<div class="">
							   <?php printf( __( '%s has responded to this review', 'bfi' ), $merchantname); ?>
								<span class="com_bookingforconnector_rating_date_small bfi-pull-right"><?php echo  $replydateLabel?></span>
							</div>
							<br />
							<?php echo  $reply; ?>
						</div>
					<?php } //replies?>

				</div>
			</div>
			<?php } ?>
		</div>	
		<?php } ?>
		<?php if ($ratings == null){ ?>
		<?php _e('No Reviews Found.', 'bfi'); ?>
		<?php } ?>
	</div>
		<?php //if (!$isFromSearch && $pagination->get('pages.total') > 1) : ?>
		<?php if ($pages > 1 ) { ?>
			<div class="pagination">
<?php   

if( get_option('permalink_structure') ) {
	$format = 'page/%#%/';
} else {
	$format = '?paged=%#%';
}
//$paginationDetails = array();
//$paginationDetails['start'] = $page * COM_BOOKINGFORCONNECTOR_ITEMPERPAGE;

  $pagination_args = array(
    'base'            => $routeMerchant .'/'._x('reviews', 'Page slug', 'bfi' ).'/%_%',
    'format'          => $format,
    'total'           => $pages,
    'current'         => $page,
    'show_all'        => false,
    'end_size'        => 5,
    'mid_size'        => 2,
    'prev_next'       => true,
    'prev_text'       => __('&laquo;'),
    'next_text'       => __('&raquo;'),
    'type'            => 'plain',
    'add_args'        => false,
    'add_fragment'    => ''
  );

  $paginate_links = paginate_links($pagination_args);
    if ($paginate_links) {
      echo "<nav class='bfi-pagination'>";
//      echo "<span class='page-numbers page-num'>Page " . $page . " of " . $numpages . "</span> ";
      echo "<span class='page-numbers page-num'>".__('Page', 'bfi')." </span> ";
      print $paginate_links;
      echo "</nav>";
    }	 ?>
			</div>
		<?php } ?>

		<?php }else{ ?>
			<div><?php _e('No Reviews Found.', 'bfi'); ?></div>
			<?php if ($merchant->RatingsContext !== 0 && $merchant->RatingsContext !== 2 ) {?>
				<div class="alert alert-block">
					<?php _e('Would you like to be the first to write a review?', 'bfi'); ?>
					<a href="<?php echo $routeMerchant;?>/<?php echo _x('review', 'Page slug', 'bfi' ) ?>" class="bfi-btn bfi-alternative"><?php _e('Write a Review', 'bfi'); ?></a>
				</div>
			<?php }?>	
		<?php } ?>	
	<div class="bfi-clearfix"></div>
	<?php  
	bfi_get_template("merchant_small_details.php",array("merchant"=>$merchant,"routeMerchant"=>$routeMerchant));	
	?>

</div>
<script type="text/javascript">
jQuery(function($) {
	var shortenOption = {
		moreText: "+ <?php _e('Details', 'bfi') ?>",
		lessText: " - <?php _e('Details', 'bfi') ?>",
		showChars: '280'
	};
	jQuery("span.expander").shorten(shortenOption);
});
</script>