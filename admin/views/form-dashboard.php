<?php
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly
?>

<style>
	html,
	body {
		margin: 0;
		height: 100px;
	}

	select {
		width: 120px;
	}
</style>
<div class='mt30'>
	<div class='panel' style='width:98%;'>
		<div class='panel-body'>
			<?php
	require_once(SM_LB_PRO_DIR.'includes/Chart_Data.php');
	$active_plugins = get_option( "active_plugins" );
	if( !in_array("woocommerce/woocommerce.php" , $active_plugins)){
			echo "<div style='margin-left:30%;'><a href='https://wordpress.org/plugins/woocommerce/' target='_blank' class='alert alert-danger'> You Should Install Woocommerce First</a> </div>";
	}

		echo '<div>
			  <div class="col-md-12 form-group">';
		echo '<div class="col-md-2 leads-builder-crm col-md-offset-8" style="text-align:right;font-weight: bold;"> Choose Filter</div>';
		echo '<div class="col-md-2"><select id="filter_combo" class="form-control"  name ="filter_comb">
			  <option value="last30days">Last 30 Days</option>
			  <option value="today">Today</option>
			  <option value="yesterday">Yesterday</option>
			  <option value="last_week">Last Week</option>
			  <option value="last_month">Last Month</option>
			  <option value="last7days">Last 7 Days</option>
			  <option value="customdate">Custom Date</option>
			  </select></div></div>
			  <div id="custom_datepicker">
			  <div class="col-md-2 col-md-offset-7"><input type="text" id="custom_datepicker_start" class="form-control" style="" placeholder="Start Date" ></div>
			  <div class="col-md-2"><input type="text" id="custom_datepicker_end" class="form-control" style="" placeholder="End Date"> </div>
			  <div class="col-md-1 nopadding"><input type="button" class="smack-btn smack-btn-primary btn-radius" id="custom_dates" value="Go"></div>
			  </div>
			  </div>';
		echo "<br><br>";
		echo "<div id='wootracking_d3_funnel' style='width:99%;'>"; // widget-Table Cell one
		echo "<div id='wootracking_chart_container' style='min-width: 10%; max-width: 55%; height: 40%; margin-left:26%'>
			  </div></div>";

		$sample_chart = SM_LB_DIR."assets/images/Sample.png";

		echo '<input type="hidden" id="loading-chart" value="'.$sample_chart.'">';
		echo "<br><br>";
		?>
			<style>
				label {
					font-size: 16px;
				}

				#wpfooter {
					position: relative;
					margin-bottom: 1%;
				}
			</style>

			<div>
				<div class='tracked_info' style='width:99%;height:48px;'>
					<span class='leads-builder-heading'> <?php echo esc_html("Tracked Events Info"); ?> </span>

					<div class='set_line_height' style='margin-left:55%;'>
						<span class="add_highlight " id='tab_login'>

							<a id="filter_login" style='color:#000000 !important;text-decoration:none;cursor:pointer;'>
								<?php echo esc_html("Login") ; ?></a> <span> | </span>
						</span>
						<span class="add_highlight" id='tab_purchase'>
							<a id='filter_cart'
								style='color:#000000 !important;text-decoration:none;cursor:pointer;'><?php echo esc_html("User Purchases");?></a>
							<span> | </span>
						</span>

						<span class="add_highlight" id='tab_abundant' >
							<a id='filter_abundant'
								style='color:#000000 !important;text-decoration:none;cursor:pointer'><?php echo esc_html("Abandon Orders");?></a>
						</span>
					</div>
				</div>
			</div>

			<?php
			global $wpdb;
			$user_query_string = admin_url()."admin.php?page=lb-customerstats&user_id=";
			$login_time = array();
			$login_event = $wpdb->get_results("select user_id,user_name from wci_user_profile_updated ORDER BY user_id ASC");
			if(!empty($login_event)){
				echo "<table class='table woocustomer-table common_hide_area' id='user_login' style='display:none;width:99%;border:1px solid #E5E5E5'>";
				echo "<tr class='thone'>
					<th class='col-md-2;'>User Name</th>
					<th class='col-md-3' style='text-align:right;'>No.of Times Login</th>
					<th class='col-md-2'></th>
					<th class='col-md-4;'>Last Login</th></tr>";
				foreach($login_event as $login_key => $login_value){
					$user_ID = $login_value->user_id;        
					$user_name = $login_value->user_name;
					$login_time = get_user_meta($user_ID,'loginTime');      
					$login_ip = get_user_meta($user_ID,'login_user_ip'); 

					if( !empty( $login_time ) ) {
						foreach($login_time as $lt_key =>$lt_value) {
							$get_login_count = $wpdb->get_results( $wpdb->prepare( "select count(*) as login_count from wci_history where user_name=%s and status=%s" , $user_name , 'login' ),ARRAY_A );
							$login_count = $get_login_count[0]['login_count'];	
								if( $login_count != 0 ){
								$i = 0;
								foreach($login_ip as $login_key => $login_value){
									$i++;
									if( $i % 2 == 0){
										echo '<tr class="rowone">';        
										echo '<td>'.$user_name.'</td>';
										echo '<td style="text-alin:right;padding-right:15px;">'.$login_count.'</td><td></td>';
										echo '<td>'.$lt_value .'</td>';  
										echo '</tr>';
									}
									else
									{
										echo '<tr class="rowtwo">'; 
										echo "<td class='col-md-2'><a id='query_user' style='text-align:right;padding-right:15px;' data-width:960px data-height:600px  href=".esc_url($user_query_string.$user_ID).">".$user_name."</a></td>";
										echo '<td class="col-md-3" style="text-align:right;padding-right:15px;">'.$login_count.'</td><td class="col-md-2"></td>';
										echo '<td class="col-md-4">'.$lt_value .'</td>';
										echo '</tr>';
									}
								}
							}
						}
					}
				}
				echo "</table>";
			}
			else{
				echo "<table class='table' id='user_login' style='display:none;width:99%;border:1px solid #E5E5E5;'>";
				echo "<tr class='thone'><th class='col-md-4;'>User Name</th><th class='col-md-4;'> No.of Times Login</th><th class='col-md-4;'>Last Login</th></tr>";
				echo"<tr class='rowtwo'><td class='col-md-4;'></td><td class='col-md-4;'>-- No Records Found --</td><td class='col-md-4;'></td></tr></table>";
			}

			global $wpdb;
			$pdt_names = $purchased_items = array();
			$purchased_items = $wpdb->get_results("select * from wci_user_purchased_history order by id desc limit 10"); 
			if(!empty($purchased_items)){
				echo "<table class='table woocustomer-table common_hide_area' id='user_purchased'  style='display:none;width:99%;border:1px solid #E5E5E5;'>";
				echo "<tr class='thone'><th class='col-md-4'>User Id</th><th class='col-md-4' >Email</th><th class='col-md-4'>UserHasPurchased</th></tr>";
					$i = 0;
				foreach($purchased_items as $p_key){
					$i++;
					$user_ID = $p_key->user_id;
					$customer_email = $p_key->user_email;
					$product_name = $p_key->product_name;
						
						if( $i % 2 == 0 ){
							echo '<tr class="rowone">';
							echo '<td style="padding-left:47px;">'.$user_ID.'</td>';
							echo '<td>'.$customer_email.'</td>';
							echo '<td>'.$product_name .'</td>';
							echo '</tr>';
						}
						else{
							echo '<tr class="rowtwo">';
							echo '<td style="padding-left:47px;">'.$user_ID.'</td>';
							echo '<td>'.$customer_email.'</td>';
							echo '<td>'.$product_name .'</td>';
							echo '</tr>';
						}
				}
				echo"</table>";
			}
			else{
				echo "<table class='table woocustomer-table' id='user_purchased' style='display:none;width:99%;'>";
				echo "<tr class='thone'><th>User Id</th><th>Email</th><th>UserHasPurchased</th></tr>";
				echo"<tr class='rowone'><td></td><td></td><td>--  No Records Found --</td></tr></table>";
			}

				echo'<br>
					<div id="abundant_orders_table">
					<div class="form-group col-md-12"><div class="col-md-2">
					<select id="abandon_range" name = "abandon_range" style="display:none;margin-left:10px;">
					<option value="">--Select--</option>
					<option value="1">One Hour</option>
					<option value="2">Two Hours</option>
					<option value="3">Three Hours</option>
					<option value="4">Four Hours</option>
					<option value="5">Five Hours</option>
					<option value="6">Six Hours</option>
					<option value="7">Seven Hours</option>
					<option value="8">Eight Hours</option>
					<option value="9">Nine Hours</option>
					<option value="10">Ten Hours</option>
					<option value="24">One Day</option>
					<option value="48">Two Days</option>
					</select></div>
					<div class="col-md-3">
					<input type="button" id="abandon_submit" style="display:none;"  name="abandon_submit" class="smack-btn smack-btn-primary btn-radius" value="Fetch Data" />
					</div>
					<div class="pull-right">

					<a href="" id="abundant_oneday" style="text-align:right;">1D </a><span class="pl15 pr15" style="color:#00a699;"> | </span> 

					<a href="" id="abundant_oneweek"  style="text-align:right;">1W </a> <span class="pl15 pr15" style="color:#00a699;"> | </span>

					<a href="" id="abundant_onemonth" style="text-align:left;">1M </a></div>
					</div></div>';
				echo '<br>';
				echo '<div id="abundant_orders">';
			global $wpdb;
			$abandon_array = $purchased_items = array();
			$abandon_array = $wpdb->get_results("select * from wci_abandon_cart where time_difference >= 1 order by order_id DESC limit 10"); 
			if(!empty($abandon_array)){
				//set scroll for contents
				echo "<div id='scroll_content'>";
				echo "<table class='table table-hover common_hide_area' id='abandon_cart_list' style='width:99%;border:1px solid #E5E5E5;'>";
				echo "<tr class='thone'><th class='col-md-4'>Customer Email</th><th class='col-md-4'>Order ID</th><th class='col-md-4'>Order Date</th></tr>";
				$i = 0;

				foreach($abandon_array as $abandon_key){
					$i++;
						$user_email = $abandon_key->user_email;
						$order_id = $abandon_key->order_id;
						$order_date = $abandon_key->date;
					if( $i %2 == 0 )
					{
						echo '<tr class="rowone">';
						echo '<td>'.sanitize_email($user_email).'</td>';
						echo '<td style="padding-left:40px;">'.esc_html($order_id).'</td>';
						echo '<td>'. esc_html($order_date).'</td>';
						echo '</tr>';
					}
					else
					{
						echo '<tr class="rowtwo">';
						echo '<td>'.sanitize_email($user_email).'</td>';
						echo '<td style="padding-left:40px;">'.esc_html($order_id).'</td>';
						echo '<td>'. $order_date.'</td>';
						echo '</tr>';
					}
				}
				echo "</table>";
				echo "</div>";//scroll end
			}
			else{
				echo "<table class='table woocustomer-table common_hide_area' id='abandon_cart_list' style='width:98%;border:1px solid #E5E5E5;'>";
				echo "<tr class='thone'><th class='col-md-4'>Customer Email</th><th class='col-md-4'>Order ID</th><th class='col-md-4'>Order Date</th></tr>";
				echo "<tr class='rowone'><td></td><td>-- No Records Found --</td><td></td></tr></table>";
			}
				echo "</div>";//END Abundant Orders

			//Most Sold Products

			$post_title = $wpdb->get_results($wpdb->prepare("select post_title from ".$wpdb->prefix."posts where post_type=%s", 'product'));
			$prod_list = array();
				foreach( $post_title as $title=>$prod){
						foreach( $prod as $key=>$productt ){
								$prod_list[] = $productt;
						}
				}
			$i = 0;
			if( !empty( $prod_list )) {
				foreach( $prod_list as $products ){
					$i++;
					//$total_sales[] = $wpdb->get_results( "select order_item_name,count(*) as total_count from {$wpdb->prefix}woocommerce_order_items as a join {$wpdb->prefix}posts as b on a.order_id=b.ID where order_item_name like '$products'");
					$total_sales[] = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT order_item_name, COUNT(*) as total_count 
							FROM {$wpdb->prefix}woocommerce_order_items as a 
							JOIN {$wpdb->prefix}posts as b ON a.order_id = b.ID 
							WHERE order_item_name LIKE %s", 
							'%' . $wpdb->esc_like($products) . '%'
						)
					);
				}
			}	

			if( !empty( $total_sales )) {
				foreach( $total_sales as $sales_count )
				{
					if( ($sales_count[0]->total_count) > 0 )
					{
						$sold_product_name = $sales_count[0]->order_item_name;
						$sold_product_count = $sales_count[0]->total_count;
						$most_sold_products[$sold_product_name] = $sold_product_count;
					}
				}
				if( !empty( $most_sold_products )) 
				{
					arsort( $most_sold_products );
				}
			}
			
			echo "<br>";
			echo "<div id='dashboard_title_three' style='width:98%;'>";
			echo "<div id='table_cell_one'>";
			echo "<div class='leads-builder-heading' >".esc_html('Most sold products'). " </div>";
			echo "<div id='most_sold_products'>";
			
			if( !empty($most_sold_products) ){
			
				echo "<table class='table woocustomer-table' style='width:100%;border:1px solid #E5E5E5;'>
				<tr class='thone'>
				<th class='col-md-7'>Product</th>
				<th class='col-md-4' style='text-align:right;'>No of times</th>
				<th class='col-md-1'></th>
				</tr>";
				$i = 0;
				foreach($most_sold_products as $ms_prod_key => $ms_prod_value ){
					$i++;
					if( $i % 2 == 0 )
					{
						echo "<tr class='rowone'>";
						echo '<td>'.esc_html( $ms_prod_key).'</td>';
						echo '<td style="text-align:right;padding-right:15px;">'.esc_html($ms_prod_value).'</td><td></td>';
						echo '</tr>';
					}
					else
					{
						echo "<tr class='rowtwo'>";
						echo '<td>'. esc_html($ms_prod_key).'</td>';
						echo '<td style="text-align:right;padding-right:15px;">'.esc_html($ms_prod_value).'</td><td></td>';
						echo '</tr>';
					}
				}
				echo "</table>";	
			}
		else
		{
			$most_sold = '';
			$most_sold .= "<table class='table woocustomer-table' style='width:100%;border:1px solid #E5E5E5;'>";
				$most_sold .= "<tr class='thone'>
				<th class='col-md-4'>Product</th>
				<th class='col-md-4'></th>
				<th class='col-md-4'>No of times</th>
				</tr>";

				$most_sold .= "<tr class='rowtwo'>";
				$most_sold .= '<td></td>';
				$most_sold .= '<td>-- No Records Found --</td>';
				$most_sold .= '<td></td>';
				$most_sold .= '</tr></table>';
			echo $most_sold;
		}
		echo "</div>";//MOST SOLD
		echo "</div>";
		// Most Visit Products
		$products = $wpdb->get_results($wpdb->prepare("select product from wci_events where button_name=%s" , 'AddToCart'),ARRAY_A );
		$prod_array = array();
		foreach( $products as $key => $val )
		{
			$prod_array[] = $val['product'] ;
		}

		$prod_count_array = array_count_values( $prod_array );
		arsort($prod_count_array);
		//end Most Visited Products

		$visit_pages = $wpdb->get_results("select information from wci_activity" ,ARRAY_A );
		
			$visit_page = array();
			$j = 0;
			foreach( $visit_pages as $visit_key => $visit_info) {
				$j++;
				$unserialized_info = unserialize( $visit_info['information'] );
				$visit_page[$j]['prodid'] = $unserialized_info['prodid'];		
				$visit_page[$j]['prod_pageurl'] = $unserialized_info['page'];
			}
			$product_name = array();
			foreach( $visit_page as $value ){
				if( preg_match("/product/",$value['prod_pageurl'] ) )
				{
					$product_id = $value['prodid'] ;
					$product_name[] = get_the_title($product_id) ;
					$prod_url = $value['prod_pageurl'] ;
				}
			}
				$product_name = array_count_values( $product_name );
				arsort( $product_name );

		//No Of Visits
		?>
					<div id='table_cell_two'>
						<div class='leads-builder-heading'> <?php echo esc_html("Most Visited products-No of Visits") ;?></div>
						<div id='product_no_of_visit'>
							<?php
		if(empty($product_name))
		{

		$prod_no_of_visit = '<table class="table woocustomer-table" style="width:100%;border:1px solid #E5E5E5;" >
							<thead>
							<tr class="thone">
							<th class="col-md-4">product Name</th>
							<th class="col-md-4"></th>
							<th>Number Of Visits</th>
							</tr>
							</thead>
							<tr class="rowtwo"> <td></td><td>-- No Records Found --</td><td></td></tr>
							</table>';
		echo $prod_no_of_visit;
		}
		else{
		?>
					<table class="table woocustomer-table" style='width:100%;border:1px solid #E5E5E5;'>
						<thead>
							<tr class="thone">
								<th class='col-md-7'>Product Name</th>
								<th class='col-md-4' style='text-align:right;'>Number Of Visits</th>
								<th class='col-md-1'></th>
							</tr>
						</thead>
						<?php
						$i = 0;
						foreach ($product_name as $url_key => $url_val){    
							$i++;			
							if( $i % 2 == 0 ){
								echo '<tr class="rowone">';
								echo '<td>'.esc_html( $url_key )."</td>";
								echo '<td style="text-align:right;padding-right:15px;">'. esc_html($url_val).'</td>';
								echo '<td></td>';
								echo'</tr>';
							}
							else {	
								echo '<tr class="rowtwo">';
								echo '<td>'. esc_html($url_key)."</td>";
								echo '<td style="text-align:right;padding-right:15px;">'.esc_html( $url_val).'</td>';
								echo '<td></td>';
								echo'</tr>';
							}
						} 
						?>
					</table>
					<?php
		}
		?>

				</div> <!-- no_of_visit -->
			</div> <!-- cell two -->
		</div> <!-- title bar 3 -->

		<?php
		$statuses = '';
		$currency = '';
		//Order Details
		global $wpdb;
		$active_plugins = get_option( "active_plugins" );
		if( in_array("woocommerce/woocommerce.php" , $active_plugins)){
			$all_statuses = $ids = array();
			$all_statuses = wc_get_order_statuses();
			$currency =  get_woocommerce_currency_symbol();
		}

		if( !empty( $all_statuses )){
			foreach($all_statuses as $status_key => $status_value){
					$statuses .= '"'.$status_key.'",';
			}
			$statuses =  rtrim($statuses,',');
		}
		if($statuses != "")
		$orders_info = '$wpdb->get_results( "select * from ".$wpdb->posts." where post_status IN ($statuses)")';
		if(isset($orders_info) && is_array($orders_info)){
			foreach($orders_info as $o_key => $o_value){
				$order = new WC_Order($o_value->ID);
				$ord_id = $o_value->ID;
				$ord_date = $order->order_date;
				$ord_email = $order->billing_email;
				$items = $order->get_items();
				$total_price = 0;
				foreach($items as $ikey => $ivalue ){

					$total_price = $total_price + $ivalue['line_total'];
				}
				$total_order_price = $order->get_total();
				$coupon = $order->get_used_coupons();
				if( !empty($coupon) )
				{
					$coupon_code = $coupon[0];
					$coupon_details = new WC_Coupon( $coupon_code );
					$coupon_amount = $coupon_details->coupon_amount;
					$coupon_discount = $coupon_details->discount_type;
				}
				else
				{
					$coupon_code = "--";
					$coupon_amount = "--";
					$coupon_discount = "--";
				}

				$customer_ranking[] = array( "order_id" => $ord_id,"order_date" => $ord_date, "price" => $total_order_price, "order_email" => $ord_email,"count" => 1, "coupon_code" => $coupon_code , "coupon_amount" => $coupon_amount );
			}
		}

		$price = array();
		if( !empty( $customer_ranking ) ){
			foreach ($customer_ranking as $key => $row)
			{
					$price[$key] = $row['price'];
			}
			array_multisort($price, SORT_DESC, $customer_ranking);
		}

		$sum_price = $sum_orders = array();
		if( !empty( $customer_ranking ) ) {
		$sum_price = array_reduce($customer_ranking, function ($a, $b) {
				isset($a[$b['order_email']]) ? $a[$b['order_email']]['price'] += $b['price'] : $a[$b['order_email']] = $b;
				return $a;
				});
		}

		$sort_price = array();

		if( !empty($sum_price) ){
			foreach ($sum_price as $key => $row)
			{
					$sort_price[$key] = $row['price'];
			}
			array_multisort($sort_price, SORT_DESC, $sum_price);
			$price_Array = array_slice($sum_price,0,5,true);
		}

		echo "<br>";
		echo "<div id='dashboard_title_four' style='width:99%;margin-left:0'>";
		echo "<br>";
		echo "<div id='table_cell_one'>";
		echo "<div class='leads-builder-heading' >Most Active Customers By Revenue </div>";
		echo "<div id='customer_by_revenue'>"; // REVENUE

		if( !empty( $price_Array )) {
			echo "<table class='table table-hover' style='width:100%;border:1px solid #E5E5E5;'>";
			echo "<tr class='thone'>
				<th class='col-md-7'>Customer Id</th>
				<th class='col-md-4' style='text-align:right;'>Purchased For ( in $currency ) </th>
				<th class='col-md-1'></th>
				</tr>";
			$i = 0;
			foreach($price_Array as $price_key => $price_value){
				$i++;
				if( $i % 2 == 0 )
				{
					$price_decimal = number_format((float)$price_value['price'], 2, '.', '');
					echo '<tr class="rowone">';
					echo '<td>'.sanitize_email($price_value['order_email']).'</td>';
					echo '<td style="text-align:right;padding-right:15px;">'.esc_html($price_decimal).'</td>';
					echo '<td></td>';
					echo '</tr>';
				}
				else
				{
					$price_decimal = number_format((float)$price_value['price'], 2, '.', '');
					echo '<tr class="rowtwo">';
					echo '<td>'.sanitize_email($price_value['order_email']).'</td>';
					echo '<td style="text-align:right;padding-right:15px;">'.esc_html($price_decimal).'</td>';
					echo '<td></td>';
					echo '</tr>';
				}
			}
		}
		else
		{
			$customer_by_revenue = "<table class='table table-hover' style='width:100%;border:1px solid #E5E5E5;'>";
			$customer_by_revenue .= "<tr class='thone'>
									 <th class='col-md-4'>Customer Id</th>
									 <th class='col-md-4'></th>
									 <th>Purchased For ( in $currency ) </th>
									 </tr>";
			$customer_by_revenue .= '<tr class="rowtwo">';
			$customer_by_revenue .= '<td></td>';
			$customer_by_revenue .= '<td> -- No Records Found --</td>';
			$customer_by_revenue .= '<td></td>';
			$customer_by_revenue .= '</tr>';
			echo $customer_by_revenue;
		}
		echo "</table>";
		echo "</div>"; // by_revenue
		echo "</div>"; // table_cell_one
		if( !empty( $customer_ranking )) {
		$sum_orders = array_reduce($customer_ranking, function ($c, $d) {
				isset($c[$d['order_email']]) ? $c[$d['order_email']]['count'] += $d['count'] : $c[$d['order_email']] = $d;
				return $c;
				});
		}
		$sort_orders = array();
		if( !empty($sum_orders) ) {
			foreach ($sum_orders as $key => $row)
			{
					$sort_orders[$key] = $row['count'];
			}
			array_multisort($sort_orders, SORT_DESC, $sum_orders);
			$price_Array = array_slice($sum_orders,0,5,true);
		}
		echo "<div id='table_cell_two'>";
		echo "<div class='leads-builder-heading' >Most Active Customers By Orders </div>";
		echo "<div id='by_orders'>";

		if( !empty($price_Array) ){
			echo "<table class='table table-hover' style='width:100%;border:1px solid #E5E5E5;'>";
			echo "<tr class='thone'>
				  <th class='col-md-7'>Customer Id</th>
				  <th class='col-md-4' style='text-align:right;'>Purchased Orders</th>
				  <th class='col-md-1'></th>
				  </tr>";
			$i =0;
			foreach($price_Array as $orders_key => $orders_value){
				$i++;
				if( $i % 2 == 0 )
				{
					echo "<tr class='rowone'>";
					echo '<td>'.sanitize_email($orders_value['order_email']).'</td>';
					echo '<td style="text-align:right;padding-right:15px;">'.esc_html($orders_value['count']).'</td><td></td>';
					echo '</tr>';
				}
				else
				{
					echo "<tr class='rowtwo'>";
					echo '<td>'.sanitize_email($orders_value['order_email']).'</td>';
					echo '<td style="text-align:right;padding-right:15px;">'.esc_html($orders_value['count']).'</td><td></td>';
					echo '</tr>';
				}
			}
		}
		else
		{
			$customers_by_orders = "<table class='table table-hover' style='width:100%;border:1px solid #E5E5E5;'>";
			$customers_by_orders .= "<tr class='thone'>
									 <th class='col-md-4'>Customer Id</th>
									 <th class='col-md-4'></th>
									 <th>Purchased Orders</th>
									 </tr>";
			$customers_by_orders .= "<tr class='rowtwo'>";
			$customers_by_orders .= '<td></td>';
			$customers_by_orders .= '<td>-- No Records Found --</td>';
			$customers_by_orders .= '<td></td>';
			$customers_by_orders .= '</tr>';
			echo $customers_by_orders;
		}
		echo "</table>";
		echo "</div>";//by_orders
		echo "</div>";
		echo "</div>";
		//CHANGE

		$info_visited_time = $serialized_visited_time = $page_list = $url_with_time = array();
		$info_visited_time = $wpdb->get_results("SELECT visited_url,spent_time FROM wci_activity ORDER BY spent_time DESC",ARRAY_A);   

		foreach($info_visited_time as $key => $value)
		{
			$most_visit[$value['visited_url']][]=$value['spent_time'];
		} 
			$k = 0;

			$most_spent_time = array();
			$most_visited_spent = $wpdb->get_results( "SELECT information from wci_activity where clicked_button in('Page visit','AddToCart')",ARRAY_A );
			foreach( $most_visited_spent as $info_key => $info_vals )
			{	$k++;
				$unserialized_spent_array = unserialize( $info_vals['information'] );
				$most_spent_time[$k]['prodid'] = $unserialized_spent_array['prodid'];	
				$most_spent_time[$k]['spent_time'] = $unserialized_spent_array['timespent'];
				$most_spent_time[$k]['visited_url'] = $unserialized_spent_array['page'];
			}

			$most_time_spent_prod_array = array();
			$count = 0;
			foreach( $most_spent_time as $prods_time_spent_array )
			{
				if( preg_match( "/product/" , $prods_time_spent_array['visited_url']  ) )
					{	$count++;
						$most_spent_prod_id = $prods_time_spent_array['prod_id'];
						$most_time_spent_product = $wpdb->get_results($wpdb->prepare("select post_title from ".$wpdb->posts." where ID=%d" , $most_spent_prod_id),ARRAY_A );
						$name_of_prod = $most_time_spent_product[0]['post_title'];
										$spent_time_of_prod = $prods_time_spent_array['spent_time'];
						$most_time_spent_prod_array[$name_of_prod][$count] = $spent_time_of_prod;
					}
			}
				foreach( $most_time_spent_prod_array as $prod_name => $most_time_product)
				{
					$product_list[$prod_name] = array_sum($most_time_product );
				}

		echo "<div id='dashboard_title_five' style='width:99%;'>";
		echo "<div id='table_cell_one'>";
		echo "<div class='leads-builder-heading' >Most Visited Products-Time Spent</div>";
		echo "<div id='time_spent'>";
		if(empty($product_list))
		{
			$time_spent = "<table class='table woocustomer-table' style='width:100%;border:1px solid #E5E5E5;'>
						   <thead>
						   <tr class='thone'>
						   <th class='col-md-4'> Product Name</th>
						   <th class='col-md-4'></th>
						   <th>Spent Time (in sec)</th>
						   </tr>
						   </thead>
						   <tr class='rowtwo'><td></td><td>-- No Records Found --</td><td></td></tr>
						   </table>";
			echo $time_spent;
		}
		else
		{
		?>
				<table class="table woocustomer-table" style='width:100%;border:1px solid #E5E5E5;'>
					<thead>
						<tr class="thone">
							<th class='col-md-7'> Product Name</th>
							<th class='col-md-4' style='text-align:right;'>Spent Time ( M & S)</th>
							<th class='col-md-1'></th>
						</tr>
					</thead>
					<?php
			arsort( $product_list );
			$i = 0;
			foreach($product_list as $most_visited_page_key=> $most_visited_page_value) {
				$i++;
				if( $i % 2 == 0 )
				{
					echo '<tr class="rowone">';
					echo '<td>'. esc_html($most_visited_page_key).'</td>';
					echo '<td style="text-align:right;padding-right:15px;">'.esc_html( gmdate('i:s' ,$most_visited_page_value)).'</td>';
					echo '<td></td>';
					echo'</tr>';
				}
				else
				{
					echo '<tr class="rowtwo">';
					echo '<td>'. esc_html($most_visited_page_key).'</td>';
					echo '<td style="text-align:right;padding-right:15px;">'.esc_html( gmdate('i:s' ,$most_visited_page_value)).'</td>';
					echo '<td></td>';
					echo'</tr>';
				}
			}
				?>
				</table>
		<?php 
		}
		echo "</div>";//time_spent
		echo "</div>";//cell one

		//Order-summary
		echo "<div id='table_cell_two'>";
		echo "<div class='leads-builder-heading' >". esc_html('Orders - Summary ( Recent 5 Orders )')."</div>";
		echo "<div id='order_summary'>";

		if( !empty( $statuses )) {
			foreach($all_statuses as $status_key => $status_value){
					$statuses .= '"'.$status_key.'",';
			}
			$statuses =  rtrim($statuses,',');
		}
		if($statuses != "")
		$orders_info = $wpdb->get_results( "select * from ".$wpdb->posts." where post_status IN ($statuses)",ARRAY_A);
		$sort_order_info = array();
		if(isset($orders_info) && $orders_info != ""){
			foreach ($orders_info as $key => $row)
			{
					$sort_order_info[$key] = $row['ID'];
			}
			array_multisort($sort_order_info, SORT_DESC, $orders_info);
			$recent_orders = array_slice($orders_info,0,5,true);
		}
		if( !empty( $recent_orders ))
		{
			echo "<table class='table table-hover' style='width:100%;border:1px solid #E5E5E5;'>";
			echo "<tr class='thone'><th class='col-md-2' style='text-align:left;'>Order Id </th><th class='col-md-1'></th>
			<th class='col-md-4'>Order Holder ID</th>
			<th class='col-md-4' style='text-align:right;'>Total(in ".$currency." )</th><th class='col-md-1'></th></tr>";
			$i = 0;
			$order_query_string = admin_url()."post.php?post=";
			foreach($recent_orders as $o_key => $o_value){
				$i++;
					$order = new WC_Order($o_value['ID']);
					$ord_id = $o_value['ID'];
					$ord_date = $order->order_date;
					$ord_email = $order->billing_email;

					$items = $order->get_items();
					$total_price = 0;
					foreach($items as $ikey => $ivalue ){

							$total_price = $total_price + $ivalue['line_total'];

					}
					$total_order_price = $order->get_total();
				if( $i % 2 == 0 )
				{
					$Order_total_decimal = number_format((float)$total_order_price, 2, '.', '');
					echo "<tr class='rowone'>";
					echo '<td style="text-align:right;padding-right:88px;"> <a target="_blank" id="modall" href="'.esc_url($order_query_string.$ord_id).'&action=edit" >'.esc_html($ord_id).'</a></td><td></td>';
					echo '<td>'.sanitize_email($ord_email).'</td>';
					echo '<td style="text-align:right;padding-right:15px;">'.esc_html($Order_total_decimal).'</td><td></td>';
					echo "</tr>";
				}
				else
				{
				$Order_total_decimal = number_format((float)$total_order_price, 2, '.', '');
					echo "<tr class='rowtwo'>";
					echo '<td style="text-align:right;padding-right:88px;"><a target="_blank" id="modall" href="'.esc_url($order_query_string.$ord_id).'&action=edit" >'.esc_html($ord_id).'</a></td><td></td>';
					echo '<td>'.$ord_email.'</td>';
					echo '<td style="text-align:right;padding-right:15px;">'.$Order_total_decimal.'</td><td></td>';
					echo "</tr>";
				}
			}	
				echo "</table>";
		}
		else
		{
			$order_summary = "<table class='table table-hover' style='width:100%;border:1px solid #E5E5E5;'>";
			$order_summary .= "<tr class='thone'><th class='col-md-4'>Order Id </th><th class='col-md-4'>Order Holder ID</th></th>
			<th class='col-md-4'>Total(in ".$currency." )</th></tr>";
			$order_summary .= "<tr class='rowtwo'>";
			$order_summary .= '<td></td>';
			$order_summary .= '<td>-- No Records Found --</td>';
			$order_summary .= '<td></td>';
			$order_summary .= "</tr></table>";
			echo $order_summary;

		}
		echo "</div>";//order summary
		echo "</div>";
		echo "</div>";
		echo "</div>";

		echo "</div>";//panel div
		echo "</div>";
		echo "</div>";