<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
$load_script = "";
require_once( SM_LB_PRO_DIR."includes/Functions.php" );
$OverallFunctionsPROObj = new OverallFunctionsPRO();
$result = $OverallFunctionsPROObj->CheckFetchedDetails();
global $wpdb,$lb_admin;
require_once(SM_LB_PRO_DIR . "includes/lb-syncuser.php");
$url = SM_LB_PRO_DIR;
$active_plugin = get_option('WpLeadBuilderProActivatedPlugin');
if( !$result['status'] )
{
	echo "<div style='font-weight:bold; padding-left:20px; color:red;'> {$result['content']} </div>";
}
else
{
	$imagepath = $url.'assets/images/';
	if( isset($_POST["smack_{$active_plugin}_user_capture_settings"]) ) {
		$this->saveSettingArray($_POST , $data['HelperObj']);
	}

	$config = get_option("smack_{$active_plugin}_user_capture_settings");

	$args = array();
	$user_query = get_users();
	$user_fields = array( 'user_login' => __('Username' , 'wp-leads-builder-any-crm-pro' ) , 'role' => __('Role' , 'wp-leads-builder-any-crm-pro' ) , 'user_nicename' => __('Nicename' , 'wp-leads-builder-any-crm-pro' ) , 'user_email' => __('E-mail' , 'wp-leads-builder-any-crm-pro' ) , 'user_url' => __('Website' , 'wp-leads-builder-any-crm-pro' ) , 'display_name' => __('Display name publicly as' , 'wp-leads-builder-any-crm-pro' ) , 'nickname' => __('Nickname' , 'wp-leads-builder-any-crm-pro' ) , 'first_name' => __('First Name' , 'wp-leads-builder-any-crm-pro' ) , 'last_name' => __('Last Name' , 'wp-leads-builder-any-crm-pro' ) , 'description' => __('Biographical Info' , 'wp-leads-builder-any-crm-pro' ) , 'phone_number'=> __('Phone Number' , 'wp-leads-builder-any-crm-pro' ), 'mobile_number'=> __('Mobile Number' , 'wp-leads-builder-any-crm-pro' ));
	$wp_user_custom_plugin = get_option('custom_plugin');
	$wp_member_plugin = "wp-members/wp-members.php" ;
	$acf_plugin = "advanced-custom-fields/acf.php" ;
	$acfpro_plugin = "advanced-custom-fields-pro/acf.php";
	$memberpress_plugin = "memberpress/memberpress.php";

	//wp-members custom_fields
	$active_plugins = get_option( "active_plugins" );
	if( in_array($wp_member_plugin , $active_plugins) && $wp_user_custom_plugin == 'wp-members' )
	{
		$wp_member_array = get_option("wpmembers_fields");
		$option = $custom_field_array = array();
		$i=0;
		if( !empty( $wp_member_array )) {
			foreach( $wp_member_array as $key=>$option_name )
			{	$i++;
			$option[$i]['label'] = $option_name['1'];
			$option[$i]['name'] = $option_name['2'];
			}

			foreach( $option as $opt_ke=>$opt_val  )
			{
				if( !array_key_exists( $opt_val['name'] , $user_fields ) ){

					$custom_field_array[$opt_val['name']] =   $opt_val['label'] ;

				}
			}
			$user_fields = array_merge( $user_fields , $custom_field_array );
		}
	}
	//End of wp-members custom fields

	//Ultimate Member
	if( in_array("ultimate-member/ultimate-member.php" , $active_plugins) && $wp_user_custom_plugin == 'ultimate-member'  )
	{
		$um_array = get_option("um_fields");
		$option = $custom_field_array = array();
		$i=0;
		if( !empty( $um_array )) {
			foreach( $um_array as $key=>$option_name )
			{
				$i++;
				$option[$i]['label'] = $option_name['label'];
				$option[$i]['metakey'] = $option_name['metakey'];

			}
			foreach( $option as $opt_ke=>$opt_val  )
			{
				if( !array_key_exists( $opt_val['metakey'] , $user_fields ) ){
					$custom_field_array[$opt_val['metakey']] =   $opt_val['label'] ;
				}
			}
			$user_fields = array_merge( $user_fields , $custom_field_array );
		}
	}

	//ACF-custom fields
	if( in_array($acf_plugin , $active_plugins) && $wp_user_custom_plugin == 'acf' )
	{
		$acf_vals = array();
		$acf = $wpdb->get_results($wpdb->prepare( "select * from ".$wpdb->posts." where post_type=%s and post_status=%s" , 'acf' , 'publish'),ARRAY_A );
		$i = 0;
		if( !empty( $acf )) {
			foreach( $acf as $idkey=>$idval )
			{

				$id = $idval["ID"] ;
				$meta_fields = $wpdb->get_results( $wpdb->prepare("select meta_value from ".$wpdb->postmeta." where post_id=%d and meta_key like %s" , $id , 'field_%'),ARRAY_A );
				foreach( $meta_fields as $mkey=>$mvalue )
				{
					$meta_values = unserialize( $mvalue['meta_value'] ) ;
					$acf_vals[$i]['key']   = $meta_values['key'];
					$acf_vals[$i]['label'] = $meta_values['label'] ;
					$acf_vals[$i]['name']  = $meta_values['name'] ;
					$i++;
				}
			}
			foreach( $acf_vals as $acfkey => $acf_vl )
			{
				$acf_array[$acf_vl['name']] = $acf_vl['label'];
			}
			if( isset( $acf_array ) && !empty($acf_array))
			{
				$user_fields = array_merge( $user_fields , $acf_array );
			}
		}
	}

	//End of ACF-custom fields

	//ACF-custom fields
	if( in_array($acfpro_plugin , $active_plugins) && $wp_user_custom_plugin == 'acfpro' )
	{
		$acfpro_vals = array();
		$acfpro = $wpdb->get_results($wpdb->prepare( "select * from ".$wpdb->posts." where post_type=%s and post_status=%s" , 'acf-field' , 'publish'),ARRAY_A );
		$i = 0;
		if( !empty( $acfpro )) {
			foreach( $acfpro as $idkey=>$idval )
			{
				$acfpro_vals[$i]['key']   = $idval['post_excerpt'];
				$acfpro_vals[$i]['label'] = $idval['post_title'] ;
				$acfpro_vals[$i]['name']  = $idval['post_excerpt'] ;
				$i++;
			}
			foreach( $acfpro_vals as $acfkey => $acf_vl )
			{
				$acfpro_array[$acf_vl['name']] = $acf_vl['label'];
			}
			if( isset( $acfpro_array ) && !empty($acfpro_array))
			{
				$user_fields = array_merge( $user_fields , $acfpro_array );
			}
		}
	}

	//END ACF PRO


	//Memberpress support
	if( in_array($memberpress_plugin , $active_plugins) && $wp_user_custom_plugin == 'member-press' ) 
	{
		$member_press = get_option('mepr_options');
		$field_list = $member_press['custom_fields'];
		$i = 0;
		$mp_custom_field_list = array();
		if( !empty( $field_list )) {
			foreach( $field_list as $custom_fields )
			{
				$mp_custom_field_list[$i]['field_key'] = $custom_fields['field_key'] ;
				$mp_custom_field_list[$i]['field_name'] = $custom_fields['field_name'];
				$mp_custom_field_list[$i]['options'] = $custom_fields['options'];
				$i++;
			}
			foreach( $mp_custom_field_list as $mp_label => $mp_option )
			{
				$mp_fields[$mp_option['field_key']] = $mp_option['field_name'];
			}

			if( !empty( $mp_fields ) )
			{
				$user_fields = array_merge( $user_fields , $mp_fields );
			}
		}
	}
	$data = new SyncUserActions();
	$module = $data->ModuleMapping($user_fields, '', '');
	//End of Memberpress support
	$fields = json_decode( json_encode( $module['fields'] ) , true );
	$select_fields = "";
	$field_options = "<option value=''>--none--</option>";
	$javascript_mandatory_array = "[";

	foreach( $fields as $key => $field )
	{
		$field_options .= "<option value='{$field['field_name']}'> {$field['field_label']} </option>";
		if( $field['field_mandatory'] == 1 )
		{
			$javascript_mandatory_array .= "'{$field['field_name']}' ,";
		}
	}
	$javascript_mandatory_array = rtrim( $javascript_mandatory_array , ' ,' );
	$javascript_mandatory_array .= "]";

?>
<div class='mt30'>
	<div class='panel_config_mapping'>
		<div class='panel-body'>
			<div class='col-md-12 form-group'>
				<div class='leads-builder-heading'>
					<center>
						<h4 class="config_mapping_heading">
							<?php echo esc_html__("Map", "wp-leads-builder-any-crm-pro" )." {$module['module']} ".__("Fields" , "wp-leads-builder-any-crm-pro" ); ?>
						</h4>
					</center>
				</div>
			</div>
			<form name="mapuserfields" id="mapuserfields" action="" method="post">
			<div class="debug_form">
				<div class='form-group col-md-12'>
					<div class=col-md-5>
						<div class='leads-builder-sub-heading'>
						<p class="user_fields_head">
							<?php echo esc_html__("User Fields" , "wp-leads-builder-any-crm-pro" ); ?></p></div>
					</div>
					<div class='col-md-5'>
						<div class='leads-builder-sub-heading'>
						<p class="user_fields">
							<?php echo esc_html($module['module']." ".__("Fields", "wp-leads-builder-any-crm-pro" )); ?>
						</p>
						</div>
					</div>
				</div>

				<?php
					$i = 0;
					$new_user_fields = array();
					$new_user_fields = $user_fields;
					$new_user_fields1 = json_encode($new_user_fields);
					foreach( $user_fields as $fieldvalue => $field_label )
					{
						?>
						<div class='form-group col-md-12'>
							<div class='col-md-4'>
								<label class='leads-builder-label'>
									<?php echo esc_html__( $field_label , "wp-leads-builder-any-crm-pro" ); ?> </label>
								<input type="hidden" name="userfield[]" id="userfield[]" value="<?php echo $fieldvalue; ?>" />
							</div>
							<div class='col-md-4'>
								<select name="<?php echo esc_html($module['module']); ?>_module_field[]" class='form_control_select'>
									<?php
										echo $field_options;
									?>
								</select>
							</div>
						</div>
						<?php
						$mapped_fields = get_option( "User{$active_plugin}{$module['module']}ModuleMapping");
						$load_script .= "<script>
										document.getElementsByName('{$module['module']}_module_field[]')[{$i}].value = '{$mapped_fields[$fieldvalue]}';
										</script>";
						$i++;
					}
				?>

				<!-- </div> -->
				<input type="hidden" id="totaluserfields" name="totaluserfields" value="<?php echo $i; ?>">
				</div>
				<div class='form-group col-md-12'>
					<div class='pull-left'>
						<input type="button"
							value="<?php echo esc_attr__('Cancel ' , 'wp-leads-builder-any-crm-pro' );?>"
							class="cancel"
							onClick="window.location.href='<?php echo rtrim($module['siteurl'] , "/")."/wp-admin/admin.php?page=lb-usersync" ?>'" />
					</div>
					<div class='pull-right'>
						<input type="button" name="saveusermodulemap" value="Update" id="saveusermodulemap"
							class="update"
							onclick="validateMapFields( '<?php echo $module['siteurl']; ?>' , '<?php echo $module['module']; ?>_module_field' , 'userfield' , <?php echo $javascript_mandatory_array; ?> ); ">
					</div>
				</div>

				<!-- </div> -->
			</form>
			<div id="loading-image"
				style="display: none; background:url(<?php echo esc_url(WP_PLUGIN_URL);?>/wp-leads-builder-any-crm-pro/assets/images/ajax-loaders.gif) no-repeat center">
			</div>

		</div>
	</div>
</div>
<?php
	echo $load_script;
}