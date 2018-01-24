<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$currencyclass = bfi_get_currentCurrency();

if(!isset($_POST['format'])) {


get_header( 'searchonsell' ); ?>

	<?php
		/**
		 * bookingfor_before_main_content hook.
		 *
		 * @hooked bookingfor_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked bookingfor_breadcrumb - 20
		 */
		do_action( 'bookingfor_before_main_content' );
	?>

		<?php if ( apply_filters( 'bookingfor_show_page_title', true ) ) : ?>

		<?php endif; ?>


<?php
}
	if(!isset($_POST['format'])) {
		unset($_SESSION['search.filterparams']);
		bfi_setSessionFromSubmittedData();
	}
//	$currpage = (get_query_var('page')) ? get_query_var('page') : 1;
	$page = bfi_get_current_page() ;

	$start = (absint($page)-1)*COM_BOOKINGFORCONNECTOR_ITEMPERPAGE; //($page - 1) * COM_BOOKINGFORCONNECTOR_ITEMPERPAGE;
        
    $searchmodel = new BookingForConnectorModelSearchOnSell;
	$searchmodel->setItemPerPage(COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);
	$searchmodel->populateState();

    $items = $searchmodel->getItems(false, false, $start);
	
    $items = is_array($items) ? $items : array();
    $total= $searchmodel->getTotal();
	$currSorting=$searchmodel->getOrdering() . "|" . $searchmodel->getDirection();

		$listNameAnalytics = 8;
		$listName = BFCHelper::$listNameAnalytics[$listNameAnalytics];// "Resources Search List";

		if(count($items) > 0 && COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1) {
			add_action('bfi_head', 'bfi_google_analytics_EEc', 10, 1);
			do_action('bfi_head', $listName);
			$allobjects = array();
			foreach ($items as $key => $value) {
				$obj = new stdClass;
				$obj->id = "" . $value->ResourceId . " - Sales Resource";
				$obj->name = $value->Name;
				$obj->category = $value->MerchantCategoryName;
				$obj->brand = $value->MerchantName;
				$obj->position = $key;
				$allobjects[] = $obj;
			}
//			$document->addScriptDeclaration('callAnalyticsEEc("addImpression", ' .json_encode($allobjects) . ', "list");');
			echo '<script type="text/javascript"><!--
			';
			echo ('callAnalyticsEEc("addImpression", ' . json_encode($allobjects) . ', "list");');
			echo "//--></script>";
		}


//	if(!empty($_SESSION['search.results']) && !empty($_SESSION['search.results']['totalresultcount'])){
//	$total = $_SESSION['search.results']['totalresultcount'];
//	}
    $pages = ceil($total / COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);
     $counterj = 0;

    $merchant_ids = '';
	$results = $items ;
	
	bfi_get_template("searchonsell/search-listing.php",array("results"=>$results,"total"=>$total,"items"=>$items,"pages"=>$pages,"page"=>$page,"currencyclass"=>$currencyclass,"listNameAnalytics"=>$listNameAnalytics));	
//	include(BFI()->plugin_path().'/templates/searchonsell/search-listing.php');
    $output = '';
    $output = $output. '</div>
    </div>
    <div id="mappa" class="bfi-tabcontent">		
    <div id="map_canvassearch" class="searchmap" style="width:100%; min-height:400px"></div>
    </div>';
  	$currURL = esc_url( get_permalink() ); 
$url = $currURL; //$_SERVER['REQUEST_URI'];
  $pagination_args = array(
    'base'            => $url. '%_%',
    'format'          => '?page=%#%',
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
add_filter( 'paginate_links', function( $link )
{
    return  
       filter_input( INPUT_GET, 'newsearch' )
       ? remove_query_arg( 'newsearch', $link )
       : $link;
} );

  $paginate_links = paginate_links($pagination_args);
    if ($paginate_links) {
      echo "<nav class='bfi-pagination'>";
//      echo "<span class='page-numbers page-num'>Page " . $page . " of " . $numpages . "</span> ";
      echo "<span class='page-numbers page-num'>".__('Page', 'bfi')." </span> ";
      print $paginate_links;
      echo "</nav>";
    }
    $output = $output. "</div></div>";

if(!isset($_POST['format'])) {

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


<?php get_footer( 'searchonsell' ); ?>
<?php
}
?>	
