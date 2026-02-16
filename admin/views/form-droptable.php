<?php
/**
 * WP Leads Builder For Any CRM.
 *
 * Copyright (C) 2010-2020, Smackcoders Inc - info@smackcoders.com
 */

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly
?>

<?php
echo '<br>';   
$droptable_config = get_option( "wp_droptablepro_settings" );
?>
<div class='mt15'>
<div class="panel"  style="width:auto; margin-right: 20px;" id="all_addons_view">
	<div class="panel-body_setting">
		<div class="drop_table_div">
			<center class="drop_table_align"><h4 class="addon_button_heading" style="margin-right:15px">Drop Table Setting</h4></center>
			<input value="<?php echo esc_attr__('Drop' , 'wp-leads-builder-any-crm-pro' );?>" onclick="drop_table_key();"
					id="droptable" type='checkbox' class="tgl tgl-skewed noicheck smack-vtiger-settings-text" name='droptable'
					<?php if(isset($droptable_config['droptable']) && sanitize_text_field($droptable_config['droptable']) == 'on') { echo "checked=checked"; } ?>
					onclick="droptable(this.id)" />
			<label id="innertext" data-tg-off="OFF" data-tg-on="ON" for="droptable" class="tgl-btn" style="font-size: 16px;"></label>
		</div>
		<center><p class="text-muted">*If enabled, plugin deactivation will permanently delete plugin data, which cannot be recovered.</p></center>
	</div>
</div>
</div>