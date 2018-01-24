<?php
/**
 * The Template for displaying all merchant list
 *
 *
 * @see 	   
 * @author 		Bookingfor
 * @package 	        Bookingfor/Templates
 * @version             2.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header(); ?>

	<?php
		/**
		 * bookingfor_before_main_content hook.
		 */
		do_action( 'bookingfor_before_main_content' );
	?>

		<?php while ( have_posts() ) : the_post(); ?>
			<article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="page-header">
					<h1 class="page-title"><?php the_title(); ?></h1>
				</header>

				<div class="page-content">
					<?php the_content(); ?>
				</div>
				<?php edit_post_link( __( 'Edit', 'bfi' ), '<span class="edit-link">', '</span>' ); ?>
			</article><!-- #post-<?php the_ID(); ?> -->

		<?php endwhile; // end of the loop. ?>
<?php 
	$merchantCategories = get_post_meta($post->ID, 'merchantcategories', true);
	$rating = get_post_meta($post->ID, 'rating', true);
	$cityids = get_post_meta($post->ID, 'cityids', true);
	$currURL = esc_url( get_permalink() ); 

    $model = new BookingForConnectorModelMerchants;
    $model->populateState();	
	$model->setItemPerPage(COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);

	$filter_order = $model->getOrdering();
	$filter_order_Dir = $model->getDirection();

	$currParam = $model->getParam();
	$currParam['categoryId'] = !empty($merchantCategories)?$merchantCategories:[];
	$currParam['rating'] = !empty($rating)?$rating:'';
	$currParam['cityids'] = !empty($cityids)?$cityids:[];
	$model->setParam($currParam);

		
	$total = $model->getTotal();
	$items = $model->getItems();
		
	$merchants = is_array($items) ? $items : array();
	add_action('wp_head', 'bfi_google_analytics_EEc', 10, 1);
	do_action('wp_head', "Merchants List");
	if( count($items) > 0){
		$paramRef = array(
			"merchants"=>$merchants,
			"total"=>$total,
			"items"=>$items,
			"currParam"=>$currParam,
			"filter_order"=>$filter_order,
			"filter_order_Dir"=>$filter_order_Dir,
			);
		bfi_get_template("merchantslist/merchantslist.php",$paramRef);	
//		include(BFI()->plugin_path().'/templates/merchantslist/merchantslist.php'); // merchant template

	}

	if(COM_BOOKINGFORCONNECTOR_CRITEOENABLED){
		$merchantsCriteo = isset($items) && !empty($items) ? array_unique(array_map(function($a) { return $a->MerchantId; }, $items)) : array();
		$criteoConfig = BFCHelper::getCriteoConfiguration(1, $merchantsCriteo);
		if(isset($criteoConfig) && isset($criteoConfig->enabled) && $criteoConfig->enabled && count($criteoConfig->merchants) > 0) {
			echo '<script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>';
				echo '<script type="text/javascript"><!--
				';
				echo ('window.criteo_q = window.criteo_q || []; 
					window.criteo_q.push( 
						{ event: "setAccount", account: '. $criteoConfig->campaignid .'}, 
						{ event: "setSiteType", type: "d" }, 
						{ event: "setEmail", email: "" }, 
						{ event: "viewList", item: '. json_encode($criteoConfig->merchants) .' }
					);');
				echo "//--></script>";

				
	//			$document->addScript('//static.criteo.net/js/ld/ld.js');
	//			$document->addScriptDeclaration('window.criteo_q = window.criteo_q || []; 
	//			window.criteo_q.push( 
	//				{ event: "setAccount", account: '. $criteoConfig->campaignid .'}, 
	//				{ event: "setSiteType", type: "d" }, 
	//				{ event: "setEmail", email: "" }, 
	//				{ event: "viewList", item: '. json_encode($criteoConfig->merchants) .' }
	//			);');
			}
		
	}
	

?>
	<?php
		/**
		 * bookingfor_after_main_content hook.
		 *
		 * @hooked bookingfor_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'bookingfor_after_main_content' );
	?>

	<?php
		/**
		 * bookingfor_sidebar hook.
		 *
		 * @hooked bookingfor_get_sidebar - 10
		 */
		do_action( 'bookingfor_sidebar' );
	?>

<?php get_footer( ); ?>
