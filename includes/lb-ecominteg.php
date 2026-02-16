<?php

if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

class EcommerceSettingsActions  {

    public function __construct()
    {
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
            // return an array of name value pairs to send data to the template
            $data = array();
            foreach( $request as $key => $REQUESTS )
            {
                foreach( $REQUESTS as $REQUESTS_KEY => $REQUESTS_VALUE )
                {
                        $data['REQUEST'][$REQUESTS_KEY] = $REQUESTS_VALUE;
                }
            }

            $data['HelperObj'] = new WPCapture_includes_helper_PRO();
            $data['module'] = $data['HelperObj']->Module;
            $data['moduleslug'] = $data['HelperObj']->ModuleSlug;
            $data['activatedplugin'] = $data['HelperObj']->ActivatedPlugin;
            $data['activatedpluginlabel'] = $data['HelperObj']->ActivatedPluginLabel;
            $data['plugin_dir']= SM_LB_PRO_DIR;
            $data['plugins_url'] = SM_LB_DIR;
            $data['siteurl'] = site_url();
            if( isset($data['REQUEST']["smack-{$data['activatedplugin']}-user-capture-settings-form"]) )
            {
                    $this->saveSettingArray($data);
            }
            return $data;
    }

	public function get_ecom_assignedto($shortcode_option)
        {
                //Assign Leads And Contacts to User

        $crm_users_list = get_option( 'crm_users' );
        $activated_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
        $assignedtouser_config = get_option( $shortcode_option );
        $assignedtouser_config_leads = $assignedtouser_config['ecom_assignedto'] ?? '';
        $Assigned_users_list = $crm_users_list[$activated_crm];

                switch( $activated_crm )
                {
                        case 'wpzohopro':	
                        case 'wpzohopluspro':
                                $html_leads = "";
                                $html_leads = '<select class="form-control"name="ecom_mapping_assignedto" id="ecom_mapping_assignedto">';
                                $content_option_leads = "";
                                $content_option_leads = "<option id='select' value='--Select--'>--Select--</option>";
                                if(isset($Assigned_users_list['user_name']))
                                $count=count($Assigned_users_list['user_name']);
                                for($i = 0; $i <  $count ; $i++)
                                {
                                        $content_option_leads.="<option id='{$Assigned_users_list['user_name'][$i]}' value='{$Assigned_users_list['id'][$i]}'";
                                        if($Assigned_users_list['id'][$i] == $assignedtouser_config_leads )
                                        {
                                                $content_option_leads .=" selected";
                                        }

                                        $content_option_leads .=">{$Assigned_users_list['user_name'][$i]}</option>";
                                }
                                $content_option_leads .= "<option id='rr_ecom_owner' value='Round Robin'";
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
                                $html_leads = '<select class="form-control" name="ecom_mapping_assignedto" id="ecom_mapping_assignedto">';
                                $content_option_leads = "";
                                $content_option_leads = "<option id='select' value='--Select--'>--Select--</option>";
                                if(isset($Assigned_users_list['last_name']))
                                $count = count($Assigned_users_list['last_name']);
                                for($i = 0; $i < $count ; $i++)
                                {
                                        $content_option_leads.="<option id='{$Assigned_users_list['last_name'][$i]}' value='{$Assigned_users_list['id'][$i]}'";
                                        if($Assigned_users_list['id'][$i] == $assignedtouser_config_leads )
                                        {
                                                $content_option_leads .=" selected";
                                        }

                                        $content_option_leads .=">{$Assigned_users_list['last_name'][$i]}</option>";
                                }
                                $content_option_leads .= "<option id='rr_ecom_owner' value='Round Robin'";
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
                                $html_leads = '<select class="form-control" name="ecom_mapping_assignedto" id="ecom_mapping_assignedto" style="min-width:69px;">';
                                $content_option_leads = "";

                                $content_option_leads = "<option id='select' value='--Select--'>--Select--</option>";

                                if(isset($Assigned_users_list['user_name']))
                                $count=count($Assigned_users_list['user_name']);
                                        for($i = 0; $i < $count ; $i++)
                                        {
                                                $content_option_leads .="<option id='{$Assigned_users_list['id'][$i]}' value='{$Assigned_users_list['id'][$i]}'";
                                                if($Assigned_users_list['id'][$i] == $assignedtouser_config_leads)
                                                {
                                                        $content_option_leads .=" selected";
                                                }

                                                $content_option_leads .=">{$Assigned_users_list['first_name'][$i]} {$Assigned_users_list['last_name'][$i]}</option>";
                                        }
                                $content_option_leads .= "<option id='rr_ecom_owner' value='Round Robin'";
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
                                $html_leads = '<select class="form-control" name="ecom_mapping_assignedto" id="ecom_mapping_assignedto" style="min-width:69px;">';
                                $content_option_leads = "";
                                $content_option_leads = "<option id='select' value='--Select--'>--Select--</option>";
                                if(isset($Assigned_users_list['user_name']))
                                $count=count($Assigned_users_list['user_name']);
                                for($i = 0; $i < $count ; $i++)
                                {
                                        $content_option_leads .="<option id='{$Assigned_users_list['id'][$i]}' value='{$Assigned_users_list['id'][$i]}'";

                                        if($Assigned_users_list['id'][$i] == $assignedtouser_config_leads)
                                        {
                                                $content_option_leads .=" selected";

                                        }
                                        $content_option_leads .=">{$Assigned_users_list['first_name'][$i]} {$Assigned_users_list['last_name'][$i]}</option>";
                                }	
                                $content_option_leads .= "<option id='rr_ecom_owner' value='Round Robin'";
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

}
