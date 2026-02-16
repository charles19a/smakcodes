<?php
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class thirdparty_mapping
{
	public function get_assignedto($shortcode_option)
	{
		//Assign Leads And Contacts to User
		$crm_users_list = get_option( 'crm_users' );
		$activated_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
		$assignedtouser_config = get_option( $shortcode_option );
		$assignedtouser_config_leads = isset($assignedtouser_config['thirdparty_assignedto']) ? $assignedtouser_config['thirdparty_assignedto'] : '';
		$Assigned_users_list = $crm_users_list[$activated_crm];
		switch( $activated_crm )
		{
			case 'wpzohopro':
			case 'wpzohopluspro':
				$html_leads = "";
				$html_leads = '<select class="form-control"  name="mapping_assignedto" id="mapping_assignedto">';
				$content_option_leads = "";
				if(isset($Assigned_users_list['user_name']))
					for($i = 0; $i < count($Assigned_users_list['user_name']) ; $i++)
					{
						$content_option_leads.="<option id='{$Assigned_users_list['user_name'][$i]}' value='{$Assigned_users_list['id'][$i]}'";
						if($Assigned_users_list['id'][$i] == $assignedtouser_config_leads )
						{
							$content_option_leads .=" selected";
						}

						$content_option_leads .=">{$Assigned_users_list['user_name'][$i]}</option>";
					}
				$content_option_leads .= "<option id='rr_existing_owner' value='Round Robin'";
				if( $assignedtouser_config_leads == 'Round Robin' )
				{
					$content_option_leads .= "selected";
				}
				$content_option_leads .= "> Round Robin</option>";
				$html_leads .= $content_option_leads;
				$html_leads .= "</select> <span style='padding-left:15px; color:red;' id='assignedto_status'></span>";

				return $html_leads;
				break;

			case 'joforce':
				$html_leads = "";
				$html_leads = '<select class="form-control" name="mapping_assignedto" id="mapping_assignedto" style="min-width:69px;">';
				$content_option_leads = "";

				if(isset($Assigned_users_list['user_name']))
					for($i = 0; $i < count($Assigned_users_list['user_name']) ; $i++)
					{
						$content_option_leads .="<option id='{$Assigned_users_list['id'][$i]}' value='{$Assigned_users_list['id'][$i]}'";
						if($Assigned_users_list['id'][$i] == $assignedtouser_config_leads)
						{
							$content_option_leads .=" selected";
						}

						$content_option_leads .=">{$Assigned_users_list['first_name'][$i]} {$Assigned_users_list['last_name'][$i]}</option>";
					}
				$content_option_leads .= "<option id='rr_existing_owner' value='Round Robin'";
				if( $assignedtouser_config_leads == 'Round Robin' )
				{
					$content_option_leads .= "selected";
				}
				$content_option_leads .= "> Round Robin</option>";
				$html_leads .= $content_option_leads;
				$html_leads .= "</select> <span style='padding-left:15px; color:red;' id='assignedto_status'></span>";
				return $html_leads;
				break;

			case 'wptigerpro':
				$html_leads = "";
				$html_leads = '<select class="form-control" name="mapping_assignedto" id="mapping_assignedto" style="min-width:69px;">';
				$content_option_leads = "";

				if(isset($Assigned_users_list['user_name']))
					for($i = 0; $i < count($Assigned_users_list['user_name']) ; $i++)
					{
						$content_option_leads .="<option id='{$Assigned_users_list['id'][$i]}' value='{$Assigned_users_list['id'][$i]}'";
						if($Assigned_users_list['id'][$i] == $assignedtouser_config_leads)
						{
							$content_option_leads .=" selected";
						}

						$content_option_leads .=">{$Assigned_users_list['first_name'][$i]} {$Assigned_users_list['last_name'][$i]}</option>";
					}
				$content_option_leads .= "<option id='rr_existing_owner' value='Round Robin'";
				if( $assignedtouser_config_leads == 'Round Robin' )
				{
					$content_option_leads .= "selected";
				}
				$content_option_leads .= "> Round Robin</option>";
				$html_leads .= $content_option_leads;
				$html_leads .= "</select> <span style='padding-left:15px; color:red;' id='assignedto_status'></span>";
				return $html_leads;
				break;

			case 'wpsugarpro':
			case 'wpsuitepro':
			case 'wpsalesforcepro':
				$html_leads = "";
				$html_leads = '<select class="form-control" name="mapping_assignedto" id="mapping_assignedto" style="min-width:69px;">';
				$content_option_leads = "";

				if(isset($Assigned_users_list['user_name']))
					for($i = 0; $i < count($Assigned_users_list['user_name']) ; $i++)
					{
						$content_option_leads .="<option id='{$Assigned_users_list['id'][$i]}' value='{$Assigned_users_list['id'][$i]}'";

						if($Assigned_users_list['id'][$i] == $assignedtouser_config_leads)
						{
							$content_option_leads .=" selected";

						}

						$content_option_leads .=">{$Assigned_users_list['first_name'][$i]} {$Assigned_users_list['last_name'][$i]}</option>";
					}
				$content_option_leads .= "<option id='rr_existing_owner' value='Round Robin'";
				if( $assignedtouser_config_leads == 'Round Robin' )
				{
					$content_option_leads .= "selected";
				}
				$content_option_leads .= "> Round Robin</option>";
				$html_leads .= $content_option_leads;
				$html_leads .= "</select> <span style='padding-left:15px; color:red;' id='assignedto_status'></span>";
				return $html_leads;
				break;

			case 'freshsales':
				$html_leads = "";
				$html_leads = '<select class="form-control" name="mapping_assignedto" id="mapping_assignedto">';
				$content_option_leads = "";
				if(isset($Assigned_users_list['last_name']))
					for($i = 0; $i < count($Assigned_users_list['last_name']) ; $i++)
					{
						$content_option_leads.="<option id='{$Assigned_users_list['last_name'][$i]}' value='{$Assigned_users_list['id'][$i]}'";
						if($Assigned_users_list['id'][$i] == $assignedtouser_config_leads )
						{
							$content_option_leads .=" selected";
						}

						$content_option_leads .=">{$Assigned_users_list['last_name'][$i]}</option>";
					}
				$content_option_leads .= "<option id='rr_existing_owner' value='Round Robin'";
				if( $assignedtouser_config_leads == 'Round Robin' )
				{
					$content_option_leads .= "selected";
				}
				$content_option_leads .= "> Round Robin</option>";
				$html_leads .= $content_option_leads;
				$html_leads .= "</select> <span style='padding-left:15px; color:red;' id='assignedto_status'></span>";

				return $html_leads;
				break;
		}	
	}

	public function get_mapping_config()
	{
		$OverallFunctionsPROObj = new OverallFunctionsPRO();
		$result = $OverallFunctionsPROObj->CheckFetchedDetails();
		$html_config = "";
		$html_config .= "<div>
						 <div class='form-group col-md-12 mt20'> <div class='exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label'> Choose Your Module </label></div>
					     <div class='exist_mapping col-md-4'> <select id='map_thirdparty_module' class='form-control' data-live-search='false' name='map_thirdparty_module'  onchange=''>";
		$html_config .= "<option value='none'>None</option>";

		if(!empty($result['leadsynced'])){
			$html_config .=	"<option value='Leads'>Leads</option>";
		}
		else{
			$html_config .=	"<option value='Leads' disabled>Leads</option>";
		}

		if(!empty($result['contactsynced'])){
			$html_config .=	"<option value='Contacts'>Contacts</option >";
		}
		else{
			$html_config .=	"<option value='Contacts' disabled>Contacts</option >";
		}

		$html_config .=	"</select></div></div><br><br>";

		$html_config .= "<div class='form-group col-md-12 mb50'> <div class='exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label'> Choose your Form Type </label></div>
						 <div class='exist_mapping col-md-4'> <select id='map_thirdparty_form' class='form-control' data-live-search='false' name='map_thirdparty_form' onchange='get_mapping_configuration(this.value)'>";
		$html_config .= "<option value='none'>None</option>
						 <option value='elementorform'>Elementor Form</option>
						 <option value='contactform'>Contact Form</option>
						 <option value='ninjaform '>Ninja Forms</option>
						 <option value='gravityform'>Gravity Forms</option>
						 <option value='calderaform'>Caldera Forms</option>
						 <option value='wpform'>WP Forms</option>
						 <option value='wpformpro'>WP Forms Pro</option>
						 </select></div></div></div>";
		return $html_config;

	}

	public function mapping_form_fields($tp_module , $thirdparty_plugin)
	{

		global $wpdb;
		$activated_crm = get_option('WpLeadBuilderProActivatedPlugin');
		$active_plugins = get_option( "active_plugins" );
		switch( $thirdparty_plugin )
		{
			case "gravityform":
				//Check Shortcode exist
				$grav_option_name = $activated_crm.'_wp_gravity';
				$save_form_id = array();
				$list_of_shortcodes = $wpdb->get_results( $wpdb->prepare( "select option_name from $wpdb->options where option_name like %s" , "$grav_option_name%" ) );
				if( !empty( $list_of_shortcodes ))
				{
					foreach( $list_of_shortcodes as $list_key => $list_val )
					{
						$shortcode_name = $list_val->option_name;
						$form_id = explode( $grav_option_name , $shortcode_name );
						$save_form_id[] = $form_id[1];
					}
				}
				$get_existing_forms = $wpdb->get_results( $wpdb->prepare( "select id,title from {$wpdb->prefix}gf_form where is_active=%d" , 1 ) );	
				$grav_form_titles = array();	
				$i = 0;
				foreach($get_existing_forms as $wp_grav_key =>  $wp_grav_title )
				{
					$i++;
					$grav_form_titles[$i]['title'] = $wp_grav_title->title;
					$grav_form_titles[$i]['id'] = $wp_grav_title->id;
				}
				$html = "";
				$html = "<div> <div class='form-group col-md-12'> <div class='col-md-6'><div id='inneroptions' class='leads-builder-heading'> Gravity Form</div> </div></div>
						<div class='form-group col-md-12'> <div class='exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label'> Choose Any One Of the Form </label></div>
						<div class='exist_mapping col-md-4'> <select class='form-control' id='thirdparty_form_title' name='thirdparty_form_title' onchange='get_thirdparty_title(this.value , \"gravityform\" , \"$tp_module\" )'>";
				$option_content = '';
				$option_content = "<option value='--None--'>--None--</option>";
				foreach( $grav_form_titles as $option_key => $option_value )
				{	
					$form_id = $option_value['id'];
					$title = $option_value['title'];
					if( !in_array( $form_id , $save_form_id))
					{
						$option_content .= "<option value='{$form_id}'>$title</option>";
					}
				}

				$html .= $option_content;
				$html .= "</select></div></div></div>";
				print_r( $html );
				die;
				break;

			case "ninjaform":

				//Check Shortcode exist
				$save_form_id = array();
				$ninja_option_name = $activated_crm."_wp_ninja";
				$list_of_shortcodes = $wpdb->get_results( $wpdb->prepare( "select option_name from $wpdb->options where option_name like %s" , "$ninja_option_name%" ) );
				if( !empty( $list_of_shortcodes ))
				{
					foreach( $list_of_shortcodes as $list_key => $list_val )
					{
						$shortcode_name = $list_val->option_name;
						$form_id = explode( $ninja_option_name , $shortcode_name );
						$save_form_id[] = $form_id[1];
					}
				}

				$get_existing_forms = $wpdb->get_results("select id,title from {$wpdb->prefix}nf3_forms");
				$ninja_form_titles = array();
				$i = 0;

				foreach($get_existing_forms as $wp_ninja_key =>  $wp_ninja_title )
				{
					$i++;
					$ninja_form_titles[$i]['title'] = $wp_ninja_title->title;
					$ninja_form_titles[$i]['id'] = $wp_ninja_title->id;
				}
				$html = "";
				$html = "<div> <div class='form-group col-md-12'> <div class='col-md-6'><div id='inneroptions' class='leads-builder-heading'> Ninja Form</div> </div></div>
						<div class='form-group col-md-12'> <div class='exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label'> Choose Any One Of the Form </label></div>
						<div class='exist_mapping col-md-4'> <select class='form-control' id='thirdparty_form_title' name='thirdparty_form_title' onchange='get_thirdparty_title(this.value , \"ninjaform\" , \"$tp_module\")'>";
				$option_content = '';
				$option_content = "<option value='--None--'>--None--</option>";
				foreach( $ninja_form_titles as $option_key => $option_value )
				{	
					$form_id = $option_value['id'];
					$title = $option_value['title'];
					if( !in_array( $form_id , $save_form_id ))
					{
						$option_content .= "<option value='{$form_id}'>$title</option>";
					}
				}

				$html .= $option_content;
				$html .= "</select></div></div></div>";
				print_r($html);
				die;
				break;

			case 'elementorform';
				$form_id = '';

				$save_form_id = array();
				$elementorOpName = $activated_crm."_wp_elementor";
				$listOfForms = $wpdb->get_results("select * from $wpdb->postmeta where meta_key ='__elementor_forms_snapshot'",ARRAY_A);
				if( !empty( $listOfForms ))
				{
					foreach($listOfForms as $eleKey =>  $eleVal )
					{					
						$formData = $eleVal['meta_value'];
						$decodedData = json_decode($formData);
						$elementorFormTitles[$i]['title'] = $decodedData[0]->name;
						$elementorFormTitles[$i]['id'] = $eleVal['post_id'];
						$i++;
					}
				}

				$html = "";
				$html = "<div> <div class='form-group col-md-12'><div class='col-md-6'> <div id='inneroptions' class='leads-builder-heading'> Elementor Form</div> </div></div>
						<div class='form-group col-md-12'> <div class='exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label'> Choose Any One Of the Form</label></div>
						<div class='exist_mapping col-md-4'> <select id='thirdparty_form_title' class='form-control' data-live-search='false' name='thirdparty_form_title' onchange='get_thirdparty_title(this.value , \"elementorform\" , \"$tp_module\")'>";
				$option_content = '';
				$option_content = "<option value='--None--'>--None--</option>";
				foreach( $elementorFormTitles as $optionKey => $optionValue )
				{       
					$form_id = $optionValue['id'];
					$title = $optionValue['title'];

					if( !in_array( $form_id , $save_form_id ))
					{
						$option_content .= "<option value='{$form_id}'>$title</option>";
					}
				}

				$html .= $option_content;
				$html .= "</select></div></div></div>";
				print_r($html);
				die;

			case 'contactform';
				//Check Shortcode exist
				$save_form_id = array();
				$contact_option_name = $activated_crm."_wp_contact";
				$list_of_shortcodes = $wpdb->get_results( $wpdb->prepare( "select option_name from $wpdb->options where option_name like %s" , "$contact_option_name%") );
				if( !empty( $list_of_shortcodes ))
				{
					foreach( $list_of_shortcodes as $list_key => $list_val )
					{
						$shortcode_name = $list_val->option_name;
						$form_id = explode( $contact_option_name , $shortcode_name );
						$save_form_id[] = $form_id[1];
					}
				}

				$get_existing_forms = $wpdb->get_results( $wpdb->prepare( "select ID,post_title from $wpdb->posts where post_type=%s" , 'wpcf7_contact_form' ) );
				$cont_form_titles = array();
				$i = 0;
				foreach($get_existing_forms as $wp_cont_key =>  $wp_cont_title )
				{
					$i++;
					$cont_form_titles[$i]['title'] = $wp_cont_title->post_title;
					$cont_form_titles[$i]['id'] = $wp_cont_title->ID;


				}
				$html = "";
				$html = "<div> <div class='form-group col-md-12'><div class='col-md-6'> <div id='inneroptions' class='leads-builder-heading'> Contact Form7</div> </div></div>
						<div class='form-group col-md-12'> <div class='exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label'> Choose Any One Of the Form</label></div>
						<div class='exist_mapping col-md-4'> <select id='thirdparty_form_title' class='form-control' data-live-search='false' name='thirdparty_form_title' onchange='get_thirdparty_title(this.value , \"contactform\" , \"$tp_module\")'>";
				$option_content = '';
				$option_content = "<option value='--None--'>--None--</option>";
				foreach( $cont_form_titles as $option_key => $option_value )
				{      
					$form_id = $option_value['id'];
					$title = $option_value['title'];
					if( !in_array( $form_id , $save_form_id ))
					{
						$option_content .= "<option value='{$form_id}'>$title</option>";
					}
				}
				$html .= $option_content;
				$html .= "</select></div></div></div>";
				print_r($html);
				die;
				break;

			case 'wpform';
				$save_form_id = array();
				$wpform_option_name = $activated_crm."_wp_wpform_lite";
				$list_of_shortcodes = $wpdb->get_results( $wpdb->prepare( "select option_name from $wpdb->options where option_name like %s" , "$wpform_option_name%") );
				if( !empty( $list_of_shortcodes ))
				{
					foreach( $list_of_shortcodes as $list_key => $list_val )
					{
						$shortcode_name = $list_val->option_name;
						$form_id = explode( $wpform_option_name , $shortcode_name );
						$save_form_id[] = $form_id[1];
					}
				}
				if(in_array( "wpforms-lite/wpforms.php" , $active_plugins)){
					$get_existing_forms = $wpdb->get_results( $wpdb->prepare( "select ID,post_title from $wpdb->posts where post_type=%s" , 'wpforms' ) );
				}
				$wpf_form_titles = array();
				$i = 0;
				foreach($get_existing_forms as $wp_wpf_key =>  $wp_wpf_title )
				{
					$i++;
					$wpf_form_titles[$i]['title'] = $wp_wpf_title->post_title;
					$wpf_form_titles[$i]['id'] = $wp_wpf_title->ID;
				}
				$html = "";
				$html = "<div> <div class='form-group col-md-12'><div class='col-md-6'> <div id='inneroptions' class='leads-builder-heading'> WP Forms</div> </div></div>
						<div class='form-group col-md-12'> <div class='exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label'> Choose Any One Of the Form</label></div>
						<div class='exist_mapping col-md-4'> <select id='thirdparty_form_title' class='form-control' data-live-search='false' name='thirdparty_form_title' onchange='get_thirdparty_title(this.value , \"wpform\" , \"$tp_module\")'>";
				$option_content = '';
				$option_content = "<option value='--None--'>--None--</option>";
				foreach( $wpf_form_titles as $option_key => $option_value )
				{    
					$form_id = $option_value['id'];
					$title = $option_value['title'];
					if( !in_array( $form_id , $save_form_id ))
					{
						$option_content .= "<option value='{$form_id}'>$title</option>";
					}
				}

				$html .= $option_content;
				$html .= "</select></div></div></div>";
				print_r($html);
				die;
				break;

			case 'wpformpro';
				$save_form_id = array();
				$wpformpro_option_name = $activated_crm."_wp_wpform_pro";
				$list_of_shortcodes = $wpdb->get_results( $wpdb->prepare( "select option_name from $wpdb->options where option_name like %s" , "$wpformpro_option_name%") );
				if( !empty( $list_of_shortcodes ))
				{
					foreach( $list_of_shortcodes as $list_key => $list_val )
					{
						$shortcode_name = $list_val->option_name;
						$form_id = explode( $wpformpro_option_name , $shortcode_name );
						$save_form_id[] = $form_id[1];
					}
				}
				if(in_array( "wpforms/wpforms.php" , $active_plugins)){
					$get_existing_forms = $wpdb->get_results( $wpdb->prepare( "select ID,post_title from $wpdb->posts where post_type=%s" , 'wpforms' ) );
				}
				$wpf_form_titles = array();
				$i = 0;
				foreach($get_existing_forms as $wp_wpf_key =>  $wp_wpf_title )
				{
					$i++;
					$wpf_form_titles[$i]['title'] = $wp_wpf_title->post_title;
					$wpf_form_titles[$i]['id'] = $wp_wpf_title->ID;
				}
				$html = "";
				$html = "<div> <div class='form-group col-md-12'><div class='col-md-6'> <div id='inneroptions' class='leads-builder-heading'> WP Forms Pro</div> </div></div>
						<div class='form-group col-md-12'> <div class='exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label'> Choose Any One Of the Form</label></div>
						<div class='exist_mapping col-md-4'> <select id='thirdparty_form_title' class='form-control' data-live-search='false' name='thirdparty_form_title' onchange='get_thirdparty_title(this.value , \"wpformpro\" , \"$tp_module\")'>";
				$option_content = '';
				$option_content = "<option value='--None--'>--None--</option>";
				foreach( $wpf_form_titles as $option_key => $option_value )
				{       
					$form_id = $option_value['id'];
					$title = $option_value['title'];
					if( !in_array( $form_id , $save_form_id ))
					{
						$option_content .= "<option value='{$form_id}'>$title</option>";
					}
				}

				$html .= $option_content;
				$html .= "</select></div></div></div>";
				print_r($html);
				die;
				break;

			case "calderaform":

				//Check Shortcode exist
				$save_form_id = array();
				$caldera_option_name = $activated_crm."_wp_caldera";
				$list_of_shortcodes = $wpdb->get_results( $wpdb->prepare( "select option_name from $wpdb->options where option_name like %s" , "$caldera_option_name%" ) );
				if( !empty( $list_of_shortcodes ))
				{
					foreach( $list_of_shortcodes as $list_key => $list_val )
					{
						$shortcode_name = $list_val->option_name;
						$form_id = explode( $caldera_option_name , $shortcode_name );
						$save_form_id[] = $form_id[1];
					}
				}

				$get_existing_forms = $wpdb->get_results("SELECT config FROM {$wpdb->prefix}cf_forms WHERE type = 'primary' ", ARRAY_A);

				$caldera_form_titles = array();
				$i = 0;

				foreach($get_existing_forms as $wp_caldera_key =>  $wp_caldera_value )
				{	
					$caldera_config = unserialize($wp_caldera_value['config']);
					$caldera_form_titles[$i]['title'] = $caldera_config['name'];
					$caldera_form_titles[$i]['id'] = $caldera_config['ID'];
					$i++;
				}
				$html = "";
				$html = "<div> <div class='form-group col-md-12'> <div class='col-md-6'><div id='inneroptions' class='leads-builder-heading'> Caldera Form</div> </div></div>
					<div class='form-group col-md-12'> <div class='exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label'> Choose Any One Of the Form </label></div>
					<div class='exist_mapping col-md-4'> <select class='form-control' id='thirdparty_form_title' name='thirdparty_form_title' onchange='get_thirdparty_title(this.value , \"calderaform\" , \"$tp_module\")'>";
				$option_content = '';
				$option_content = "<option value='--None--'>--None--</option>";

				foreach( $caldera_form_titles as $option_key => $option_value )
				{	
					$form_id = $option_value['id'];
					$title = $option_value['title'];
					if( !in_array( $form_id , $save_form_id ))
					{
						$option_content .= "<option value='{$form_id}'>$title</option>";
					}
				}

				$html .= $option_content;
				$html .= "</select></div></div></div>";
				print_r($html);
				die;
				break;

			default:
				$html = "";
				$html .= "<span style='color:red;font-size:16px;margin-left:12%;'> Please choose any form type above  </span>";
				print_r($html);
				die;
				break;                       	

		}	
	}

	public function get_thirdparty_form_fields()
	{
		global $wpdb;
		$activated_crm = get_option('WpLeadBuilderProActivatedPlugin');
		$third_module = sanitize_text_field($_REQUEST['third_module']);
		$thirdparty_form_name  = sanitize_text_field( $_REQUEST['form_title'] );
		$thirdparty_plugin = sanitize_text_field( $_REQUEST['third_plugin'] );
		switch( $thirdparty_plugin )
		{
		case 'gravityform':
			$shortcode = $activated_crm."_wp_gravity".$thirdparty_form_name;
			$config = get_option( $shortcode );
			$gravity_config = isset($config['fields']) ? $config['fields'] : '';
			$assigned_to_user = $this->get_assignedto($shortcode);
			$map_options = '';
			$map_options .= "<div>	
							 <div class='form-group col-md-12'> <div class='exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label'> Duplicate Handling</label> </div> 
							 <div class='exist_mapping col-md-4'> <select class='form-control' id='duplicate_handling'><option value='skip'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip' )
			{
				$map_options .= "selected=selected";	
			}
			$map_options .= "> Skip</option>";
			if($activated_crm=="mailchimp"){

			}
			else{
				$map_options.="<option value='skip_both'";
				if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip_both' )
				{
					$map_options .= "selected=selected";
				}
				$map_options .= "></option>";
			}

			$map_options.="<option value='update'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'update' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .= ">Update</option> <option value='create'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'create' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .="> Create </option></select></div></div></div>";
			$map_options .= "<div><div class='form-group col-md-12'> <div class='assign_leads col-md-6'> <label id='innertext' class='leads-builder-label' >";

			if( $third_module == "Leads")
			{
				$map_options .= "Lead Owner";
				$map_options .= "</label></div><div class='exist_mapping col-md-4'> $assigned_to_user</div></div><div class='form-group col-md-12'><div col-md-6>";
			} else if( $third_module == "Contacts" )
			{
				$map_options .= "Contact Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
			}
			$map_options .= "</label></div></div>";

			if( $thirdparty_form_name != "--None--" )
			{
				$get_json_array = $wpdb->get_results( $wpdb->prepare( "select display_meta from {$wpdb->prefix}gf_form_meta where form_id=%d" , $thirdparty_form_name ) );
				$gravity_arr = json_decode( $get_json_array[0]->display_meta );	
				$gravity_fields = $gravity_arr->fields;
				foreach( $gravity_fields as $grav_key => $grav_value )
				{
					$grav_field_id = $grav_value->id;
					$gravity_form_labels[$grav_field_id] = $grav_value->label;
				}

				$CaptureDataObj = new CaptureData();
				$crm_fields = $CaptureDataObj->get_crmfields_by_settings( $activated_crm , $third_module );
				$j = 1;

				$js_mandatory_fields = array();
				$js_mandatory_fields_hidden = array();
				foreach( $crm_fields as $crm_field_key => $crm_fields_vals ) {
					$crm_field_labels[$crm_fields_vals->field_label] = $crm_fields_vals->field_name;
					if( $crm_fields_vals->field_mandatory == 1 )
					{
						if(!in_array($crm_fields_vals->field_name, $js_mandatory_fields))
							$js_mandatory_fields[] = $crm_fields_vals->field_label;
						$js_mandatory_fields_hidden[] = $crm_fields_vals->field_name;
					}
					$j++;
				}
				$js_mandatory_array = json_encode($js_mandatory_fields);
				$js_mandatory_array_hidden = json_encode($js_mandatory_fields_hidden);
				$crm_field_options = '';
				$crm_field_options .= "<option>--None--</option>";
				foreach( $crm_field_labels as $field_key => $crm_field_label )
				{
					$crm_field_options .= "<option value='{$crm_field_label}'> $crm_field_label</option>";
				}  

				$fields_html = '';
				$fields_html .= "<div><div class='form-group col-md-12'><div class='exist_mapping col-md-6'> <div class='leads-builder-heading'>GravityForm Fields</div></div><div class='exist_mapping col-md-4'><div class='leads-builder-heading'> CRM Fields</div></div></div> ";
				$i = 1;	
				foreach( $gravity_form_labels as $grav_id => $grav_label)
				{
					$fields_html .= "<div class='form-group col-md-12'>
									 <div class='col-md-6'><label class='leads-builder-label'> $grav_label </label></div>
									 <input type='hidden' name='thirdpartyfield_$i' id='thirdpartyfield_$i' value='$grav_label' />";
					$fields_html .= "<div class='exist_mapping col-md-4'><select class='form-control'  name='crm_fields_$i' id='crm_fields_$i' >";
					$crm_field_options = '';
					$crm_field_options .= "<option>--None--</option>";
					foreach( $crm_field_labels as $field_key => $crm_field_label ) // Prepare crm fileds drop down
					{

						$crm_field_options .= "<option value='{$crm_field_label}'";
						if(is_array( $gravity_config)) {
							foreach( $gravity_config as $config_key => $config_val ) // configuration
							{
								if( $grav_label == $config_key && $crm_field_label == $config_val ) //match label and fieldname
								{
									$crm_field_options .= "selected=selected";//select when the configuration exist
								}
							}
						}
						$crm_field_options .= "> $field_key</option>";
					}
					$fields_html .= $crm_field_options;			
					$fields_html .=	"</select>
						</div>
						</div>";
					$i++;
				}
			}
			else
			{
				$fields_html = "";
				$map_options = "";
				$fields_html .= "<span style='color:red;font-size:18px;margin-left:23%;'>Please choose any form</span>";
			}
			$fields_html .= "<input type='hidden' value='$i' id='total_field_count'>";
			$fields_html .= "<input type='hidden' value='$third_module' id='module'>";
			$fields_html .= "<input type='hidden' value='$activated_crm' id='active_crm'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_form_name' id='form_name'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_plugin' id='thirdparty_plugin'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array' id='crm_mandatory_fields'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array_hidden' id='crm_mandatory_fields_hidden'>";
			$fields_html .= "</div>";
			$html_data_array = array();
			$html_data_array['map_options'] = $map_options;
			$html_data_array['fields_html'] = $fields_html;
			print_r( json_encode($html_data_array) );
			die;
			break;

		case 'ninjaform':
			$shortcode = $activated_crm."_wp_ninja".$thirdparty_form_name;
			$config = get_option( $shortcode );
			$ninja_config = isset($config['fields']) ? $config['fields'] : '';
			$assigned_to_user = $this->get_assignedto($shortcode);
			$map_options = '';
			$map_options .= "<div>	
							 <div class='form-group col-md-12'> <div class='exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label'> Duplicate Handling</label> </div> 
							 <div class='exist_mapping col-md-4'> <select class='form-control' id='duplicate_handling'><option value='skip'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip' )
			{
				$map_options .= "selected=selected";	
			}
			$map_options .= "> Skip</option>";
			if($activated_crm=="mailchimp"){

			}
			else{
				$map_options.="	<option value='skip_both'";
				if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip_both' )
				{
					$map_options .= "selected=selected";
				}
				$map_options .= "> Skip if already a Contact or Lead</option>";
			}
			$map_options.="	<option value='update'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'update' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .= ">Update</option> <option value='create'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'create' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .="> Create </option></select></div></div></div>";

			$map_options .= "<div><div class='form-group col-md-12'> <div class='assign_leads exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label' >";

			if( $third_module == "Leads")
			{
				$map_options .= "Lead Owner";
				$map_options .= "</label></div><div class='exist_mapping col-md-4'> $assigned_to_user</div></div><div class='form-group col-md-12'><div col-md-6>";
			} else if( $third_module == "Contacts" )
			{
				$map_options .= "Contact Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</td>";
			}
			else if($third_module=="Members")
			{
				$map_options .= "Member Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</td>";
			}
			$map_options .= "</label></div></div>";


			if( $thirdparty_form_name != "--None--" )
			{		
				$get_json_array = $wpdb->get_results( $wpdb->prepare( "select parent_id,label from {$wpdb->prefix}nf3_fields where parent_id=%d and type !=%s" , $thirdparty_form_name , 'submit' ) );
				$i = 1;
				$ninja_form_labels = array();
				foreach( $get_json_array as $ninja_key => $ninja_data )
				{
					$ninja_form_labels[$i] = $ninja_data->label; 	
					$i++;
				}

				$CaptureDataObj = new CaptureData();
				$crm_fields = $CaptureDataObj->get_crmfields_by_settings( $activated_crm , $third_module );
				$j = 1;

				$js_mandatory_fields = array();
				$js_mandatory_fields_hidden = array();
				foreach( $crm_fields as $crm_field_key => $crm_fields_vals ) {
					$crm_field_labels[$crm_fields_vals->field_label] = $crm_fields_vals->field_name;
					if( $crm_fields_vals->field_mandatory == 1 )
					{
						if(!in_array($crm_fields_vals->field_name, $js_mandatory_fields))
							$js_mandatory_fields[] = $crm_fields_vals->field_label;
						$js_mandatory_fields_hidden[] = $crm_fields_vals->field_name;
					}
					$j++;
				}
				$js_mandatory_array = json_encode($js_mandatory_fields);
				$js_mandatory_array_hidden = json_encode($js_mandatory_fields_hidden);
				$crm_field_options = '';
				$crm_field_options .= "<option>--None--</option>";
				foreach( $crm_field_labels as $field_key => $crm_field_label )
				{

					$crm_field_options .= "<option value='{$crm_field_label}'> $crm_field_label</option>";
				}  


				$fields_html = '';
				$fields_html .= "<div><div class='form-group col-md-12'><div class='exist_mapping col-md-6'> <div class='leads-builder-heading'>NinjaForm Fields</div></div><div class='exist_mapping col-md-4'> <div class='leads-builder-heading'>CRM Fields</div></div></div> ";
				$i = 1;	
				foreach( $ninja_form_labels as $nin_id => $nin_label)
				{
					$fields_html .= "<div class='form-group col-md-12'>
								     <div class='col-md-6'><label class='leads-builder-label'> $nin_label </label></div>
									 <input type='hidden' name='thirdpartyfield_$i' id='thirdpartyfield_$i' value='$nin_label' />";
					$fields_html .= "<div class='exist_mapping col-md-4'><select class='form-control'  name='crm_fields_$i' id='crm_fields_$i' >";
					$crm_field_options = '';
					$crm_field_options .= "<option>--None--</option>";
					foreach( $crm_field_labels as $field_key => $crm_field_label ) // Prepare crm fileds drop down
					{

						$crm_field_options .= "<option value='{$crm_field_label}'";
						if(!empty( $ninja_config)) {
							foreach( $ninja_config as $config_key => $config_val ) // configuration
							{
								if( $nin_label == $config_key && $crm_field_label == $config_val ) //match label and fieldname
								{
									$crm_field_options .= "selected=selected";//select when the configuration exist
								}
							}
						}
						$crm_field_options .= "> $field_key</option>";
					}
					$fields_html .= $crm_field_options;			
					$fields_html .=	"</select>
						</div>
						</div>";
					$i++;
				}
			}
			else
			{
				$fields_html = "";
				$map_options = "";
				$fields_html .= "<span style='color:red;font-size:18px;margin-left:23%;'>Please choose any form</span>";
			}
			$fields_html .= "<input type='hidden' value='$i' id='total_field_count'>";
			$fields_html .= "<input type='hidden' value='$third_module' id='module'>";
			$fields_html .= "<input type='hidden' value='$activated_crm' id='active_crm'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_form_name' id='form_name'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_plugin' id='thirdparty_plugin'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array' id='crm_mandatory_fields'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array_hidden' id='crm_mandatory_fields_hidden'>";
			$fields_html .= "</div>";
			$html_data_array = array();
			$html_data_array['map_options'] = $map_options;
			$html_data_array['fields_html'] = $fields_html;
			print_r( json_encode($html_data_array) );
			die;
			break;

		case 'elementorform':
			$shortcode = $activated_crm."_wp_elementor".$thirdparty_form_name;
			$config = get_option( $shortcode );

			$ifconfig = isset($config['fields']) ? $config['fields'] : '';
			$elementorConfig = $ifconfig;
			$assigned_to_user = $this->get_assignedto($shortcode);
			$map_options = '';
			$map_options .= "<div>	
							 <div class='form-group col-md-12'> <div class='exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label'> Duplicate Handling</label> </div> 
							 <div class='exist_mapping col-md-4'> <select class='form-control' id='duplicate_handling'><option value='skip'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip' )
			{
				$map_options .= "selected=selected";	
			}
			$map_options .= " > Skip</option>'";

			if($activated_crm=="mailchimp"){

			}else{
				$map_options.="<option value='skip_both'";
				if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip_both' )
				{
					$map_options .= "selected=selected";
				}

				$map_options .= "> Skip if already a Contact or Lead</option>";
			}
			$map_options.="	<option value='update'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'update' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .= ">Update</option> <option value='create'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'create' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .="> Create </option></select></div></div></div>";


			$map_options .= "<div><div class='form-group col-md-12'> <div class='assign_leads exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label' >";

			if( $third_module == "Leads")
			{
				$map_options .= "Lead Owner";
				$map_options .= "</label></div><div class='exist_mapping col-md-4'> $assigned_to_user</div></div><div class='form-group col-md-12'><div col-md-6>";
			} else if( $third_module == "Contacts" )
			{
				$map_options .= "Contact Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
			}
			else if($third_module=="Members")
			{
				$map_options .= "Member Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
			}
			$map_options .= "</label></div></div>";		

			if( $thirdparty_form_name != '--None--' )
			{
				//$getFormData = $wpdb->get_results("select meta_value from $wpdb->postmeta where meta_key ='__elementor_forms_snapshot' and post_id = $thirdparty_form_name",ARRAY_A);				
				$getFormData = $wpdb->get_results(
					$wpdb->prepare("
						SELECT meta_value 
						FROM {$wpdb->postmeta} 
						WHERE meta_key = '__elementor_forms_snapshot' 
						AND post_id = %d
					", $thirdparty_form_name),
					ARRAY_A
				);
				$formData = $getFormData[0]['meta_value'];
				$decodedData = json_decode($formData);
				$formFields = $decodedData[0]->fields;

				foreach($formFields as $elVal){

					$decodedFormFields = json_decode(json_encode($elVal), true);
					$elementor_form_labels[$decodedFormFields['id']] = $decodedFormFields['label']; 
				}

				$CaptureDataObj = new CaptureData();
				$crm_fields = $CaptureDataObj->get_crmfields_by_settings( $activated_crm , $third_module );
				$j = 1;

				$js_mandatory_fields = array();
				$js_mandatory_fields_hidden = array();

				foreach( $crm_fields as $crm_field_key => $crm_fields_vals ) {
					$crm_field_labels[$crm_fields_vals->field_label] = $crm_fields_vals->field_name;
					if( $crm_fields_vals->field_mandatory == 1 )
					{
						if(!in_array($crm_fields_vals->field_name, $js_mandatory_fields))
							$js_mandatory_fields[] = $crm_fields_vals->field_label;
						$js_mandatory_fields_hidden[] = $crm_fields_vals->field_name;
					}
					$j++;
				}
				$js_mandatory_array = json_encode($js_mandatory_fields);
				$js_mandatory_array_hidden = json_encode($js_mandatory_fields_hidden);
				$crm_field_options = '';
				$crm_field_options .= "<option>--None--</option>";
				foreach( $crm_field_labels as $field_key => $crm_field_label )
				{
					$crm_field_options .= "<option value='{$crm_field_label}'> $crm_field_label</option>";
				}  

				$fields_html = '';
				$fields_html .= "<div><div class='form-group col-md-12'><div class='exist_mapping col-md-6'> <div class='leads-builder-heading'>ElementorForm Fields</div></div><div class='exist_mapping col-md-4'> <div class='leads-builder-heading'>CRM Fields</div></div></div> ";
				$i = 1;	

				foreach( $elementor_form_labels as $ele_id => $ele_label)
				{
					$fields_html .= "<div class='form-group col-md-12'>
									 <div class='col-md-6'><label class='leads-builder-label'> $ele_label </label></div>
									 <input type='hidden' name='thirdpartyfield_$i' id='thirdpartyfield_$i' value='$ele_id' />";
					$fields_html .= "<div class='col-md-4'><select class='form-control'  name='crm_fields_$i' id='crm_fields_$i' >";
					$crm_field_options = '';
					$crm_field_options .= "<option>--None--</option>";
					foreach( $crm_field_labels as $field_key => $crm_field_label ) // Prepare crm fileds drop down
					{
						$crm_field_options .= "<option value='{$crm_field_label}'";

						if(is_array($elementorConfig )) {
							foreach( $elementorConfig as $conKey => $conVal ) // configuration
							{
								if( $ele_label == $conKey && $crm_field_label == $conVal ) //match label and fieldname
								{
									$crm_field_options .= "selected=selected";//select when the configuration exist
								}
							}
						}
						$crm_field_options .= "> $field_key</option>";
					}
					$fields_html .= $crm_field_options;			
					$fields_html .=	"</select>
						</div>
						</div>";
					$i++;
				}
			}
			else
			{
				$fields_html = "";
				$map_options = "";
				$fields_html .= "<span style='color:red;font-size:18px;margin-left:23%;'>Please choose any form</span>";
			}
			$fields_html .= "<input type='hidden' value='$i' id='total_field_count'>";
			$fields_html .= "<input type='hidden' value='$third_module' id='module'>";
			$fields_html .= "<input type='hidden' value='$activated_crm' id='active_crm'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_form_name' id='form_name'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_plugin' id='thirdparty_plugin'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array' id='crm_mandatory_fields'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array_hidden' id='crm_mandatory_fields_hidden'>";
			$fields_html .= "</div>";
			$html_data_array = array();
			$html_data_array['map_options'] = $map_options;
			$html_data_array['fields_html'] = $fields_html;
			print_r( json_encode($html_data_array) );
			die;
			break;

		case 'contactform':
			$shortcode = $activated_crm."_wp_contact".$thirdparty_form_name;
			$config = get_option( $shortcode );
			$ifconfig = isset($config['fields']) ? $config['fields'] : '';
			$contact_config = $ifconfig;
			$assigned_to_user = $this->get_assignedto($shortcode);
			$map_options = '';
			$map_options .= "<div>	
							 <div class='form-group col-md-12'> <div class='exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label'> Duplicate Handling</label> </div> 
							 <div class='exist_mapping col-md-4'> <select class='form-control' id='duplicate_handling'><option value='skip'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip' )
			{
				$map_options .= "selected=selected";	
			}
			$map_options .= " > Skip</option>'";

			if($activated_crm=="mailchimp"){

			}else{
				$map_options.="<option value='skip_both'";
				if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip_both' )
				{
					$map_options .= "selected=selected";
				}

				$map_options .= "> Skip if already a Contact or Lead</option>";
			}
			$map_options.="	<option value='update'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'update' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .= ">Update</option> <option value='create'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'create' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .="> Create </option></select></div></div></div>";


			$map_options .= "<div><div class='form-group col-md-12'> <div class='assign_leads exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label' >";

			if( $third_module == "Leads")
			{
				$map_options .= "Lead Owner";
				$map_options .= "</label></div><div class='exist_mapping col-md-4'> $assigned_to_user</div></div><div class='form-group col-md-12'><div col-md-6>";
			} else if( $third_module == "Contacts" )
			{
				$map_options .= "Contact Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
			}
			else if($third_module=="Members")
			{
				$map_options .= "Member Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
			}
			$map_options .= "</label></div></div>";		

			if( $thirdparty_form_name != '--None--' )
			{
				$get_json_array = $wpdb->get_results( $wpdb->prepare( "select ID,post_content from $wpdb->posts where ID=%d" , $thirdparty_form_name ) );
				$contact_post_content = $get_json_array[0]->post_content;

				$fields = $this->getTextBetweenBrackets( $contact_post_content );

				$i = 0;
				foreach( $fields as $cfkey => $cfval )
				{
					if( preg_match( '/\s/' , $cfval ) )
					{
						$final_arr = explode( ' ' , $cfval );
						$contact_form_labels[$i] = rtrim( $final_arr[1] , ']' );
						$i++;
					}
				}
				$CaptureDataObj = new CaptureData();
				$crm_fields = $CaptureDataObj->get_crmfields_by_settings( $activated_crm , $third_module );
				$j = 1;

				$js_mandatory_fields = array();
				$js_mandatory_fields_hidden = array();
				foreach( $crm_fields as $crm_field_key => $crm_fields_vals ) {
					$crm_field_labels[$crm_fields_vals->field_label] = $crm_fields_vals->field_name;
					if( $crm_fields_vals->field_mandatory == 1 )
					{
						if(!in_array($crm_fields_vals->field_name, $js_mandatory_fields))
							$js_mandatory_fields[] = $crm_fields_vals->field_label;
						$js_mandatory_fields_hidden[] = $crm_fields_vals->field_name;
					}
					$j++;
				}
				$js_mandatory_array = json_encode($js_mandatory_fields);
				$js_mandatory_array_hidden = json_encode($js_mandatory_fields_hidden);
				$crm_field_options = '';
				$crm_field_options .= "<option>--None--</option>";
				foreach( $crm_field_labels as $field_key => $crm_field_label )
				{
					$crm_field_options .= "<option value='{$crm_field_label}'> $crm_field_label</option>";
				}  

				$fields_html = '';
				$fields_html .= "<div><div class='form-group col-md-12'><div class='exist_mapping col-md-6'> <div class='leads-builder-heading'>ContactForm Fields</div></div><div class='exist_mapping col-md-4'> <div class='leads-builder-heading'>CRM Fields</div></div></div> ";
				$i = 1;	
				foreach( $contact_form_labels as $cont_id => $cont_label)
				{
					$fields_html .= "<div class='form-group col-md-12'>
									 <div class='col-md-6'><label class='leads-builder-label'> $cont_label </label></div>
									 <input type='hidden' name='thirdpartyfield_$i' id='thirdpartyfield_$i' value='$cont_label' />";
					$fields_html .= "<div class='col-md-4'><select class='form-control'  name='crm_fields_$i' id='crm_fields_$i' >";
					$crm_field_options = '';
					$crm_field_options .= "<option>--None--</option>";
					foreach( $crm_field_labels as $field_key => $crm_field_label ) // Prepare crm fileds drop down
					{
						$crm_field_options .= "<option value='{$crm_field_label}'";

						if(is_array($contact_config )) {
							foreach( $contact_config as $config_key => $config_val ) // configuration
							{
								if( $cont_label == $config_key && $crm_field_label == $config_val ) //match label and fieldname
								{
									$crm_field_options .= "selected=selected";//select when the configuration exist
								}
							}
						}
						$crm_field_options .= "> $field_key</option>";
					}
					$fields_html .= $crm_field_options;			
					$fields_html .=	"</select>
						</div>
						</div>";
					$i++;
				}
			}
			else
			{
				$fields_html = "";
				$map_options = "";
				$fields_html .= "<span style='color:red;font-size:18px;margin-left:23%;'>Please choose any form</span>";
			}
			$fields_html .= "<input type='hidden' value='$i' id='total_field_count'>";
			$fields_html .= "<input type='hidden' value='$third_module' id='module'>";
			$fields_html .= "<input type='hidden' value='$activated_crm' id='active_crm'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_form_name' id='form_name'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_plugin' id='thirdparty_plugin'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array' id='crm_mandatory_fields'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array_hidden' id='crm_mandatory_fields_hidden'>";
			$fields_html .= "</div>";
			$html_data_array = array();
			$html_data_array['map_options'] = $map_options;
			$html_data_array['fields_html'] = $fields_html;
			print_r( json_encode($html_data_array) );
			die;
			break;

		case 'wpform':
			$shortcode = $activated_crm."_wp_wpform_lite".$thirdparty_form_name;
			$config = get_option( $shortcode );
			$config_fields = isset($config['fields']) ? $config['fields'] : '';
			$wpform_config = $config_fields;
			$assigned_to_user = $this->get_assignedto($shortcode);
			$map_options = '';
			$map_options .= "<div>	
							 <div class='form-group col-md-12'> <div class='exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label'> Duplicate Handling</label> </div> 
							 <div class='exist_mapping col-md-4'> <select class='form-control' id='duplicate_handling'><option value='skip'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip' )
			{
				$map_options .= "selected=selected";	
			}
			$map_options .= " > Skip</option>";
			if($activated_crm=="mailchimp"){

			}else{
				$map_options.="<option value='skip_both'";
				if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip_both' )
				{
					$map_options .= "selected=selected";
				}
				$map_options .= " > Skip if already a Contact or Lead</option>";
			}
			$map_options.="<option value='update'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'update' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .= ">Update</option> <option value='create'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'create' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .="> Create </option></select></div></div></div>";


			$map_options .= "<div><div class='form-group col-md-12'> <div class='assign_leads exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label' >";

			if( $third_module == "Leads")
			{
				$map_options .= "Lead Owner";
				$map_options .= "</label></div><div class='exist_mapping col-md-4'> $assigned_to_user</div></div><div class='form-group col-md-12'><div col-md-6>";
			} else if( $third_module == "Contacts" )
			{
				$map_options .= "Contact Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
			}else if( $third_module == "Members" )
			{
				$map_options .= "Member Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
			}
			$map_options .= "</label></div></div>";		

			if( $thirdparty_form_name != '--None--' )
			{
				$get_json_array = $wpdb->get_results( $wpdb->prepare( "select ID,post_content from $wpdb->posts where ID=%d" , $thirdparty_form_name ) );
				$wpform_post_content = json_decode($get_json_array[0]->post_content);

				$wpform_all_fields = $wpform_post_content->fields;

				foreach($wpform_all_fields as $wpform_values){
					$wpform_form_labels[$wpform_values->id] = $wpform_values->label;
				}

				$CaptureDataObj = new CaptureData();
				$crm_fields = $CaptureDataObj->get_crmfields_by_settings( $activated_crm , $third_module );
				$j = 1;

				$js_mandatory_fields = array();
				$js_mandatory_fields_hidden = array();
				foreach( $crm_fields as $crm_field_key => $crm_fields_vals ) {
					$crm_field_labels[$crm_fields_vals->field_label] = $crm_fields_vals->field_name;
					if( $crm_fields_vals->field_mandatory == 1 )
					{
						if(!in_array($crm_fields_vals->field_name, $js_mandatory_fields))
							$js_mandatory_fields[] = $crm_fields_vals->field_label;
						$js_mandatory_fields_hidden[] = $crm_fields_vals->field_name;
					}
					$j++;
				}
				$js_mandatory_array = json_encode($js_mandatory_fields);
				$js_mandatory_array_hidden = json_encode($js_mandatory_fields_hidden);
				$crm_field_options = '';
				$crm_field_options .= "<option>--None--</option>";
				foreach( $crm_field_labels as $field_key => $crm_field_label )
				{
					$crm_field_options .= "<option value='{$crm_field_label}'> $crm_field_label</option>";
				}  

				$fields_html = '';
				$fields_html .= "<div><div class='form-group col-md-12'><div class='exist_mapping col-md-6'> <div class='leads-builder-heading'>WpForm Fields</div></div><div class='exist_mapping col-md-4'> <div class='leads-builder-heading'>CRM Fields</div></div></div> ";
				$i = 1;	
				foreach( $wpform_form_labels as $wpf_id => $wpf_label)
				{
					$fields_html .= "<div class='form-group col-md-12'>
									 <div class='col-md-6'><label class='leads-builder-label'> $wpf_label </label></div>
									 <input type='hidden' name='thirdpartyfield_$i' id='thirdpartyfield_$i' value='$wpf_label' />";
					$fields_html .= "<div class='col-md-4'><select class='form-control'  name='crm_fields_$i' id='crm_fields_$i' >";
					$crm_field_options = '';
					$crm_field_options .= "<option>--None--</option>";
					foreach( $crm_field_labels as $field_key => $crm_field_label ) // Prepare crm fileds drop down
					{

						$crm_field_options .= "<option value='{$crm_field_label}'";
						if(is_array($wpform_config )) {
							foreach( $wpform_config as $config_key => $config_val ) // configuration
							{
								if( $wpf_label == $config_key && $crm_field_label == $config_val ) //match label and fieldname
								{
									$crm_field_options .= "selected=selected";//select when the configuration exist
								}
							}
						}
						$crm_field_options .= "> $field_key</option>";
					}
					$fields_html .= $crm_field_options;			
					$fields_html .=	"</select>
						</div>
						</div>";
					$i++;
				}
			}
			else
			{
				$fields_html = "";
				$map_options = "";
				$fields_html .= "<span style='color:red;font-size:18px;margin-left:23%;'>Please choose any form</span>";
			}
			$fields_html .= "<input type='hidden' value='$i' id='total_field_count'>";
			$fields_html .= "<input type='hidden' value='$third_module' id='module'>";
			$fields_html .= "<input type='hidden' value='$activated_crm' id='active_crm'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_form_name' id='form_name'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_plugin' id='thirdparty_plugin'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array' id='crm_mandatory_fields'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array_hidden' id='crm_mandatory_fields_hidden'>";
			$fields_html .= "</div>";
			$html_data_array = array();
			$html_data_array['map_options'] = $map_options;
			$html_data_array['fields_html'] = $fields_html;
			print_r( json_encode($html_data_array) );
			die;
			break;

		case 'wpformpro':
			$shortcode = $activated_crm."_wp_wpform_pro".$thirdparty_form_name;
			$config = get_option( $shortcode );
			$config_fields = isset($config['fields']) ? $config['fields'] : '';
			$wpformpro_config = $config_fields;
			$assigned_to_user = $this->get_assignedto($shortcode);
			$map_options = '';
			$map_options .= "<div>	
							 <div class='form-group col-md-12'> <div class='exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label'> Duplicate Handling</label> </div> 
							 <div class='exist_mapping col-md-4'> <select class='form-control' id='duplicate_handling'><option value='skip'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip' )
			{
				$map_options .= "selected=selected";	
			}
			$map_options .= " > Skip</option>";
			if($activated_crm=="mailchimp"){

			}
			else{
				$map_options.="	<option value='skip_both'";
				if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip_both' )
				{
					$map_options .= "selected=selected";
				}
				$map_options .= " > Skip if already a Contact or Lead</option>";
			}
			$map_options.="<option value='update'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'update' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .= ">Update</option> <option value='create'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'create' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .="> Create </option></select></div></div></div>";


			$map_options .= "<div><div class='form-group col-md-12'> <div class='assign_leads exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label' >";

			if( $third_module == "Leads")
			{
				$map_options .= "Lead Owner";
				$map_options .= "</label></div><div class='exist_mapping col-md-4'> $assigned_to_user</div></div><div class='form-group col-md-12'><div col-md-6>";
			} else if( $third_module == "Contacts" )
			{
				$map_options .= "Contact Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
			}else if( $third_module == "Members" )
			{
				$map_options .= "Member Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
			}
			$map_options .= "</label></div></div>";		

			if( $thirdparty_form_name != '--None--' )
			{
				$get_json_array = $wpdb->get_results( $wpdb->prepare( "select ID,post_content from $wpdb->posts where ID=%d" , $thirdparty_form_name ) );
				$wpformpro_post_content = json_decode($get_json_array[0]->post_content);

				$wpformpro_all_fields = $wpformpro_post_content->fields;

				foreach($wpformpro_all_fields as $wpformpro_values){
					$wpformpro_form_labels[$wpformpro_values->id] = $wpformpro_values->label;
				}

				$CaptureDataObj = new CaptureData();
				$crm_fields = $CaptureDataObj->get_crmfields_by_settings( $activated_crm , $third_module );
				$j = 1;

				$js_mandatory_fields = array();
				$js_mandatory_fields_hidden = array();
				foreach( $crm_fields as $crm_field_key => $crm_fields_vals ) {
					$crm_field_labels[$crm_fields_vals->field_label] = $crm_fields_vals->field_name;
					if( $crm_fields_vals->field_mandatory == 1 )
					{
						if(!in_array($crm_fields_vals->field_name, $js_mandatory_fields))
							$js_mandatory_fields[] = $crm_fields_vals->field_label;
						$js_mandatory_fields_hidden[] = $crm_fields_vals->field_name;
					}
					$j++;
				}
				$js_mandatory_array = json_encode($js_mandatory_fields);
				$js_mandatory_array_hidden = json_encode($js_mandatory_fields_hidden);
				$crm_field_options = '';
				$crm_field_options .= "<option>--None--</option>";
				foreach( $crm_field_labels as $field_key => $crm_field_label )
				{
					$crm_field_options .= "<option value='{$crm_field_label}'> $crm_field_label</option>";
				}  

				$fields_html = '';
				$fields_html .= "<div><div class='form-group col-md-12'><div class='exist_mapping col-md-6'> <div class='leads-builder-heading'>WpForm Pro Fields</div></div><div class='exist_mapping col-md-4'> <div class='leads-builder-heading'>CRM Fields</div></div></div> ";
				$i = 1;	
				foreach( $wpformpro_form_labels as $wpf_id => $wpf_label)
				{
					$fields_html .= "<div class='form-group col-md-12'>
									 <div class='col-md-6'><label class='leads-builder-label'> $wpf_label </label></div>
									 <input type='hidden' name='thirdpartyfield_$i' id='thirdpartyfield_$i' value='$wpf_label' />";
					$fields_html .= "<div class='col-md-4'><select class='form-control'  name='crm_fields_$i' id='crm_fields_$i' >";
					$crm_field_options = '';
					$crm_field_options .= "<option>--None--</option>";
					foreach( $crm_field_labels as $field_key => $crm_field_label ) // Prepare crm fileds drop down
					{

						$crm_field_options .= "<option value='{$crm_field_label}'";
						if(is_array($wpformpro_config )) {
							foreach( $wpformpro_config as $config_key => $config_val ) // configuration
							{
								if( $wpf_label == $config_key && $crm_field_label == $config_val ) //match label and fieldname
								{
									$crm_field_options .= "selected=selected";//select when the configuration exist
								}
							}
						}
						$crm_field_options .= "> $field_key</option>";
					}
					$fields_html .= $crm_field_options;			
					$fields_html .=	"</select>
						</div>
						</div>";
					$i++;
				}
			}
			else
			{
				$fields_html = "";
				$map_options = "";
				$fields_html .= "<span style='color:red;font-size:18px;margin-left:23%;'>Please choose any form</span>";
			}
			$fields_html .= "<input type='hidden' value='$i' id='total_field_count'>";
			$fields_html .= "<input type='hidden' value='$third_module' id='module'>";
			$fields_html .= "<input type='hidden' value='$activated_crm' id='active_crm'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_form_name' id='form_name'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_plugin' id='thirdparty_plugin'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array' id='crm_mandatory_fields'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array_hidden' id='crm_mandatory_fields_hidden'>";
			$fields_html .= "</div>";
			$html_data_array = array();
			$html_data_array['map_options'] = $map_options;
			$html_data_array['fields_html'] = $fields_html;
			print_r( json_encode($html_data_array) );
			die;
			break;

		case 'calderaform':
			$shortcode = $activated_crm."_wp_caldera".$thirdparty_form_name;
			$config = get_option( $shortcode );
			$configfields = isset($config['fields']) ? $config['fields'] : '';
			$caldera_config = $configfields;
			$assigned_to_user = $this->get_assignedto($shortcode);
			$map_options = '';
			$map_options .= "<div>	
							 <div class='form-group col-md-12'> <div class='exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label'> Duplicate Handling</label> </div> 
							 <div class='exist_mapping col-md-4'> <select class='form-control' id='duplicate_handling'><option value='skip'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip' )
			{
				$map_options .= "selected=selected";	
			}
			$map_options .= "> Skip</option>";
			if($activated_crm=="mailchimp"){

			}
			else{
				$map_options.="<option value='skip_both'";
				if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip_both' )
				{
					$map_options .= "selected=selected";
				}
				$map_options .= "> Skip if already a Contact or Lead</option>";
			}
			$map_options.="	<option value='update'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'update' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .= ">Update</option> <option value='create'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'create' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .="> Create </option></select></div></div></div>";

			$map_options .= "<div><div class='form-group col-md-12'> <div class='assign_leads exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label' >";

			if( $third_module == "Leads")
			{
				$map_options .= "Lead Owner";
				$map_options .= "</label></div><div class='exist_mapping col-md-4'> $assigned_to_user</div></div><div class='form-group col-md-12'><div col-md-6>";
			} else if( $third_module == "Contacts" )
			{
				$map_options .= "Contact Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</td>";
			}else if( $third_module == "Members" )
			{
				$map_options .= "Member Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</td>";
			}
			$map_options .= "</label></div></div>";


			if( $thirdparty_form_name != "--None--" )
			{	

				//$get_json_array = $wpdb->get_results( "SELECT config from {$wpdb->prefix}cf_forms where form_id = '$thirdparty_form_name' and type = 'primary' ",ARRAY_A );
				$get_json_array = $wpdb->get_results(
					$wpdb->prepare("
						SELECT config 
						FROM {$wpdb->prefix}cf_forms 
						WHERE form_id = %s 
						AND type = 'primary'
					", $thirdparty_form_name),
					ARRAY_A
				);
				$caldera_form_labels = array();
				foreach( $get_json_array as $caldera_key => $calders_data )
				{
					$caldera_config_data = unserialize($calders_data['config']);
					$caldera_fields = $caldera_config_data['fields'];
					$i = 1;
					foreach($caldera_fields as $caldera_field_key => $caldera_field_value){

						$caldera_form_labels[$caldera_field_key] = $caldera_field_value['label']; 

						if(isset($caldera_field_value['config']['type']) && $caldera_field_value['config']['type'] == 'submit'){
							unset($caldera_form_labels[$caldera_field_key]);
						}
						$i++;
					}	
				}

				$CaptureDataObj = new CaptureData();
				$crm_fields = $CaptureDataObj->get_crmfields_by_settings( $activated_crm , $third_module );
				$j = 1;

				$js_mandatory_fields = array();
				$js_mandatory_fields_hidden = array();
				foreach( $crm_fields as $crm_field_key => $crm_fields_vals ) {
					$crm_field_labels[$crm_fields_vals->field_label] = $crm_fields_vals->field_name;
					if( $crm_fields_vals->field_mandatory == 1 )
					{
						if(!in_array($crm_fields_vals->field_name, $js_mandatory_fields))
							$js_mandatory_fields[] = $crm_fields_vals->field_label;
						$js_mandatory_fields_hidden[] = $crm_fields_vals->field_name;
					}
					$j++;
				}
				$js_mandatory_array = json_encode($js_mandatory_fields);
				$js_mandatory_array_hidden = json_encode($js_mandatory_fields_hidden);
				$crm_field_options = '';
				$crm_field_options .= "<option>--None--</option>";
				foreach( $crm_field_labels as $field_key => $crm_field_label )
				{

					$crm_field_options .= "<option value='{$crm_field_label}'> $crm_field_label</option>";
				}  


				$fields_html = '';
				$fields_html .= "<div><div class='form-group col-md-12'><div class='exist_mapping col-md-6'> <div class='leads-builder-heading'>Caldera Form Fields</div></div><div class='exist_mapping col-md-4'> <div class='leads-builder-heading'>CRM Fields</div></div></div> ";
				$i = 1;	
				foreach( $caldera_form_labels as $nin_id => $nin_label)
				{
					$fields_html .= "<div class='form-group col-md-12'>
									 <div class='col-md-6'><label class='leads-builder-label'> $nin_label </label></div>
									 <input type='hidden' name='thirdpartyfield_$i' id='thirdpartyfield_$i' value='$nin_id' />";
					$fields_html .= "<div class='exist_mapping col-md-4'><select class='form-control'  name='crm_fields_$i' id='crm_fields_$i' >";
					$crm_field_options = '';
					$crm_field_options .= "<option>--None--</option>";
					foreach( $crm_field_labels as $field_key => $crm_field_label ) // Prepare crm fileds drop down
					{

						$crm_field_options .= "<option value='{$crm_field_label}'";
						if(is_array( $caldera_config)) {
							foreach( $caldera_config as $config_key => $config_val ) // configuration
							{
								if( $nin_label == $config_key && $crm_field_label == $config_val ) //match label and fieldname
								{
									$crm_field_options .= "selected=selected";//select when the configuration exist
								}
							}
						}
						$crm_field_options .= "> $field_key</option>";
					}
					$fields_html .= $crm_field_options;			
					$fields_html .=	"</select>
						</div>
						</div>";
					$i++;
				}
			}
			else
			{
				$fields_html = "";
				$map_options = "";
				$fields_html .= "<span style='color:red;font-size:18px;margin-left:23%;'>Please choose any form</span>";
			}
			$fields_html .= "<input type='hidden' value='$i' id='total_field_count'>";
			$fields_html .= "<input type='hidden' value='$third_module' id='module'>";
			$fields_html .= "<input type='hidden' value='$activated_crm' id='active_crm'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_form_name' id='form_name'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_plugin' id='thirdparty_plugin'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array' id='crm_mandatory_fields'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array_hidden' id='crm_mandatory_fields_hidden'>";
			$fields_html .= "</div>";
			$html_data_array = array();
			$html_data_array['map_options'] = $map_options;
			$html_data_array['fields_html'] = $fields_html;
			print_r( json_encode($html_data_array) );
			die;
			break;
		default:
			$html = "";
			$html .= "<span style='color:red;font-size:16px;margin-left:12%;'> Please configure your third party form under Form Settings  </span>";
			return $html;
			break;      	

		}
	}

	public function getTextBetweenBrackets($post_content) {

		$data_type_array = array( 'text' , 'email' , 'date' , 'checkbox' , 'select' , 'url' , 'number' , 'textarea' , 'radio' , 'quiz' , 'file', 'acceptance','hidden', 'tel' , 'dynamichidden' );
		$contact_labels = array();

		foreach( $data_type_array as $dt_key => $dt_val )
		{
			$patternn = "(\[$dt_val(\s|\*\s)(.*)\])";
			preg_match_all($patternn, $post_content, $matches);

			if( !empty( $matches[1] ))
			{
				$contact_labels[] = $matches[0];	
			}

			$i =0;
			$merge_array = array();
			foreach( $contact_labels as $cf7key => $cf7value )
			{
				foreach( $cf7value as $cf_get_key => $cf_get_fields )
				{
					$merge_array[] = $cf_get_fields;
				} 	
			}	

		}
		return $merge_array;
	}

	public function map_thirdparty_form_fields()
	{

		$config_data = $_REQUEST['post_data'];	
		$form_title = sanitize_text_field($_REQUEST['form_title']);
		$third_plugin = sanitize_text_field($_REQUEST['third_plugin']);
		$third_module = sanitize_text_field($_REQUEST['third_module']);
		$third_crm = sanitize_text_field($_REQUEST['third_crm']);
		$third_duplicate = isset($_REQUEST['third_duplicate']) ? sanitize_text_field($_REQUEST['third_duplicate']) : '';
		$third_assignedto = isset($_REQUEST['third_assigedto']) ? sanitize_text_field($_REQUEST['third_assigedto']) : '';
		$third_assignedto_name = sanitize_text_field( $_REQUEST['assignedto_name'] );	

		foreach( $config_data as $data_key => $data_val  )
		{
			if( preg_match('/^thirdpartyfield/' , $data_key ) )
			{
				$thirdparty_key = ltrim( $data_key , 'thirdpartyfield_' );
				$thirdparty_labels[$thirdparty_key] = $data_val; // Make thirdparty label array
			}

			if( preg_match('/^crm_fields/' , $data_key ) )
			{
				$crm_field_key = ltrim( $data_key , 'crm_fields_' );
				if( $data_val != '--None--' )
				{
					$crm_labels[$crm_field_key] = $data_val; // Make crm labels array -take only mapped values
				}
			}     	
		}
		$get_keys_crm_labels = array_keys($crm_labels); // get keys from crm labels- to prepare mapped thirdparty labels

		foreach( $thirdparty_labels as $tp_key => $tp_val )
		{
			foreach( $get_keys_crm_labels as $index_val )
			{
				if( $tp_key == $index_val )//check crm key index with thirdparty label  array
				{
					$thirdparty_mapped_labels[$tp_key] = $tp_val;  // prepare mapped values for thirdparty label array
				}
			}
		}
		$mapped_array = array_combine( $thirdparty_mapped_labels , $crm_labels ); // Combine final mapped array(thirdparty, crm fields)

		$final_mapped_array = array();
		$final_mapped_array['form_title'] = $form_title;
		$final_mapped_array['third_plugin'] = $third_plugin;
		$final_mapped_array['third_module'] = $third_module;
		$final_mapped_array['thirdparty_crm'] = $third_crm;
		$final_mapped_array['thirdparty_duplicate'] = $third_duplicate;
		$final_mapped_array['thirdparty_assignedto'] = $third_assignedto;
		$final_mapped_array['thirdparty_assignedto_name'] = $third_assignedto_name;
		$final_mapped_array['fields'] = $mapped_array;
		$activated_crm = get_option('WpLeadBuilderProActivatedPlugin');
		switch(  $third_plugin )
		{
		case 'gravityform':
			$option = $activated_crm.'_wp_gravity'.$form_title;
			break;

		case 'ninjaform':
			$option = $activated_crm.'_wp_ninja'.$form_title;
			break;

		case 'contactform':
			$option = $activated_crm.'_wp_contact'.$form_title;
			break;
		case 'wpform':
			$option = $activated_crm.'_wp_wpform_lite'.$form_title;
			break;	
		case 'wpformpro':
			$option = $activated_crm.'_wp_wpform_pro'.$form_title;
			break;	
		case 'calderaform':
			$option = $activated_crm.'_wp_caldera'.$form_title;
			break;
		case 'elementorform':
			$option = $activated_crm.'_wp_elementor'.$form_title;
			break;
		}
		$check_exist_array = get_option( $option );
		if( !empty( $check_exist_array['tp_roundrobin'] ))
		{
			$final_mapped_array['tp_roundrobin'] = $check_exist_array['tp_roundrobin'];
		}			
		update_option( $option , $final_mapped_array );
		die;	
	}

	//Show Already mapped configuration 

	public function show_mapped_config()
	{
		global $wpdb;
		$activated_crm = get_option('WpLeadBuilderProActivatedPlugin');
		$third_module = sanitize_text_field($_REQUEST['third_module']);
		$thirdparty_form_name  = sanitize_text_field( $_REQUEST['form_id'] );
		$thirdparty_title = sanitize_text_field( $_REQUEST['form_title'] );
		$thirdparty_plugin = sanitize_text_field( $_REQUEST['third_plugin'] );

		switch( $thirdparty_plugin )
		{
		case 'gravityform':
			$shortcode = $activated_crm."_wp_gravity".$thirdparty_form_name;
			$config = get_option( $shortcode );
			$gravity_config = $config['fields'];
			$assigned_to_user = $this->get_assignedto($shortcode);
			$map_options = '';
			$map_options .= "<div>
							 <div class='form-group col-md-12'><div class='exist_mapping col-md-6'><label id='innertext' class='leads-builder-label'> Module Type</label> </div><div class='exist_mapping col-md-4'> $third_module</div></div>
							 <div class='form-group col-md-12'><div class='exist_mapping col-md-6'><label id='innertext'> Form Type </label></div><div class='exist_mapping col-md-4'> $thirdparty_plugin</div></div>
							 <div class='form-group col-md-12'><div class='exist_mapping col-md-6'><label id='innertext' class='leads-builder-label'> Form Title </label></div><div class='exist_mapping col-md-4'> $thirdparty_title</div></div>
							 <div class='form-group col-md-12'> <div class='exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label'> Duplicate Handling</label> </div> 
							 <div class='exist_mapping col-md-4'> <select class='form-control' id='duplicate_handling'><option value='skip'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip' )
			{
				$map_options .= "selected=selected";	
			}
			$map_options .= "> Skip</option>";
			if($activated_crm=="mailchimp"){

			}
			else{
				$map_options.="<option value='skip_both'";
				if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip_both' )
				{
					$map_options .= "selected=selected";
				}
				$map_options .= "> Skip if already a Contact or Lead</option>";
			}

			$map_options.="	<option value='update'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'update' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .= ">Update</option> <option value='create'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'create' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .="> Create </option></select></div></div></div>";

			$map_options .= "<div ><div class='form-group col-md-12'> <div class='assign_leads exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label' >";
			if( $third_module == "Leads")
			{
				$map_options .= "Lead Owner";
				$map_options .= "</label></div><div class='exist_mapping col-md-4'> $assigned_to_user</div></div><div class='form-group col-md-12'><div col-md-6>";
			} else if( $third_module == "Contacts" )
			{
				$map_options .= "Contact Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
			}else if( $third_module == "Members" )
			{
				$map_options .= "Member Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
			}
			$map_options .= "</label></div></div>";

			if( $thirdparty_form_name != "--None--" )
			{
				$get_json_array = $wpdb->get_results( $wpdb->prepare( "select display_meta from {$wpdb->prefix}gf_form_meta where form_id=%d" , $thirdparty_form_name ) );
				$gravity_arr = json_decode( $get_json_array[0]->display_meta );	
				$gravity_fields = $gravity_arr->fields;
				foreach( $gravity_fields as $grav_key => $grav_value )
				{
					$grav_field_id = $grav_value->id;
					$gravity_form_labels[$grav_field_id] = $grav_value->label;
				}

				$CaptureDataObj = new CaptureData();
				$crm_fields = $CaptureDataObj->get_crmfields_by_settings( $activated_crm , $third_module );
				$j = 1;

				$js_mandatory_fields = array();
				$js_mandatory_fields_hidden = array();
				foreach( $crm_fields as $crm_field_key => $crm_fields_vals ) {
					$crm_field_labels[$crm_fields_vals->field_label] = $crm_fields_vals->field_name;
					if( $crm_fields_vals->field_mandatory == 1 )
					{
						if(!in_array($crm_fields_vals->field_name, $js_mandatory_fields))
							$js_mandatory_fields[] = $crm_fields_vals->field_label;
						$js_mandatory_fields_hidden[] = $crm_fields_vals->field_name;
					}
					$j++;
				}
				$js_mandatory_array = json_encode($js_mandatory_fields);
				$js_mandatory_array_hidden = json_encode($js_mandatory_fields_hidden);
				$crm_field_options = '';
				$crm_field_options .= "<option>--None--</option>";
				foreach( $crm_field_labels as $field_key => $crm_field_label )
				{

					$crm_field_options .= "<option value='{$crm_field_label}'> $crm_field_label</option>";
				}  


				$fields_html = '';
				$fields_html .= "<div><div class='form-group col-md-12'><div class='exist_mapping col-md-6'> <div class='leads-builder-heading'>GravityForm Fields</div></div><div class='exist_mapping col-md-4'> <div class='leads-builder-heading'>CRM Fields</div></div> </div>";
				$i = 1;	
				foreach( $gravity_form_labels as $grav_id => $grav_label)
				{
					$fields_html .= "<div class='form-group col-md-12'>
									 <div class='col-md-6'><label class='leads-builder-label'> $grav_label </label></td></div>
									 <input type='hidden' name='thirdpartyfield_$i' id='thirdpartyfield_$i' value='$grav_label' />";
					$fields_html .= "<div class='col-md-4'><select class='form-control'  name='crm_fields_$i' id='crm_fields_$i' >";
					$crm_field_options = '';
					$crm_field_options .= "<option>--None--</option>";
					foreach( $crm_field_labels as $field_key => $crm_field_label ) // Prepare crm fileds drop down
					{

						$crm_field_options .= "<option value='{$crm_field_label}'";
						foreach( $gravity_config as $config_key => $config_val ) // configuration
						{
							if( $grav_label == $config_key && $crm_field_label == $config_val ) //match label and fieldname
							{
								$crm_field_options .= "selected=selected";//select when the configuration exist
							}
						}

						$crm_field_options .= "> $field_key</option>";
					}
					$fields_html .= $crm_field_options;			
					$fields_html .=	"</select>
						</div>
						</div>";
					$i++;
				}
			}
			else
			{
				$fields_html = "";
				$map_options = "";
				$fields_html .= "<span style='color:red;font-size:18px;margin-left:23%;'>Please choose any form</span>";
			}
			$fields_html .= "<input type='hidden' value='$i' id='total_field_count'>";
			$fields_html .= "<input type='hidden' value='$third_module' id='module'>";
			$fields_html .= "<input type='hidden' value='$activated_crm' id='active_crm'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_form_name' id='form_name'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_plugin' id='thirdparty_plugin'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array' id='crm_mandatory_fields'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array_hidden' id='crm_mandatory_fields_hidden'>";
			$fields_html .= "</div>";
			$html_data_array = array();
			$html_data_array['map_options'] = $map_options;
			$html_data_array['fields_html'] = $fields_html;
			print_r( json_encode($html_data_array) );
			die;
			break;

		case 'ninjaform':
			$shortcode = $activated_crm."_wp_ninja".$thirdparty_form_name;
			$config = get_option( $shortcode );
			$ninja_config = $config['fields'];
			$assigned_to_user = $this->get_assignedto($shortcode);
			$map_options = '';
			$map_options .= "<div>
							 <div class='form-group col-md-12'><div class='exist_mapping col-md-6'><label id='innertext' class='leads-builder-label'> Module Type</label> </div><div class='exist_mapping col-md-4'> $third_module</div></div>
							 <div class='form-group col-md-12'><div class='exist_mapping col-md-6'><label id='innertext' class='leads-builder-label'> Form Type </label></div><div class='exist_mapping col-md-4'> $thirdparty_plugin</div></div>
							 <div class='form-group col-md-12'><div class='exist_mapping col-md-6'><label id='innertext' class='leads-builder-label'> Form Title </label></div><div class='exist_mapping col-md-4'> $thirdparty_title</div></div>
							 <div class='form-group col-md-12'> <div class='col-md-6'> <label id='innertext' class='leads-builder-label'> Duplicate Handling</label> </div> 
							 <div class='exist_mapping col-md-4'> <select class='form-control' id='duplicate_handling'><option value='skip'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip' )
			{
				$map_options .= "selected=selected";	
			}
			$map_options .= "> Skip</option>";
			if($activated_crm=="mailchimp"){

			}else{
				$map_options.="	<option value='skip_both'";
				if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip_both' )
				{
					$map_options .= "selected=selected";
				}
				$map_options .= "> Skip if already a Contact or Lead</option>";
			}

			$map_options.="<option value='update'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'update' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .= ">Update</option> <option value='create'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'create' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .="> Create </option></select></div></div></div>";

			$map_options .="<div><div class='form-group col-md-12'> <div class='assign_leads exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label' >";
			if( $third_module == "Leads")
			{
				$map_options .= "Lead Owner";
				$map_options .= "</label></div><div class='exist_mapping col-md-4'> $assigned_to_user</div></div><div class='form-group col-md-12'><div col-md-6>";
			} else if( $third_module == "Contacts" )
			{
				$map_options .= "Contact Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
			}else if( $third_module == "Members" )
			{
				$map_options .= "Member Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
			}
			$map_options .= "</label></div></div>";


			if( $thirdparty_form_name != "--None--" )
			{		
				$get_json_array = $wpdb->get_results( $wpdb->prepare( "select parent_id,label from {$wpdb->prefix}nf3_fields where parent_id=%d and type !=%s" , $thirdparty_form_name , 'submit' ) );
				$i = 1;
				$ninja_form_labels = array();
				foreach( $get_json_array as $ninja_key => $ninja_data )
				{
					$ninja_form_labels[$i] = $ninja_data->label; 	
					$i++;
				}
				$CaptureDataObj = new CaptureData();
				$crm_fields = $CaptureDataObj->get_crmfields_by_settings( $activated_crm , $third_module );
				$j = 1;

				$js_mandatory_fields = array();
				$js_mandatory_fields_hidden = array();
				foreach( $crm_fields as $crm_field_key => $crm_fields_vals ) {
					$crm_field_labels[$crm_fields_vals->field_label] = $crm_fields_vals->field_name;
					if( $crm_fields_vals->field_mandatory == 1 )
					{
						if(!in_array($crm_fields_vals->field_name, $js_mandatory_fields))
							$js_mandatory_fields[] = $crm_fields_vals->field_label;
						$js_mandatory_fields_hidden[] = $crm_fields_vals->field_name;
					}
					$j++;
				}
				$js_mandatory_array = json_encode($js_mandatory_fields);
				$js_mandatory_array_hidden = json_encode($js_mandatory_fields_hidden);
				$crm_field_options = '';
				$crm_field_options .= "<option>--None--</option>";
				foreach( $crm_field_labels as $field_key => $crm_field_label )
				{
					$crm_field_options .= "<option value='{$crm_field_label}'> $crm_field_label</option>";
				}  

				$fields_html = '';
				$fields_html .= "<div><div class='form-group col-md-12'><div class='exist_mapping col-md-6'><div class='leads-builder-heading'> NinjaForm Fields</div></div><div class='exist_mapping col-md-4'> <div class='leads-builder-heading'>CRM Fields</div></div></div> ";
				$i = 1;	
				foreach( $ninja_form_labels as $nin_id => $nin_label)
				{
					$fields_html .= "<div class='form-group col-md-12'>
									 <div class='col-md-6'><label class='leads-builder-label'> $nin_label </label></div>
									 <input type='hidden' name='thirdpartyfield_$i' id='thirdpartyfield_$i' value='$nin_label' />";
					$fields_html .= "<div class='exist_mapping col-md-4'><select class='form-control'  name='crm_fields_$i' id='crm_fields_$i' >";
					$crm_field_options = '';
					$crm_field_options .= "<option>--None--</option>";
					foreach( $crm_field_labels as $field_key => $crm_field_label ) // Prepare crm fileds drop down
					{

						$crm_field_options .= "<option value='{$crm_field_label}'";
						foreach( $ninja_config as $config_key => $config_val ) // configuration
						{
							if( $nin_label == $config_key && $crm_field_label == $config_val ) //match label and fieldname
							{
								$crm_field_options .= "selected=selected";//select when the configuration exist
							}
						}

						$crm_field_options .= "> $field_key</option>";
					}
					$fields_html .= $crm_field_options;			
					$fields_html .=	"</select>
						</div>
						</div>";
					$i++;
				}
			}
			else
			{
				$fields_html = "";
				$map_options = "";
				$fields_html .= "<span style='color:red;font-size:18px;margin-left:23%;'>Please choose any form</span>";
			}
			$fields_html .= "<input type='hidden' value='$i' id='total_field_count'>";
			$fields_html .= "<input type='hidden' value='$third_module' id='module'>";
			$fields_html .= "<input type='hidden' value='$activated_crm' id='active_crm'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_form_name' id='form_name'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_plugin' id='thirdparty_plugin'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array' id='crm_mandatory_fields'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array_hidden' id='crm_mandatory_fields_hidden'>";
			$fields_html .= "</div>";
			$html_data_array = array();
			$html_data_array['map_options'] = $map_options;
			$html_data_array['fields_html'] = $fields_html;
			print_r( json_encode($html_data_array) );
			die;
			break;

		case 'contactform':
			$shortcode = $activated_crm."_wp_contact".$thirdparty_form_name;
			$config = get_option( $shortcode );
			$contact_config = $config['fields'];
			$assigned_to_user = $this->get_assignedto($shortcode);
			$map_options = '';
			$map_options .= "<div>	
							 <div class='form-group col-md-12'><div class='exist_mapping col-md-6'><label id='innertext' class='leads-builder-label'> Module Type</label> </div><div class='col-md-4'> $third_module</div></div>
							 <div class='form-group col-md-12'><div class='exist_mapping col-md-6'><label id='innertext' class='leads-builder-label'> Form Type </label></div><div class='col-md-4'> $thirdparty_plugin</div></div>
							 <div class='form-group col-md-12'><div class='exist_mapping col-md-6'><label id='innertext' class='leads-builder-label'> Form Title </label></div><div class='exist_mapping col-md-4'> $thirdparty_title</div></div>
							 <div class='form-group col-md-12'> <div class='exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label'> Duplicate Handling</label> </div> 
							 <div class='exist_mapping col-md-4'> <select class='form-control' id='duplicate_handling'><option value='skip'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip' )
			{
				$map_options .= "selected=selected";	
			}
			$map_options .= "> Skip</option>";
			if($activated_crm=="mailchimp"){

			}else{
				$map_options.="<option value='skip_both'";
				if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip_both' )
				{
					$map_options .= "selected=selected";
				}
				$map_options .= " > Skip if already a Contact or Lead</option>";
			}
			$map_options.="	<option value='update'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'update' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .= ">Update</option> <option value='create'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'create' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .="> Create </option></select></div></div></div>";

			$map_options .="<div><div class='form-group col-md-12'> <div class='assign_leads exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label' >";
			if( $third_module == "Leads")
			{
				$map_options .= "Lead Owner";
				$map_options .= "</label></div><div class='exist_mapping col-md-4'> $assigned_to_user</div></div><div class='form-group col-md-12'><div col-md-6>";
			} else if( $third_module == "Contacts" )
			{
				$map_options .= "Contact Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
			}else if( $third_module == "Members" )
			{
				$map_options .= "Member Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
			}
			$map_options .= "</label></div></div>";

			if( $thirdparty_form_name != '--None--' )
			{
				$get_json_array = $wpdb->get_results( $wpdb->prepare( "select ID,post_content from $wpdb->posts where ID=%d" , $thirdparty_form_name ) );
				$contact_post_content = $get_json_array[0]->post_content;
				$fields = $this->getTextBetweenBrackets( $contact_post_content );
				$i = 0;
				foreach( $fields as $cfkey => $cfval )
				{
					if( preg_match( '/\s/' , $cfval ) )
					{
						$final_arr = explode( ' ' , $cfval );
						$contact_form_labels[$i] = rtrim( $final_arr[1] , ']' );
						$i++;
					}
				}
				$CaptureDataObj = new CaptureData();
				$crm_fields = $CaptureDataObj->get_crmfields_by_settings( $activated_crm , $third_module );
				$j = 1;

				$js_mandatory_fields = array();
				$js_mandatory_fields_hidden = array();
				foreach( $crm_fields as $crm_field_key => $crm_fields_vals ) {
					$crm_field_labels[$crm_fields_vals->field_label] = $crm_fields_vals->field_name;
					if( $crm_fields_vals->field_mandatory == 1 )
					{
						if(!in_array($crm_fields_vals->field_name, $js_mandatory_fields))
							$js_mandatory_fields[] = $crm_fields_vals->field_label;
						$js_mandatory_fields_hidden[] = $crm_fields_vals->field_name;
					}
					$j++;
				}
				$js_mandatory_array = json_encode($js_mandatory_fields);
				$js_mandatory_array_hidden = json_encode($js_mandatory_fields_hidden);
				$crm_field_options = '';
				$crm_field_options .= "<option>--None--</option>";
				foreach( $crm_field_labels as $field_key => $crm_field_label )
				{
					$crm_field_options .= "<option value='{$crm_field_label}'> $crm_field_label</option>";
				}  

				$fields_html = '';
				$fields_html .= "<div><div class='form-group col-md-12'><div class='exist_mapping col-md-6'><div class='leads-builder-heading'> ContactForm Fields</div></div><div class='exist_mapping col-md-4'><div class='leads-builder-heading'> CRM Fields</div></div> </div>";
				$i = 1;	

				foreach( $contact_form_labels as $cont_id => $cont_label)
				{
					$fields_html .= "<div class='form-group col-md-12'>
									 <div class='col-md-6'><label class='leads-builder-label'> $cont_label </label></div>
									 <input type='hidden' name='thirdpartyfield_$i' id='thirdpartyfield_$i' value='$cont_label' />";
					$fields_html .= "<div class='col-md-4'><select class='form-control' style='width:150px;' name='crm_fields_$i' id='crm_fields_$i' >";
					$crm_field_options = '';
					$crm_field_options .= "<option>--None--</option>";

					foreach( $crm_field_labels as $field_key => $crm_field_label ) // Prepare crm fileds drop down
					{
						$crm_field_options .= "<option value='{$crm_field_label}'";
						foreach( $contact_config as $config_key => $config_val ) // configuration
						{
							if( $cont_label == $config_key && $crm_field_label == $config_val ) //match label and fieldname
							{
								$crm_field_options .= "selected=selected";//select when the configuration exist
							}
						}

						$crm_field_options .= "> $field_key</option>";
					}
					$fields_html .= $crm_field_options;			
					$fields_html .=	"</select>
									 </div>
									 </div>";
					$i++;
				}
			}
			else
			{
				$fields_html = "";
				$map_options = "";
				$fields_html .= "<span style='color:red;font-size:18px;margin-left:23%;'>Please choose any form</span>";
			}
			$fields_html .= "<input type='hidden' value='$i' id='total_field_count'>";
			$fields_html .= "<input type='hidden' value='$third_module' id='module'>";
			$fields_html .= "<input type='hidden' value='$activated_crm' id='active_crm'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_form_name' id='form_name'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_plugin' id='thirdparty_plugin'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array' id='crm_mandatory_fields'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array_hidden' id='crm_mandatory_fields_hidden'>";
			$fields_html .= "</div>";
			$html_data_array = array();
			$html_data_array['map_options'] = $map_options;
			$html_data_array['fields_html'] = $fields_html;
			print_r( json_encode($html_data_array) );
			die;
			break;

		case 'wpform':

			$shortcode = $activated_crm."_wp_wpform_lite".$thirdparty_form_name;
			$config = get_option( $shortcode );
			$wpform_config = $config['fields'];
			$assigned_to_user = $this->get_assignedto($shortcode);
			$map_options = '';
			$map_options .= "<div>	
							 <div class='form-group col-md-12'><div class='exist_mapping col-md-6'><label id='innertext' class='leads-builder-label'> Module Type</label> </div><div class='col-md-4'> $third_module</div></div>
							 <div class='form-group col-md-12'><div class='exist_mapping col-md-6'><label id='innertext' class='leads-builder-label'> Form Type </label></div><div class='col-md-4'> $thirdparty_plugin</div></div>
							 <div class='form-group col-md-12'><div class='exist_mapping col-md-6'><label id='innertext' class='leads-builder-label'> Form Title </label></div><div class='exist_mapping col-md-4'> $thirdparty_title</div></div>
							 <div class='form-group col-md-12'> <div class='exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label'> Duplicate Handling</label> </div> 
							 <div class='exist_mapping col-md-4'> <select class='form-control' id='duplicate_handling'><option value='skip'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip' )
			{
				$map_options .= "selected=selected";	
			}
			$map_options .= "> Skip</option>";
			if($activated_crm=="mailchimp"){

			}else{
				$map_options.="<option value='skip_both'";
				if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip_both' )
				{
					$map_options .= "selected=selected";
				}
				$map_options .= " > Skip if already a Contact or Lead</option>";
			}
			$map_options.="	<option value='update'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'update' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .= ">Update</option> <option value='create'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'create' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .="> Create </option></select></div></div></div>";

			$map_options .="<div><div class='form-group col-md-12'> <div class='assign_leads exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label' >";
			if( $third_module == "Leads")
			{
				$map_options .= "Lead Owner";
				$map_options .= "</label></div><div class='exist_mapping col-md-4'> $assigned_to_user</div></div><div class='form-group col-md-12'><div col-md-6>";
			} else if( $third_module == "Contacts" )
			{
				$map_options .= "Contact Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
			}else if( $third_module == "Members" )
			{
				$map_options .= "Member Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
			}
			$map_options .= "</label></div></div>";

			if( $thirdparty_form_name != '--None--' )
			{
				$get_json_array = $wpdb->get_results( $wpdb->prepare( "select ID,post_content from $wpdb->posts where ID=%d" , $thirdparty_form_name ) );
				$wpform_post_content = json_decode($get_json_array[0]->post_content);

				$wpform_all_fields = $wpform_post_content->fields;

				foreach($wpform_all_fields as $wpform_values){
					$wpform_form_labels[$wpform_values->id] = $wpform_values->label;
				}

				$CaptureDataObj = new CaptureData();
				$crm_fields = $CaptureDataObj->get_crmfields_by_settings( $activated_crm , $third_module );
				$j = 1;

				$js_mandatory_fields = array();
				$js_mandatory_fields_hidden = array();
				foreach( $crm_fields as $crm_field_key => $crm_fields_vals ) {
					$crm_field_labels[$crm_fields_vals->field_label] = $crm_fields_vals->field_name;
					if( $crm_fields_vals->field_mandatory == 1 )
					{
						if(!in_array($crm_fields_vals->field_name, $js_mandatory_fields))
							$js_mandatory_fields[] = $crm_fields_vals->field_label;
						$js_mandatory_fields_hidden[] = $crm_fields_vals->field_name;
					}
					$j++;
				}
				$js_mandatory_array = json_encode($js_mandatory_fields);
				$js_mandatory_array_hidden = json_encode($js_mandatory_fields_hidden);
				$crm_field_options = '';
				$crm_field_options .= "<option>--None--</option>";
				foreach( $crm_field_labels as $field_key => $crm_field_label )
				{
					$crm_field_options .= "<option value='{$crm_field_label}'> $crm_field_label</option>";
				}  

				$fields_html = '';
				$fields_html .= "<div><div class='form-group col-md-12'><div class='exist_mapping col-md-6'><div class='leads-builder-heading'> WpForm Fields</div></div><div class='exist_mapping col-md-4'><div class='leads-builder-heading'> CRM Fields</div></div> </div>";
				$i = 1;	
				foreach( $wpform_form_labels as $wpf_id => $wpf_label)
				{
					$fields_html .= "<div class='form-group col-md-12'>
									 <div class='col-md-6'><label class='leads-builder-label'> $wpf_label </label></div>
									 <input type='hidden' name='thirdpartyfield_$i' id='thirdpartyfield_$i' value='$wpf_label' />";
					$fields_html .= "<div class='col-md-4'><select class='form-control' style='width:150px;' name='crm_fields_$i' id='crm_fields_$i' >";
					$crm_field_options = '';
					$crm_field_options .= "<option>--None--</option>";
					foreach( $crm_field_labels as $field_key => $crm_field_label ) // Prepare crm fileds drop down
					{
						$crm_field_options .= "<option value='{$crm_field_label}'";
						foreach( $wpform_config as $config_key => $config_val ) // configuration
						{
							if( $wpf_label == $config_key && $crm_field_label == $config_val ) //match label and fieldname
							{
								$crm_field_options .= "selected=selected";//select when the configuration exist
							}
						}

						$crm_field_options .= "> $field_key</option>";
					}
					$fields_html .= $crm_field_options;			
					$fields_html .=	"</select>
						</div>
						</div>";
					$i++;
				}
			}
			else
			{
				$fields_html = "";
				$map_options = "";
				$fields_html .= "<span style='color:red;font-size:18px;margin-left:23%;'>Please choose any form</span>";
			}
			$fields_html .= "<input type='hidden' value='$i' id='total_field_count'>";
			$fields_html .= "<input type='hidden' value='$third_module' id='module'>";
			$fields_html .= "<input type='hidden' value='$activated_crm' id='active_crm'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_form_name' id='form_name'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_plugin' id='thirdparty_plugin'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array' id='crm_mandatory_fields'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array_hidden' id='crm_mandatory_fields_hidden'>";
			$fields_html .= "</div>";
			$html_data_array = array();
			$html_data_array['map_options'] = $map_options;
			$html_data_array['fields_html'] = $fields_html;
			print_r( json_encode($html_data_array) );
			die;
			break;

		case 'wpformpro':

			$shortcode = $activated_crm."_wp_wpform_pro".$thirdparty_form_name;
			$config = get_option( $shortcode );
			$wpformpro_config = $config['fields'];
			$assigned_to_user = $this->get_assignedto($shortcode);
			$map_options = '';
			$map_options .= "<div>	
							 <div class='form-group col-md-12'><div class='exist_mapping col-md-6'><label id='innertext' class='leads-builder-label'> Module Type</label> </div><div class='col-md-4'> $third_module</div></div>
							 <div class='form-group col-md-12'><div class='exist_mapping col-md-6'><label id='innertext' class='leads-builder-label'> Form Type </label></div><div class='col-md-4'> $thirdparty_plugin</div></div>
							 <div class='form-group col-md-12'><div class='exist_mapping col-md-6'><label id='innertext' class='leads-builder-label'> Form Title </label></div><div class='exist_mapping col-md-4'> $thirdparty_title</div></div>
							 <div class='form-group col-md-12'> <div class='exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label'> Duplicate Handling</label> </div> 
							 <div class='exist_mapping col-md-4'> <select class='form-control' id='duplicate_handling'><option value='skip'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip' )
			{
				$map_options .= "selected=selected";	
			}
			$map_options .= "> Skip</option>";
			if($activated_crm=="mailchimp"){

			}else{
				$map_options.="	<option value='skip_both'";
				if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip_both' )
				{
					$map_options .= "selected=selected";
				}
				$map_options .= " > Skip if already a Contact or Lead</option>";
			}
			$map_options.="	<option value='update'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'update' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .= ">Update</option> <option value='create'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'create' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .="> Create </option></select></div></div></div>";

			$map_options .="<div><div class='form-group col-md-12'> <div class='assign_leads exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label' >";
			if( $third_module == "Leads")
			{
				$map_options .= "Lead Owner";
				$map_options .= "</label></div><div class='exist_mapping col-md-4'> $assigned_to_user</div></div><div class='form-group col-md-12'><div col-md-6>";
			} else if( $third_module == "Contacts" )
			{
				$map_options .= "Contact Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
			}else if( $third_module == "Members" )
			{
				$map_options .= "Member Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
			}
			$map_options .= "</label></div></div>";

			if( $thirdparty_form_name != '--None--' )
			{
				$get_json_array = $wpdb->get_results( $wpdb->prepare( "select ID,post_content from $wpdb->posts where ID=%d" , $thirdparty_form_name ) );
				$wpformpro_post_content = json_decode($get_json_array[0]->post_content);

				$wpformpro_all_fields = $wpformpro_post_content->fields;

				foreach($wpformpro_all_fields as $wpformpro_values){
					$wpformpro_form_labels[$wpformpro_values->id] = $wpformpro_values->label;
				}

				$CaptureDataObj = new CaptureData();
				$crm_fields = $CaptureDataObj->get_crmfields_by_settings( $activated_crm , $third_module );
				$j = 1;

				$js_mandatory_fields = array();
				$js_mandatory_fields_hidden = array();
				foreach( $crm_fields as $crm_field_key => $crm_fields_vals ) {
					$crm_field_labels[$crm_fields_vals->field_label] = $crm_fields_vals->field_name;
					if( $crm_fields_vals->field_mandatory == 1 )
					{
						if(!in_array($crm_fields_vals->field_name, $js_mandatory_fields))
							$js_mandatory_fields[] = $crm_fields_vals->field_label;
						$js_mandatory_fields_hidden[] = $crm_fields_vals->field_name;
					}
					$j++;
				}
				$js_mandatory_array = json_encode($js_mandatory_fields);
				$js_mandatory_array_hidden = json_encode($js_mandatory_fields_hidden);
				$crm_field_options = '';
				$crm_field_options .= "<option>--None--</option>";
				foreach( $crm_field_labels as $field_key => $crm_field_label )
				{
					$crm_field_options .= "<option value='{$crm_field_label}'> $crm_field_label</option>";
				}  

				$fields_html = '';
				$fields_html .= "<div><div class='form-group col-md-12'><div class='exist_mapping col-md-6'><div class='leads-builder-heading'> WpForm Pro Fields</div></div><div class='exist_mapping col-md-4'><div class='leads-builder-heading'> CRM Fields</div></div> </div>";
				$i = 1;	
				foreach( $wpformpro_form_labels as $wpf_id => $wpf_label)
				{
					$fields_html .= "<div class='form-group col-md-12'>
									 <div class='col-md-6'><label class='leads-builder-label'> $wpf_label </label></div>
									 <input type='hidden' name='thirdpartyfield_$i' id='thirdpartyfield_$i' value='$wpf_label' />";
					$fields_html .= "<div class='col-md-4'><select class='form-control' style='width:150px;' name='crm_fields_$i' id='crm_fields_$i' >";
					$crm_field_options = '';
					$crm_field_options .= "<option>--None--</option>";
					foreach( $crm_field_labels as $field_key => $crm_field_label ) // Prepare crm fileds drop down
					{
						$crm_field_options .= "<option value='{$crm_field_label}'";
						foreach( $wpformpro_config as $config_key => $config_val ) // configuration
						{
							if( $wpf_label == $config_key && $crm_field_label == $config_val ) //match label and fieldname
							{
								$crm_field_options .= "selected=selected";//select when the configuration exist
							}
						}

						$crm_field_options .= "> $field_key</option>";
					}
					$fields_html .= $crm_field_options;			
					$fields_html .=	"</select>
						</div>
						</div>";
					$i++;
				}
			}
			else
			{
				$fields_html = "";
				$map_options = "";
				$fields_html .= "<span style='color:red;font-size:18px;margin-left:23%;'>Please choose any form</span>";
			}
			$fields_html .= "<input type='hidden' value='$i' id='total_field_count'>";
			$fields_html .= "<input type='hidden' value='$third_module' id='module'>";
			$fields_html .= "<input type='hidden' value='$activated_crm' id='active_crm'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_form_name' id='form_name'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_plugin' id='thirdparty_plugin'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array' id='crm_mandatory_fields'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array_hidden' id='crm_mandatory_fields_hidden'>";
			$fields_html .= "</div>";
			$html_data_array = array();
			$html_data_array['map_options'] = $map_options;
			$html_data_array['fields_html'] = $fields_html;
			print_r( json_encode($html_data_array) );
			die;
			break;

		case 'elementorform':
			$shortcode = $activated_crm."_wp_elementor".$thirdparty_form_name;
			$config = get_option( $shortcode );
			$elementorConfig = $config['fields'];

			$assigned_to_user = $this->get_assignedto($shortcode);
			$map_options = '';
			$map_options .= "<div>
							 <div class='form-group col-md-12'><div class='exist_mapping col-md-6'><label id='innertext' class='leads-builder-label'> Module Type</label> </div><div class='exist_mapping col-md-4'> $third_module</div></div>
							 <div class='form-group col-md-12'><div class='exist_mapping col-md-6'><label id='innertext' class='leads-builder-label'> Form Type </label></div><div class='exist_mapping col-md-4'> $thirdparty_plugin</div></div>
							 <div class='form-group col-md-12'><div class='exist_mapping col-md-6'><label id='innertext' class='leads-builder-label'> Form Title </label></div><div class='exist_mapping col-md-4'> $thirdparty_title</div></div>
							 <div class='form-group col-md-12'> <div class='col-md-6'> <label id='innertext' class='leads-builder-label'> Duplicate Handling</label> </div> 
							 <div class='exist_mapping col-md-4'> <select class='form-control' id='duplicate_handling'><option value='skip'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip' )
			{
				$map_options .= "selected=selected";	
			}
			$map_options .= "> Skip</option>";
			if($activated_crm=="mailchimp"){

			}else{
				$map_options.="	<option value='skip_both'";
				if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip_both' )
				{
					$map_options .= "selected=selected";
				}
				$map_options .= "> Skip if already a Contact or Lead</option>";
			}
			$map_options.="<option value='update'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'update' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .= ">Update</option> <option value='create'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'create' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .="> Create </option></select></div></div></div>";

			$map_options .="<div><div class='form-group col-md-12'> <div class='assign_leads exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label' >";
			if( $third_module == "Leads")
			{
				$map_options .= "Lead Owner";
				$map_options .= "</label></div><div class='exist_mapping col-md-4'> $assigned_to_user</div></div><div class='form-group col-md-12'><div col-md-6>";
			} else if( $third_module == "Contacts" )
			{
				$map_options .= "Contact Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
			}else if( $third_module == "Members" )
			{
				$map_options .= "Member Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
			}
			$map_options .= "</label></div></div>";

			if( $thirdparty_form_name != '--None--' )
			{
				//$getFormData = $wpdb->get_results("select meta_value from $wpdb->postmeta where meta_key ='__elementor_forms_snapshot' and post_id = $thirdparty_form_name",ARRAY_A);
				$getFormData = $wpdb->get_results(
					$wpdb->prepare("
						SELECT meta_value 
						FROM {$wpdb->postmeta} 
						WHERE meta_key = '__elementor_forms_snapshot' 
						AND post_id = %s
					", $thirdparty_form_name),
					ARRAY_A
				);				
				$formData = $getFormData[0]['meta_value'];
				$decodedData = json_decode($formData);
				$formFields = $decodedData[0]->fields;

				foreach($formFields as $elVal){

					$decodedFormFields = json_decode(json_encode($elVal), true);
					$elementor_form_labels[$decodedFormFields['id']] = $decodedFormFields['label']; 
				}

				$CaptureDataObj = new CaptureData();
				$crm_fields = $CaptureDataObj->get_crmfields_by_settings( $activated_crm , $third_module );
				$j = 1;

				$js_mandatory_fields = array();
				$js_mandatory_fields_hidden = array();

				foreach( $crm_fields as $crm_field_key => $crm_fields_vals ) {
					$crm_field_labels[$crm_fields_vals->field_label] = $crm_fields_vals->field_name;
					if( $crm_fields_vals->field_mandatory == 1 )
					{
						if(!in_array($crm_fields_vals->field_name, $js_mandatory_fields))
							$js_mandatory_fields[] = $crm_fields_vals->field_label;
						$js_mandatory_fields_hidden[] = $crm_fields_vals->field_name;
					}
					$j++;
				}
				$js_mandatory_array = json_encode($js_mandatory_fields);
				$js_mandatory_array_hidden = json_encode($js_mandatory_fields_hidden);
				$crm_field_options = '';
				$crm_field_options .= "<option>--None--</option>";
				foreach( $crm_field_labels as $field_key => $crm_field_label )
				{
					$crm_field_options .= "<option value='{$crm_field_label}'> $crm_field_label</option>";
				}  

				$fields_html = '';
				$fields_html .= "<div><div class='form-group col-md-12'><div class='exist_mapping col-md-6'> <div class='leads-builder-heading'>ElementorForm Fields</div></div><div class='exist_mapping col-md-4'> <div class='leads-builder-heading'>CRM Fields</div></div></div> ";
				$i = 1;	

				foreach( $elementor_form_labels as $ele_id => $ele_label)
				{
					$fields_html .= "<div class='form-group col-md-12'>
									 <div class='col-md-6'><label class='leads-builder-label'> $ele_label </label></div>
									 <input type='hidden' name='thirdpartyfield_$i' id='thirdpartyfield_$i' value='$ele_id' />";
					$fields_html .= "<div class='col-md-4'><select class='form-control'  name='crm_fields_$i' id='crm_fields_$i' >";
					$crm_field_options = '';
					$crm_field_options .= "<option>--None--</option>";
					foreach( $crm_field_labels as $field_key => $crm_field_label ) // Prepare crm fileds drop down
					{
						$crm_field_options .= "<option value='{$crm_field_label}'";

						if(is_array($elementorConfig )) {
							foreach( $elementorConfig as $conKey => $conVal ) // configuration
							{
								if( $ele_id == $conKey && $crm_field_label == $conVal ) //match label and fieldname
								{
									$crm_field_options .= "selected=selected";//select when the configuration exist
								}
							}
						}
						$crm_field_options .= "> $field_key</option>";
					}

					$fields_html .= $crm_field_options;			
					$fields_html .=	"</select>
						</div>
						</div>";
					$i++;
				}
			}
			else
			{
				$fields_html = "";
				$map_options = "";
				$fields_html .= "<span style='color:red;font-size:18px;margin-left:23%;'>Please choose any form</span>";
			}

			$fields_html .= "<input type='hidden' value='$i' id='total_field_count'>";
			$fields_html .= "<input type='hidden' value='$third_module' id='module'>";
			$fields_html .= "<input type='hidden' value='$activated_crm' id='active_crm'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_form_name' id='form_name'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_plugin' id='thirdparty_plugin'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array' id='crm_mandatory_fields'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array_hidden' id='crm_mandatory_fields_hidden'>";
			$fields_html .= "</div>";
			$html_data_array = array();
			$html_data_array['map_options'] = $map_options;
			$html_data_array['fields_html'] = $fields_html;
			print_r( json_encode($html_data_array) );
			die;
			break;
		case 'calderaform':
			$shortcode = $activated_crm."_wp_caldera".$thirdparty_form_name;
			$config = get_option( $shortcode );
			$caldera_config = $config['fields'];
			$assigned_to_user = $this->get_assignedto($shortcode);
			$map_options = '';
			$map_options .= "<div>
							 <div class='form-group col-md-12'><div class='exist_mapping col-md-6'><label id='innertext' class='leads-builder-label'> Module Type</label> </div><div class='exist_mapping col-md-4'> $third_module</div></div>
							 <div class='form-group col-md-12'><div class='exist_mapping col-md-6'><label id='innertext' class='leads-builder-label'> Form Type </label></div><div class='exist_mapping col-md-4'> $thirdparty_plugin</div></div>
							 <div class='form-group col-md-12'><div class='exist_mapping col-md-6'><label id='innertext' class='leads-builder-label'> Form Title </label></div><div class='exist_mapping col-md-4'> $thirdparty_title</div></div>
							 <div class='form-group col-md-12'> <div class='col-md-6'> <label id='innertext' class='leads-builder-label'> Duplicate Handling</label> </div> 
							 <div class='exist_mapping col-md-4'> <select class='form-control' id='duplicate_handling'><option value='skip'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip' )
			{
				$map_options .= "selected=selected";	
			}
			$map_options .= "> Skip</option>";
			if($activated_crm=="mailchimp"){

			}else{
				$map_options.="	<option value='skip_both'";
				if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'skip_both' )
				{
					$map_options .= "selected=selected";
				}
				$map_options .= "> Skip if already a Contact or Lead</option>";
			}
			$map_options.="<option value='update'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'update' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .= ">Update</option> <option value='create'";
			if( isset( $config['thirdparty_duplicate']) && $config['thirdparty_duplicate'] == 'create' )
			{
				$map_options .= "selected=selected";
			}
			$map_options .="> Create </option></select></div></div></div>";

			$map_options .="<div><div class='form-group col-md-12'> <div class='assign_leads exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label' >";
			if( $third_module == "Leads")
			{
				$map_options .= "Lead Owner";
				$map_options .= "</label></div><div class='exist_mapping col-md-4'> $assigned_to_user</div></div><div class='form-group col-md-12'><div col-md-6>";
			} else if( $third_module == "Contacts" )
			{
				$map_options .= "Contact Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
			}else if( $third_module == "Members" )
			{
				$map_options .= "Member Owner";
				$map_options .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
			}
			$map_options .= "</label></div></div>";


			if( $thirdparty_form_name != "--None--" )
			{		

				//$get_json_array = $wpdb->get_results( "SELECT config from {$wpdb->prefix}cf_forms where form_id = '$thirdparty_form_name' and type = 'primary' ",ARRAY_A );
				$get_json_array = $wpdb->get_results(
					$wpdb->prepare("
						SELECT config 
						FROM {$wpdb->prefix}cf_forms 
						WHERE form_id = %s 
						AND type = 'primary'
					", $thirdparty_form_name),
					ARRAY_A
				);	
				$caldera_form_labels = array();
				foreach( $get_json_array as $caldera_key => $calders_data )
				{
					$caldera_config_data = unserialize($calders_data['config']);
					$caldera_fields = $caldera_config_data['fields'];
					$i = 1;
					foreach($caldera_fields as $caldera_field_key => $caldera_field_value){

						$caldera_form_labels[$caldera_field_key] = $caldera_field_value['label']; 

						if(isset($caldera_field_value['config']['type']) && $caldera_field_value['config']['type'] == 'submit'){
							unset($caldera_form_labels[$caldera_field_key]);
						}
						$i++;
					}
				}	

				$CaptureDataObj = new CaptureData();
				$crm_fields = $CaptureDataObj->get_crmfields_by_settings( $activated_crm , $third_module );
				$j = 1;

				$js_mandatory_fields = array();
				$js_mandatory_fields_hidden = array();
				foreach( $crm_fields as $crm_field_key => $crm_fields_vals ) {
					$crm_field_labels[$crm_fields_vals->field_label] = $crm_fields_vals->field_name;
					if( $crm_fields_vals->field_mandatory == 1 )
					{
						if(!in_array($crm_fields_vals->field_name, $js_mandatory_fields))
							$js_mandatory_fields[] = $crm_fields_vals->field_label;
						$js_mandatory_fields_hidden[] = $crm_fields_vals->field_name;
					}
					$j++;
				}
				$js_mandatory_array = json_encode($js_mandatory_fields);
				$js_mandatory_array_hidden = json_encode($js_mandatory_fields_hidden);
				$crm_field_options = '';
				$crm_field_options .= "<option>--None--</option>";
				foreach( $crm_field_labels as $field_key => $crm_field_label )
				{

					$crm_field_options .= "<option value='{$crm_field_label}'> $crm_field_label</option>";
				}  

				$fields_html = '';
				$fields_html .= "<div><div class='form-group col-md-12'><div class='exist_mapping col-md-6'><div class='leads-builder-heading'> CalderaForm Fields</div></div><div class='exist_mapping col-md-4'> <div class='leads-builder-heading'>CRM Fields</div></div></div> ";
				$i = 1;	
				foreach( $caldera_form_labels as $cal_id => $cal_label)
				{
					$fields_html .= "<div class='form-group col-md-12'>
									 <div class='col-md-6'><label class='leads-builder-label'> $cal_label </label></div>
									 <input type='hidden' name='thirdpartyfield_$i' id='thirdpartyfield_$i' value='$cal_id' />";
					$fields_html .= "<div class='exist_mapping col-md-4'><select class='form-control'  name='crm_fields_$i' id='crm_fields_$i' >";
					$crm_field_options = '';
					$crm_field_options .= "<option>--None--</option>";
					foreach( $crm_field_labels as $field_key => $crm_field_label ) // Prepare crm fileds drop down
					{
						$crm_field_options .= "<option value='{$crm_field_label}'";
						foreach( $caldera_config as $config_key => $config_val ) // configuration
						{
							if( $cal_id == $config_key && $crm_field_label == $config_val ) //match label and fieldname
							{
								$crm_field_options .= "selected=selected";//select when the configuration exist
							}
						}
						$crm_field_options .= "> $field_key</option>";
					}
					$fields_html .= $crm_field_options;			
					$fields_html .=	"</select>
									 </div>
									 </div>";
					$i++;
				}
			}
			else
			{
				$fields_html = "";
				$map_options = "";
				$fields_html .= "<span style='color:red;font-size:18px;margin-left:23%;'>Please choose any form</span>";
			}
			$fields_html .= "<input type='hidden' value='$i' id='total_field_count'>";
			$fields_html .= "<input type='hidden' value='$third_module' id='module'>";
			$fields_html .= "<input type='hidden' value='$activated_crm' id='active_crm'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_form_name' id='form_name'>";
			$fields_html .= "<input type='hidden' value='$thirdparty_plugin' id='thirdparty_plugin'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array' id='crm_mandatory_fields'>";
			$fields_html .= "<input type='hidden' value='$js_mandatory_array_hidden' id='crm_mandatory_fields_hidden'>";
			$fields_html .= "</div>";
			$html_data_array = array();
			$html_data_array['map_options'] = $map_options;
			$html_data_array['fields_html'] = $fields_html;
			print_r( json_encode($html_data_array) );
			die;
			break;

		default:
			$html = "";
			$html .= "<span style='color:red;font-size:16px;margin-left:12%;'> Please configure your third party form under Form Settings  </span>";
			return $html;
			break;      	

		}
	}

	function delete_mapped_configuration()
	{	
		$activated_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
		$thirdparty_plugin = sanitize_text_field( $_REQUEST['third_plugin'] );
		$thirdparty_form_id = sanitize_text_field( $_REQUEST['form_id'] );
		switch( $thirdparty_plugin )
		{
		case 'gravityform':
			$option_name = $activated_crm.'_wp_gravity'.$thirdparty_form_id;
			break;

		case 'ninjaform':
			$option_name = $activated_crm.'_wp_ninja'.$thirdparty_form_id;
			break;

		case 'contactform':
			$option_name = $activated_crm.'_wp_contact'.$thirdparty_form_id;
			break;
		case 'wpform':
			$option_name = $activated_crm.'_wp_wpform_lite'.$thirdparty_form_id;
			break;
		case 'wpformpro':
			$option_name = $activated_crm.'_wp_wpform_pro'.$thirdparty_form_id;
			break;
		case 'calderaform':
			$option_name = $activated_crm.'_wp_caldera'.$thirdparty_form_id;
			break;
		case 'elementorform':
			$option_name = $activated_crm.'_wp_elementor'.$thirdparty_form_id;
			break;
		} 
		delete_option( $option_name );die;
	}

}
