<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$merchantname = BFCHelper::getLanguage($merchant->Name, $GLOBALS['bfi_lang'], null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 


?>

<div class="com_bookingforconnector_merchantdetails com_bookingforconnector_merchantdetails-t<?php echo  $merchant->MerchantTypeId?>">
	<?php //echo JHTML::link(JRoute::_('index.php?option=com_bookingforconnector&view=merchantdetails&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name),true,-1), JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_RETURN') ,array('class' => ' bfi-pull-right'));?>
	<!--<h2 class="com_bookingforconnector_merchantdetails-name"><?php // echo  $merchantname?> 
		<span class="bfi_merchantdetails-rating bfi_merchantdetails-rating<?php  // echo  $merchant->Rating ?>">
			<span class="bfi_merchantdetails-ratingText">Rating <?php  // echo  $merchant->Rating ?></span>
		</span>
	</h2> -->
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
   }
	?>
	<br /><br />
	<?php if (isset($summaryRatings)): ?>
	<div class="bfi-rating-container">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 bfi-text-center">
				<div class="bfi-rating_valuation">
					<div class="bfi-rating-title"><?php echo $rating_text['merchants_reviews_text_value_'.$totalInt]; ?></div>
					<div class="bfi-rating-value"><?php echo  $total; ?></div>
					<div class="bfi-rating-based"><?php printf( __( 'Based on %1$s Reviews.' , 'bfi' ), $summaryRatings->Count ); ?></div>
				</div>
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>4">
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
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>1">
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>4">
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
			<form action="<?php echo $routeMerchant; ?>" method="post" name="adminForm" id="adminForm" class="bfi-rating-filter ratingformfilter">
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
					<?php if (($merchant->RatingsContext === 2 || $merchant->RatingsContext === 3 ) && ($merchant->RatingsType==0 || $merchant->RatingsType==2)) :?>
						<a href="<?php echo $routeRating; ?>" class="btn btn-warning bfi-pull-right"><?php _e('Write a Review', 'bfi') ?></a>
					<?php endif; ?>
			</form>
		<?php if ($ratings != null): ?>
		<div class="bfi-merchantdetails-ratings">
			<br />
			<?php foreach($ratings as $rating): ?>
			<?php 
			$creationDateLabel = "";
			if (isset($rating->CreationDate)) {
				
				
				$creationDate = BFCHelper::parseJsonDate($rating->CreationDate,'Y-m-d');
				$jdate  = new DateTime($creationDate);
				$creationDateLabel = __('Reviewed', 'bfi') . ' ' .$jdate->format('d/m/Y');
			}
			$checkInDateLabel = "";
			if (isset($rating->CheckInDate)) {
				$checkInDate = BFCHelper::parseJsonDate($rating->CheckInDate,'Y-m-d');
				$jdate  = new DateTime($checkInDate);
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
				$reply =$rating->Reply;			
			}
			?>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?> bfi-rating-list">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>2 ">
					<strong><?php echo  $rating->Name; ?></strong><br />
					<?php echo $location; ?><br />
					<?php 
					if(!empty($rating->TypologyId)){
						echo $list[$rating->TypologyId] ;
					}
					?><br />
					<?php if (!empty($rating->Label)) :?>
						<br />
						<div class="bfi-rating-lineheight">
							<?php echo $checkInDateLabel; ?>
						</div>
					<?php endif; ?>
					<?php if (!empty($rating->ResourceId)) :?>
						<br />
						<div class="bfi-rating-lineheight">
							<?php _e('Reference', 'bfi'); ?><br />
							 <?php 
								$resourceName = BFCHelper::getLanguage($rating->ResourceName, $GLOBALS['bfi_lang'], null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
							 ?>
							<a class="" href="<?php //echo $route ?>" id="nameAnchor<?php echo $rating->ResourceId?>"><?php echo  $resourceName ?></a>

						</div>
					<?php endif; ?>
						<br />
				</div>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>10 ">
					<div class=" arrow_box ">
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>">
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>6">
								<div class="bfi-rating-value_small"><?php echo  $rating->Total; ?></div>
								<div class="bfi-rating-title_small"><?php echo $rating_text['merchants_reviews_text_value_'.$t]; ?></div>
							</div>
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>6 com_bookingforconnector_rating_date_small ">
								<div class="bfi-pull-right" ><?php echo  $creationDateLabel?></div>
								<?php if (!empty($rating->Label)) :?>
									<div class="com_bookingforconnector_rating_sign-check bfi-pull-right" ><?php printf( __( 'Review certified by %1$s', 'bfi' ), $rating->Label ); ?></div>
								<?php endif; ?>
							</div>
						</div>
					</div>
					
					<div class=" rating_details ">
					<?php if($rating->NotesData !="") :?>
						<p > <span class="label label-info"><b><?php _e('+', 'bfi') ?></b></span>
						<span class="expander"><?php echo  stripslashes($rating->NotesData); ?></span>
						</p>
						<br />
					<?php endif; ?>
					<?php if($rating->NotesData1 !="") :?>
						<p ><span class="label label-warning"><b><?php _e('-', 'bfi') ?></b></span>
						<span class="expander"><?php echo  stripslashes($rating->NotesData1); ?></span>
						</p>
					<?php endif; ?>
					</div>
					<?php if (!empty($reply)) : ?>
						<div class=" rating_details arrow_box_top">
							<div class="">
							   <?php printf( __( '%s has responded to this review', 'bfi' ), $merchantname); ?>
								<span class="com_bookingforconnector_rating_date_small bfi-pull-right"><?php echo  $replydateLabel?></span>
							</div>
							<br />
							<?php echo  $reply; ?>
						</div>
					<?php endif; //replies?>

				</div>
			</div>
			<?php endforeach?>
		</div>	
		<?php endif?>
		<?php if ($ratings == null): ?>
		<?php _e('No Reviews Found.'); ?>
		<?php endif?>
	</div>

		<?php else:?>
			<?php if (($merchant->RatingsContext === 2 || $merchant->RatingsContext === 3 ) && ($merchant->RatingsType==0 || $merchant->RatingsType==2)) :?>
				<div class="alert alert-block">
					<?php _e('Would you like to be the first to write a review?', 'bfi'); ?>
					<a href="<?php echo $routeRating;?>" class="btn btn-info"><?php _e('Write a Review', 'bfi'); ?></a>
				</div>
			<?php endif?>	
		<?php endif?>	
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