<?php
/**
 * Adds settings to the permalinks admin settings page
 *
 * @class       BFI_Admin_Permalink_Settings
 * @author      BookingFor
 * @category    Admin
 * @package     BookingFor/Admin
 * @version     2.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BFI_Admin_Permalink_Settings' ) ) :

/**
 * BFI_Admin_Permalink_Settings Class.
 */
class BFI_Admin_Permalink_Settings {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		$this->settings_init();
		$this->settings_save();
	}

	/**
	 * Init our settings.
	 */
	public function settings_init() {
		// Add a section to the permalinks page
		add_settings_section( 'bookingfor-permalink', __( 'BookingFor Permalinks', 'bfi' ), array( $this, 'settings' ), 'permalink' );

		// Add our settings
		add_settings_field(
			'bookingfor_search_availability_slug',            // id
			__( 'BookingFor search result page', 'bfi' ),   // setting title
			array( $this, 'search_availability_slug_input' ),  // display callback
			'permalink',                                    // settings page
			'optional'                                      // settings section
		);
		add_settings_field(
			'bookingfor_merchantdetails_slug',                 // id
			__( 'BookingFor Merchant details page', 'bfi' ),        // setting title
			array( $this, 'merchantdetails_slug_input' ),       // display callback
			'permalink',                                    // settings page
			'optional'                                      // settings section
		);
		add_settings_field(
			'bookingfor_accommodation_details_slug',           // id
			__( 'BookingFor Accommodation details page', 'bfi' ),  // setting title
			array( $this, 'accommodation_details_slug_input' ), // display callback
			'permalink',                                    // settings page
			'optional'                                      // settings section
		);
		add_settings_field(
			'bookingfor_onsell_details_slug',           // id
			__( 'BookingFor On Sell details page', 'bfi' ),  // setting title
			array( $this, 'onsell_details_slug_input' ), // display callback
			'permalink',                                    // settings page
			'optional'                                      // settings section
		);
		add_settings_field(
			'bookingfor_merchantlist_slug',                 // id
			__( 'BookingFor Merchant List page', 'bfi' ),        // setting title
			array( $this, 'merchantlist_slug_input' ),       // display callback
			'permalink',                                    // settings page
			'optional'                                      // settings section
		);
		add_settings_field(
			'bookingfor_cartdetails_slug',                 // id
			__( 'BookingFor Cart Details page', 'bfi' ),        // setting title
			array( $this, 'cartdetails_slug_input' ),       // display callback
			'permalink',                                    // settings page
			'optional'                                      // settings section
		);
		
	}

	/**
	 * Show a slug input box.
	 */
	public function search_availability_slug_input() {
		$permalinks = get_option( 'bookingfor_permalinks' );
		?>
		<input name="bookingfor_search_availability_slug" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['search_availability_base'] ) ) echo esc_attr( $permalinks['search_availability_base'] ); ?>" placeholder="<?php echo esc_attr_x('search-availability', 'slug', 'bfi') ?>" />
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function merchantdetails_slug_input() {
		$permalinks = get_option( 'bookingfor_permalinks' );
		?>
		<input name="bookingfor_merchantdetails_slug" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['merchantdetails_base'] ) ) echo esc_attr( $permalinks['merchantdetails_base'] ); ?>" placeholder="<?php echo esc_attr_x('merchantdetails', 'slug', 'bfi') ?>" />
		<?php
	}
	/**
	 * Show a slug input box.
	 */
	public function merchantlist_slug_input() {
		$permalinks = get_option( 'bookingfor_permalinks' );
		?>
		<input name="bookingfor_merchantlist_slug" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['merchantlist_base'] ) ) echo esc_attr( $permalinks['merchantlist_base'] ); ?>" placeholder="<?php echo esc_attr_x('merchantlist', 'slug', 'bfi') ?>" />
		<?php
	}
	/**
	 * Show a slug input box.
	 */
	public function cartdetails_slug_input() {
		$permalinks = get_option( 'bookingfor_permalinks' );
		?>
		<input name="bookingfor_cartdetails_slug" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['cartdetails_base'] ) ) echo esc_attr( $permalinks['cartdetails_base'] ); ?>" placeholder="<?php echo esc_attr_x('cartdetails', 'slug', 'bfi') ?>" />
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function accommodation_details_slug_input() {
		$permalinks = get_option( 'bookingfor_permalinks' );
		?>
		<input name="bookingfor_accommodation_details_slug" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['accommodation_details_base'] ) ) echo esc_attr( $permalinks['accommodation_details_base'] ); ?>"  placeholder="<?php echo esc_attr_x('accommodation-details', 'slug', 'bfi') ?>"/> 
		<?php
	}
	/**
	 * Show a slug input box.
	 */
	public function onsell_details_slug_input() {
		$permalinks = get_option( 'bookingfor_permalinks' );
		?>
		<input name="bookingfor_onsell_details_slug" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['onsell_details_base'] ) ) echo esc_attr( $permalinks['onsell_details_base'] ); ?>"  placeholder="<?php echo esc_attr_x('onsell-details', 'slug', 'bfi') ?>"/> 
		<?php
	}

	/**
	 * Show the settings.
	 */
	public function settings() {
		echo wpautop( __( 'These settings control the permalinks used specifically for BookingFor.', 'bfi' ) );

		$permalinks        = get_option( 'bookingfor_permalinks' );
		$bookingfor_permalink = isset( $permalinks['bookingfor_base'] ) ? $permalinks['bookingfor_base'] : '';

		// Get Search Availability page
		$searchavailability_page_id   = bfi_get_page_id( 'searchavailability' );
		$base_slug      = urldecode( ( $searchavailability_page_id > 0 && get_post( $searchavailability_page_id ) ) ? get_page_uri( $searchavailability_page_id ) : _x( 'search-availability', 'default-slug', 'bfi' ) );
		$bookingfor_base   = _x( 'search-availability', 'default-slug', 'bfi' );

		$structures = array(
			0 => '',
			1 => '/' . trailingslashit( $base_slug ) //,
//			2 => '/' . trailingslashit( $base_slug ) . trailingslashit( '%product_cat%' )
		);
		?>
		<table class="form-table bfi-permalink-structure">
			<tbody>
				<tr>
					<th><label><input name="bookingfor_permalink" type="radio" value="<?php echo esc_attr( $structures[0] ); ?>" class="bfitog" <?php checked( $structures[0], $bookingfor_permalink ); ?> /> <?php _e( 'Default', 'bfi' ); ?></label></th>
					<td><code class="default-example"><?php echo esc_html( home_url() ); ?>/?product=sample-product</code> <code class="non-default-example"><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $bookingfor_base ); ?></code></td>
				</tr>
				<?php if ( $searchavailability_page_id ) : ?>
					<tr>
						<th><label><input name="bookingfor_permalink" type="radio" value="<?php echo esc_attr( $structures[1] ); ?>" class="bfitog" <?php checked( $structures[1], $bookingfor_permalink ); ?> /> <?php _e( 'Search Availability base', 'bfi' ); ?></label></th>
						<td><code><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $base_slug ); ?></code></td>
					</tr>
				<?php endif; ?>
				<tr>
					<th><label><input name="bookingfor_permalink" id="bookingfor_custom_selection" type="radio" value="custom" class="tog" <?php checked( in_array( $bookingfor_permalink, $structures ), false ); ?> />
						<?php _e( 'Custom Base', 'bfi' ); ?></label></th>
					<td>
						<input name="bookingfor_permalink_structure" id="bookingfor_permalink_structure" type="text" value="<?php echo esc_attr( $bookingfor_permalink ); ?>" class="regular-text code"> <span class="description"><?php _e( 'Enter a custom base to use. A base <strong>must</strong> be set or WordPress will use default instead.', 'bfi' ); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		<script type="text/javascript">
			jQuery( function() {
				jQuery('input.bfitog').change(function() {
					jQuery('#bookingfor_permalink_structure').val( jQuery( this ).val() );
				});
				jQuery('.permalink-structure input').change(function() {
					jQuery('.bfi-permalink-structure').find('code.non-default-example, code.default-example').hide();
					if ( jQuery(this).val() ) {
						jQuery('.bfi-permalink-structure code.non-default-example').show();
						jQuery('.bfi-permalink-structure input').removeAttr('disabled');
					} else {
						jQuery('.bfi-permalink-structure code.default-example').show();
						jQuery('.bfi-permalink-structure input:eq(0)').click();
						jQuery('.bfi-permalink-structure input').attr('disabled', 'disabled');
					}
				});
				jQuery('.permalink-structure input:checked').change();
				jQuery('#bookingfor_permalink_structure').focus( function(){
					jQuery('#bookingfor_custom_selection').click();
				} );
			} );
		</script>
		<?php
	}

	/**
	 * Save the settings.
	 */
	public function settings_save() {
		if ( ! is_admin() ) {
			return;
		}

		// We need to save the options ourselves; settings api does not trigger save for the permalinks page.
		if ( isset( $_POST['permalink_structure'] ) ) {
			$permalinks = get_option( 'bookingfor_permalinks' );

			if ( ! $permalinks ) {
				$permalinks = array();
			}

			$permalinks['search_availability_base']    = bfi_sanitize_permalink( trim( $_POST['bookingfor_search_availability_slug'] ) );
			$permalinks['merchantdetails_base']         = bfi_sanitize_permalink( trim( $_POST['bookingfor_merchantdetails_slug'] ) );
			$permalinks['accommodation_details_base']   = bfi_sanitize_permalink( trim( $_POST['bookingfor_accommodation_details_slug'] ) );
			$permalinks['merchantlist_base']   = bfi_sanitize_permalink( trim( $_POST['bookingfor_merchantlist_slug'] ) );
			$permalinks['cartdetails_base']   = bfi_sanitize_permalink( trim( $_POST['bookingfor_cartdetails_slug'] ) );
			$permalinks['onsell_details_base']   = bfi_sanitize_permalink( trim( $_POST['bookingfor_onsell_details_slug'] ) );
			
			// BookingFor base.
			$bookingfor_permalink = isset( $_POST['bookingfor_permalink'] ) ? bfi_clean( $_POST['bookingfor_permalink'] ) : '';

			if ( 'custom' === $bookingfor_permalink ) {
				if ( isset( $_POST['bookingfor_permalink_structure'] ) ) {
					$bookingfor_permalink = preg_replace( '#/+#', '/', '/' . str_replace( '#', '', trim( $_POST['bookingfor_permalink_structure'] ) ) );
				} else {
					$bookingfor_permalink = '/';
				}

				// This is an invalid base structure and breaks pages.
//				if ( '%product_cat%' == $bookingfor_permalink ) {
//					$bookingfor_permalink = '/' . _x( 'product', 'slug', 'bfi' ) . '/' . $bookingfor_permalink;
//				}
			} elseif ( empty( $bookingfor_permalink ) ) {
				$bookingfor_permalink = false;
			}

			$permalinks['bookingfor_base'] = bfi_sanitize_permalink( $bookingfor_permalink );

			// Search Availability base may require verbose page rules if nesting pages.
			$searchavailability_page_id   = bfi_get_page_id( 'searchavailability' );
			$searchavailability_permalink = ( $searchavailability_page_id > 0 && get_post( $searchavailability_page_id ) ) ? get_page_uri( $searchavailability_page_id ) : _x( 'searchavailability', 'default-slug', 'bfi' );

			if ( $searchavailability_page_id && trim( $permalinks['bookingfor_base'], '/' ) === $searchavailability_permalink ) {
				$permalinks['use_verbose_page_rules'] = true;
			}

			update_option( 'bookingfor_permalinks', $permalinks );
		}
	}
}

endif;

return new BFI_Admin_Permalink_Settings();