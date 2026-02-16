<?php
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

class ecom_configuration{

	public function change_module_config()
        {
                $fields_html  = "";
		$fields_html .= "<div id='ecommerce-mapping' class='ecommerce-mapping'>
                                <div>
                                <div id='inneroptions' class='leads-builder-heading'>Configure Field Mapping</div>
                                </div>
                                <div class='form-group col-md-12'>
                                <div class='col-md-3'>
                                <div id='ecom_innertext' class='leads-builder-sub-heading'> WooCommerce Fields </div>
                                </div>
                                <div class='col-md-4'>
                                <div id='ecom_innertext' class='leads-builder-sub-heading'> CRM Fields </div>
                                </div>
                                </div>";
                $activated_crm = sanitize_text_field( $_REQUEST['ecom_active_crm'] );
                $third_module = sanitize_text_field( $_REQUEST['ecom_module'] );
		//Update ecom wc module
		update_option( 'ecom_wc_module' , $third_module );		

		$wc_config = get_option( "ecom_wc_{$activated_crm}_{$third_module}_config" );
		$wc_config_fields = $wc_config['fields'];	

                // To fetch the checkout form fields.
                $checkout_fields = new WC_Countries();
                $checkout_fields = $checkout_fields->get_address_fields();
                //Get Woocommerce Checkout Fields       
                $wc_field_labels = array();
                foreach( $checkout_fields as $check_key => $check_value )
                {
			if( $check_value['label'] != "" )
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
                $js_mandatory_fields = array();
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
                $i = 1;
                foreach( $wc_field_labels as $cont_id => $cont_label)
                {
                $fields_html .= "<div class='form-group col-md-12'>
                                <div class='col-md-3'><label class='leads-builder-label'> $cont_label </label></div>
                                <input type='hidden' name='ecom_field_$i' id='ecom_field_$i' value='$cont_label' />";


                $fields_html .= "<div class='col-md-3'><select class='lead_searchable form-control' name='ecom_crm_fields_$i' id='ecom_crm_fields_$i' >";
                                $crm_field_options = '';
                $crm_field_options .= "<option>--None--</option>";
                foreach( $crm_field_labels as $field_key => $crm_field_label ) // Prepare crm fileds drop down
                {

                        $crm_field_options .= "<option value='{$crm_field_label}'";
                        foreach( $wc_config_fields as $config_key => $config_val ) // configuration
                        {
                                if( $cont_label == $config_key && $crm_field_label == $config_val ) //match label and fieldname
                                {
                                        $crm_field_options .= "selected=selected";//select when the configuration exist
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
                $ecom_assignedto_name = $wc_config['ecom_assignedto'];
                echo "<input type='hidden' id='ecom_wc_owner' value='$ecom_assignedto_name'>";
                $fields_html .= "</div>";
                echo "<input type='hidden' value='$i' id='ecom_total_field_count'>";
                echo "<input type='hidden' value='$third_module' id='ecom_module'>";
                echo "<input type='hidden' value='$js_mandatory_array' id='ecom_crm_mandatory_fields'>";
                print_r( $fields_html );die;
//END COPY
        }

	public function map_ecom_module_configuration()
	{
                $ecom_final_mapping= [];
                $thirdparty_mapped_labels = [];
                $crm_labels=[];
		$activated_crm = get_option( "WpLeadBuilderProActivatedPlugin" );
		$ecom_final_module = sanitize_text_field( $_REQUEST['ecom_module'] );
		$ecom_final_assignedto = sanitize_text_field( $_REQUEST['ecom_assigned_to'] );
		$ecom_final_posted_data = $_REQUEST['postdata'];	
		
		foreach( $ecom_final_posted_data as $data_key => $data_val  )
                {
                        if( preg_match('/^ecom_field/' , $data_key ) )
                        {
                                $thirdparty_key = ltrim( $data_key , 'ecom_field_' );
                                $thirdparty_labels[$thirdparty_key] = $data_val; // Make thirdparty label array
                        }

                        if( preg_match('/^ecom_crm_fields/' , $data_key ) )
                        {
                                $crm_field_key = ltrim( $data_key , 'ecom_crm_fields_' );
                                if( $data_val != '--None--' )
                                {
                                	$crm_labels[$crm_field_key] = $data_val; // Make crm labels array -take only mapped values
                                }
                        }
			if( $data_key == 'ecom_convert_lead' )
			{
				$save_convert_lead = $data_val;
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
		$ecom_final_mapping['fields'] = $mapped_array;
		$ecom_final_mapping['ecom_module'] = $ecom_final_module;
		$ecom_final_mapping['ecom_assignedto'] = $ecom_final_assignedto;
		$ecom_final_mapping['ecom_duplicate_option'] = 'cretae';
		$ecom_final_mapping['ecom_crm'] = $activated_crm;
		$ecom_final_mapping['ecom_converted_lead'] = $save_convert_lead;
		update_option( "ecom_wc_{$activated_crm}_{$ecom_final_module}_config" , $ecom_final_mapping);
	}
}
?>
