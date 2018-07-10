/*
Document   :  Dashboard
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/
// Initialization and events code for the app
pspDashboard = (function ($) {
    "use strict";

    // public
    var debug = false;
    var maincontainer = null;

	// init function, autoload
	function init()
	{
		maincontainer = $(".psp-main");
		triggers();
	};
	
	function loadAudience()
	{
		var graph = $("#psp-audience-visits-graph"); 
		
		jQuery.post(ajaxurl, {
			'action' 		: 'pspGoogleAPIRequest',
			'sub_action' 	: 'getAudience',
			'from_date'		: graph.data('fromdate'),
			'to_date'		: graph.data('todate'),
			'debug'			: debug
		}, function(response) {

			//fixed in 2016-12-05
			//data not received!
			if( typeof(response.checkProfile) == "undefined" || response.checkProfile.status == 'invalid' ){
				//graph.parents('.psp-panel-widget').eq(0).remove();
				graph.parents('.psp-dashboard-box-audience_overview').eq(0).remove(); //fixed in 2016-12-05
				
				return false;
			}

			if (response.__access.status == 'invalid') {
				//$(".psp-panel, .psp-grid_1_3").css({'display': 'none'}); //hide info panels!
				pspFreamwork.to_ajax_loader_close();
				if ( response.__access.isalert == 'yes' )
					swal(response.__access.msg);
				return false;
			}

			//fixed in 2016-12-05
			if( response.getAudience.status == 'valid' ){

				var data = response.getAudience.data.rows;
				var dataPageViewsDate = [];
				for(var i = 0; i < data.pageviews.length; i++) {
					var currentDate = new Date(data.pageviews[i][0]);
					var currentDateFormated = currentDate.getDay() +'/'+ currentDate.getMonth() +'/'+ currentDate.getFullYear();
					dataPageViewsDate.push(currentDateFormated);
				};

				var presets = window.chartColors; 
				var utils = Samples.utils;
				var inputs = {
					min: -100,
					max: 100,
					count: 8,
					decimals: 2,
					continuity: 1
				};

				var options = {
					maintainAspectRatio: false, 
					spanGaps: false,
					elements: {
						line: {
							tension: 0.000001
						}
					},
					plugins: {
						filler: {
							propagate: false
						}
					},
					scales: {
						xAxes: [{
							ticks: {
								autoSkip: true,
								maxRotation: 0
							}
						}],
						yAxes: [{
							ticks: {
								autoSkip: true
							}
						}]
					}
				};

				var _dataSets = {};
				
				var dataPageViews = [];
				for(var i = 0; i < data.pageviews.length; i++) {
					dataPageViews.push(data.pageviews[i][1]);
				};

				_dataSets['pageViews'] = {
					borderColor: '#ff9f40',
					backgroundColor: '#ff6384',
					pointBorderColor: '#ff6384',
					pointBackgroundColor:  '#ff6384',
					data: dataPageViews,
					label: 'Page Views',
					fill: false,
					pointRadius: 5
				};
				
				var dataNewVisits = [];
				for(var i = 0; i < data.newVisits.length; i++) {
					dataNewVisits.push(data.newVisits[i][1]);
				};
				_dataSets['newVisits'] = {
					borderColor: '#47c2a5',
					backgroundColor: '#489be8',
					pointBorderColor: '#489be8',
					pointBackgroundColor:  '#489be8',
					data: dataNewVisits,
					label: 'New Visits',
					fill: false,
					pointRadius: 5
				};

				var datauniquePageviews = [];
				for(var i = 0; i < data.uniquePageviews.length; i++) {
					datauniquePageviews.push(data.uniquePageviews[i][1]);
				};
				_dataSets['uniquePageviews'] = {
					borderColor: '#ffcd56',
					backgroundColor: '#ffcd56',
					pointBorderColor: '#ff9f40',
					pointBackgroundColor:  '#ff9f40',
					data: datauniquePageviews,
					label: 'Unique Page Views',
					fill: false,
					pointRadius: 5
				};

				var dataVisitBounceRate = [];
				for(var i = 0; i < data.visitBounceRate.length; i++) {
					dataVisitBounceRate.push(data.visitBounceRate[i][1]);
				};
				_dataSets['VisitBounceRate'] = {
					borderColor: '#ff6384',
					backgroundColor: '#753ce7',
					pointBorderColor: '#753ce7',
					pointBackgroundColor:  '#753ce7',
					data: dataVisitBounceRate,
					label: 'Visit Bounce Rate',
					fill: false,
					pointRadius: 5
				};
				
				var html = [];
				

				var __dataSets = [];
				$.each( _dataSets, function(key, value){
					__dataSets.push( value );
				} );

				[false, 'origin', 'start', 'end'].forEach(function(boundary, index) {
					new Chart('psp-audience-visits-graph', {
						type: 'line',
						data: {
							labels: dataPageViewsDate,
							datasets: __dataSets
						},
						options: utils.merge(options, {
							title: {
								text: 'fill: ' + boundary,
								display: false
							},
							legend: {
								position: "bottom",
								labels: {
									padding: 25,
									boxWidth: 65,
									padding: 25,
									fontSize: 16,
								}
							},
							tooltips: {
				                enabled: true,
				                mode: 'single',
				                callbacks: {
				                    label: function(tooltipItems, data) {
				                    	if( 
				                    		data.datasets[tooltipItems.datasetIndex].label == 'New Visits' ||
				                    		data.datasets[tooltipItems.datasetIndex].label == 'Page Views' 
				                    	){
					                    	return data.datasets[tooltipItems.datasetIndex].label +': ' + tooltipItems.yLabel;
				                    	}

				                    	if( data.datasets[tooltipItems.datasetIndex].label == 'Unique Page Views' ){
					                    	return data.datasets[tooltipItems.datasetIndex].label +': ' + tooltipItems.yLabel;
				                    	}

				                    	if( data.datasets[tooltipItems.datasetIndex].label == 'Visit Bounce Rate' ){
					                    	return data.datasets[tooltipItems.datasetIndex].label +': ' + tooltipItems.yLabel.toFixed(2) + "%";
				                    	}
					                }
				                }
				            },
						})
					});
				});
				/*
				var data = response.getAudience.data.rows; 
				var opts = {
					series: {
						lines: { show: true },
						points: { show: true }
					},
					tooltip: true,
					tooltipOpts: {
						defaultTheme: true,
						content: "%x<br />%s: %y",
						xDateFormat: "%d/%m/%y"
					},
					xaxis: {
						mode: "time",
						timeformat: "%d/%m/%y"
					},
					grid: {
						hoverable: true,
						clickable: true,
						borderWidth: null
					}
				};
		
				var datasets = [
					{ data: data.newVisits, label: "% New Visits", color: "#E15656" },
					{ data: data.visits, label: "Visits", color: "#61A5E4" },
					{ data: data.avgTimeOnPage, label: "Avg. Visit Duration", color: "#37aa37" },
					{ data: data.visitBounceRate, label: "Bounce Rate", color: "#A6D037" },
					{ data: data.pageviews, label: "Pageviews", color: "#ad6dd6"},
					{ data: data.uniquePageviews, label: "Unique Visitors", color: "#a91c83" }
				];
				
				var plot = $.plot(graph, datasets, opts);
				
				// remove the loading
				graph.css('background-image', 'none');
				*/
			}else{
			    graph.html( response.getAudience.reason );

                // remove the loading
                graph.css('background-image', 'none');

				//graph.parents('.psp-panel-widget').eq(0).remove();
				graph.parents('.psp-dashboard-box-audience_overview').eq(0).remove(); //fixed in 2016-12-05
			}
			
		}, 'json');
		
	}
	
	function tooltip()
	{
		var xOffset = -30,
			yOffset = -300,
			winW 	= $(window).width();
		
		$(".psp-aa-products-container ul li a").hover(function(e){
			
			var that = $(this),
				preview = that.data('preview');

			$("body").append("<p id='psp-aa-preview'>"+ ( '<img src="' + ( preview ) + '" >' ) +"</p>");
			
			var new_left = e.pageX + yOffset;
			
			if( new_left > (winW - 640) ){
				new_left = (winW - 640)
			}
			$("#psp-aa-preview")
				.css("top",(e.pageY - xOffset) + "px")
				.css("left",(new_left) + "px")
				.fadeIn("fast");
	    },
		function(){
			this.title = this.t;
			$("#psp-aa-preview").remove();
	    });
		
	
		$(".psp-aa-products-container ul li a").mousemove(function(e){
			
			var new_left = e.pageX + yOffset;
			if( new_left > (winW - 640) ){
				new_left = (winW - 640)
			}
			
			$("#psp-aa-preview")
				.css("top",(e.pageY - xOffset) + "px")
				.css("left",(new_left) + "px");
		});
	}
	
	function boxLoadAjaxContent( box )
	{
		var allAjaxActions = [];
		pspFreamwork.to_ajax_loader( "Loading..." );
		box.find('.is_ajax_content').each(function(key, value){
			
			var alias = $(value).text().replace( /\n/g, '').replace("{", "").replace("}", "");
			$(value).attr('id', 'psp-row-alias-' + alias);
			allAjaxActions.push( alias );
		});
  
		jQuery.post(ajaxurl, {
			'action' 		: 'pspDashboardRequest',
			'sub_actions'	: allAjaxActions.join(","),
			'debug'			: debug
		}, function(response) {
			
			$.each(response, function(key, value){
				if( value.status == 'valid' ){
					var row = box.find( "#psp-row-alias-" + key );
					row.html(value.html);
					
					row.removeClass('is_ajax_content');
					pspFreamwork.to_ajax_loader_close();
					
					tooltip();
				} 
			});
			
		}, 'json');
	}
	
	function triggers()
	{
		maincontainer.find(">div").each( function(e){
			var that = $(this);
			
			// check if box has ajax content
			if( that.find('.is_ajax_content').size() > 0 ){
				boxLoadAjaxContent(that);
			}
		});
		
		$("#psp-audience-visits-graph").each(function(){
			loadAudience();
		});
		
		$(".psp-aa-products-tabs").on('click', "li:not(.on) a", function(e){
			e.preventDefault();
			
			var that = $(this),
				alias = that.attr('class').split("items-"),
				alias = alias[1];
			
			$('.psp-aa-products-container').hide();
			$("#aa-prod-" + alias).show();
			
			$(".psp-aa-products-tabs").find("li.on").removeClass('on');
			that.parent('li').addClass('on');
		});
	}

	// external usage
	return {
		"init": init
    }
})(jQuery);
