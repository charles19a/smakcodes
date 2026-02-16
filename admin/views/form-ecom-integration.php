<?php
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

global $wpdb;
require_once( SM_LB_PRO_DIR."includes/lb-ecominteg.php" );

	$active_plugins = get_option( "active_plugins" );
	if( !in_array("woocommerce/woocommerce.php" , $active_plugins)){
		echo "<div style='margin-left:30%;margin-top:5%;'><a href='https://wordpress.org/plugins/woocommerce/' target='_blank' class='alert alert-danger'> You Should Install Woocommerce First</a> </div>";
		return false;
	}

	$activated_crm = get_option('WpLeadBuilderProActivatedPlugin');
	$siteurl = site_url();
	$siteurl = esc_url( $siteurl );
	$config = get_option("wp_{$activated_crm}_settings");
	$ecom_module = get_option( 'ecom_wc_module' );
	$convert_lead = get_option( 'ecom_wc_convert_lead' );
	$OverallFunctionsPROObj = new OverallFunctionsPRO();
	$result = $OverallFunctionsPROObj->CheckFetchedDetails();

	if( empty($convert_lead) )
	{
		update_option( 'ecom_wc_convert_lead' , 'on' );
		$convert_lead = 'on';
	}
?>
<div class='clearfix'></div>
<div class='mt30'>
		<div class="panel" style="width:auto; margin-right: 20px;">
		<div class='panel-body'>
			<input type='hidden' id='ecom_module_value' value="<?php echo $ecom_module?>">
			<div id='ecommerce_integration'>
				<!-- Start -->
				<form id='ecom_integration' method="post" action="">
					<input type="hidden" id="plug_URL" value="<?php echo esc_url(SM_LB_PRO_DIR);?>" />
					<div class="wp-common-crm-content" style="width: 1000px;float: left;height:auto;">
						<!-- Common -->


						<div class="mb30 mt20">
							<label id="inneroptions" class='leads-builder-heading'>
								<?php echo esc_html__('You are now using WooCommerce store*' , "wp-leads-builder-any-crm-pro" ); ?>
							</label>
						</div>

						<div class="form-group col-md-12">
							<div class="col-md-6">
								<label id="inneroptions" class='leads-builder-label'>
									<?php echo esc_html__('Capture customer information as' , "wp-leads-builder-any-crm-pro" ); ?>
								</label>
							</div>
							<div class="col-md-2">
								<select class='form_control_select' data-live-search='false' id='ecom_module'
									onchange="change_ecom_configuration(this.id)">

									<option id='ecom_module_notenabled' value='Not Enabled' <?php if(  $ecom_module == 'Not Enabled' )
									{
										echo "selected";
									}
									?>> Not Enabled </option>
									<option id='ecom_module_leads' value='Leads' <?php
										if(  $ecom_module == 'Leads' ){
											echo "selected";  
										}
										elseif( empty($result['leadsynced'] )){
											echo "disabled";  
										}
										?>> Leads 
									</option>
									<option id='ecom_module_contacts' value='Contacts' <?php
										if(  $ecom_module == 'Contacts' ){
											echo "selected";
										}
										elseif( empty($result['contactsynced'] )){
											echo "disabled";  
										}
										?>> Contacts 
									</option>
								</select>
							</div>
						</div>

						<div class="form-group col-md-12">

							<div id='hide_convert'>
								<div class="col-md-6">
									<label
										class='leads-builder-label'><?php echo esc_html__('Convert Leads into Contacts automatically  on successful order' , "wp-leads-builder-any-crm-pro" ); ?></label>
								</div>
								<div class="col-md-2">
									<div class="switch">
										<!-- tfa button -->
										<input id="ecom_convert_lead" type='checkbox'
											class="tgl tgl-skewed noicheck smack-vtiger-settings-text"
											name='ecom_convert_lead'
											<?php if(isset($convert_lead) && $convert_lead == 'on') { ?> value='on'
											<?php echo "checked=checked"; } else { ?> value='off' <?php } ?>
											onclick="convert_lead(this.value)" />
										<label id="innertext" data-tg-off="OFF" data-tg-on="ON" for="ecom_convert_lead"
											class="tgl-btn TFA_check" style="font-size: 16px;">
										</label>
										<!-- tfa btn End -->
									</div>
								</div> <!--  CONVERT -->

							</div>
						</div>
						<div>
							<div id='choose_owner' class="form-group col-md-12">
								<div class="col-md-6">
									<label id="inneroptions" class='module_owner leads-builder-label'> </label>
								</div>
								<div class="col-md-2" id='change_ecom_owner'>
									<?php 
									$activated_crm = get_option('WpLeadBuilderProActivatedPlugin');
									$ecom_option_name = "ecom_wc_{$activated_crm}_{$ecom_module}_config";
									require_once( SM_LB_PRO_DIR."includes/lb-ecominteg.php" );
									$get_assignedto = new EcommerceSettingsActions();
									echo $get_assignedto->get_ecom_assignedto($ecom_option_name);
									?>
								</div>
							</div>
							<input type='hidden' value='<?php echo $activated_crm; ?>' id='ecom_active_crm'>
						</div>

						<div id='load_ecom_fields'>
							<?php 
							if( $ecom_module != 'Not Enabled' ){
							?>

							<div id="ecommerce-mapping" class="ecommerce-mapping">
								<div class="woo_store">
									<label id="inneroptions"
										class='leads-builder-heading'><h4><?php echo esc_html__('Configure Field Mapping' , "wp-leads-builder-any-crm-pro" ); ?></h4></label>
								</div>
								<div class='form-group col-md-12' id="woo_form">
									<div class='col-md-4 '>
										<label id="ecom_innertext" class='leads-builder-sub-heading'>
											<?php echo esc_html__('WooCommerce Fields' , "wp-leads-builder-any-crm-pro" ); ?>
										</label>
									</div>
									<div class='col-md-4'>
										<label id="ecom_innertext" class='leads-builder-sub-heading'>
											<?php echo esc_html__('CRM Fields' , "wp-leads-builder-any-crm-pro" ); ?>
										</label>
									</div>
								</div>
	<?php
	$wc_config = get_option( "ecom_wc_{$activated_crm}_{$ecom_module}_config" );
	$wc_config_fields = $wc_config['fields'] ?? '';
	$third_module = $ecom_module;

	// To fetch the checkout form fields.
	global $woocommerce;
	$checkout_fields = new WC_Countries();
	$checkout_fields = $checkout_fields->get_address_fields();
	//Get Woocommerce Checkout Fields	
	$wc_field_labels = array();
	foreach( $checkout_fields as $check_key => $check_value )
	{
		if( isset($check_value['label']) && $check_value['label'] != "" )
		{
			$wc_field_labels[] = $check_value['label'];	
		}
		else
		{
			if( $check_key == "billing_address_2" )
			{
				$wc_field_labels[] = "Address2";
			}
		}
	}

	//Get CRM Fields
	$CaptureDataObj = new CaptureData();
	$crm_fields = $CaptureDataObj->get_crmfields_by_settings( $activated_crm , $third_module );
	$j = 1;
	$js_mandatory_fields = $crm_field_labels = array();
	foreach( $crm_fields as $crm_field_key => $crm_fields_vals ) {
		$crm_field_labels[$j] = $crm_fields_vals->field_name;
		if( $crm_fields_vals->field_mandatory == 1 )
		{
			if(!in_array($crm_fields_vals->field_name, $js_mandatory_fields))
				$js_mandatory_fields[] = $crm_fields_vals->field_name;
		}
		$j++;
	}
	$js_mandatory_array = json_encode($js_mandatory_fields);
	$crm_field_options = '';
	$crm_field_options .= "<option>--None--</option>";
	foreach( $crm_field_labels as $field_key => $crm_field_label )
	{
		$crm_field_options .= "<option value='{$crm_field_label}'> $crm_field_label</option>";
	}

	//COPY		
	$fields_html = "";
	$i = 1;
	foreach( $wc_field_labels as $cont_id => $cont_label)
	{
		$fields_html .= "<div class='form-group col-md-12'>
			<div class='col-md-3'><label class='leads-builder-label'> $cont_label </label></div>
			<input type='hidden' name='ecom_field_$i' id='ecom_field_$i' value='$cont_label' />";


		$fields_html .= "<div class='col-md-3'><select  name='ecom_crm_fields_$i' id='ecom_crm_fields_$i' class='form-control'>";
		$crm_field_options = '';
		$crm_field_options .= "<option>--None--</option>";
		foreach( $crm_field_labels as $field_key => $crm_field_label ) // Prepare crm fileds drop down
		{

			$crm_field_options .= "<option value='{$crm_field_label}'";
			if( !empty( $wc_config_fields )) {
				foreach( $wc_config_fields as $config_key => $config_val ) // configuration
				{
					if( $cont_label == $config_key && $crm_field_label == $config_val ) //match label and fieldname
					{
						$crm_field_options .= "selected=selected";//select when the configuration exist
					}
				}       
			}
			$crm_field_options .= "> $crm_field_label</option>";
		}
		$fields_html .= $crm_field_options;
		$fields_html .= "</select>
			</div>
			</div>";
		$i++;
	}
	echo $fields_html;
	//END COPY

							?>
							</div><!-- ecommerce-mapping below div close -->

							<?php
							}
							?>

						</div> <!-- END ecom_module_fields -->
						<!-- Load Ecom fields -->
						<input type='hidden' value="<?php echo $i ;?>" id='ecom_total_field_count'>
						<input type='hidden' value="<?php echo $third_module; ?>" id='ecom_module'>
						<input type='hidden' value='<?php echo $js_mandatory_array;?>' id='ecom_crm_mandatory_fields'>

						<div class='clearfix'></div>
						<div class="col-md-12 form-group">
							<div id='ecom_save'>
								<div class='col-md-12' id="save_button">
									<div class="col-md-6"></div>
									<div class="col-md-4">
									<input type="button" class="update"
										name="map_ecom_fields" value="Save" id="map_ecom_fields"
										onclick="map_ecom_crm_fields();">
									</div>
								</div>
							</div>
						</div>

					</div> <!-- Common -->
				</form> </div> <!-- END -->

				<div id="loading-sync"
					style="display: none; background:url(<?php echo esc_url(WP_PLUGIN_URL);?>/wp-leads-builder-any-crm-pro/assets/images/ajax-loaders.gif) no-repeat center">
					<?php echo esc_html__('' , 'wp-leads-builder-any-crm-pro' ); ?></div>

				<div id="loading-image"
					style="display: none; background:url(<?php echo esc_url(WP_PLUGIN_URL);?>/wp-leads-builder-any-crm-pro/assets/images/ajax-loaders.gif) no-repeat center">
					<?php echo esc_html__('' , "wp-leads-builder-any-crm-pro"  ); ?> </div>

			</div>
		</div>
	</div>