<?php
/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class FieldOperations
{
	public $nonceKey = null;
	public function __construct() {
		require_once(SM_LB_PRO_DIR.'includes/Functions.php');
	}
	function saveFormFields( $options , $onAction , $editShortCodes , $formtype = "post" )
	{
		$HelperObj = new WPCapture_includes_helper_PRO();
		$module = $HelperObj->Module;
		$moduleslug = $HelperObj->ModuleSlug;
		$save_fields_config=[];
		$Ordered_field_config=[];
		$data=[];
		$save_field_config = array();
		$crmtype = sanitize_text_field($_REQUEST['crmtype']);
		$module = sanitize_text_field($_REQUEST['module']);
		$moduleslug = rtrim( strtolower($module) , "s");
		$options = "smack_fields_shortcodes";
		if( isset($_POST ['savefields'] ) && (sanitize_text_field($_POST ['savefields']) == "GenerateShortcode")) {
			$config_fields = get_option("smack_{$crmtype}_{$moduleslug}_fields-tmp");
			$config_contact_shortcodes = get_option($options);
		} else {
			$options = "smack_fields_shortcodes";
			$config_contact_shortcodes = get_option($options);
			$config_fields = $config_contact_shortcodes[$editShortCodes];
		}
		foreach( $config_fields as $shortcode_attributes => $fields )
		{
			if($shortcode_attributes == "fields")
			{
				foreach( $fields as $key => $field )
				{
					$save_field_config["fields"][$key] = $field;
				
					if( !isset($field['mandatory']) || $field['mandatory'] != 2 )
					{
						if(isset($_POST['select'.$key]))
						{
							$save_field_config['fields'][$key]['publish'] = 1;
						}
						else
						{
							$save_field_config['fields'][$key]['publish'] = 0;
						}
					}
					else
					{
						$save_field_config['fields'][$key]['publish'] = 1;
					}

					if( !isset($field['mandatory']) || $field['mandatory'] != 2 )
					{
						if(isset($_POST['mandatory'.$key]))
						{
							$save_field_config['fields'][$key]['wp_mandatory'] = 1;
							$save_field_config['fields'][$key]['publish'] = 1;
						}
						else
						{
							$save_field_config['fields'][$key]['wp_mandatory'] = 0;
						}
					}
					else
					{
						$save_field_config['fields'][$key]['wp_mandatory'] = 1;
					}

					$save_field_config['fields'][$key]['display_label'] = sanitize_text_field($_POST['fieldlabel'.$key]);
				}
			}
			else
			{
				$save_field_config[$shortcode_attributes] = $fields;
			}
		}
		if(!isset($save_fields_config["check_duplicate"]))
		{
			$save_fields_config["check_duplicate"] = 'none';
		}
		else if(isset($save_fields_config["check_duplicate"]) && ($save_fields_config["check_duplicate"] === 1))
		{
			$save_fields_config["check_duplicate"] === 'skip';
		}
		else if(isset($save_fields_config["check_duplicate"]) && ($save_fields_config["check_duplicate"] === 0))
		{
			$save_fields_config["check_duplicate"] = 'none';
		}

		$extra_fields = array( "formtype" , "enableurlredirection" , "redirecturl" , "errormessage" , "successmessage" , "assignedto" , "check_duplicate" , "enablecaptcha");

		foreach( $extra_fields as $extra_field )
		{
			if(isset( $_POST[$extra_field]))
			{
				$save_field_config[$extra_field] = $_POST[$extra_field];
			}
			else
			{
				unset($save_field_config[$extra_field]);
			}
		}
		for( $i = 0; $i < $_REQUEST['no_of_rows']; $i++ )
		{
			$REQUEST_DATA[$i] = $_REQUEST['position'.$i];
		}

		asort($REQUEST_DATA);

		$i = 0;
		foreach( $REQUEST_DATA as $key => $value )
		{
			$Ordered_field_config['fields'][$i] = $save_field_config['fields'][$key];
			$i++;
		}
		$save_field_config['fields'] = $Ordered_field_config['fields'];
		$save_field_config['crm'] = $_REQUEST['crmtype'];
		if( isset($_POST ['savefields'] ) && (sanitize_text_field($_POST ['savefields']) == "GenerateShortcode"))
		{
			$OverallFunctionObj = new OverallFunctionsPRO();
			$random_string = $OverallFunctionObj->CreateNewFieldShortcode( $_REQUEST['crmtype'] , $_REQUEST['module'] );
			$config_contact_shortcodes[$random_string] = $config_fields;
			update_option("smack_fields_shortcodes", $config_contact_shortcodes);
			update_option("smack_{$crmtype}_{$moduleslug}_fields-tmp" , $save_field_config);
			exit;
		}
		else
		{
			$config_contact_shortcodes[$_REQUEST['EditShortcode']] = $save_field_config;
			update_option("smack_fields_shortcodes", $config_contact_shortcodes);
			update_option("smack_{$crmtype}_{$moduleslug}_fields-tmp" , $save_field_config);
		}
		$data['display'] = "";
		return $data;
	}

	function formFields( $options, $onAction, $editShortCodes , $formtype = "post" )
	{
		$CaptureData = new CaptureData();
		$content1 = '';
		$config_leads_fields = $CaptureData->formfields_settings( $editShortCodes );	
		$imagepath = SM_LB_DIR.'assets/images/' ;
		$imagepath = esc_url( $imagepath );
		$content = '<input type="hidden" name="field-form-hidden" value="field-form"/><div>';
		$i = 0; 
		if(!isset($config_leads_fields['fields'][0]))
		{
			$content.= '<p style="color:red;font-size:20px;text-align:center;margin-top:-22px;margin-bottom:20px;">'.__("Crm fields are not yet synchronised", "wp-leads-builder-any-crm-pro" ).' </p>';
		} else {
			$content.= '<form method="post" name = "userform" id="userform" action="'.SM_LB_DIR.'/includes/class-lb-manage-shortcodes.php">
						<table class="table" style="border: 1px solid #dddddd;width:100%;margin-bottom:26px;margin-top:0px" id="sort_table">
						<thead>
						<tr class="smack_highlight smack_alt lb-table-heading" style="border-bottom: 1px solid #dddddd;">
						<th style="width: 2%;"></th>
						<th class="smack-field-td-middleit" align="left" style="width: 8%;">
						<input type="checkbox" name="selectall" id="selectall" style="margin-top:-3px"/>
						</th>
						<th style="width: 30%;" align="left"><h5>'.__('Field Name', 'wp-leads-builder-any-crm-pro' ).'</h5>
						</th>
						<th style="width: 14%;" class="smack-field-td-middleit" align="left"><h5>'.__('Show Field', 'wp-leads-builder-any-crm-pro' ).'</h5>
						</th>
						<th style="width: 14%;" class="smack-field-td-middleit" align="left"><h5>'.__('Mandatory' , 'wp-leads-builder-any-crm-pro' ).'</h5>
						</th>
						<th style="width: 30%;" class="smack-field-td-middleit" align="left" style="width:20%;"><h5>'.__('Field Label Display' , 'wp-leads-builder-any-crm-pro' ).'</h5>
						</th>
						</tr></thead><tbody>';

			$count=count($config_leads_fields['fields']);
			for($i=0; $i < $count ; $i++)
			{
				// if( $config_leads_fields['fields'][$i]['wp_mandatory'] == 1 ) {
				// 	$madantory_checked = 'checked="checked"';
				// } else {
				// 	$madantory_checked = "";
				// }
				$field_id = $config_leads_fields['fields'][$i]['field_id'];
				if( isset($config_leads_fields['fields'][$i]['mandatory']) && $config_leads_fields['fields'][$i]['mandatory'] == 2) {
					if($i % 2 == 1)
						$content1.='<tr class="smack_highlight smack_alt">';
					else
						$content1.='<tr class="smack_highlight">';
						$content1 .= '<td style="width: 2%; float: right;" class="sortable">::</td>';
						$content1 .= '<td style="width: 8%;" class="smack-field-td-middleit tdsort"><input type="checkbox" class="pos_checkbx" name="select'.$field_id.'" id="select'.$i.'" disabled=disabled checked=checked ></td>
									<td style="width: 30%;">'.$config_leads_fields['fields'][$i]['label'].' *</td>
									<td style="width: 14%;" class="smack-field-td-middleit">';
						$content1 .= '<a name="publish'.$field_id.'" id="publish'.$i.'" onclick="'."alert('".__('This field is mandatory, cannot hide' , 'wp-leads-builder-any-crm' )."')".'"> <img src="'.$imagepath.'tick_strict.png"/></a></td>';
						$content1 .= '<td style="width: 14%;" class="smack-field-td-middleit"><input type="checkbox" name="mandatory'.$field_id.'" id="mandatory'.$i.'" disabled=disabled checked=checked ></td>';
						$content1 .= '<td style="width: 30%;" class="smack-field-td-middleit"><input type="text" class="form-control" name="fieldlabel'.$field_id.'"  id="field_label_display_'.$i.'" value="'.$config_leads_fields['fields'][$i]['display_label'].'"></td>
									</tr>';
				}
				else
				{
					if($i % 2 == 1)
						$content1.='<tr class="smack_highlight smack_alt">';
					else
						$content1.='<tr class="smack_highlight">';
						$content1.='<td style="width: 2%; float: right;" class="sortable">::</td>';
						$content1.='<td style="width: 8%;" class="smack-field-td-middleit tdsort">';
					if($config_leads_fields['fields'][$i]['publish'] == 1){
						$content1.= '<input type="checkbox" name="select'.$field_id.'" id="select'.$i.'" class="pos_checkbx">';
					}
					else
					{
						$content1.= '<input type="checkbox" name="select'.$field_id.'" id="select'.$i.'" class="pos_checkbx">';
					}
					$content1.='</td>
								<td style="width: 30%;">'.$config_leads_fields['fields'][$i]['label'].'</td>
								<td style="width: 14%;" class="smack-field-td-middleit">';
					if($config_leads_fields['fields'][$i]['publish'] == 1  || $config_leads_fields['fields'][$i]['publish'] == ''){
						$content1.='<p name="publish'.$field_id.'" id="publish'.$i.'" ><span class="is_show_widget" style="color: #019E5A;">Yes</span></p>';
					}
					else{
						$content1.='<p name="publish'.$field_id.'" id="publish'.$i.'" ><span class="not_show_widget" style="color: #FF0000;">No</span></p>';
					}
					$content1.='</td>';
					$content1.='<td style="width: 14%;" class="smack-field-td-middleit">';
					if($config_leads_fields['fields'][$i]["wp_mandatory"] == 1  || $config_leads_fields['fields'][$i]["wp_mandatory"] == '') {
						$content1 .= '<p name="mandatory'.$field_id.'" id="mandatory'.$i.'" >
									  <span class="is_show_widget" style="color: #019E5A;">'.__("Yes", "wp-leads-builder-any-crm-pro" ).'</span>
									  </p>';
					} else {
						$content1 .= '<p name="mandatory'.$field_id.'" id="mandatory'.$i.'" >
									  <span class="not_show_widget" style="color: #FF0000;">'.__("No", "wp-leads-builder-any-crm-pro" ).'</span>
									  </p>';
					}
					$content1 .= '</td>';
					$content1 .= '<td style="width: 30%;" class="smack-field-td-middleit" ><input type="text"  class="form-control" id="field_label_display_'.$i.'" name ="fieldlabel'.$field_id.'" value="'.$config_leads_fields['fields'][$i]['display_label'].'"></td>
						          </tr>';
				}
			}
			$content1.="<input type='hidden' name='no_of_rows' id='no_of_rows' value={$i} />";
			$content.= $content1;
			$content.= '</tbody></table>
						</form>';
		}
		?>
		<script>
		jQuery(document ).ready(function(){
			jQuery("tbody").sortable({
			update:function( event, ui ){
				var orderArray = new Array;
				var siteurl = "<?php echo site_url(); ?>";
				var module = '<?php echo $_REQUEST['module']; ?>';
				var option = 'smack_fields_shortcoders';
				var shortcode = '<?php echo $_REQUEST['EditShortcode']; ?>';
				var onAction = '<?php echo $_REQUEST['onAction']; ?>';
				var crmtype = document.getElementById("lead_crmtype").value;
				var bulkaction = 'Update Order';
				var chkarray = [];
				var labelarray = [];
				jQuery("#sort_table").find('tr').each(function (i, el) {
					if( i != 0){
						var tds = jQuery(this).find('td.tdsort');
						var idx = tds.eq(0).find('input').attr('id');
						var namex = tds.eq(0).find('input').attr('name');
						var get_pos = idx.split("select");
						var get_field_id = namex.split("select");
						orderArray.push(get_field_id[1]);
					}
				});
				var orderarray = JSON.stringify(orderArray);
				var flag = true;
				jQuery.ajax({
				type: 'POST',
					url: ajaxurl,
					data: {
					'action'     : 'adminAllActionsPRO',
						'doaction'   : 'CheckformExits',
						'siteurl'    : siteurl,
						'module'     : module,
						'crmtype'    : crmtype,
						'option'     : option,
						'onAction'   : onAction,
						'shortcode'  : shortcode,
						'bulkaction' : bulkaction,
						'chkarray'   : chkarray,
						'labelarray' : labelarray,
						'orderarray' : orderarray,
				},
					success:function(data) {
						console.log(data); //return false;
						document.getElementById('loading-image').style.display = "none";
						if(data == "Not synced") {
							alert("Must Fetch fields before Saving Settings");
							flag = false;
							return false;
						} else {
							swal('Success', 'Field order updated successfully!', 'success');
						}
					},
					error: function(errorThrown){
					}
				});
				return flag;
			}
		});
		});
		jQuery('tbody').sortable({
		handle: '.handle'
		});
		</script>	
	<?php
		return $content;
	}

	function enableMandatoryFields( $selectedfields , $shortcode_name )
	{
		global $wpdb;
		$string = "";
		$enable_mandtry = $wpdb->get_results("select ffm.form_field_sequence , ffm.rel_id , sm.shortcode_id from wp_smackleadbulider_form_field_manager as ffm inner join wp_smackleadbulider_shortcode_manager as sm on ffm.shortcode_id = sm.shortcode_id where sm.shortcode_name = '$shortcode_name' order by ffm.form_field_sequence");
		if(!empty($selectedfields)) {
			foreach ( $selectedfields as $fields ) {
				$string .= "'" . $enable_mandtry[ $fields ]->rel_id . "',";
			}
		}
		$trim = rtrim($string, ',');
		$mandatory = $enable_mandtry[0]->shortcode_id;
		$wpdb->query("update wp_smackleadbulider_form_field_manager set wp_field_mandatory = '1', state = '1' where rel_id in ($trim) and shortcode_id = '$mandatory'");
	}

	function disableMandatoryFields( $selectedfields, $shortcode_name )
	{
		global $wpdb;
		$string1 = "";

		$disable_mandtry = $wpdb->get_results( "select ffm.form_field_sequence , ffm.rel_id , sm.shortcode_id from wp_smackleadbulider_form_field_manager as ffm inner join wp_smackleadbulider_shortcode_manager as sm on ffm.shortcode_id = sm.shortcode_id where sm.shortcode_name = '$shortcode_name' order by ffm.form_field_sequence" );

		foreach($selectedfields as $fields)
		{
			$string1 .= "'" . $disable_mandtry[$fields]->rel_id . "',";
		}
		$trim1 = rtrim($string1, ',');
		$wps_mandatory = $disable_mandtry[0]->shortcode_id;
		$wpdb->query("update wp_smackleadbulider_form_field_manager set wp_field_mandatory = '0' where rel_id in ($trim1) and shortcode_id = '$wps_mandatory'");
	}

	function saveFieldLabelDisplay( $fieldDisplayLabels , $shortcode_name )
	{
		global $wpdb;
		$fieldLabel = $wpdb->get_results( "select ffm.form_field_sequence , ffm.rel_id , sm.shortcode_id from wp_smackleadbulider_form_field_manager as ffm inner join wp_smackleadbulider_shortcode_manager as sm on ffm.shortcode_id = sm.shortcode_id where sm.shortcode_name = '$shortcode_name' order by ffm.form_field_sequence" );
		$wps_mandatory = $fieldLabel[0]->shortcode_id;
		foreach( $fieldDisplayLabels as $key => $fields )
		{
			$wpdb->query( "update wp_smackleadbulider_form_field_manager set display_label = '{$fields}' where rel_id = {$fieldLabel[$key]->rel_id} and shortcode_id = '$wps_mandatory'" );
		}
	}

	function enableFields( $selectedfields , $shortcode_name )
	{
		global $wpdb;
		$string2 = "";
		$enable_showfields = $wpdb->get_results($wpdb->prepare("select ffm.form_field_sequence , ffm.rel_id , sm.shortcode_id from wp_smackleadbulider_form_field_manager as ffm inner join wp_smackleadbulider_shortcode_manager as sm on ffm.shortcode_id = sm.shortcode_id where sm.shortcode_name = %s order by ffm.form_field_sequence", $shortcode_name));
		if( isset( $selectedfields ) ) {
			foreach($selectedfields as $fields)
			{
				$string2 .= "'" . $enable_showfields[$fields]->rel_id . "',";
			}
		}
		$trim2 = rtrim($string2, ',');
		$wps_enablefields = $enable_showfields[0]->shortcode_id;
		$wpdb->query("update wp_smackleadbulider_form_field_manager set state = '1' where rel_id in ($trim2) and shortcode_id = '$wps_enablefields'");
	}

	function disableFields( $selectedfields, $shortcode_name )
	{
		global $wpdb;
		$string3 = "";	
		$disable_showfields = $wpdb->get_results($wpdb->prepare("select ffm.form_field_sequence, ffm.rel_id, sm.shortcode_id, sm.module, sm.crm_type from wp_smackleadbulider_form_field_manager as ffm inner join wp_smackleadbulider_shortcode_manager as sm on ffm.shortcode_id = sm.shortcode_id where sm.shortcode_name = %s order by ffm.form_field_sequence", $shortcode_name)); // Modified by Fredrick

		// Added by Fredrick
		$module = $disable_showfields[0]->module;
		$crm_type = $disable_showfields[0]->crm_type;
		if($module == 'Leads') {
			$shortcode_module = 'lead';
		} else {
			$shortcode_module = 'contact';
		}
		$option_name = 'smack_' . $crm_type . '_' . $shortcode_module . '_fields-tmp';
		$crm_fields = get_option($option_name);
		$madantory_fields = ''; //array();
		$disable_crm_fields = $crm_fields['fields'];
		$disable_crm_fields = array_combine(range(1, count($disable_crm_fields)), array_values($disable_crm_fields));
		if(!empty($disable_crm_fields) && is_array($disable_crm_fields)){
			foreach($disable_crm_fields as $key => $val) {
				if($val['mandatory'] == 2)
					$madantory_fields .= $key . ',';
			}
		}
		$madantory_fields = substr($madantory_fields, 0, -1);
		// ends here
	
		if( isset( $selectedfields ) ) {
			foreach($selectedfields as $fields)
			{	
				$string3 .= "'" . $disable_showfields[$fields]->rel_id . "',";
			}
		}
		$trim3 = rtrim($string3, ',');
		$wps_disablefields = $disable_showfields[0]->shortcode_id;
		$wpdb->query("update wp_smackleadbulider_form_field_manager set state = '0' where rel_id in ($trim3) and shortcode_id = '$wps_disablefields'");
		$wpdb->query("update wp_smackleadbulider_form_field_manager set state = '1' where rel_id in ($madantory_fields) and shortcode_id = '$wps_disablefields'");
	}

	function updateFieldsOrder( $field_order, $shortcode_name )
	{
		$field_order = array_flip($field_order);
		global $wpdb;
		$get_shortcode_id = $wpdb->get_results($wpdb->prepare("select shortcode_id from wp_smackleadbulider_shortcode_manager where shortcode_name = %s and crm_type = %s", $shortcode_name,$_REQUEST['crmtype']));
		$shortcode_id = $get_shortcode_id[0]->shortcode_id;

		$get_existing_field_order = $wpdb->get_results($wpdb->prepare("select field_id,rel_id, form_field_sequence from wp_smackleadbulider_form_field_manager where shortcode_id = %d order by form_field_sequence", $shortcode_id));print_r($get_existing_field_order);
		foreach($get_existing_field_order as $key => $ffOrder) {
			$wpdb->get_results($wpdb->prepare("update wp_smackleadbulider_form_field_manager set form_field_sequence ={$field_order[$ffOrder->field_id]} where rel_id ={$ffOrder->rel_id} "));
		}
	}
}
class ManageShortcodesActions {

	public $nonceKey = null;
	public function __construct()
	{
		require_once(SM_LB_PRO_DIR.'includes/Functions.php');
		$helperObj = new OverallFunctionsPRO();
	}

	/**
	 * The actions index method
	 * @param array $request
	 * @return array
	 */

	public function executeIndex($request)
	{
		// return an array of name value pairs to send data to the template
		$data = array();
		return $data;
	}

	public function executeView($request)
	{
		$data = array();
		$data['plugin_url']= SM_LB_PRO_DIR;
		$data['onAction'] = 'onCreate';
		$data['siteurl'] = site_url();
		$data['nonce_key'] = $this->nonceKey;
		return $data;
	}

	public function executeManageFields1($request)
	{
		$data = $request;
		return $data;
	}
	public function ManageFields($shortcode, $crmtype, $module, $bulkaction, $chkarray, $labelarray, $orderarray)
	{
		$labelArray = stripslashes($labelarray);
		$FieldOperation = new FieldOperations();
		$CaptureData = new CaptureData();
		$config_leads_fields = $CaptureData->formfields_settings( $shortcode );
		$chkArray = json_decode(stripslashes($chkarray));
		$orderArray = json_decode(stripslashes($orderarray));
		$labelArray = stripslashes($labelarray);
		$newlabelarray = json_decode($labelArray);
		if( isset( $bulkaction ) )
		{
			$selectedfields = array();
			$fieldLabelDisplay = array();
			if(!empty($config_leads_fields['fields'])) {
				foreach ( $config_leads_fields['fields'] as $index => $fInfo ) {
					$current_field_positions[ $fInfo['field_id'] ] = $fInfo['order'];
				}
			}
			if(!empty($chkArray)) {
				foreach ( $chkArray as $key => $value ) {
					$selectedfields[] = $value;
				}
			}
			if(!empty($orderArray)) {
				foreach ( $orderArray as $key1 => $value1 ) {
					$new_field_positions[ $key1 + 1 ] = $value1; #$current_field_positions[]
				}
			}
			if(!empty($newlabelarray)) {
				foreach ( $newlabelarray as $key2 => $value2 ) {
					$fieldLabelDisplay[] = $value2;
				}
			}

			$bulkaction = isset($bulkaction) ? trim($bulkaction) : 'enable_field';
			$shortcode_name = $shortcode;
			switch( $bulkaction )
			{
			case 'Enable Field':
				$FieldOperation->enableFields( $selectedfields , $shortcode_name );
				break;
			case 'Disable Field':
				$FieldOperation->disableFields( $selectedfields , $shortcode_name );
				break;
			case 'Update Order':
				$FieldOperation->updateFieldsOrder( $new_field_positions , $shortcode_name );
				break;
			case 'Enable Mandatory':
				$FieldOperation->enableMandatoryFields( $selectedfields , $shortcode_name );
				break;
			case 'Disable Mandatory':
				$FieldOperation->disableMandatoryFields( $selectedfields , $shortcode_name );
				break;
			case 'Save Display Label':
				$FieldOperation->saveFieldLabelDisplay( $fieldLabelDisplay , $shortcode_name );
				break;
			}
		}
		//Action 1
		//support for Ninja forms 
		//first create the  ninjs form title  in wp_ninja_forms table

		//check the selected Third party plugin	
		$get_edit_shortcode = $shortcode;
		$thirdPartyPlugin = get_option('Thirdparty_'.$shortcode);
		$get_thirdparty_title = get_option( $get_edit_shortcode );
		if($thirdPartyPlugin == 'ninjaform' ) {
			if( !empty( $get_thirdparty_title ) ) {
				$title = $get_thirdparty_title;
			} else {
				$title = $crmtype . '-' . $module . '-' . $shortcode;
			}
			$obj = new CallManageShortcodesCrmObj();
			// create the form in ninja form table
			$nin_form_id = $obj->ninjaFromTitle($title,$thirdPartyPlugin,$shortcode);
		}

		if($thirdPartyPlugin == 'contactform' )    {
			$title = $crmtype . '-'.$module.'-'.$shortcode;
			$obj = new CallManageShortcodesCrmObj();
		}

		if($thirdPartyPlugin == 'wpform' )    {
			$title = $crmtype . '-'.$module.'-'.$shortcode;
			$obj = new CallManageShortcodesCrmObj();
		}

		if($thirdPartyPlugin == 'wpformpro' )    {
			$title = $crmtype . '-'.$module.'-'.$shortcode;
			$obj = new CallManageShortcodesCrmObj();
		}

		if($thirdPartyPlugin == 'gravityform') 	{
			if( !empty( $get_thirdparty_title ) )
			{
				$title = $get_thirdparty_title;	
			}
			else
			{
				$title = $crmtype . '-'.$module.'-'.$shortcode;		
			}
			$obj = new CallManageShortcodesCrmObj();
			//create the form in gravity form table
			update_option('blogdescription','gravity half inside');
			$gravity_form_id = $obj->gravityFromTitle($title,$thirdPartyPlugin,$shortcode);

		}

		//Action 2
		// ninjs form field format
		if($thirdPartyPlugin == 'ninjaform')        {
			$obj->formatNinjaFields($thirdPartyPlugin,$shortcode,$nin_form_id,$bulkaction,$title);
		}

		if($thirdPartyPlugin == 'contactform' )        {
			$get_edit_shortcode = $shortcode;
			$get_thirdparty_title = get_option( $get_edit_shortcode );

			if( !empty($get_thirdparty_title  ))
			{
				$title = $get_thirdparty_title;
			}
			else
			{
				$title = $get_edit_shortcode;
			}	
			$obj->formatContactFields($thirdPartyPlugin, $title , $shortcode);
		}
		if($thirdPartyPlugin == 'wpform' )        {
			$get_edit_shortcode = $shortcode;
			$get_thirdparty_title = get_option( $get_edit_shortcode );

			if( !empty($get_thirdparty_title  ))
			{
				$title = $get_thirdparty_title;
			}
			else
			{
				$title = $get_edit_shortcode;
			}	
			$obj->formatContactFields($thirdPartyPlugin, $title , $shortcode);
		}

		if($thirdPartyPlugin == 'wpformpro' )        {
			$get_edit_shortcode = $shortcode;
			$get_thirdparty_title = get_option( $get_edit_shortcode );

			if( !empty($get_thirdparty_title  ))
			{
				$title = $get_thirdparty_title;
			}
			else
			{
				$title = $get_edit_shortcode;
			}	
			$obj->formatContactFields($thirdPartyPlugin, $title , $shortcode);
		}

		if($thirdPartyPlugin == 'gravityform')
		{
			if( !empty( $get_thirdparty_title ) )
			{
				$title = $get_thirdparty_title;
			}
			else
			{
				$title = $crmtype . '-'.$module.'-'.$shortcode;
			}
			$obj->formatGravityFields($thirdPartyPlugin,$shortcode,$gravity_form_id,$title);
		}


		$data = array();
		return $data;
	}

	public static function CreateShortcode($module)
	{
		global $lb_admin;
		$data=[];
		$shortcode_details=[];
		$data['HelperObj'] = new WPCapture_includes_helper_PRO();
		$crmtype = $data["HelperObj"]->ActivatedPlugin;
		$moduleslug = rtrim( strtolower($module) , "s");
		$tmp_option = "smack_{$crmtype}_{$moduleslug}_fields-tmp";
		// Function call
		$shortcodeObj = new CaptureData();
		$OverallFunctions = new OverallFunctionsPRO();
		$randomstring = $OverallFunctions->CreateNewFieldShortcode( $crmtype , $module );
		$is_redirection='';
		$url_redirection = '';
		$google_captcha='';
		$config_fields['crm'] = $crmtype;
		$users_list = get_option('crm_users');
		$users = isset($users_list[$crmtype]['id']) ? $users_list[$crmtype]['id'][0] : '';

		$assignee = $users;
		$shortcode_details['name'] = $randomstring;
		$shortcode_details['type'] = 'post';
		$shortcode_details['assignto'] = $assignee;
		$shortcode_details['isredirection'] = $is_redirection;
		$shortcode_details['urlredirection'] = $url_redirection;
		$shortcode_details['captcha'] = $google_captcha;
		$shortcode_details['crm_type'] = $crmtype;
		$shortcode_details['module'] = $module;
		$shortcode_details['errormesg'] = '';
		$shortcode_details['successmesg'] = '';
		$shortcode_details['duplicate_handling'] = '';
		$lb_admin->setShortcodeDetails($shortcode_details);
		$shortcode_id = $shortcodeObj->formShorcodeManager($shortcode_details);
		$config_fields = $shortcodeObj->get_crmfields_by_settings($crmtype, $module);
		foreach( $config_fields as $field )
		{
			$shortcodeObj->insertFormFieldManager( $shortcode_id , $field->field_id , $field->field_mandatory , '1' , $field->field_type, $field->field_values , $field->field_sequence, $field->field_label );
		}

		$config_shortcodes = get_option("smack_fields_shortcodes");
		$config_shortcodes[$randomstring] = $config_fields;
		$details =array();
		$details['shortcode'] = $randomstring;
		$details['module'] = $module;
		$details['crmtype'] =  $crmtype;
		return $details;
		//	exit;
	}

	public function DeleteShortcode($shortcode)
	{
		global $wpdb;
		// return an array of name value pairs to send data to the template
		$delete_short = $shortcode;
		$deletedata = $wpdb->get_results("select shortcode_id from wp_smackleadbulider_shortcode_manager where shortcode_name = '$delete_short'");
		$deleteid = $deletedata[0]->shortcode_id;
		$wpdb->query("delete from wp_smackleadbulider_shortcode_manager where shortcode_id = '$deleteid'");
		$wpdb->query( "delete from wp_smackleadbulider_form_field_manager where shortcode_id = '$deleteid'" );
		return $deletedata;
		exit;
	}
}

class CallManageShortcodesCrmObj extends ManageShortcodesActions
{
	private static $_instance = null;
	public static function getInstance()
	{
		if( !is_object(self::$_instance) ) 
			self::$_instance = new CallManageShortcodesCrmObj();
		return self::$_instance;
	}

	public function ninjaFromTitle($title,$thirdparty,$shortcode)	{

		global $wpdb;
		//update_option('blogdescription','ninjainside');
		//get Ninja form fields
		$date =  date("Y-m-d h:i:sa");
		$admin_email = get_option('admin_email');

		//Get Ninja Form is to be inserted
		$ninjaid = $wpdb->get_var("select MAX(id) from ".$wpdb->prefix ."nf3_forms");
		$newid = $ninjaid + 1;
		$checkid = $wpdb->get_results("select * from wp_smackformrelation where  shortcode='{$shortcode}' and thirdparty='ninjaform'");

		if(empty($checkid)){
			$wpdb->insert( $wpdb->prefix .'nf3_forms' , array(  'id' => $newid, 'title'=> $title,'created_at' => $date , 'updated_at' => 'NULL' ) );
			$metaformdetails = array(
									'objectType' => 'Form Setting',
									'editActive' => '',
									'show_title' => '1',
									'clear_complete' => '1',
									'hide_complete' => '1',
									'default_label_pos' => 'above',
									'wrapper_class' => '',
									'element_class' => '',
									'add_submit' => '1',
									'currency' => '',
									'logged_in' => '',
									'not_logged_in_msg' => '',
									'sub_limit_number' => NULL ,
									'sub_limit_msg' => '',
									'calculations' => 'a:0:{}',
									'_seq_num' => 3
			);
			//Form_meta
			foreach($metaformdetails as $key => $value) {
				$wpdb->insert( $wpdb->prefix .'nf3_form_meta' , array( 'parent_id' => $newid , 'key'=> $key ,'value'=>$value ) );
			}

			//nf3_actions
			$actions = array( 'successmessage' , 
				'email' , 
				'save' );
			foreach($actions as $key => $value) {
				$wpdb->insert( $wpdb->prefix .'nf3_actions' , array( 'title' => NULL, 'key'=> NULL ,'type' => $value , 'active' =>'1' , 'parent_id' => $newid ,'created_at' => $date , 'updated_at' => NULL ) );
			}
			$fetch_meta_parent_ids=[];
			//Get Action Meta parent ids
			$get_action_meta_parent_ids = array();
			$get_action_meta_parent_ids = $wpdb->get_results($wpdb->prepare("select id from ".$wpdb->prefix."nf3_actions where parent_id=%d", $newid));
			foreach($get_action_meta_parent_ids as $action_meta_key => $action_meta_id) 
			{
				$fetch_meta_parent_ids[] = $action_meta_id->id;
			} 

			//Actions Meta
			$admin_email = get_option('admin_email');
			$successmsg_meta_id  = $fetch_meta_parent_ids[0]; 
			$admin_email_meta_id = $fetch_meta_parent_ids[1];
			$savesubmission_meta_id = $fetch_meta_parent_ids[2];

			//Success Message - Action meta
			$successmsg = array(
								'objectType' => 'Action',
								'objectDomain'=>'actions',
								'editActive'=>'',
								'label'=> 'SuccessMessage',
								'message'=>'Your form has been successfully submitted',
								'order'=>'1',
								'payment_gateways'=>'',
								'payment_total'=>'',
								'tag'=>'',
								'to'=>'',
								'email_subject'=>'',
								'email_message'=>'',
								'email_message_plain'=>'',
								'from_name'=>'',
								'from_address'=>'',
								'reply_to'=>'',
								'email_format'=>'html',
								'cc'=>'',
								'bcc'=>'',
								'attach_csv'=>'',
								'redirect_url'=>'',
								'success_msg'=>'Your form has been successfully submitted'
			);
			foreach($successmsg as $key => $value) {
				$wpdb->insert( $wpdb->prefix .'nf3_action_meta' , array( 'parent_id' => $successmsg_meta_id , 'key'=> $key ,'value'=>$value ) );
			}

			//Admin email Entry
			$adminemail = array(	
								'objectType'=>'Action',
								'objectDomain'=>'actions',
								'editActive'=>'',
								'label'=>'Admin Email',
								'to'=>$admin_email,
								'subject'=>'Ninja Forms Submission',
								'message'=>'{field:all_fields}',
								'order'=>'2',
								'payment_gateways'=>'',
								'payment_total'=>'',
								'tag'=>'',
								'email_subject'=>'',
								'email_message'=>'',
								'email_message_plain'=>'',
								'from_name'=>'',
								'from_address'=>'',
								'reply_to'=>'',
								'email_format'=>'html',
								'cc'=>'',
								'bcc'=>'',
								'attach_csv'=>''
			);
			foreach($adminemail as $key => $value) {
				$wpdb->insert( $wpdb->prefix .'nf3_action_meta' , array( 'parent_id' => $admin_email_meta_id , 'key'=> $key ,'value'=>$value ) );
			}
			//Action 3 - Save subject
			$savesub = array(	
							'objectType'=>'Action',
							'objectDomain'=>'actions',
							'editActive'=>'',
							'label'=>'Save Submission',
							'order'=>'3',
							'payment_gateways'=>'',
							'payment_total'=>'',
							'tag'=>'','to'=>'',
							'email_subject'=>'',
							'email_message'=>'',
							'email_message_plain'=>'',
							'from_name'=>'',
							'from_address'=>'',
							'reply_to'=>'',
							'email_format'=>'html',
							'cc'=>'','bcc'=>'',
							'attach_csv'=>'',
							'redirect_url'=>''
			);
			foreach($savesub as $key => $value) {
				$wpdb->insert( $wpdb->prefix .'nf3_action_meta' , array( 'parent_id' => $savesubmission_meta_id , 'key'=> $key ,'value'=>$value ) );
			}

			//check id is already present in the wp_smackformrelation table
			$wpdb->insert( 'wp_smackformrelation' , array(  'shortcode' => $shortcode , 'thirdparty' => $thirdparty , 'thirdpartyid' => $newid ) );
			return $newid;
		}
		else {
			$thirdparty_formid = $checkid[0]->thirdpartyid;
			//Update Title
			$wpdb->update( $wpdb->prefix ."nf3_forms" , array( 'title' => $title ) , array(  'id' => $thirdparty_formid ) );
			$newid = $wpdb->get_var( $wpdb->prepare( "select id from ". $wpdb->prefix ."nf3_forms where title=%s " , $title ) );
			//Return Alredy existing ID
			return $newid;
		}
	}

	public function formatNinjaFields($thirdparty_form, $shortcode, $newid, $action, $title) {

		global $wpdb;
		$date =  date("Y-m-d h:i:sa");
		$ninja_array = $fields_array = $form = array();

		//get fields frmtNinjaFieldsm		
		$word_form_enable_fields = $wpdb->get_results("select a.rel_id,a.wp_field_mandatory,a.custom_field_type,a.custom_field_values,a.display_label from wp_smackleadbulider_form_field_manager as a join wp_smackleadbulider_shortcode_manager as b where b.shortcode_id=a.shortcode_id and b.shortcode_name='{$shortcode}' and a.state=1 order by form_field_sequence");
		$checkid = $wpdb->get_results( $wpdb->prepare( "select * from wp_smackformrelation where thirdpartyid=%d and thirdparty=%s" , $newid , 'ninjaform' ) );
		$active_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );

		if(!empty($checkid)) {

			//Get Field meta table ids
			$get_meta_fields_ids = array();
			$get_meta_fields_ids = $wpdb->get_results($wpdb->prepare("select id from ".$wpdb->prefix."nf3_fields where parent_id=%d", $newid));
			//Delete fields 
			$wpdb->query( $wpdb->prepare( "delete from ". $wpdb->prefix ."nf3_fields where parent_id=%d" , $newid ) );

			//Delete Field Meta items
			foreach($get_meta_fields_ids as $meta_field_key => $meta_field_ids)
			{
				$wpdb->delete($wpdb->prefix.'nf3_field_meta' , array('parent_id' => $meta_field_ids->id) , array('%d'));
			}

			//delete thirdparty record our relation table
			$wpdb->query( $wpdb->prepare( "delete from wp_smackthirdpartyformfieldrelation where thirdpartyformid=%d" , $newid ) );
			$orderr = 1;
			foreach($word_form_enable_fields as $mainkey=>$mainvalue) {
				$type = $mainvalue->custom_field_type;
				$label = $mainvalue->display_label;
				if( $active_crm == 'freshsales' && ($label == 'Time zone' ))
				{
					//Skip the time zone entry
				}
				else
				{
					switch($type) {
					case 'picklist':
					case 'multipicklist':
						if($type == 'picklist')
							$type = 'listselect';
						else
							$type = 'listmultiselect';
						$get_fields = unserialize($mainvalue->custom_field_values);
						$my_fields = $fields_array = array();
						foreach($get_fields as $get_key => $get_value)
						{
							$my_fields[] = array(
								'label' => $get_value['label'],
								'value' => $get_value['value'],
								'errors'=> array(),
								'settingModel' => array(
									'settings'=> '',
									'error' => '',
									'hide_merge_tags'=>'',
									'name' => 'options',
									'type' => 'option-repeater',
									'label'=> 'Options <a href="#" class="add-new">Add New</a>',
									'columns' => array(
										'label' => array('header'=>'Label','default'=>''),
										'value' => array('header'=>'Value','default'=>''),
										'calc'  => array('header'=>'Calc','default'=>''),
										'selected'=> array('header'=>'<span class="dashicons dashicons-yes"></span>','default'=> 0 ),
									),

								),
							);
						}
						$fields_array['options'] = serialize($my_fields);
						$fields_array['objectType'] = 'Field';
						$fields_array['objectDomain'] = 'fields';
						$fields_array['order'] = $orderr;
						$fields_array['label_pos'] = 'default';
						break;
					case 'date':
						$type = 'date';
						$fields_array['objectType'] = 'Field';
						$fields_array['objectDomain'] = 'fields';
						$fields_array['order'] = $orderr;
						$fields_array['label_pos'] = 'default';
						$fields_array['date_format']= 'DD/MM/YYYY';
						break;
					case 'string':
					case 'url':
					case 'phone':
					case 'currency':
					case 'integer':
					case 'text':
					case 'email':
						$type = 'textbox';
						$fields_array['objectType'] = 'Field';
						$fields_array['objectDomain'] = 'fields';
						$fields_array['order'] = $orderr;
						$fields_array['label_pos'] = 'default';
						$fields_array['input_limit_type'] = 'characters';
						$fields_array['input_limit_msg'] = 'Character(s) left';
						break;
					case 'boolean':
						$type = 'checkbox';
						$fields_array['objectType'] = 'Field';
						$fields_array['objectDomain'] = 'fields';
						$fields_array['order'] = $orderr;
						$fields_array['label_pos'] = 'right';
						$fields_array['default_value'] = 'unchecked';
						break;
					case '':
						$type = 'textbox';
						$fields_array['objectType'] = 'Field';
						$fields_array['objectDomain'] = 'fields';
						$fields_array['order'] = $orderr;
						$fields_array['label_pos'] = 'default';
						$fields_array['input_limit_type'] = 'characters';
						$fields_array['input_limit_msg'] = 'Character(s) left';
						break;
					default :
						$fields_array['objectType'] = 'Field';
						$fields_array['objectDomain'] = 'fields';
						$fields_array['order'] = $orderr;
						$fields_array['label_pos'] = 'default';
						$fields_array['input_limit_type'] = 'characters';
						$fields_array['input_limit_msg'] = 'Character(s) left';
						break;
					}

					$ninja_array['label'] = $mainvalue->display_label;

					$fields_array['required'] = $mainvalue->wp_field_mandatory;
					$ninja_arra = serialize($ninja_array['label']);
					$my_label = strtolower($ninja_array['label']);
					$my_label = str_replace(' ','_',$my_label);
					$form[] = $my_label;

					//Get Last id and insert FIELDS TABLE	
					$wpdb->insert($wpdb->prefix.'nf3_fields' , array( 'label' => $ninja_array['label'] , 'key' => $my_label , 'type' => $type , 'parent_id' => $newid , 'created_at' => $date , 'updated_at' => NULL));

					//Get last inserted Field ID
					$CurrentId = $wpdb->get_var("select MAX(id) from ". $wpdb->prefix ."nf3_fields");
					foreach($fields_array as $key => $value) {
						$wpdb->insert( $wpdb->prefix .'nf3_field_meta' , array( 'parent_id' => $CurrentId,'key'=> $key ,'value'=>$value ) );
					}

					$thirdpartypluginname = $thirdparty_form;
					//map the smackc form anr ninja forms
					$wpdb->query("insert into wp_smackthirdpartyformfieldrelation (smackshortcodename,smackfieldid,smackfieldslable,thirdpartypluginname,thirdpartyformid,thirdpartyfieldids) values('{$shortcode}','{$mainvalue->rel_id}','{$mainvalue->display_label}','{$thirdpartypluginname}','{$newid}','{$mainvalue->display_label}')");

				}// Check for fresh sales time zone .. skip
				$orderr++;
			}

			//Add Submit button
			$wpdb->insert($wpdb->prefix.'nf3_fields' , array( 'label' => 'Submit' , 'key' => 'submit' , 'type' => 'submit' , 'parent_id' => $newid , 'created_at' => $date , 'updated_at' => NULL));

			//Get Last Field meta id
			$get_submit_meta_id = $wpdb->get_var($wpdb->prepare("select id from ".$wpdb->prefix."nf3_fields where parent_id=%d and type=%s", $newid , 'submit'));

			$submit_array = array( 'objectType'=> 'Field',
				'objectDomain'=> 'fields',
				'order'=> '9999',
				'processing_label'=> 'Processing',
			);
			foreach($submit_array as $key => $value) {
				$wpdb->insert($wpdb->prefix.'nf3_field_meta' , array( 'parent_id'=> $get_submit_meta_id, 'key'=> $key,'value'=>$value));
			}
		}

		//Form_meta table
		$form_meta_fields = serialize($form);

		//Delete formContentData Before insert
		$wpdb->delete($wpdb->prefix.'nf3_form_meta' , array('parent_id' => $newid , 'key' => 'formContentData' ) , array('%d' , '%s'));
		$wpdb->insert($wpdb->prefix.'nf3_form_meta' , array('parent_id' => $newid , 'key' => 'formContentData' , 'value' => $form_meta_fields));

	}

	public function formatContactFields($thirdparty_form,$title,$shortcode){
		global $wpdb;
		$word_form_enable_fields = $wpdb->get_results("select a.rel_id,a.wp_field_mandatory,a.custom_field_type,a.custom_field_values,a.display_label from wp_smackleadbulider_form_field_manager as a join wp_smackleadbulider_shortcode_manager as b where b.shortcode_id=a.shortcode_id and b.shortcode_name='{$shortcode}' and a.state=1 order by form_field_sequence");
		$checkid = $wpdb->get_var( $wpdb->prepare( "select thirdpartyid from wp_smackformrelation where shortcode =%s and thirdparty=%s" , $shortcode , 'contactform' ) );

		if(!empty($checkid))
		{
			$wpdb->query( $wpdb->prepare( "delete from wp_smackthirdpartyformfieldrelation where thirdpartyformid=%d" , $checkid ) );
		}
		$contact_array = '';
		foreach($word_form_enable_fields as $key=>$value) {
			$type = $value->custom_field_type;
			$labl = $value->display_label;
			$label = preg_replace('/[^a-zA-Z]+/','_',$labl);
			$label = ltrim($label,'_');
			$mandatory = $value->wp_field_mandatory;
			$cont_array = array();
			$cont_array = unserialize($value->custom_field_values);
			$string ="";
			if( !empty( $cont_array ) )
			{
				foreach($cont_array as $val) {
					$string .= "\"{$val['label']}\" ";
				}
			}
			$str = rtrim($string,',');
			if($mandatory == 0)
			{
				$man ="";
			}
			else
			{
				$man ="*";
			}
			switch($type)
			{
			case 'phone':
			case 'currency':
			case 'text':
			case 'integer':
			case 'string':
				$contact_array .= "<p>".  $label ."".$man. "<br />[text".$man." ".  $label."] </p>" ;
				break;
			case 'email':
				$contact_array .= "<p>".  $label ."".$man. "<br />[email".$man." ". $label."] </p>" ;
				break;
			case 'url':
				$contact_array .= "<p>".  $label ."".$man. "<br />[url".$man." ". $label."] </p>" ;
				break;
			case 'picklist':
				$contact_array .= "<p>".  $label ."".$man. "<br />[select".$man." ". $label." " .$str."] </p>" ;
				$str ="";
				break;
			case 'boolean':
				$contact_array .= "<p>[checkbox".$man." ". $label." "."label_first "."\" $label\""."] </p>" ;
				break;
			case 'date':
				$contact_array .= "<p>".  $label ."".$man. "<br />[date".$man." ". $label." min:1950-01-01 max:2050-12-31 placeholder \"YYYY-MM-DD\"] </p>" ;
				break;
			case '':
				$contact_array .= "<p>".  $label ."".$man. "<br />[text".$man." ".  $label."] </p>" ;
				break;
			default:
				break;
			}
		}
		$contact_array .= "<p><br /> [submit "." \"Submit\""."]</p>";
		$meta = $contact_array;
		$checkid = $wpdb->get_var( $wpdb->prepare( "select thirdpartyid from wp_smackformrelation inner join {$wpdb->prefix}posts on {$wpdb->prefix}posts.ID = wp_smackformrelation.thirdpartyid and {$wpdb->prefix}posts.post_status='publish' where shortcode =%s and thirdparty=%s" , $shortcode , 'contactform'  ) );

		if(empty($checkid))
		{
			$contform = array (
				'post_title'  => $title,
				'post_content'=> $contact_array,
				'post_type'   => 'wpcf7_contact_form',
				'post_status' => 'publish',
				'post_name'   => $shortcode
			);
			$id = wp_insert_post($contform);
			$content2 = "[contact-form-7 id=\"$id\" title=\"$shortcode\"]";
			$contform2 = array (
				'post_title'  => $id,
				'post_content'=> $content2,
				'post_type'   => 'post',
				'post_status' => 'publish',
				'post_name'   => $id
			);
			wp_insert_post($contform2);

			$post_id = $id;
			$meta_key ='_form';
			$meta_value = $meta;
			update_post_meta($post_id,$meta_key,$meta_value);
			$wpdb->query( "update wp_smackformrelation set thirdpartyid = {$id} where thirdparty='contactform' and shortcode ='{$shortcode}'" );
		}
		else
		{

			$wpdb->update( $wpdb->posts , array( 'post_content' => $contact_array , 'post_title' => $title ) , array( 'ID' => $checkid ) );
			$wpdb->update( $wpdb->postmeta , array( 'meta_value' => $meta ) , array( 'post_id' => $checkid , 'meta_key' => '_form'));
			$id = $checkid;
		}
		$thirdPartyPlugin = $thirdparty_form;
		$obj = new CallManageShortcodesCrmObj();
		$obj->contactFormRelation($shortcode,$id,$thirdPartyPlugin,$word_form_enable_fields);

		$checkid = $wpdb->get_var( $wpdb->prepare( "select thirdpartyid from wp_smackformrelation inner join {$wpdb->prefix}posts on {$wpdb->prefix}posts.ID = wp_smackformrelation.thirdpartyid and {$wpdb->prefix}posts.post_status='publish' where shortcode =%s and thirdparty=%s" , $shortcode , 'wpform'  ) );
		if(empty($checkid))
		{
			$contform = array (
				'post_title'  => $title,
				'post_content'=> $contact_array,
				'post_type'   => 'wpforms',
				'post_status' => 'publish',
				'post_name'   => $shortcode
			);
			$id = wp_insert_post($wpfform);
			$content2 = "[wpforms id=\"$id\" title=\"$shortcode\"]";
			$contform2 = array (
				'post_title'  => $id,
				'post_content'=> $content2,
				'post_type'   => 'post',
				'post_status' => 'publish',
				'post_name'   => $id
			);
			wp_insert_post($contform2);

			$post_id = $id;
			$meta_key ='_form';
			$meta_value = $meta;
			update_post_meta($post_id,$meta_key,$meta_value);
			$wpdb->query( "update wp_smackformrelation set thirdpartyid = {$id} where thirdparty='wpform' and shortcode ='{$shortcode}'" );
		}
		else
		{

			$wpdb->update( $wpdb->posts , array( 'post_content' => $contact_array , 'post_title' => $title ) , array( 'ID' => $checkid ) );
			$wpdb->update( $wpdb->postmeta , array( 'meta_value' => $meta ) , array( 'post_id' => $checkid , 'meta_key' => '_form'));
			$id = $checkid;
		}
		$thirdPartyPlugin = $thirdparty_form;
		$obj = new CallManageShortcodesCrmObj();
		$obj->contactFormRelation($shortcode,$id,$thirdPartyPlugin,$word_form_enable_fields);

		$checkid = $wpdb->get_var( $wpdb->prepare( "select thirdpartyid from wp_smackformrelation inner join {$wpdb->prefix}posts on {$wpdb->prefix}posts.ID = wp_smackformrelation.thirdpartyid and {$wpdb->prefix}posts.post_status='publish' where shortcode =%s and thirdparty=%s" , $shortcode , 'wpformpro'  ) );
		if(empty($checkid))
		{
			$contform = array (
				'post_title'  => $title,
				'post_content'=> $contact_array,
				'post_type'   => 'wpforms',
				'post_status' => 'publish',
				'post_name'   => $shortcode
			);
			$id = wp_insert_post($wpfform);
			$content2 = "[wpforms id=\"$id\" title=\"$shortcode\"]";
			$contform2 = array (
				'post_title'  => $id,
				'post_content'=> $content2,
				'post_type'   => 'post',
				'post_status' => 'publish',
				'post_name'   => $id
			);
			wp_insert_post($contform2);

			$post_id = $id;
			$meta_key ='_form';
			$meta_value = $meta;
			update_post_meta($post_id,$meta_key,$meta_value);
			$wpdb->query( "update wp_smackformrelation set thirdpartyid = {$id} where thirdparty='wpformpro' and shortcode ='{$shortcode}'" );
		}
		else
		{

			$wpdb->update( $wpdb->posts , array( 'post_content' => $contact_array , 'post_title' => $title ) , array( 'ID' => $checkid ) );
			$wpdb->update( $wpdb->postmeta , array( 'meta_value' => $meta ) , array( 'post_id' => $checkid , 'meta_key' => '_form'));
			$id = $checkid;
		}
		$thirdPartyPlugin = $thirdparty_form;
		$obj = new CallManageShortcodesCrmObj();
		$obj->contactFormRelation($shortcode,$id,$thirdPartyPlugin,$word_form_enable_fields);

	}

	public function contactFormRelation($shortcode,$id,$thirdparty,$enablefields)
	{
		global $wpdb;
		//TODO update tables
		$checkid = $wpdb->get_var( $wpdb->prepare( "select thirdpartyid from wp_smackformrelation where shortcode =%s" , $shortcode ) );
		if(empty($checkid))
		{
			$wpdb->insert( 'wp_smackformrelation' , array( 'shortcode' => $shortcode, 'thirdparty' => $thirdparty , 'thirdpartyid' => $id ) );
		}
		foreach($enablefields as $value)
		{
			$labl = $value->display_label;
			$labid = preg_replace('/[^a-zA-Z]+/','_',$labl);
			$labid = ltrim($labid,'_');
			$wpdb->insert( 'wp_smackthirdpartyformfieldrelation' , array( 'smackshortcodename' => $shortcode , 'smackfieldid' => $value->rel_id , 'smackfieldslable' => $value->display_label , 'thirdpartypluginname' => $thirdparty , 'thirdpartyformid' => $id , 'thirdpartyfieldids' => $labid ) );
		}
	}

	public function gravityFromTitle($title,$thirdparty,$shortcode)
	{
		global $wpdb;
		update_option('blogdescription','insidegravity');
		$formid = $wpdb->get_var("select id from ".$wpdb->prefix."gf_form order by id desc limit 1");
		$id = $formid + 1;

		$date =  date("Y-m-d h:i:sa");
		$checkid = $wpdb->get_results("select * from wp_smackformrelation where  shortcode='{$shortcode}' and thirdparty='gravityform'");
		if(empty($checkid))
		{
			$wpdb->query("insert into ".$wpdb->prefix."gf_form(id,title,date_created) values('{$id}','{$title}','{$date}')");

			$wpdb->query("insert into wp_smackformrelation (shortcode,thirdparty,thirdpartyid) values('{$shortcode}','{$thirdparty}','{$id}')");
			return $id;
		}
		else
		{
			$thirdparty_formid = $checkid[0]->thirdpartyid;
			$wpdb->update( $wpdb->prefix ."gf_form" , array( 'title' => $title ) , array( 'id' => $thirdparty_formid ) );	
			$id = $wpdb->get_var("select id from ". $wpdb->prefix ."gf_form where title='{$title}'");
			return $id;
		}

	}

	public function formatGravityFields($thirdparty_form,$shortcode,$id,$title)
	{
		global $wpdb;
		$gravity_array = $bool_choice = $bool_input = [];
		$word_form_enable_fields = $wpdb->get_results("select a.rel_id,a.wp_field_mandatory,a.custom_field_type,a.custom_field_values,a.display_label from wp_smackleadbulider_form_field_manager as a join wp_smackleadbulider_shortcode_manager as b where b.shortcode_id=a.shortcode_id and b.shortcode_name='{$shortcode}' and a.state=1 order by form_field_sequence");
		$checkid = $wpdb->get_results("select * from wp_smackformrelation where thirdpartyid='{$id}' and thirdparty='gravityform'");

		if(!empty($checkid))
		{
			$wpdb->query("delete from ". $wpdb->prefix ."gf_form_meta where form_id='{$id}'");
			$wpdb->query("delete from wp_smackthirdpartyformfieldrelation where thirdpartyformid='{$id}'");
			$gravity_array['title'] = $title;
			$gravity_array['description'] = $shortcode;
			$gravity_array['labelPlacement'] = 'top_label';
			$gravity_array['descriptionPlacement'] = 'below';
			$gravity_array['button'] = array('type' =>'text' , 'text'=>'Submit' ,'imageUrl'=>'');
			$i = 0; $j =0;
			$active_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
			foreach($word_form_enable_fields as $key=>$value)
			{
				$i++;
				$type = $value->custom_field_type;
				$labl = $value->display_label;
				$mandatory = $value->wp_field_mandatory;
				if($mandatory == 0)
				{
					$man ='';
				}
				else
				{
					$man ='1';
				}

				if( $active_crm == 'freshsales' && $labl == 'Time zone' )
				{
					// Skip for time zone
				}
				else
				{
					switch($type)
					{
					case 'phone':
					case 'currency':
					case 'text':
					case 'integer':
					case 'email':
					case 'url':
					case 'string':
					case '':
						$gravity_array['fields'][] =
							array(	'type'=>'text',
								'id'=> $i ,
								'label'=> $labl ,
								'adminLabel'=>'',
								'isRequired'=> $man ,
								'size'=>'medium' ,
								'errorMessage'=>'',
								'inputs'=>null,
								'labelPlacement'=>'',
								'descriptionPlacement'=>'',
								'subLabelPlacement'=>'',
								'placeholder'=>'',

							);
						break;
					case 'date':
						$gravity_array['fields'][] =
							array(
								'type' => 'date',
								'id' => $i ,
								'label' => $labl ,
								'adminLabel' => '' ,
								'isRequired' => $man ,
								'size' => 'medium' ,
								'dateType' => 'datepicker' ,
								'calendarIconType' => 'calander' ,
								'dateFormat' => 'ymd_dash' ,
								'pageNumber' => 1 ,
								'formId' => $id ,
							);
						break;
					case 'boolean':
						$bool_choice[] =array(
							'text' => $labl,
							'value' => $labl,
							'isSelected' =>''
						);
						$bool_input[] = array(
							'id' => 1,
							'label' => $labl,
							'name' => ''
						);

						$gravity_array['fields'][] =
							array(  'type'=>'checkbox',
								'id'=> $i ,
								'label'=> $labl ,
								'adminLabel'=>'',
								'isRequired'=> $man ,
								'size'=>'medium' ,
								'pageNumber' => 1,
								'choices' => $bool_choice,
								'inputs' => $bool_input,
								'formId'=>$id
							);
						unset( $bool_choice );
						unset( $bool_input );
						break;
					case 'picklist':
						$choice_array = array();
						$picklist = unserialize($value->custom_field_values);
						$j = 0;
						foreach($picklist as $val)
						{ $j++;
						if( $active_crm == 'freshsales' )
						{
							$choice_array[] = array (
								'text'      => $val['label'],
								'value'     => $val['id'],
								'isSelected'=> ''
							);
						}
						else {
							$choice_array[] = array (
								'text'      => $val['label'],
								'value'     => $val['label'],
								'isSelected'=> ''

							);
						}
						}
						$gravity_array['fields'][] =
							array( 'type' => 'select' ,
								'id' => $i ,
								'label' =>$labl ,
								'adminLabel' =>'' ,
								'isRequired' => $man ,
								'size' => 'medium',
								'pageNumber' =>1 ,
								'choices' => $choice_array,
								'formId' => $id
							);
						unset( $choice_array );
						break;

					}

					$gravity_array['id'] = $id;
					$gravity_array['useCurrentUserAsAuthor'] = '1';
					$gravity_array['useCurrentUserAsAuthor'] = 'true';
					$gravity_array['postContentTemplateEnabled'] = 'false';
					$gravity_array['postTitleTemplateEnabled'] = 'false';
					$gravity_array['postTitleTemplate'] = '';
					$gravity_array['postContentTemplate'] = '';
					$gravity_array['lastPageButton'] = null;
					$gravity_array['pagination'] = null;
					$gravity_array['firstPageCssClass'] = null;
					$grav_array = array();
					$grav_array = json_encode($gravity_array);
					//get third party plugin name
					$thirdpartypluginname = $thirdparty_form;
					$wpdb->query("insert into wp_smackthirdpartyformfieldrelation (smackshortcodename,smackfieldid,smackfieldslable,thirdpartypluginname,thirdpartyformid,thirdpartyfieldids) values('{$shortcode}','{$value->rel_id}','{$value->display_label}','{$thirdpartypluginname}','{$id}','{$labl}')");
				} // Skip fresh sales time zone
			}
			$confirm_array = array();
			$confirm_array[$id] = array(
										'id'          => $id ,
										'name'        => 'Default Confirmation',
										'isDefault'   => 1,
										'type'        => 'message' ,
										'message'     => 'Successfully Submitted !!' ,
										'url'         => '' ,
										'pageId'      => '' ,
										'queryString' => ''
			);
			$confirm = serialize($confirm_array);

			$notify_array = array();
			$notify_array[$id] = array(
										'id'     => $id ,
										'to'     => '{admin_email}' ,
										'name'   => 'Admin Notification' ,
										'event'  => 'form_submission' ,
										'toType' => 'email' ,
										'subject'=> 'New submission from {form_title}' ,
										'message'=> '{all_fields}'
			);
			$notify = serialize($notify_array);

			$wpdb->query("insert into ". $wpdb->prefix ."gf_form_meta (form_id,display_meta,confirmations,notifications) values('{$id}','{$grav_array}','{$confirm}','{$notify}')");

		}
	}

}
