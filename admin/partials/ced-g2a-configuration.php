<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://cedcommerce.com
 * @since      1.0.0
 *
 * @package    Woocommerce_GunBroker_Importer
 * @subpackage Woocommerce_GunBroker_Importer/admin/partials
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/* Save G2A Credentails */
if( isset( $_POST['ced_g2a_save_configuration_details'] ) )
{
	$ced_g2a_g2a_details = isset( $_POST['ced_g2a_config_details'] ) ? $_POST['ced_g2a_config_details'] : array();
	if( is_array( $ced_g2a_g2a_details ) && !empty( $ced_g2a_g2a_details ) )
	{
		update_option( 'ced_g2a_config_details', $ced_g2a_g2a_details );

		$fileName = CED_G2A_PATH."admin/lib/ced-g2a-authorize.php";
		if( file_exists($fileName) )
		{
			require_once $fileName;
			$Ced_G2A_Authorize = new Ced_G2A_Authorize;
			$Ced_G2A_Authorize_instance = $Ced_G2A_Authorize->get_instance();

			$response = $Ced_G2A_Authorize_instance->authorize_account($ced_g2a_g2a_details);
			// print_r($response);
			if( /*isset( $response['accessToken'] ) && $response['accessToken'] != "" */true){
				// update_option( 'ced_g2a_access_token', $response['accessToken'] );
				$_SESSION['ced-g2a-saved-settings'] = __("Account Validated Successfully",'woocommerce-g2a-importer');
			}
			else
			{
				$_SESSION['ced-g2a-saved-settings'] = __("Unable to authorize the account. Please check the details.",'woocommerce-g2a-importer');
			}
		}

	}
}

$ced_g2a_g2a_details = get_option( 'ced_g2a_config_details', array() );

if( isset( $_SESSION['ced-g2a-saved-settings'] ) )
{
	?>
	<div id="message" class="updated notice notice-success is-dismissible">
		<p><?php echo $_SESSION['ced-g2a-saved-settings']; ?></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'siroop-woocommerce-order-automation' ); ?></span>
		</button>
	</div>
	<?php
	unset( $_SESSION['ced-g2a-saved-settings'] );
}
?>

<div class="ced-g2a-content-wrapper">
	
	<div class="ced-g2a-heading-wrapper ced-g2a-configuration-heading-wrapper">
		<h2><?php _e( 'G2A Configuration', 'woocommerce-g2a-importer' ); ?></h2>
	</div>
	<div class="ced-g2a-settings-wrapper ced-g2a-configuration-settings-wrapper">
		<div class="ced-g2a-configuration-settings-table-wrapper">
			<form method="post">
				<table class="ced-g2a-configuration-settings-table">
					<tbody>
						<tr>
							<td>
								<label><?php _e( 'Select Mode of Operation', 'woocommerce-g2a-importer' ); ?></label>
							</td>
							<td>
								<?php 
								$sandbox = "";
								$production = "";
								if( isset( $ced_g2a_g2a_details['ced_g2a_mode'] ) && !empty( $ced_g2a_g2a_details['ced_g2a_mode'] ) )
								{
									if( $ced_g2a_g2a_details['ced_g2a_mode'] == "sandbox" )
									{
										$sandbox = "selected";
									}
									else
									{
										$production = "selected";
									}
								}
								?>
								<select class="ced-g2a-select" name="ced_g2a_config_details[ced_g2a_mode]">
									<option value=""><?php _e( '--Select--', 'woocommerce-g2a-importer' ); ?></option>
									<option value="sandbox" <?php echo $sandbox; ?>><?php _e( 'Sandbox Mode', 'woocommerce-g2a-importer' ); ?></option>
									<option value="production" <?php echo $production; ?>><?php _e( 'Production Mode', 'woocommerce-g2a-importer' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label><?php _e( 'G2A Hash Key', 'woocommerce-g2a-importer' ); ?></label>
							</td>
							<td>
								<input type="text" value="<?php echo isset( $ced_g2a_g2a_details['ced_g2a_hashKey'] ) ? $ced_g2a_g2a_details['ced_g2a_hashKey'] : ''; ?>" class="ced-g2a-text-field ced_g2a_hashKey" name="ced_g2a_config_details[ced_g2a_hashKey]" placeholder="<?php _e( 'Enter G2A Hash Key', 'woocommerce-g2a-importer' ); ?>"></input>
							</td>
						</tr>
						<tr>
							<td>
								<label><?php _e( 'G2A Secret Key', 'woocommerce-g2a-importer' ); ?></label>
							</td>
							<td>
								<input type="text" value="<?php echo isset( $ced_g2a_g2a_details['ced_g2a_secretKey'] ) ? $ced_g2a_g2a_details['ced_g2a_secretKey'] : ''; ?>" class="ced-g2a-text-field ced-g2a-secretKey" name="ced_g2a_config_details[ced_g2a_secretKey]" placeholder="<?php _e( 'Enter G2A Secret Key', 'woocommerce-g2a-importer' ); ?>"></input>
							</td>
						</tr>
						<tr>
							<td>
								<label><?php _e( 'G2A Seller Email', 'woocommerce-g2a-importer' ); ?></label>
							</td>
							<td>
								<input type="text" value="<?php echo isset( $ced_g2a_g2a_details['ced_g2a_sellerEmail'] ) ? $ced_g2a_g2a_details['ced_g2a_sellerEmail'] : ''; ?>" class="ced-g2a-text-field ced-g2a-sellerEmail" name="ced_g2a_config_details[ced_g2a_sellerEmail]" placeholder="<?php _e( 'Enter G2A Seller Email', 'woocommerce-g2a-importer' ); ?>"></input>
							</td>
						</tr>
						<tr>
                            <td>
                                <label><?php _e( 'Cron Path for Product Sync', 'woocommerce-g2a-importer' ); ?></label>
                            </td>
                            <td>
                                <input type="text" class="ced-g2a-text-field" readonly value="<?php echo CED_G2A_PATH."includes/ced-g2a-sync-cron.php"; ?>"></input>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label><?php _e( 'Cron Path for Auto Import', 'woocommerce-g2a-importer' ); ?></label>
                            </td>
                            <td>
                                <input type="text" class="ced-g2a-text-field" readonly value="<?php echo CED_G2A_PATH."includes/ced-g2a-auto-import-cron.php"; ?>"></input>
                            </td>
                        </tr>
						<tr class="ced-g2a-save-config-row">
							<td></td>
							<td>
								<input type="submit" name="ced_g2a_save_configuration_details" class="ced-g2a-save-configuration-button ced-g2a-button" value="<?php _e( 'Save Details', 'woocommerce-g2a-importer' ); ?>"></input>
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
	</div>

</div>