<?php
/**
 * Admin View: Settings
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$curlEnabled = true;
$result = null;
$resultOk = false;
$msg="";

if (!function_exists('curl_init') || !function_exists('curl_exec')) {
	$curlEnabled = false;
}
if($curlEnabled){
	$wsHelper = new wsQueryHelper(COM_BOOKINGFORCONNECTOR_WSURL, COM_BOOKINGFORCONNECTOR_API_KEY);

	$checkUrl = $wsHelper->url_exists();

	if($checkUrl){
		$options = array(
				'path' => '/Checkstatus',
				'data' => array(
						'$format' => 'json'
				)
		);
		$url = $wsHelper->getQuery($options);

		//$r = $this->helper->executeQuery($url);
		$r = $wsHelper->executeQuery($url);
		
		if (isset($r)) {
			$res = json_decode($r);

	//echo "<pre>";
	//echo print_r($res);
	//echo "</pre>";
	//
			if (!empty($res->d->results)){
				$result = $res->d->results;
			}elseif(!empty($res->d)){
				$result = $res->d;
			}elseif(!empty($res)){
				$result = $res;
			}
		}

		if(!empty($result)){
			if (!empty($result->error) ){
				if (!empty($result->error->message) ){
					if (!empty($result->error->message->value) ){
						$msg=$result->error->message->value;
					}
				}
			}else{
				if (!empty($result->IsActive) ){
					$resultOk = true;
				}else{
					$msg=" Utente non attivo";
				}
				if(!empty($result->ValidationStart)){

					$validationStart = DateTime::createFromFormat('d/m/Y', BFCHelper::parseJsonDate($result->ValidationStart),new DateTimeZone('UTC'));
					if($validationStart> new DateTime('UTC')){
						$resultOk = false;
						$msg = $msg . " - data inizio validità: " . $validationStart;
					}
				}
				if(!empty($result->ValidationEnd)){

					$validationEnd = DateTime::createFromFormat('d/m/Y', BFCHelper::parseJsonDate($result->ValidationEnd),new DateTimeZone('UTC'));
					
					if($validationEnd< new DateTime('UTC')){
						$resultOk = false;
						$msg = $msg . " - data fine validità: " . $validationEnd;
					}
				}
			}
			// check date validità
			/*
			if ($result->ValidationStart=(null) ){
				$resultOk = true;
			}
			*/

		}
	}
}
?>
<div class="wrap bookingfor">
	<h1>BookingFor Panel</h1>
	<p>
	<table class="bfi-table bfi-table-striped bfi-table-bordered table-condensed">
		<tbody>
		<tr>
			<td>Version</td>
			<td>
				<?php echo $GLOBALS['bookingfor']->version; ?>
			</td>
		</tr>
		<tr>
			<td>Subscription Code</td>
			<td>
				<?php echo COM_BOOKINGFORCONNECTOR_SUBSCRIPTION_KEY ?>
				<?php
					if(empty(COM_BOOKINGFORCONNECTOR_SUBSCRIPTION_KEY)){
					echo '<div class="error" style="margin:10px 0 0">ERROR! no Url\'s service insert. Please go to Setting and enter a correct Url\'s service</div>';
					}
				?>
			</td>
		</tr>
		<tr>
			<td>WS online</td>
			<td><span class="bfi-badgeadmin" style="<?php echo ($checkUrl)? "background-color: #398439;": " background-color: #d43f3a; " ?>">&nbsp;</span></td>
		</tr>
		<tr>
			<td>WS Connettivity </td>
			<td> 
		<?php
			if(empty($wsHelper->errmsg)){
		?>
				<span class="bfi-badgeadmin" style="background-color: #398439;"><?php echo $wsHelper->infomsg ?>&nbsp;</span>
		<?php
		}else{
		?>
				<span class="bfi-badgeadmin" style="background-color: #d43f3a;"><?php echo $wsHelper->errmsg ?></span>
		<?php

			$curlversion=curl_version();
			if (version_compare($curlversion["version"], '7.29', 'le'))
			{
				echo "<br />Curl Version Out-of-Date (min request: 7.30 attual version " . $curlversion["version"] . ") ";
			} else {
				echo "<br />Curl Version OK ";
			}

			if(OPENSSL_VERSION_NUMBER < 0x10001000) {
				echo "<br />OpenSSL Version Out-of-Date";
			} else {
				echo "<br />OpenSSL Version OK ";
			}
		}
		?>
			</td>
		</tr>
		<tr>
			<td>Account</td>
			<td><span class="bfi-badgeadmin" style="<?php echo (!empty($resultOk)? "background-color: #398439;" : "background-color: #d43f3a;") ?>">&nbsp;</span> <?php echo $msg ?></td>
		</tr>
		<tr>
			<td>PHP version</td>
			<td><?php echo PHP_VERSION ?>
				<?php
					if (version_compare(PHP_VERSION, '5.5.0', '<')) {

						echo '<span class="badge" style="background-color: #d43f3a;">Min Version 5.5 </span>';
					}
				?>
			
			</td>
		</tr>
		<tr>
			<td>Encrypts data</td>
			<td><?php 
					if (version_compare(COM_BOOKINGFORCONNECTOR_CRYPTOVERSION, '1', '<')) {

						echo '<span class="badge" style="background-color: #d43f3a;">No Cryptography Extensions find </span>';
					}
					if (version_compare(PHP_VERSION, '7.0.0', '>') && version_compare(COM_BOOKINGFORCONNECTOR_CRYPTOVERSION, '2', '<')) {

						echo '<span class="badge" style="background-color: #d43f3a;">No Cryptography Extensions find for this PHP version '.PHP_VERSION.'  </span>';
					}
					if (COM_BOOKINGFORCONNECTOR_CRYPTOVERSION == 1) {

						echo 'Cryptography Extensions find for this PHP version '.PHP_VERSION.': Mcrypt ';
					}
					if (COM_BOOKINGFORCONNECTOR_CRYPTOVERSION == 2) {

						echo 'Cryptography Extensions find for this PHP version '.PHP_VERSION.': OpenSSL ';
					}
			?>
			</td>
		</tr>
		</tbody>
	</table>
<?php 
//if exist dir allow to clear 
			if (file_exists (COM_BOOKINGFORCONNECTOR_CACHEDIR) && !BFI_Admin::is_dir_empty(COM_BOOKINGFORCONNECTOR_CACHEDIR)) {
			
			echo "<h3>" . __( "Delete Cached Data", 'bfi' ) . "</h3>";
			echo "<p>" . __( "Cached data are stored on your server in files. If you need to delete them, use the button below.", 'bfi' ) . "</p>";
			echo '<form name="wp_cache_content_delete" action="?page=bfi-settings" method="post">';
			echo '<input type="hidden" name="bfi_delete_cache" />';
			echo '<div class="submit"><input id="deletecachedata" class="button-secondary" type="submit" value="' . __( 'Delete Cache', 'bfi' ) . ' " /></div>';
			wp_nonce_field('bfi-cache');
			echo "</form>\n";
			}

?>

	</p>
	

	<form method="post" action="options.php">
		<?php
			settings_fields("section");
			do_settings_sections("bfi-options");      
		?>          
		<p class="submit">
			<?php if ( empty( $GLOBALS['hide_save_button'] ) ) { ?>
				<input name="save" class="button-primary bookingfor-save-button" type="submit" value="<?php esc_attr_e( 'Save changes', 'bfi' ); ?>" />
			<?php } ?>
			<?php // wp_nonce_field( 'bookingfor-settings' ); ?>
		</p>
	</form>
</div>