<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.edisonave.com
 * @since      1.0.0
 *
 * @package    Wppromexp
 * @subpackage Wppromexp/admin/partials
 */
?>

<div class="wrap wppromexp-panel clr">
	<h1>WP Prometheus Exporter</h1>
	<p>Wordpress Exporter suitable for scrapping statistics into Prometheus.</p>
	
	<div class="left clr">

			   <form method="post" action="<?php $this->admin_link("wppromexp_settings_process", 'noheader=true'); ?>" >

				<div>

						<p class="description">
							Set these fields to enable the WP Prometheus Exporter
						<table class="form-table">
							<tbody>
								
								<tr>
									<th scope="accesskey">
										<label for="accesskey">Access Key</label>
									</th>
									<td>
										<input name="accesskey" type="text" id="accesskey" value="<?php echo $access_key; ?>" class="regular-text">
										<br />Key used to access the exporter. 
									</td>
								</tr>
								
							</tbody>
						</table>

						
						<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"  /></p>
					</div>
				</form>
				
				<h3>Promethueus Settings</h3>
				Your URL is: <?php echo home_url(); ?>/wp-json/metrics

			</div>


</div>
