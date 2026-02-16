<?php
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
}
if($_REQUEST['page'] == 'lb-campaign'){
?>

<form name="campaign" method='post'>
	<br>
	<table style='width:100%'>
		<tr>
			<td style='width:20%'>
			<td>
			<td>
				<label id="inneroptions" style="font-weight:bold;">Add Campaign</label>
			</td>
			<td></td>
		</tr>
		<tr>
			<td><br></td>
		</tr>
	</table>
	<table style='width:100%;margin-left:5%;'>
		<input type='hidden' id='plug_url' value="<?php echo SM_LB_URL ;?>">
		<tr>
			<td style='width:20%;'>
				<label id="innertext" style="font-weight:bold;">Campaign Name</label></td>
			<td><input type="text" name="campaign_name" id='campaign_name' value="" placeholder='Campaign Name*'></td>
		</tr>

		<tr>
			<td> <br> </td>
		</tr>

		<tr>
			<td>
				<label id="innertext" style="font-weight:bold;"> UTM Source</label><br></td>
			<td><input type="text" name="utm_source" id='utm_source' value="" placeholder='UTM Source*'></td>
		</tr>

		<tr>
			<td> <br> </td>
		</tr>

		<tr>
			<td>
				<label id="innertext" style="font-weight:bold;">Medium </label>
			</td>
			<td>
				<input type="text" name="camp_medium" id='camp_medium' value="" placeholder='Medium*'>
			</td>
		</tr>

		<tr>
			<td> <br> </td>
		</tr>

		<tr>
			<td>
				<label id="innertext" style="font-weight:bold;">UTM Name </label>
			</td>
			<td>
				<input type="text" name="utm_name" id='utm_name' value="" placeholder='UTM Name*'>
			</td>
		</tr>
		<tr>
			<td> <br> </td>
		</tr>

		<tr>
			<td></td>
			<td style='padding-left:11%'>
				<input class='btn btn-primary' type="button" value="Next >>" id='camp_next' name='camp_next'
					onclick='save_campaign();' />
			</td>
		</tr>
	</table>
</form>

<?php
}else { ?>
<form name="campaign" method='post'>
	<br>
	<table style='width:100%'>
		<tr>
			<td style='width:20%'>
			<td>
			<td>
				<label id="inneroptions" style="font-weight:bold;">Mailing source</label>
			</td>
			<td></td>
		</tr>
		<tr>
			<td><br></td>
		</tr>
	</table>
	<table style='width:100%;margin-left:5%;'>
		<input type='hidden' id='plug_url' value="<?php echo SM_LB_URL ;?>">
		<tr>
			<td style='width:20%;'>
				<label id="innertext" style="font-weight:bold;">Choose your source</label></td>
			<td><select name='mail_source' id='mail_source' style='width:170px;'>
					<option name='select' value='--Select--'>--Select--</option>
					<option name='mailchimp' value='MailChimp'>MailChimp</option>
				</select>
			</td>
		</tr>

		<tr>
			<td> <br> </td>
		</tr>
		<?php 
		$mc_apikey = get_option('mc_apikey');
		if(!empty($mc_apikey)){
			echo "<input type='hidden' id='check_apikey_available' value='yes'>";
			$get_mc_campaign_list = get_option('mc_campaign_list');
			if(!empty($get_mc_campaign_list)){
			?>
		<tr id='camp_list'>
			<td style='width:20%;'><label id="innertext" style="font-weight:bold;">Choose Campaign Name</label></td>
			<td>
				<select id='campaign_list' style='width:170px;' onchange="get_campaign_id();">
					<option value='select'> --Select-- </option>
					<?php
				foreach($get_mc_campaign_list as $mc_key => $mc_val )
				{
					echo "<option value='$mc_key'>$mc_val</option>";
				}
			?>
				</select>
			</td>
		</tr>
		<?php
			}
		}
	else{
	?>
		<input type='hidden' id='check_apikey_available' value='no'>;
		<tr id='camp_list' style='display:none'>
			<td style='width:20%;'><label id="innertext" style="font-weight:bold;">Choose Campaign Name</label></td>
			<td>
				<select id='campaign_list' style='width:170px;' onchange="get_campaign_id();">
					<option value='select'> --Select-- </option>
				</select>

			</td>
		</tr>
		<?php
	}
	?>
		<tr>
			<td> <br> </td>
		</tr>
		<tr>
			<td></td>
			<td style='padding-left:9%'>
				<input class='btn btn-primary' type="button" value="Finish" id='mapping_end' name='finish'
					onclick='finish_campaign();' style='display:none;' />
			</td>
		</tr>
	</table>
</form>
<?php	
}
?>

<!-- Modal Box -->

<head>
	<meta charset="utf-8">
</head>

<div class="container" style="width:100%;">
	<!-- Trigger the modal with a button -->

	<!-- Modal -->
	<div class="modal fade" id="mapping-modalbox" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" id="close_map_modal" data-dismiss="modal"
						onclick="">&times;</button>
					<h4 class="modal-title" style="text-align:center;color:green;">MailChimp Credentials</h4>
				</div>
				<div class="modal-bodyy" style='position:relative;padding:20px'>
					<div style='padding-left:60px;' id="clear_contents">
						<p id='show_form_list'>

							<table style='width:100%;margin-left:5%;'>
								<tr>
									<td style='width:30%;'>
										<label id="innertext" style="font-weight:bold;">MailChimp API Key</label></td>
									<td><input type="text" name="MC_apikey" id='MC_apikey' value=""
											placeholder='API Key*'></td>
								</tr>
							</table>

						</p>
					</div>

					<p style='padding-left:60px;' id="display_form_lists">
					</p>
					<p style='padding-left:60px;' id="mapping_options">
					</p>

					<form name='mapping_fields' id='mapping_fields' onsubmit="return false;">
						<div id="CRM_field_mapping" style="margin-top:50px;padding-left:60px;">


						</div>
						<div class="modal-footer">
							<input type="button" class="btn btn-primary" name="map_crm_fields" value="Save"
								id="save_apikey" onclick="save_mc_apikey();">
							<button type="button" id="close" class="btn btn-primary" data-dismiss="modal"
								onclick="">Close</button>
						</div>
					</form>
				</div>

			</div>

			<!-- Modal Box End -->


			<script>
				jQuery(document).ready(function () {
					jQuery(".mapping-modalbox").hide();
					jQuery("#mail_source").change(function () {
						var mail_source = jQuery("#mail_source").val();
						if (mail_source == '--Select--') {
							jQuery.alert(
								'<h5><b><center style="color:red"> Please choose MailChimp</center></b></h5>');
							return false;
						}
						var check_apikey_avail = jQuery("#check_apikey_available").val();
						if (check_apikey_avail == 'no') {
							jQuery("#mapping-modalbox").modal();
							jQuery("#clear_contents").show();
						} else {
							jQuery.alert(
								'<h5><b><center style="color:green"> API Setting already configured</center></b></h5>'
								);
							return false;
						}
					});
				});
			</script>