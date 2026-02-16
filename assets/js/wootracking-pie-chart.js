var jQuery = jQuery.noConflict();
jQuery(document).ready(function () {
	
	jQuery( "#wootracking_pie" ).hide();
	jQuery("#user_profile_info").hide();	
	jQuery( "#user_profile_info" ).height("3");
	var selected_person;
	var selected_user_id;
	jQuery("#fetch_user_data").click(function(){
		 jQuery( "#wootracking_pie" ).empty();
		 jQuery( "#user_profile_stats").empty();
		 jQuery( "#user_history" ).hide();
		 jQuery( "#user_profile_info" ).show();
		 jQuery( "#wci_empty_data_msg" ).hide();
		 selected_user_id = jQuery("#selected_user").val();
		 selected_person = jQuery( "#selected_user option:selected" ).text();
jQuery("#default-info").hide();

jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
dataType:'json',        
data: {
            'action': 'fetch_pie_data',
	     'selected_customer' : selected_person,
	     'selected_user_id' : selected_user_id
        },

        cache: false,
        success: function (response) { 
//var result = JSON.parse(response);    
if(response == 'null'){
	jQuery("#user_profile_info" ).hide();
	jQuery("#wci_empty_data_msg" ).show();
	jQuery('#wci_empty_data_msg').html("<div style='text-align:center:width:310px;height:400px;padding-top:150px;max-width:620px;margin-left:200px;'><p class='alert alert-danger' style='font-size:20px;font-family:sans-serif;'><strong>ALERT : </strong><br><br>This user is not visited yet</p></div>");
	jQuery('#selected_user_table').html("");

}
	else{ 
	jQuery( "#user_profile_info" ).height('30em');
	
document.getElementById("user_profile_stats").innerHTML=response.stats;
	document.getElementById("selected_user_table").innerHTML=response.table;
	jQuery( "#user_history" ).hide();
	jQuery( "#wootracking_pie" ).show();
//AVATAR
var colours = ["#1abc9c", "#2ecc71", "#3498db", "#9b59b6", "#34495e", "#16a085", "#27ae60", "#2980b9", "#8e44ad", "#2c3e50", "#f1c40f", "#e67e22", "#e74c3c", "#95a5a6", "#f39c12", "#d35400", "#c0392b", "#bdc3c7", "#7f8c8d"];

var name1 = response.target_person;
var name2 = '';
//    nameSplit = name.split(" "),
 //   initials = nameSplit[0].charAt(0).toUpperCase() + nameSplit[1].charAt(0).toUpperCase();
var initials = name1[0].toUpperCase();
var charIndex = initials.charCodeAt(0) - 65,
    colourIndex = charIndex % 19;

var canvas = document.getElementById("user-icon");
var context = canvas.getContext("2d");

var canvasWidth = $(canvas).attr("width"),
    canvasHeight = $(canvas).attr("height"),
    canvasCssWidth = canvasWidth,
    canvasCssHeight = canvasHeight;

if (window.devicePixelRatio) {
    $(canvas).attr("width", canvasWidth * window.devicePixelRatio);
    $(canvas).attr("height", canvasHeight * window.devicePixelRatio);
    $(canvas).css("width", canvasCssWidth);
    $(canvas).css("height", canvasCssHeight);
    context.scale(window.devicePixelRatio, window.devicePixelRatio);
}

context.fillStyle = colours[colourIndex];
context.fillRect (0, 0, canvas.width, canvas.height);
context.font = "40px Arial";
context.textAlign = "center";
context.fillStyle = "#FFF";
context.fillText(initials, canvasCssWidth / 2, canvasCssHeight / 1.5);


//AVATAR


	var name=['VisitedPages','AddToCart','ApplyCoupon','Checkout','SuccessPurchases'];
var value=[response.chart.visited_pages,response.chart.AddToCart,response.chart.ApplyCoupon,response.chart.Checkout, response.chart.Success_purchase];


var ctx=document.getElementById("wootracking_pie").getContext("2d");
//ctx.wootracking_pie.width=800;
//ctx.wootracking_pie.height=500;
window.barchart=new Chart(ctx,{
type:'bar',
//  barGap:4,
  //barSizeRatio:0.50,
  //element: 'wootracking_pie',
 

/*data: [
    { name: 'VisitedPages', value: response.chart.visited_pages },
    { name: 'AddToCart', value: response.chart.AddToCart },
    { name: 'ApplyCoupon', value: response.chart.ApplyCoupon },
    { name: 'Checkout', value: response.chart.Checkout },
    { name: 'SuccessPurchases', value: response.chart.Success_purchase},
  ],

  xLabel: 'name',
  yLabel: 'value',
  label: 'Value',*/
  //parseTime: false,
  //xLabelMargin: 10,
  //xLabelAngle: 20,
//  pointSize: 2,

data:
{
labels:name,

datasets:[{
data:value,
borderColor:window.chartColors.blue,
backgroundColor: [
		 'rgba(75, 192, 192, 0.2)',
		 'rgba(75, 192, 192, 0.2)',
		 'rgba(75, 192, 192, 0.2)',
		 'rgba(75, 192, 192, 0.2)',
		 'rgba(75, 192, 192, 0.2)',
                 ],
borderWidth:1,
},{

}]
},
options:
{
scales: {
            xAxes: [{
                ticks: {
                    fontSize: 20
                }
            }]
        },
//fullWidth:false,
//responsive:true,
//defaultFontSize:'50',
//fontColor:'rgb(0,0,255)',
}

});	

	}
      }
    });

   });
});
