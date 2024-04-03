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
if( isset( $_POST['ced_g2a_save_settings'] ) )
{
	update_option("ced_g2a_chunk_product",array());
	$ced_g2a_g2a_details = isset( $_POST['ced_g2a_settings'] ) ? $_POST['ced_g2a_settings'] : array();
	if( is_array( $ced_g2a_g2a_details ) && !empty( $ced_g2a_g2a_details ) )
	{
		update_option( 'ced_g2a_settings', $ced_g2a_g2a_details );
	}
	if(isset($ced_g2a_g2a_details['ced_g2a_import_frequency']) && !empty($ced_g2a_g2a_details['ced_g2a_import_frequency']))
	{
		wp_clear_scheduled_hook('ced_g2a_auto_import_cron_job');
		wp_schedule_event(time(), $ced_g2a_g2a_details['ced_g2a_import_frequency'], 'ced_g2a_auto_import_cron_job');
	}
	else
	{
		wp_clear_scheduled_hook('ced_g2a_auto_import_cron_job');
	}
}

$ced_g2a_g2a_details = get_option( 'ced_g2a_settings', array() );

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
		<h2><?php _e( 'General Settings', 'woocommerce-g2a-importer' ); ?></h2>
	</div>
	<div class="ced-g2a-settings-wrapper ced-g2a-configuration-settings-wrapper">
		<div class="ced-g2a-configuration-settings-table-wrapper">
			<form method="post">
				<table class="ced-g2a-configuration-settings-table">
					<tbody>
						<tr>
							<td>
								<label><?php _e( 'Select Product State to be imported', 'woocommerce-g2a-importer' ); ?></label>
							</td>
							<td>
								<?php 
								$draft = "";
								$publish = "";
								if( isset( $ced_g2a_g2a_details['ced_g2a_product_state'] ) && !empty( $ced_g2a_g2a_details['ced_g2a_product_state'] ) )
								{
									if( $ced_g2a_g2a_details['ced_g2a_product_state'] == "draft" )
									{
										$draft = "selected";
									}
									else
									{
										$publish = "selected";
									}
								}
								?>
								<select class="ced-g2a-select" name="ced_g2a_settings[ced_g2a_product_state]">
									<option value=""><?php _e( '--Select--', 'woocommerce-g2a-importer' ); ?></option>
									<option value="draft" <?php echo $draft; ?>><?php _e( 'Draft Mode', 'woocommerce-g2a-importer' ); ?></option>
									<option value="publish" <?php echo $publish; ?>><?php _e( 'Publish Mode', 'woocommerce-g2a-importer' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label><?php _e( 'Price Markup Type', 'woocommerce-g2a-importer' ); ?></label>
							</td>
							<td>
								<?php 
								$flat = "";
								$percent = "";
								if( isset( $ced_g2a_g2a_details['ced_g2a_price_markup_type'] ) && !empty( $ced_g2a_g2a_details['ced_g2a_price_markup_type'] ) )
								{
									if( $ced_g2a_g2a_details['ced_g2a_price_markup_type'] == "flat" )
									{
										$flat = "selected";
									}
									else
									{
										$percent = "selected";
									}
								}
								?>
								<select class="ced-g2a-select" name="ced_g2a_settings[ced_g2a_price_markup_type]">
									<option value=""><?php _e( '--Select--', 'woocommerce-g2a-importer' ); ?></option>
									<option value="flat" <?php echo $flat; ?>><?php _e( 'Flat', 'woocommerce-g2a-importer' ); ?></option>
									<option value="percent" <?php echo $percent; ?>><?php _e( 'Percentage', 'woocommerce-g2a-importer' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
                            <td>
                                <label><?php _e( 'Price Markup Value', 'woocommerce-g2a-importer' ); ?></label>
                            </td>
                            <td>
                            	<?php 
                            	$markup_value = isset( $ced_g2a_g2a_details['ced_g2a_price_markup_value'] ) ? $ced_g2a_g2a_details['ced_g2a_price_markup_value'] : "";
                            	?>
                                <input type="text" name="ced_g2a_settings[ced_g2a_price_markup_value]" value="<?php echo $markup_value; ?>"></input>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <label><?php _e( 'Auto Import Frequency', 'woocommerce-g2a-importer' ); ?></label>
                            </td>
                            <td>
                            	<select name="ced_g2a_settings[ced_g2a_import_frequency]" class="g2a-sync-scheduling">
								<option value="0" <?php if (isset($ced_g2a_g2a_details['ced_g2a_import_frequency']) && $ced_g2a_g2a_details['ced_g2a_import_frequency'] == "0")echo "selected"; ?>><?php _e('Disabled','ced-umb-g2a')?></option>
								<option value="daily" <?php if (isset($ced_g2a_g2a_details['ced_g2a_import_frequency']) && $ced_g2a_g2a_details['ced_g2a_import_frequency'] == "daily")echo "selected";?>><?php _e('Daily','ced-umb-g2a')?></option>
								<option value="twicedaily" <?php if (isset($ced_g2a_g2a_details['ced_g2a_import_frequency']) && $ced_g2a_g2a_details['ced_g2a_import_frequency'] == "twicedaily")echo "selected";?>><?php _e('Twice Daily','ced-umb-g2a')?></option>
								<option value="ced_g2a_6min" <?php if (isset($ced_g2a_g2a_details['ced_g2a_import_frequency']) && $ced_g2a_g2a_details['ced_g2a_import_frequency'] == "ced_g2a_6min")echo "selected"; ?>><?php _e('Every 6 Minutes','ced-umb-g2a')?></option>
								<option value="ced_g2a_10min" <?php if (isset($ced_g2a_g2a_details['ced_g2a_import_frequency']) && $ced_g2a_g2a_details['ced_g2a_import_frequency'] == "ced_g2a_10min")echo "selected"; ?>><?php _e('Every 10 Minutes','ced-umb-g2a')?></option>
								<option value="ced_g2a_15min" <?php if (isset($ced_g2a_g2a_details['ced_g2a_import_frequency']) && $ced_g2a_g2a_details['ced_g2a_import_frequency'] == "ced_g2a_15min")echo "selected"; ?>><?php _e('Every 15 Minutes','ced-umb-g2a')?></option>
								<option value="ced_g2a_30min" <?php if (isset($ced_g2a_g2a_details['ced_g2a_import_frequency']) && $ced_g2a_g2a_details['ced_g2a_import_frequency'] == "ced_g2a_30min")echo "selected"; ?>><?php _e('Every 30 Minutes','ced-umb-g2a')?></option>
								
							</select>
                            </td>
                        </tr>
                        <tr>
							<td>
								<label><?php _e( 'Enable Auto Import', 'woocommerce-g2a-importer' ); ?></label>
							</td>
							<td>
								<?php 
								$yes = "";
								$no = "";
								if( isset( $ced_g2a_g2a_details['ced_g2a_enable_auto_import'] ) && !empty( $ced_g2a_g2a_details['ced_g2a_enable_auto_import'] ) )
								{
									if( $ced_g2a_g2a_details['ced_g2a_enable_auto_import'] == "yes" )
											$yes = "selected";
									
									if( $ced_g2a_g2a_details['ced_g2a_enable_auto_import'] == "no" )
										$no = "selected";
								}
								?>
								<select class="ced-g2a-select" name="ced_g2a_settings[ced_g2a_enable_auto_import]">
									<option value=""><?php _e( '--Select--', 'woocommerce-g2a-importer' ); ?></option>
									<option value="yes" <?php echo $yes; ?>><?php _e( 'Yes', 'woocommerce-g2a-importer' ); ?></option>
									<option value="no" <?php echo $no; ?>><?php _e( 'No', 'woocommerce-g2a-importer' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label><?php _e( 'Enable Price Sync', 'woocommerce-g2a-importer' ); ?></label>
							</td>
							<td>
								<?php 
								$yes = "";
								$no = "";
								if( isset( $ced_g2a_g2a_details['ced_g2a_enable_price_sync'] ) && !empty( $ced_g2a_g2a_details['ced_g2a_enable_price_sync'] ) )
								{
									if( $ced_g2a_g2a_details['ced_g2a_enable_price_sync'] == "yes" )
											$yes = "selected";
									
									if( $ced_g2a_g2a_details['ced_g2a_enable_price_sync'] == "no" )
										$no = "selected";
									
								}
								?>
								<select class="ced-g2a-select" name="ced_g2a_settings[ced_g2a_enable_price_sync]">
									<option value=""><?php _e( '--Select--', 'woocommerce-g2a-importer' ); ?></option>
									<option value="yes" <?php echo $yes; ?>><?php _e( 'Yes', 'woocommerce-g2a-importer' ); ?></option>
									<option value="no" <?php echo $no; ?>><?php _e( 'No', 'woocommerce-g2a-importer' ); ?></option>
								</select>
							</td>
						</tr>

						<tr class="ced-g2a-save-config-row">
							<td></td>
							<td>
								<input type="submit" name="ced_g2a_save_settings" class="ced-g2a-save-configuration-button ced-g2a-button" value="<?php _e( 'Save Details', 'woocommerce-g2a-importer' ); ?>"></input>
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
	</div>

</div>