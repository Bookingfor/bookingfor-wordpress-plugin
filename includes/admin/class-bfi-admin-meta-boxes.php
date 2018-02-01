<?php
/**
 * BookingFor Meta Boxes
 *
 * Sets up the write panels used by products and orders (custom post types).
 *
 * @author      BookingFor
 * @category    Admin
 * @package     BookingFor/Admin/Meta Boxes
 * @version     2.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'BFI_Admin_Meta_Boxes' ) ) :
/**
 * BFI_Admin_Meta_Boxes.
 */
class BFI_Admin_Meta_Boxes {

	/**
	 * Is meta boxes saved once?
	 *
	 * @var boolean
	 */
	private static $saved_meta_boxes = false;

	/**
	 * Meta box error messages.
	 *
	 * @var array
	 */
	public static $meta_box_errors  = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'rename_meta_boxes' ), 20 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );

		// Error handling (for showing errors from meta boxes on next page load)
		add_action( 'admin_notices', array( $this, 'output_errors' ) );
		add_action( 'shutdown', array( $this, 'save_errors' ) );

	
//		add_action( 'bookingfor_process_product_meta', 'BFI_Meta_Box_Product_Data::save', 10, 2 );

	}

	/**
	 * Add an error message.
	 * @param string $text
	 */
	public static function add_error( $text ) {
		self::$meta_box_errors[] = $text;
	}

	/**
	 * Save errors to an option.
	 */
	public function save_errors() {
		update_option( 'bookingfor_meta_box_errors', self::$meta_box_errors );
	}

	/**
	 * Show any stored error messages.
	 */
	public function output_errors() {
		$errors = maybe_unserialize( get_option( 'bookingfor_meta_box_errors' ) );

		if ( ! empty( $errors ) ) {

			echo '<div id="bookingfor_errors" class="error notice is-dismissible">';

			foreach ( $errors as $error ) {
				echo '<p>' . wp_kses_post( $error ) . '</p>';
			}

			echo '</div>';

			// Clear
			delete_option( 'bookingfor_meta_box_errors' );
		}
	}

	/**
	 * Add WC Meta boxes.
	 */
	public function add_meta_boxes() {
		$post_id = isset($_GET['post']) ? $_GET['post'] : (isset($_POST['post_ID']) ? $_POST['post_ID'] :0) ;
		// checks for Order page ID
		if ($post_id == bfi_get_page_id( 'orderdetails' ))
		{
			add_meta_box( 'bfi-checkmode', __( 'Autentication Mode', 'bfi' ), array( $this, 'BFI_Meta_Box_Order_CheckMode' ), 'page', 'normal' );
		}

		add_meta_box( 'bfi-merchantcategories', __( 'Categories', 'bfi' ),  array( $this, 'BFI_Meta_Box_MerchantCategories' ) , 'merchantlist', 'normal' );

	}

	public function BFI_Meta_Box_Order_CheckMode($post) {
		$values = get_post_custom( $post->ID );
		$selected = isset( $values['checkmode'] ) ? esc_attr( $values['checkmode'][0] ) :'';
		wp_nonce_field( 'bookingfor_save_data', 'bookingfor_meta_nonce' );
		?>		 
		<p>
			<label for="checkmode"><?php _e('Autentication Mode', 'bfi') ?></label>
			<select name="checkmode" id="checkmode">
				<option value="5" <?php selected( $selected, '5' ); ?> >OrderId + Email</option>
				<option value="25" <?php selected( $selected, '25' ); ?> >OrderId + Firstname + Lastname</option>
			</select>
		</p>
		<?php    
	}

//	public static function add_merchantlist_meta_boxes() {
//			add_meta_box( 'bfi-merchantcategories', __( 'Categories', 'bfi' ),  'BFI_Meta_Box_MerchantCategories' , 'page', 'normal' );
//
//
//	}

	public function BFI_Meta_Box_MerchantCategories($post) {
//		$values = get_post_custom( $post->ID );
//		$selected = isset( $values['merchantcategories'] ) ? esc_attr( $values['merchantcategories'][0] ) :'';
		$merchantCategoriesSelected = get_post_meta($post->ID, 'merchantcategories', true);
		$ratingSelected = get_post_meta($post->ID, 'rating', true);
		$cityidsSelected = get_post_meta($post->ID, 'cityids', true);

				
		if(empty($merchantCategoriesSelected)){
			$merchantCategoriesSelected = array();
		}
		if(empty($cityidsSelected)){
			$cityidsSelected = array();
		}
//		if(empty($ratingSelected)){
//			$ratingSelected = array();
//		}
		
		wp_nonce_field( 'bookingfor_save_data', 'bookingfor_meta_nonce' );
		
		$merchantCategories = BFCHelper::getMerchantCategories();

		$options = array();
		if ($merchantCategories)
		{
			foreach($merchantCategories as $merchantCategory)
			{
				$options[$merchantCategory->MerchantCategoryId] = $merchantCategory->Name;
			}
		}
		

		?>		 
		<p>
				<?php _e('Merchant category filter', 'bfi') ?></label><br />
				<?php  foreach ($options as $key => $value) { ?>

					<label class="checkbox"><input type="checkbox" name="merchantcategories[]" value="<?php echo $key ?>" <?php echo (in_array($key, $merchantCategoriesSelected)) ? 'checked="checked"' : ''; ?> /><?php echo $value ?></label><br />
				<?php } ?>
		</p>
		<p>
				<?php _e('Merchant Rating filter', 'bfi') ?></label><br />
					<select name="rating" id="rating">
						<option value="0" <?php selected( $ratingSelected, '0' ); ?> ><?php _e('All', 'bfi') ?></option>
						<option value="1" <?php selected( $ratingSelected, '1' ); ?> >*</option>
						<option value="2" <?php selected( $ratingSelected, '2' ); ?> >**</option>
						<option value="3" <?php selected( $ratingSelected, '3' ); ?> >***</option>
						<option value="4" <?php selected( $ratingSelected, '4' ); ?> >****</option>
						<option value="5" <?php selected( $ratingSelected, '5' ); ?> >*****</option>
					</select>
		</p>
<?php 
		$locationZones = BFCHelper::getLocations();
		$optionsLZ = array();
		if (!empty($locationZones))
		{
			foreach($locationZones as $lz)
			{
				$optionsLZ[$lz->CityId] = $lz->Name;
			}
						
		?>		
				<?php _e('Cities', 'bfi') ?></label><br />
				<select name="cityids[]" id="cityids" multiple="multiple" class="select2">
				<?php  foreach ($optionsLZ as $key => $value) { ?>

					<option value="<?php echo $key ?>" <?php echo (in_array($key, $cityidsSelected)) ? 'selected' : ''; ?> /><?php echo $value ?></option>
				<?php } ?>
				</select>
		<?php    
		}
	}


	/**
	 * Remove bloat.
	 */
	public function remove_meta_boxes() {
	}

	/**
	 * Rename core meta boxes.
	 */
	public function rename_meta_boxes() {
		global $post;
	}

	/**
	 * Check if we're saving, the trigger an action based on the post type.
	 *
	 * @param  int $post_id
	 * @param  object $post
	 */
	public function save_meta_boxes( $post_id, $post ) {
		// $post_id and $post are required
		if ( empty( $post_id ) || empty( $post ) || self::$saved_meta_boxes ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check the nonce
		if ( empty( $_POST['bookingfor_meta_nonce'] ) || ! wp_verify_nonce( $_POST['bookingfor_meta_nonce'], 'bookingfor_save_data' ) ) {
			return;
		}

		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		self::$saved_meta_boxes = true;

		// checks for Order page ID
		if ($post_id == bfi_get_page_id( 'orderdetails' ))
		{
			if( isset( $_POST['checkmode'] ) ){
				update_post_meta( $post_id, 'checkmode', esc_attr( $_POST['checkmode'] ) );
			}
		}

		if ( get_post_type() == 'merchantlist' ) {
		
			if( isset( $_POST['merchantcategories'] ) ){
				update_post_meta( $post_id, 'merchantcategories', $_POST['merchantcategories']  );
			}else{
				update_post_meta( $post_id, 'merchantcategories', ''  );
			}
			if( isset( $_POST['rating'] ) ){
				update_post_meta( $post_id, 'rating', $_POST['rating']  );
			}else{
				update_post_meta( $post_id, 'rating', ''  );
			}
			if( isset( $_POST['cityids'] ) ){
				update_post_meta( $post_id, 'cityids', $_POST['cityids']  );
			}else{
				update_post_meta( $post_id, 'cityids', ''  );
			}
		}	
		
	}

}
endif;

new BFI_Admin_Meta_Boxes();