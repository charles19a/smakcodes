<?php
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly
?>

<div class='mt30'>
	<div class='panel' style='width:98%;'>
		<div class='panel-body'>
			<?php
global $wpdb;
require_once(SM_LB_PRO_DIR.'includes/Chart_Data.php');
		echo '<table id="opportunity_filter" style="width:99%" ><tr>
			<td class="col-md-10">
			</td>
			<td>
			<a href="#" id="opportunity_oneday" style="text-align:right;">1D </a><span style="padding-left:10px;color:#00a699"> | </span> </td>
			<td style="float:left;">
			<a href="#" id="opportunity_oneweek"  style="text-align:right;">1W </a> <span style="padding-left:10px;color:#00a699;"> | </span></td>
			<td style="float:left;">
			<a href="#" id="opportunity_onemonth" style="margin-left:15px;text-align:right;">1M </a> </td>
			</tr></table>';
	
		echo "<div class='header_title'><div class='leads-builder-heading'>".esc_html('Unsuccessful Payments')." </div></div>";
		echo "<div id='failed_payment'>";
		//Failed Payments
		$failed_payments = $wpdb->get_results( $wpdb->prepare("select * from wci_successful_purchases where order_status=%s" , 'Failed' ),ARRAY_A );

	if(empty( $failed_payments )  ) {
		?>
				<table class="table woocustomer-table" style='width:99%;border:1px solid #E5E5E5;'>

					<tr class="thone" style='width:100%;'>
						<th class='col-md-3'> Customer Name</th>
						<th class='col-md-2'> Email</th>
						<th class='col-md-2'> Order Id</th>
						<th class='col-md-3'> Products</th>
						<th class='col-md-2'> Date </th>
					</tr>

					<tr class="rowtwo">
						<td></td>
						<td></td>
						<td>--No Records Found--</td>
						<td></td>
						<td></td>
					</tr>
				</table>

				<?php
	}
	else
	{
		?>
				<table class="table woocustomer-table" style='width:99%;border:1px solid #E5E5E5;'>

					<tr class="thone">
						<th class='col-md-3;'> Customer Name </th>
						<th class='col-md-2'> Email</th>
						<th class='col-md-2'> Order Id</th>
						<th class='col-md-3'> Products</th>
						<th class='col-md-2'> Date</th>
					</tr>

					<?php
			$i = 0;
			foreach( $failed_payments as  $failed_payments_users )
			{
				$i++;
				if( $i % 2 == 0 )
				{
					echo "<tr class='rowone'>";
					echo "<td>". esc_html($failed_payments_users['user_name']) ."</td>";
					echo "<td>". sanitize_email($failed_payments_users['user_email']) ."</td>";
					echo "<td style='padding-left:40px;'>". esc_html($failed_payments_users['order_id']) ."</td>";
					echo "<td>". esc_html($failed_payments_users['products']) ."</td>";
					echo "<td>". esc_html($failed_payments_users['date']) ."</td>";
					echo "</tr>";	
				}
				else
				{
					echo "<tr class='rowtwo'>";
								echo "<td>". esc_html($failed_payments_users['user_name']) ."</td>";
								echo "<td>". sanitize_email($failed_payments_users['user_email']) ."</td>";
								echo "<td style='padding-left:40px;'>". esc_html($failed_payments_users['order_id']) ."</td>";
								echo "<td>". esc_html($failed_payments_users['products']) ."</td>";
								echo "<td>". esc_html($failed_payments_users['date']) ."</td>";
								echo "</tr>";
				}
			}
		?>
				</table>
				<?php
	}

	echo "</div>";
	// END Failed Payments

	//ABUNDANT 
	$check_abandon_empty_flag = 0;
	$get_session_ids = array();
	$get_session_ids = $wpdb->get_results( "select distinct session_id from wci_user_session where is_cart='1'" , ARRAY_A);
			echo "<div class='header_title'><div class='leads-builder-heading'>".esc_html('Abandon Cart')." </div></div>";
			echo "<div id='abundant_cart'>";
	if(empty( $get_session_ids )  )
	{
		?>
				<table class="table woocustomer-table" style='width:99%;border:1px solid #E5E5E5;'>

					<tr class="thone">
						<th class='col-md-3;'> Customer Name</th>
						<th class='col-md-3;'> Country</th>
						<th class='col-md-3;'> Product</th>
						<th class='col-md-3;'> Date</th>
					</tr>

					<tr class="rowtwo">
						<td></td>
						<td style="text-align:center;">--No Records Found--</td>
						<td></td>
						<td></td>
					</tr>
				</table>
				<?php
	}
	else
	{
		?>
				<table class="table woocustomer-table" style='width:99%;border:1px solid #E5E5E5;'>

					<tr class="thone">
						<th class='col-md-3;'> Customer Name </th>
						<th class='col-md-3;'> Country</th>
						<th class='col-md-3;'> Product</th>
						<th class='col-md-3;'> Date</th>
					</tr>
					<?php

			foreach( $get_session_ids as $sess_key => $sess_val)
			{
				$Checkout_users = $wpdb->get_results( $wpdb->prepare( "select user_name,country,product_data,date from wci_user_session where session_id=%d and is_checkout=%d" , $sess_val['session_id'] , '1' ));
				$Payment_users = $wpdb->get_results( $wpdb->prepare( "select distinct user_name from wci_user_session where session_id=%d and is_payment=%d" , $sess_val['session_id'] , '1' ));
				if( empty( $Payment_users ))
				{		
					if( !empty( $Checkout_users ))
					{
						$i = 0;
						foreach( $Checkout_users as $Checkout_key => $Checkout_val ){
							$i++;
							if( $i %  2 == 0 )
							{
								echo "<tr class='rowone'>";
								echo "<td class='col-md-3'>". esc_html($Checkout_val->user_name) ."</td>";
								echo "<td class='col-md-3'>". esc_html($Checkout_val->country) ."</td>";
								echo "<td class='col-md-3'>". esc_html($Checkout_val->product_data) ."</td>";
								echo "<td class='col-md-3'>". esc_html($Checkout_val->date) ."</td>";
								echo "</tr>";
							}
							else
							{
								echo "<tr class='rowtwo'>";
								echo "<td class='col-md-3'>". esc_html($Checkout_val->user_name) ."</td>";
								echo "<td class='col-md-3'>". esc_html($Checkout_val->country) ."</td>";
								echo "<td class='col-md-3'>". esc_html($Checkout_val->product_data) ."</td>";
								echo "<td class='col-md-3'>". esc_html($Checkout_val->date) ."</td>";
								echo "</tr>";
							}
						}
						$check_abandon_empty_flag = 1;
					}
				}
			}		
		?>
					<?php
		if( $check_abandon_empty_flag == 0 ){
			echo '<tr class="rowtwo"><td></td><td style="text-align:center;">--No Records Found--</td><td></td><td></td></tr>';
		}
	}
	echo "</table></div>";
	//END Checkout and not completed payment users

	//ADDTOCart Users

	// New code Add To Cart

	echo "<div class='header_title'><div class='leads-builder-heading'>". esc_html('Add To Cart') ." </div></div>";
	echo '<div id="Addtocart_users">';
	$get_session_ids = $wpdb->get_results( "select distinct session_id from wci_user_session where is_cart='1'" , ARRAY_A);
	$check_cart_empty_flag = 0;
	if(empty( $get_session_ids ) ) {
		?>
					<table class="table woocustomer-table" style='width:99%;border:1px solid #E5E5E5;'>
						<thead>
							<tr class="thone" style="width:100%;">
								<th class='col-md-3;'> Customer Name</th>
								<th class='col-md-3;'> Country </th>
								<th class='col-md-3;'> Product </th>
								<th class='col-md-3;'> Date </th>
							</tr>
						</thead>
						<tr class="rowtwo">
							<td></td>
							<td style="text-align:center;">--No Records Found--</td>
							<td></td>
							<td></td>
						</tr>
					</table>
					<?php
	}
	else
	{
		?>
					<table class="table woocustomer-table" style='width:99%;border:1px solid #E5E5E5;'>

						<tr class="thone">
							<th class='col-md-3;'> Customer Name </th>
							<th class='col-md-3;'> Country </th>
							<th class='col-md-3;'> Product </th>
							<th class='col-md-3;'> Date </th>

						</tr>

						<?php

	//New code
			foreach( $get_session_ids as $sess_key => $sess_val)
			{
					$Cart_users = $wpdb->get_results( $wpdb->prepare( "select user_name,country,product_data,date from wci_user_session where session_id=%d and is_cart=%d" , $sess_val['session_id'] , '1') );
					$Checkout_users = $wpdb->get_results( $wpdb->prepare( "select distinct user_name from wci_user_session where session_id=%d and is_checkout=%d" , $sess_val['session_id'] , '1' ));
					if( empty( $Checkout_users ))
					{
						if( !empty( $Cart_users )){
							$i = 0;
							foreach( $Cart_users as $Cart_key => $Cart_val ) {
								$i++;
								if( $i % 2 == 0 )
								{
									echo "<tr class='rowone'>";
									echo "<td class='col-md-3'>". esc_html($Cart_val->user_name) ."</td>";
									echo "<td class='col-md-3'>". esc_html($Cart_val->country) ."</td>";
									echo "<td class='col-md-3'>". esc_html($Cart_val->product_data) ."</td>";
									echo "<td class='col-md-3'>". esc_html($Cart_val->date) ."</td>";
									echo "</tr>";
								}
								else
								{
									echo "<tr class='rowtwo'>";
									echo "<td class='col-md-3'>". esc_html($Cart_val->user_name) ."</td>";
									echo "<td class='col-md-3'>". esc_html($Cart_val->country) ."</td>";
									echo "<td class='col-md-3'>". esc_html($Cart_val->product_data) ."</td>";
									echo "<td class='col-md-3'>". esc_html($Cart_val->date) ."</td>";
									echo "</tr>";
								}
							}
							$check_cart_empty_flag = 1;
						}
					
					}
			}	

		?>
			<?php
			if( $check_cart_empty_flag == 0 ){
				echo '<tr class="rowtwo"><td></td><td style="text-align:center;">--No Records Found--</td><td></td><td></td></tr>';	
			}
	}
	echo "</table></div>";
	//Successful_payments

	$success_payments = $wpdb->get_results( $wpdb->prepare("select * from wci_successful_purchases where order_status=%s" , 'Completed' ),ARRAY_A );
	echo "<div class='header_title'><div class='leads-builder-heading'>Successful Payments </div></div>";
	echo "<div id='success_payment'>";
	if(empty( $success_payments )  )
	{
		?>
						<table class="table woocustomer-table" style='width:99%;border:1px solid #E5E5E5;'>

							<tr class="thone">
								<th class='col-md-3'> Customer Name</th>
								<th class='col-md-2'> Email</th>
								<th class='col-md-2'> Order Id</th>
								<th class='col-md-3'> Products</th>
								<th class='col-md-2'> Date </th>
							</tr>

							<tr class="rowtwo">
								<td></td>
								<td></td>
								<td>--No Records Found--</td>
								<td></td>
								<td></td>
							</tr>
						</table>

						<?php
	}
	else
	{
		?>
						<table class="table woocustomer-table" style='width:99%;border:1px solid #E5E5E5;'>

							<tr class="thone">
								<th class='col-md-3'> Customer Name </th>
								<th class='col-md-2'> Email</th>
								<th class='col-md-2'> Order Id</th>
								<th class='col-md-3'> Products</th>
								<th class='col-md-2'> Date</th>
							</tr>

							<?php
			$i = 0;
			foreach( $success_payments as  $success_payments_users )
			{
				$i++;
				if( $i % 2 == 0 )
				{
					echo "<tr class='rowone'>";
					echo "<td>". esc_html($success_payments_users['user_name']) ."</td>";
					echo "<td>". sanitize_email($success_payments_users['user_email']) ."</td>";
					echo "<td style='padding-left:40px;'>". esc_html($success_payments_users['order_id']) ."</td>";
					echo "<td>". esc_html($success_payments_users['products']) ."</td>";
					echo "<td>". esc_html($success_payments_users['date']) ."</td>";
					echo "</tr>";
				}
				else
				{
					echo "<tr class='rowtwo'>";
					echo "<td>". esc_html($success_payments_users['user_name']) ."</td>";
					echo "<td>". sanitize_email($success_payments_users['user_email']) ."</td>";
					echo "<td style='padding-left:40px;'>". esc_html($success_payments_users['order_id']) ."</td>";
					echo "<td>". esc_html($success_payments_users['products']) ."</td>";
					echo "<td>". esc_html($success_payments_users['date']) ."</td>";
					echo "</tr>";
				}
			}
		?>
						</table>
						<style>
							#wpfooter {
								position: relative;
								margin-bottom: 1%;
							}
						</style>
						<?php
	}
	echo "</div>"; // success Payment
	?>
		</div>
		<!--panel div close -->
	</div>
</div>