var jQuery = jQuery.noConflict();
jQuery(document).ready(function () {

    jQuery( "#abundant_orders" ).hide();
    jQuery( "#abundant_orders_table" ).hide();

//OPPORTUNITIES

//ONE DAY
    jQuery( "#opportunity_oneday" ).click( function(event){
        event.preventDefault();
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action': 'wci_opportunity_filter_oneday',
                'abandon_range' : 'oneday',
            },
            cache: false,
            success: function (response) {
                jQuery( '#failed_payment' ).empty();
                jQuery( '#success_payment').empty();
                jQuery( '#abundant_cart').empty();
                jQuery( '#Addtocart_users').empty();
                var wci_oppor_arr = JSON.parse( response );
                var wci_failed_payment = wci_oppor_arr.failed_payment;
                var wci_success_payment = wci_oppor_arr.success_payment;
                var wci_abundant_cart = wci_oppor_arr.abundant_cart;
                var wci_addtocart = wci_oppor_arr.addtocart;

                if( wci_failed_payment != "" )
                {
                    jQuery('#failed_payment').append( wci_failed_payment );
                }
                if( wci_success_payment != "" )
                {
                    jQuery('#success_payment').append( wci_success_payment );
                }
                if( wci_abundant_cart != "" )
                {
                    jQuery('#abundant_cart').append( wci_abundant_cart );

                }
                if( wci_addtocart != "" )
                {
                    jQuery('#Addtocart_users').append( wci_addtocart );
                }

            },
            error: function(errorThrown)
            {
                console.log( errorThrown);
            }
        });

    });

//ONE WEEK
    jQuery( "#opportunity_oneweek" ).click( function(event){
        event.preventDefault();
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action': 'wci_opportunity_filter_oneweek',
                'abandon_range' : 'oneweek',
            },
            cache: false,
            success: function (response) {
                jQuery( '#failed_payment' ).empty();
                jQuery( '#success_payment').empty();
                jQuery( '#abundant_cart').empty();
                jQuery( '#Addtocart_users').empty();
                var wci_oppor_array = JSON.parse( response );
                var wci_failed_payment = wci_oppor_array.failed_payment;
                var wci_success_payment = wci_oppor_array.success_payment;
                var wci_abundant_cart = wci_oppor_array.abundant_cart;
                var wci_addtocart = wci_oppor_array.addtocart;
                if( wci_failed_payment != "" )
                {
                    jQuery('#failed_payment').append( wci_failed_payment );
                }
                if( wci_success_payment != "" )
                {
                    jQuery('#success_payment').append( wci_success_payment );
                }
                if( wci_abundant_cart != "" )
                {
                    jQuery('#abundant_cart').append( wci_abundant_cart );
                }
                if( wci_addtocart != "" )
                {
                    jQuery('#Addtocart_users').append( wci_addtocart );
                }


            },
            error: function(errorThrown)
            {
                console.log( errorThrown);
            }
        });

    });

//ONE MONTH
    jQuery( "#opportunity_onemonth" ).click( function(event){

        event.preventDefault();
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action': 'wci_opportunity_filter_onemonth',
                'abandon_range' : 'onemonth',
            },
            cache: false,
            success: function (response) {
                jQuery( '#failed_payment' ).empty();
                jQuery( '#success_payment').empty();
                jQuery( '#abundant_cart').empty();
                jQuery( '#Addtocart_users').empty();
                var wci_oppor_arr = JSON.parse( response );
                var wci_failed_payment = wci_oppor_arr.failed_payment;
                var wci_success_payment = wci_oppor_arr.success_payment;
                var wci_abundant_cart = wci_oppor_arr.abundant_cart;
                var wci_addtocart = wci_oppor_arr.addtocart;
                if( wci_failed_payment != "" )
                {
                    jQuery('#failed_payment').append( wci_failed_payment );
                }
                if( wci_success_payment != "" )
                {
                    jQuery('#success_payment').append( wci_success_payment );
                }
                if( wci_abundant_cart != "" )
                {
                    jQuery('#abundant_cart').append( wci_abundant_cart );
                }
                if( wci_addtocart != "" )
                {
                    jQuery('#Addtocart_users').append( wci_addtocart );
                }

            },
            error: function(errorThrown)
            {
                console.log( errorThrown);
            }
        });

    });

//END OPPORTUNITIES FILTER

//FILTERS

    jQuery( "#abundant_oneday" ).click( function(event){
        event.preventDefault();
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action': 'wci_abandon_filter_oneday',
                'abandon_range' : 'oneday',
            },
            cache: false,
            success: function (response) {
                jQuery( '#abundant_orders' ).empty();
                var abundant_orders = JSON.parse( response );
                if( abundant_orders != "" )
                {
                    jQuery('#abundant_orders').append( abundant_orders );
                }
            },
            error: function(errorThrown)
            {

            }
        });

    });

    //oneweek
    jQuery( "#abundant_oneweek" ).click( function(event){
        event.preventDefault();
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action': 'wci_abandon_filter_oneweek',
                'abandon_range' : 'oneweek',
            },
            cache: false,
            success: function (response) {
                jQuery( '#abundant_orders' ).empty();
                var abundant_orders = JSON.parse( response );
                if( abundant_orders != "" )
                {
                    jQuery('#abundant_orders').append( abundant_orders );
                }
            },
            error: function(errorThrown)
            {
                console.log( errorThrown);
            }
        });

    });

    //onemonth
    jQuery( "#abundant_onemonth" ).click( function(event){
        event.preventDefault();
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action': 'wci_abandon_filter_onemonth',
                'abandon_range' : 'onemonth',
            },
            cache: false,
            success: function (response) {
                jQuery( '#abundant_orders' ).empty();
                var abundant_orders = JSON.parse( response );
                if( abundant_orders != "" )
                {
                    jQuery('#abundant_orders').append( abundant_orders );
                }
            },
            error: function(errorThrown)
            {
                console.log( errorThrown);
            }
        });

    });


    //END ABUNDANT ORDERS


    jQuery("#filter_submit").click(function(){
        var selected_option = jQuery("#filter_value").val();
        if(selected_option == "select")
        {
            alert("Kindly Select the Filter type");
            return false;
        }
    });


    jQuery( '.datepicker' ).datepicker({
        format : 'yyyy-mm-dd'
    });
    jQuery('.datepicker').on('changeDate', function(ev){
        jQuery(this).datepicker('hide');
    });


    jQuery('#from_date').hide();
    jQuery('#to_date').hide();
    jQuery('#custom_date_label1').hide();
    jQuery('#custom_date_label2').hide();
    jQuery('#filter_value').change(function(){
        var filter_val = jQuery('#filter_value').val();
        if( filter_val == 'customDate'){
            jQuery('#from_date').show();
            jQuery('#to_date').show();
            jQuery('#custom_date_label1').show();
            jQuery('#custom_date_label2').show();

        }

        if( filter_val != 'customDate'){
            jQuery('#from_date').hide();
            jQuery('#to_date').hide();
            jQuery('#custom_date_label1').hide();
            jQuery('#custom_date_label2').hide();
        }

    });

//NEW CODE

//ADD CLASS
    jQuery( "#tab_login" ).addClass( "add_highlight");
    jQuery( "#tab_purchase" ).removeClass( "add_highlight");
    jQuery( "#tab_abundant" ).removeClass( "add_highlight");

    // Login
    jQuery("#user_login").show();
    jQuery("#abandon_submit").hide();
    jQuery( "#filter_login" ).click( function(event){
        jQuery( "#tab_login" ).addClass( "add_highlight");
        jQuery( "#tab_purchase" ).removeClass( "add_highlight");
        jQuery( "#tab_abundant" ).removeClass( "add_highlight");
        jQuery("#user_login").show();
        jQuery("#user_logout").hide();
        jQuery("#abundant_orders_table").hide();
        jQuery("#user_purchased").hide();
        jQuery("#abandon_cart_list").hide();
        jQuery("#abandon_range").hide();
        jQuery("#abandon_submit").hide();
        event.preventDefault();
    });

    jQuery( "#filter_cart" ).click( function(event){
        event.preventDefault();
        jQuery( "#tab_login" ).removeClass( "add_highlight");
        jQuery( "#tab_purchase" ).addClass( "add_highlight");
        jQuery( "#tab_abundant" ).removeClass( "add_highlight");

        jQuery("#user_purchased").show();
        jQuery("#user_login").hide();
        jQuery("#abundant_orders_table").hide();
        jQuery("#user_logout").hide();
        jQuery("#abandon_cart_list").hide();
        jQuery("#abandon_range").hide();
        jQuery("#abandon_submit").hide();

    });

    jQuery( "#filter_abundant" ).click( function(event) {
        event.preventDefault();
        jQuery( "#tab_login" ).removeClass( "add_highlight");
        jQuery( "#tab_purchase" ).removeClass( "add_highlight");
        jQuery( "#tab_abundant" ).addClass( "add_highlight");
        jQuery("#abandon_submit").show();
        var test_class = jQuery("#abandon_range").hasClass('selectpicker');
        if( test_class == false ){
            jQuery("#abandon_range").show().addClass('selectpicker').addClass('form-control');
        }
        else
        {
            //jQuery("#abandon_range").show();
        }
        //jQuery(".selectpicker").selectpicker('refresh');
        jQuery(".bootstrap-select").click(function () {
            jQuery(this).addClass("open");
        });
        jQuery("#abandon_cart_list").show();
        jQuery("#abundant_orders").show();
        jQuery("#abundant_orders_table").show();
        jQuery("#user_login").hide();
        jQuery("#user_logout").hide();
        jQuery("#user_purchased").hide();
    });

    jQuery( "#abandon_items" ).submit( function(event) {

        jQuery("#user_login").hide();
        jQuery('#user_login').css('display', 'none');
        jQuery("#user_login").hide();
        jQuery("#abandon_submit").show();
        jQuery("#abandon_range").show();
        jQuery("#user_logout").hide();
        jQuery("#user_purchased").hide();
    });

//ABANDON ORDERS
    jQuery( "#abandon_submit" ).click( function() {
        var range = jQuery( "#abandon_range" ).val();

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action': 'wci_abandon_filter',
                'abandon_range' : range,
            },
            cache: false,
            success: function (response) {
                jQuery( '#abundant_orders' ).empty();
                var abundant_orders = JSON.parse( response );
                if( abundant_orders != "" )
                {
                    jQuery('#abundant_orders').append( abundant_orders );
                }
            },
            error: function(errorThrown)
            {
                console.log( errorThrown);
            }
        });
    });
//END ABANDON ORDERS

    jQuery( "#custom_datepicker" ).hide();


//Filter For chart
    //COMBO DATE FILTER
    jQuery( "#filter_combo" ).change( function(){
        jQuery( "#wootracking_chart_container" ).empty();
        var filter_value = jQuery( "#filter_combo" ).val();
        if( filter_value != "customdate" )
        {
            jQuery( "#most_sold_products").height("");
            jQuery( "#product_no_of_visit" ).height("");
            jQuery( "#customer_by_revenue" ).height("");
            jQuery( "#by_orders" ).height("");
            jQuery( "#custom_datepicker" ).hide();
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    'action': 'wci_funnel_chart',
                    'filter_type': filter_value,
                },

                cache: false,
                success: function (response) {
                    var result = JSON.parse(response);
                    if (result.info.total_visitors == 0 && result.info.AddToCart == 0  && result.info.Checkout == 0 && result.info.SuccessfulPurchases == 0) {
                        jQuery("#wootracking_chart_container").empty();
                        swal('Warning!','No Data Found on this date' , 'warning');
                        var wci_sample_chart = jQuery("#loading-chart").val();
                        jQuery( '#wootracking_chart_container' ).html( "<img src="+wci_sample_chart+">" );
                        //jQuery('#wootracking_chart_container').html("<div style='text-align:center:width:250px;height:400px;padding-top:150px;'><p class='alert alert-danger' style='font-size:20px;font-family:sans-serif;'><strong>ALERT : </strong><br><br> No Data Found on this date</p></div>");
                    }
                    else {

                        var data = [
                            ['Total Visitors',   result.info.total_visitors , '#2F91B8' , '#FFFFFF'],
                            ['AddToCart', result.info.AddToCart , '#3E3E3E', '#FFFFFF'],
                            ['Checkout',    result.info.Checkout, '#9B9B9B' , '#FFFFFF'],
                            ['SuccessfulPurchases',  result.info.SuccessfulPurchases , '#95A926' , '#FFFFFF'],
                        ];

                        width = jQuery('#wootracking_d3_funnel').width();
                        var options = {
                            chart: {
                                width: width-600,
                                height: 400,
                                bottomWidth: 1 / 3,
                                animate : 300 ,
                            },
                            block: {
                                //dynamicHeight: true,
                                //fillType: "solid" ,
                                hoverEffects: true,
                                fill : {
                                    type : "gradient" ,
                                }
                            },
                            label : {
                                fontSize : "15px",
                            },
                        };
                        var funnel = new D3Funnel("#wootracking_chart_container");
                        funnel.draw(data, options);
                    }


// 1--- WIDGET ONE

//MOST SOLD
                    jQuery( "#most_sold_products").empty();
                    jQuery( "#most_sold_products" ).append( result.most_sold );
                    var MS_height = jQuery( "#most_sold_products").height();


//NO OF VISIT
                    jQuery( "#product_no_of_visit" ).empty();
                    jQuery( "#product_no_of_visit" ).append( result.no_of_visit );
                    var No_of_visit_height = jQuery( "#product_no_of_visit" ).height();

                    if( MS_height > No_of_visit_height )
                    {
                        jQuery( "#product_no_of_visit" ).height( MS_height + 20 );
                    }
                    else if( MS_height < No_of_visit_height )
                    {
                        jQuery( "#most_sold_products").height( No_of_visit_height + 20  );
                    }

// --- END WIDGET ONE

// 2-- WIDGET TWO
//REVENUE

                    jQuery( "#customer_by_revenue" ).empty();
                    jQuery( "#customer_by_revenue" ).append( result.customers_by_revenue );
                    var revenue_height = jQuery( "#customer_by_revenue" ).height();
//ORDERS
                    jQuery( "#by_orders").empty();
                    jQuery( "#by_orders" ).append( result.customers_by_orders );
                    var orders_height = jQuery( "#by_orders" ).height();

                    if( revenue_height > orders_height )
                    {
                        jQuery( "#by_orders" ).height( revenue_height + 20 );
                    }
                    else if( revenue_height < orders_height )
                    {
                        jQuery( "#customer_by_revenue" ).height( orders_height +20  );
                    }

// -- END WIDGET TWtomd


//TIME SPENT
                    jQuery( "#time_spent" ).empty();
                    jQuery( "#time_spent" ).append( result.time_spent );
                    jQuery( "#time_spent" ).css('height','auto');


// order summary
                    jQuery( "#order_summary").empty();
                    jQuery( "#order_summary" ).append( result.order_summary );
                    jQuery( "#order_summary" ).css('height', 'auto');

                },
                error:function(errorthrown)
                {
                    console.log( errorthrown);
                }
            });
        }
        else
        {
            jQuery( "#wootracking_chart_container" ).empty();
            jQuery('#custom_datepicker_start').addClass('datepicker');
            jQuery('#custom_datepicker_end').addClass('datepicker');
            jQuery( '.datepicker' ).datepicker({
                format : 'yyyy-mm-dd'
            });
            jQuery('.datepicker').on('changeDate', function(ev){
                jQuery(this).datepicker('hide');
            });

            jQuery( "#custom_datepicker" ).show();

            jQuery( "#custom_dates" ).click(function() {

                jQuery( "#most_sold_products").height("");
                jQuery( "#product_no_of_visit" ).height("");
                jQuery( "#customer_by_revenue" ).height("");
                jQuery( "#by_orders" ).height("");

                var from_date = jQuery( "#custom_datepicker_start" ).val();
                var to_date = jQuery("#custom_datepicker_end").val();
                jQuery.ajax({
                    type: 'post',
                    url: ajaxurl,
                    data: {
                        'action': 'wci_funnel_chart',
                        'filter_type': filter_value,
                        'from_date' : from_date,
                        'to_date' : to_date
                    },

                    cache: false,
                    success: function (response) {
                        jQuery( "#wootracking_chart_container" ).empty();
                        var result = JSON.parse(response);
                        if (result.info.total_visitors == 0 && result.info.AddToCart == 0  && result.info.Checkout == 0 && result.info.SuccessfulPurchases == 0) {    		jQuery( "#wootracking_chart_container" ).empty();
                            swal('Warning!','No Data Found on this date' , 'warning');
                            var wci_sample_chart = jQuery("#loading-chart").val();
                            jQuery( '#wootracking_chart_container' ).html( "<img src="+wci_sample_chart+">" );
                            //jQuery('#wootracking_chart_container').html("<div style='text-align:center:width:250px;height:400px;padding-top:150px;'><p class='alert alert-danger' style='font-size:20px;font-family:sans-serif;'><strong>ALERT : </strong><br><br> No Data Found on this date</p></div>");
                        }
                        else {
                            var data = [
                                ['Total Visitors',   result.info.total_visitors , '#2F91B8' , '#FFFFFF'],
                                ['AddToCart', result.info.AddToCart , '#3E3E3E', '#FFFFFF'],
                                ['Checkout',    result.info.Checkout, '#9B9B9B' , '#FFFFFF'],
                                ['SuccessfulPurchases',  result.info.SuccessfulPurchases , '#95A926' , '#FFFFFF'],
                            ];

                            width = jQuery('#wootracking_d3_funnel').width();
                            var options = {
                                chart: {
                                    width: width-600,
                                    height: 400,
                                    bottomWidth: 1 / 3,
                                    animate : 300 ,
                                },
                                block: {
                                    //dynamicHeight: true,
//            fillType: "solid",
                                    hoverEffects: true,
                                    fill : {
                                        type : "gradient" ,
                                    }
                                },
                                label : {
                                    fontSize : "15px",
                                },
                            };

                            var funnel = new D3Funnel("#wootracking_chart_container");
                            funnel.draw(data, options);


                        }

//NEW
// 1--- WIDGET ONE

//MOST SOLD
                        jQuery( "#most_sold_products").empty();
                        jQuery( "#most_sold_products" ).append( result.most_sold );
                        var MS_height = jQuery( "#most_sold_products").height();


//NO OF VISIT
                        jQuery( "#product_no_of_visit" ).empty();
                        jQuery( "#product_no_of_visit" ).append( result.no_of_visit );
                        var No_of_visit_height = jQuery( "#product_no_of_visit" ).height();

                        if( MS_height > No_of_visit_height )
                        {
                            jQuery( "#product_no_of_visit" ).height( MS_height + 20 );
                        }
                        else if( MS_height < No_of_visit_height )
                        {
                            jQuery( "#most_sold_products").height( No_of_visit_height + 20  );
                        }

// --- END WIDGET ONE

// 2-- WIDGET TWO
//REVENUE

                        jQuery( "#customer_by_revenue" ).empty();
                        jQuery( "#customer_by_revenue" ).append( result.customers_by_revenue );
                        var revenue_height = jQuery( "#customer_by_revenue" ).height();
//ORDERS
                        jQuery( "#by_orders").empty();
                        jQuery( "#by_orders" ).append( result.customers_by_orders );
                        var orders_height = jQuery( "#by_orders" ).height();

                        if( revenue_height > orders_height )
                        {
                            jQuery( "#by_orders" ).height( revenue_height + 20 );
                        }
                        else if( revenue_height < orders_height )
                        {
                            jQuery( "#customer_by_revenue" ).height( orders_height +20  );
                        }


// -- END WIDGET TWO

//TIME SPENT
                        jQuery( "#time_spent" ).empty();
                        jQuery( "#time_spent" ).append( result.time_spent );
                        jQuery( "#time_spent" ).css('height','auto');

// order summary
                        jQuery( "#order_summary").empty();
                        jQuery( "#order_summary" ).append( result.order_summary );
                        jQuery( "#order_summary" ).css('height','auto');




                    },
                    error:function(errorThrown)
                    {
                        console.log( errorThrown);
                    }
                });

            });
        }
    });

//END COMBO DATE FILTER


//Overall now
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            'action': 'wci_funnel_chart',
        },

        cache: false,
        success: function (response) {
            jQuery( "#funnel_datepicker" ).val("");
            var result = JSON.parse(response);
            if (result.info.total_visitors == 0 && result.info.AddToCart == 0  && result.info.Checkout == 0 && result.info.SuccessfulPurchases == 0) {
                var wci_sample_chart = jQuery("#loading-chart").val();

                jQuery( '#wootracking_chart_container' ).html( "<img src="+wci_sample_chart+">" );
            }
            else {
                var data = [
                    ['Total Visitors',   result.info.total_visitors , '#2F91B8' , '#FFFFFF'],
                    ['AddToCart', result.info.AddToCart , '#3E3E3E', '#FFFFFF'],
                    ['Checkout',    result.info.Checkout, '#9B9B9B' , '#FFFFFF'],
                    ['SuccessfulPurchases',  result.info.SuccessfulPurchases , '#95A926' , '#FFFFFF'],
                ];
                width = jQuery('#wootracking_d3_funnel').width();

                var options = {
                    chart: {
                        width: width-600,
                        height: 400,
                        bottomWidth: 1 / 3,
                        animate : 300 ,
                    },
                    block: {
                        //dynamicHeight: true,
                        //fillType: "solid",
                        hoverEffects: true,
                        fill : {
                            type : "gradient" ,
                        }
                    },
                    label : {
                        fontSize : "15px",
                    },
                };
                var funnel = new D3Funnel("#wootracking_chart_container");
                funnel.draw(data, options);
            }
// 1--- WIDGET ONE

//MOST SOLD
            jQuery( "#most_sold_products").empty();
            jQuery( "#most_sold_products" ).append( result.most_sold );
            var MS_height = jQuery( "#most_sold_products").height();


//NO OF VISIT
            jQuery( "#product_no_of_visit" ).empty();
            jQuery( "#product_no_of_visit" ).append( result.no_of_visit );
            var No_of_visit_height = jQuery( "#product_no_of_visit" ).height();

            if( MS_height > No_of_visit_height )
            {
                jQuery( "#product_no_of_visit" ).height( MS_height + 20 );
            }
            else if( MS_height < No_of_visit_height )
            {
                jQuery( "#most_sold_products").height( No_of_visit_height + 20  );
            }

// --- END WIDGET ONE

// 2-- WIDGET TWO
//REVENUE

            jQuery( "#customer_by_revenue" ).empty();
            jQuery( "#customer_by_revenue" ).append( result.customers_by_revenue );
            var revenue_height = jQuery( "#customer_by_revenue" ).height();
//ORDERS
            jQuery( "#by_orders").empty();
            jQuery( "#by_orders" ).append( result.customers_by_orders );
            var orders_height = jQuery( "#by_orders" ).height();

            if( revenue_height > orders_height )
            {
                jQuery( "#by_orders" ).height( revenue_height + 20 );
            }
            else if( revenue_height < orders_height )
            {
                jQuery( "#customer_by_revenue" ).height( orders_height +20  );
            }
// -- END WIDGET TWO

//TIME SPENT
            jQuery( "#time_spent" ).empty();
            jQuery( "#time_spent" ).append( result.time_spent );
            var time_spent = jQuery( "#time_spent" ).height();
// order summary
            jQuery( "#order_summary").empty();
            jQuery( "#order_summary" ).append( result.order_summary );
            var order_summary = jQuery( "#order_summary" ).height();
            if(time_spent > order_summary) {
                jQuery( "#order_summary" ).height( time_spent + 20 );
            } else if (order_summary > time_spent) {
                jQuery( "#time_spent" ).height( order_summary + 20 );
            }
        },
        error: function( errorThrown )
        {
            console.log( errorThrown );
        }
    });
});
