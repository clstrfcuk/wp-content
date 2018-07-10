/*
Document   :  Social Stats
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/

// Initialization and events code for the app
pspGoogleAnalytics = (function ($) {
	"use strict";

	// public
	var debug_level = 0;
	var maincontainer = null;

	// init function, autoload
	(function init() {
		// load the triggers
		$(document).ready(function(){
			maincontainer = $(".psp-main");
			
			triggers();
		});
	})();

	function authApp( code, callback )
	{
		jQuery.post(ajaxurl, {
			'action' 		: 'pspGoogleAPIRequest',
			'sub_action' 	: 'authApp',
			'code'			: code,
			'debug_level'	: debug_level
		}, function(response) {

			if( typeof(callback) == 'function' ){
				callback( response );
			}
		}, 'json');
	}

	function getProfiles( )
	{
		jQuery.post(ajaxurl, {
			'action' 		: 'pspGoogleAPIRequest',
			'sub_action' 	: 'getProfiles',
			'debug_level'	: debug_level
		}, function(response) {

			if (response.__access.status == 'invalid') {
				swal(response.__access.msg);
				return true;
			}else{

				var __select = $("select[name='psp-wizard-profiles']");
				__select.html('');
				$.each( response['getProfiles']['data']['accounts'], function(key, value){
					var __option = $('<option value="' + ( key ) + '">' + ( value ) + '</option>');
					__select.append( __option );
				} );
			}

			$(".psp-loader-holder").remove();
		}, 'json');
	}

	function setProfile()
	{
		jQuery.post(ajaxurl, {
			'action' 		: 'pspGoogleAPIRequest',
			'sub_action' 	: 'setProfiles',
			'profile_id' 	: $("select[name='psp-wizard-profiles']").val(),
			'debug_level'	: debug_level
		}, function(response) {
			$(".psp-loader-holder").remove();

		}, 'json');
	}

	function loadAuthWizard( auth_url )
	{
		var html = [],
			__container = maincontainer.parents('.psp').eq(0),
			data_url = __container.data('url');

		if( typeof(data_url) == "undefined" ){
			data_url = $(".psp-setup").data('url');
		}
		var url = data_url + 'assets';

		function __html_step1()
		{
			var html = [];
			html.push( '<div class="psp-step psp-step-1">' );
			html.push( 	'<div class="psp-wizard-top">' );
			html.push( 		'<img src="' + ( url ) + '/step1-1.png" alt="Step 1">' );
			html.push( 		'<p>Authentificate your Google Account with <span>Premium SEO Pack</span></p>' );
			html.push( 		'<img src="' + ( url ) + '/step1-2.png" alt="Step 1 PSP logo">' );
			html.push( 	'</div>' );
			html.push( 	'<div class="psp-wizard-bottom">' );
			html.push( 		'<button class="psp-cancel">Cancel</button>' );
			html.push( 		'<button class="psp-next">Next <i class="psp-checks-arrow-right4"></i></button>' );
			html.push( 	'</div>' );
			html.push( '</div>' );
			
			return html.join( "\n" );
		}

		function __html_step2()
		{
			var html = [];
			html.push( '<div class="psp-step psp-step-2">' );
			html.push( 	'<div class="psp-wizard-top">' );
			html.push( 		'<img src="' + ( url ) + '/step2.png" alt="Step 2">' );
			html.push( 		'<button class="psp-get-code">Authenticate with your Google account</button>' );
			html.push( 		'<p>Copy the Google Code into the box below then click next</p>' );
			html.push( 		'<input type="text" placeholder="Paste code here" id="psp-google-code">' );
			html.push( 	'</div>' );
			html.push( 	'<div class="psp-wizard-bottom">' );
			html.push( 		'<button class="psp-cancel">Cancel</button>' );
			html.push( 		'<button class="psp-next">Next <i class="psp-checks-arrow-right4"></i></button>' );
			html.push( 	'</div>' );
			html.push( '</div>' );
			
			return html.join( "\n" );
		}

		function __html_step3()
		{
			var html = [];
			html.push( '<div class="psp-step psp-step-3">' );
			html.push( 	'<div class="psp-loader-holder"><div class="psp-loader"></div> loading profiles ...</div>' );
			html.push( 	'<div class="psp-wizard-top">' );
			html.push( 		'<img src="' + ( url ) + '/step3.png" alt="Step 2">' );
			html.push( 		'<p>Select Google Analytics Profile </p>' );
			html.push( 		'<div class="psp-wizard-profiles">' );
			html.push( 			'<select name="psp-wizard-profiles">' );
			html.push( 				'<option value="">loading ...</option>' );
			html.push( 			'</select>' );
			html.push( 		'</div>' );
			html.push( 	'</div>' );
			html.push( 	'<div class="psp-wizard-bottom">' );
			html.push( 		'<button class="psp-cancel">Cancel</button>' );
			html.push( 		'<button class="psp-next">Next <i class="psp-checks-arrow-right4"></i></button>' );
			html.push( 	'</div>' );
			html.push( '</div>' );
			
			return html.join( "\n" );
		}

		function __html_step4()
		{
			var html = [];
			html.push( '<div class="psp-step psp-step-4">' );
			html.push( 	'<div class="psp-loader-holder"><div class="psp-loader"></div> saving profile ... </div>' );
			html.push( 	'<div class="psp-wizard-top">' );
			html.push( 		'<img src="' + ( url ) + '/step4.png" alt="Step 2">' );
			html.push( 		'<p>Finished!</p>' );
			html.push( 		'<p class="psp-finish-message">You can now go to the Google Analytics Module!</p>' );
			html.push( 	'</div>' );
			html.push( 	'<div class="psp-wizard-bottom">' );
			html.push( 		'<button class="psp-cancel">Close</button>' );
			html.push( 	'</div>' );
			html.push( '</div>' );
			
			return html.join( "\n" );
		}

		html.push( '<div class="psp-wizard-wrapper" data-step="1">' );
		html.push( __html_step1() );
		html.push( __html_step2() );
		html.push( __html_step3() );
		html.push( __html_step4() );
		html.push( '</div>' );

		__container.append( html.join( "\n" ) );
		var wizard_wrapper = __container.find(".psp-wizard-wrapper");

		__container.on('click', '.psp-cancel', function(e){
			e.preventDefault();

			wizard_wrapper.fadeOut();

			var step = wizard_wrapper.data('step');
			
			if( step == 4 ){
				window.location.reload();
				return true;
			}
		});

		$('body').on('click', '.psp-get-code', function(e){
			e.preventDefault();
			var newwindow = window.open( auth_url,'Google Authorize App','height=550,width=650' );

			newwindow.focus();
		});

		__container.on('click', '.psp-next', function(e){
			e.preventDefault();

			var step = (wizard_wrapper.data('step') + 1);

			function next_step()
			{
				wizard_wrapper.find( ".psp-step" ).hide();
				wizard_wrapper.find( ".psp-step-" + step ).show();
				wizard_wrapper.data('step', step);
			}

			if( step == 3 ){
				var code = $.trim( $("#psp-google-code").val() );
				if( code == "" ){
					step = 2;
					next_step();
					swal( "Copy the Google Code into this box then click next!", '', 'error');
				}else{
					authApp( code, function( response ){
						console.log(response );
						if( response['authApp'].status == 'invalid' ){
							swal( response['authApp']['msg'], '', 'error');
							step = 2;
							next_step();
						}else{
							getProfiles();
						}
					} );
				}
			}

			next_step();

			if( step == 4 ){
				setProfile();
			}
		});
	}
	
	function loadAudience()
	{
		if ( $('#psp-wrapper').find('.psp-error-using-module').length > 0 ) {
			pspFreamwork.to_ajax_loader_close();
			return false;
		}

		jQuery.post(ajaxurl, {
			'action' 		: 'pspGoogleAPIRequest',
			'sub_action' 	: 'getAudience,getAudienceDemographics,getAudienceSystem',
			'from_date'		: $("#psp-filter-by-date-from").val(),
			'to_date'		: $("#psp-filter-by-date-to").val(),
			'debug_level'	: debug_level
		}, function(response) {

			if( response.checkProfile.status == 'invalid' ){
				
				//loadAuthWizard();
				make_auth_with_save( $(this) );

				var step = 3;
				var wizard_wrapper = $(".psp-wizard-wrapper");
				wizard_wrapper.find( ".psp-step" ).hide();
				wizard_wrapper.find( ".psp-step-" + step ).show();
				wizard_wrapper.data('step', step);

				//getProfiles();
				pspFreamwork.to_ajax_loader_close();
				return true;
			}

			//data not received!
			if (response.__access.status == 'invalid') {
				
				pspFreamwork.to_ajax_loader_close();
				if( typeof(response.__access.alias) != "undefined" && response.__access.alias == 'need_to_auth' ){
					//loadAuthWizard();
					make_auth_with_save( $(this) );
				}else{
					if ( response.__access.isalert == 'yes' ){
						swal(response.__access.msg);
					}
				}
				return false;
			}



			//getAudience
			if( response.getAudience.status == 'valid' ){
				make_getAudience( response.getAudience.data );
			} else {
				$("#psp-audience-visits-graph").html( response.getAudience.reason );

				// remove the loading
				$("#psp-audience-visits-graph").css('background-image', 'none');
			}
			
			//getAudienceDemographics
			if( response.getAudienceDemographics.status == 'valid' ){  
				make_getAudienceDemographics( response.getAudienceDemographics.data );
			}

			if( response.contentCountry.status == 'valid' ){  
				build_map( response.contentCountry.data );
			}
			if( response.channelGrouping.status == 'valid' ){  
				if ( response.channelGrouping.data != 'null' ) {
					make_getTopChannels( response.channelGrouping.data );
				}else{
					$('#psp-top-channels-graph').html('No data available'); 
				}
			}
			
			setTimeout( function(){
				$("#psp-gAnalytics-wrapper .row").each( function(){
					var that = $(this),
						maxheight = 0,
						child = that.find(">div > .panel");

					child.each(function(){
						var that2 = $(this),
							_height = that2.height();
						if( _height > maxheight ){
							maxheight = _height;
						}
					});

					child.height( maxheight );
				});

			}, 1500 );
			
			pspFreamwork.to_ajax_loader_close();
		}, 'json');
	}
	
	function make_getAudience(response) 
	{
		$(".psp-filters-labels-wrapper").html('');

		var _the_chart = '';

		function build_audience_graph()
		{
			var data = response.rows;
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
				pointBorderColor: '#753ce7',
				pointBackgroundColor:  '#753ce7',
				data: dataVisitBounceRate,
				label: 'Visit Bounce Rate',
				fill: false,
				pointRadius: 5
			};
			
			var html = [];
			$.each( _dataSets, function(key, value){
				html.push( '<div class="psp-check-filter">' );
				html.push( 	'<input type="checkbox" name="psp-' + ( key ) + '" checked />' );
				html.push( 	'<span>' + ( value.label ) + '</span>' );
				html.push( '</div>' );
			} );

			if( $(".psp-filters-labels-wrapper input").length == 0 ){
				$(".psp-filters-labels-wrapper").html( html.join("\n") );

				pspFreamwork.init_custom_checkbox();
			}

			var __dataSets = [];
			$.each( _dataSets, function(key, value){
				var __input = $(".psp-filters-labels-wrapper input[name='psp-" + ( key ) + "']");

				if( __input.is(":checked") == true ){
					__dataSets.push( value );
				}
			} );
			
			[false, 'origin', 'start', 'end'].forEach(function(boundary, index) {
				_the_chart = new Chart('psp-audience-visits-graph', {
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
						legend: false,
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
		}

		build_audience_graph( response );

		$(".psp-filters-labels-wrapper").on('DOMSubtreeModified', 'input', function(){
			var that = $(this);
			_the_chart.destroy();

			delete  window._the_chart;
			$("#psp-audience-visits-graph").remove();
			$(".chartjs-hidden-iframe").remove();

			var __cont = $(".psp-audience-graph");
			var __canvas = $('<canvas />', {
				'id': 'psp-audience-visits-graph',
			});

			__cont.prepend( __canvas );

			build_audience_graph( response );
		});

		// remove the loading
		$("#psp-audience-visits-graph").css('background-image', 'none');
	}

	function make_getTopChannels(response) {
 	
		response = $.parseJSON( response );

		var prefab_data = [];

		if( typeof(response[0]) != "undefined" ){
			prefab_data.push( response[0][1] );
		}
		if( typeof(response[1]) != "undefined" ){
			prefab_data.push( response[1][1] );
		}
		if( typeof(response[2]) != "undefined" ){
			prefab_data.push( response[2][1] );
		}
		if( typeof(response[3]) != "undefined" ){
			prefab_data.push( response[3][1] );
		}
		if( typeof(response[4]) != "undefined" ){
			prefab_data.push( response[4][1] );
		}
		if( typeof(response[5]) != "undefined" ){
			prefab_data.push( response[5][1] );
		}
		var config = {
			type: 'pie',
			data: {
				datasets: [{
					data: prefab_data,
					backgroundColor: [
						'#36a2eb',
						'#ffcd56',
						'#9966ff',
						'#ff9f40',
						'#8e44ad',
						'#2c3e50',
					],
					label: 'Top Channel'
				}],
				labels: [
					"Referral",
					"Organic Search",
					"Direct",
					"Social",
					"Email",
					"Affiliates",
				]
			},
			options: {
				legend: {
					position: 'bottom',
					fullWidth: true
				},
				responsive: true
			}
		};

		var ctx = document.getElementById("top-channels").getContext("2d");
		new Chart(ctx, config);
		

	}
	
	function make_getAudienceDemographics(response) 
	{
		// create some alias
		var __groups = [ 'contentPages', 'getReferral' ],
		html = response.html;
	
		$.each(__groups, function(key, val) {
			var container = $(".psp-" + ( val ) + "-container");
			container.html(html[val]); //apply data!

			if( val == 'contentPages' ){
				add_paging_table( container, 10 );
			}

			if( val == 'getReferral' ){
				add_paging_table( container, 5 );
			}
		});
	}

	function add_paging_table( container, per_page )
	{
		var paged = 1,
			items = container.find("table .psp-allow-paging");
		
		var limit = paged * per_page - 1;
		items.each(function(index){
			if( index >= (limit - per_page)  && index <= limit ){
				$(this).addClass("is_show");
			}
		});

		var paging_wrapper = $("<div />", {
			'class': 'tablenav'
		});

		container.append( paging_wrapper );

		function build_paging()
		{
			if( items.length > 0 ){

				var paging = $("<div />", {
					'class': 'tablenav-pages'
				});

				paging.append("<div class='displaying-num'>" + ( items.length ) + " items</div>");

				var html = [];

				var steps = Math.floor( items.length / per_page );

				var prev_page = paged - 1;
				if( prev_page < 1 ){
					prev_page = 1;
				}
				var next_page = paged + 1;
				if( next_page > steps ){
					next_page = steps;
				}

				//console.log(steps, next_page,prev_page, paged );

				html.push( '<span class="pagination-links">' );
				html.push( 	'<a class="first-page  psp-jump-page" title="Go to the first page" href="#paged=1">«</a>' );
				html.push( 	'<a class="prev-page  psp-jump-page" title="Go to the previous page" href="#paged=' + ( prev_page ) + '">‹</a>' );
				html.push( 	'<span class="paging-input"><input class="current-page" title="Current page" type="text" name="paged" value="' + ( paged ) + '" size="2" style="width: 45px;"> of <span class="total-pages">' + ( steps ) + '</span></span>' );
				html.push( 	'<a class="next-page  psp-jump-page" title="Go to the next page" href="#paged=' + ( next_page ) + '">›</a>' );
				html.push( 	'<a class="last-page  psp-jump-page" title="Go to the last page" href="#paged=' + ( steps ) + '">»</a></span>' );

				html.push( '</span>' );

				paging.append( html.join("\n") );

				paging_wrapper.html( paging );
			}
		}

		build_paging();

		container.on('click', ".psp-jump-page", function(e){
			e.preventDefault();

			var that = $(this);

			paged = parseInt(that.attr('href').replace("#paged=", ''));
			limit = paged * per_page - 1;

			items.each(function(index){
				if( index >= (limit - per_page + 1)  && index <= limit ){
					$(this).addClass("is_show");
				}else{
					$(this).removeClass("is_show");
				}
			});

			build_paging();
		});
	}

	function plotAccordingToChoices( choiceContainer, datasets, plot_elm, opts ) 
	{	
		var data = [];
		$("#audience-choose-container").find("input:checked").each(function () {
			var key = $(this).attr("name");
			if (key && datasets[key]) {
				data.push(datasets[key]);
			}
		});

		if (data.length > 0) {
			$.plot( plot_elm, data, opts);
		}
	}

	function build_map( map_data )
	{
		google.charts.load('current', {
			'packages':['geochart'],
			// See: https://developers.google.com/chart/interactive/docs/basic_load_libs#load-settings
			'mapsApiKey': 'AIzaSyBFSzAG_1mw-qlBTB6W8buTq3uzdhQmdpI'
		});
		google.charts.setOnLoadCallback(drawRegionsMap);
		
		function drawRegionsMap() {
			var data = google.visualization.arrayToDataTable(  $.parseJSON( map_data ) );
			var options = {
				colorAxis: {
					colors: [
					'#fee50e', '#ff9f3f', '#f54343']
				},
				backgroundColor: '#fff',
				datalessRegionColor: '#dbdbdb',
				defaultColor: '#f5f5f5',
			};

			var chart = new google.visualization.GeoChart( $(".psp-country-container")[0] );

			chart.draw(data, options);
		}
	}

	function createInterface()
	{
		pspFreamwork.to_ajax_loader( "Loading..." );
		
		if ( $('#psp-wrapper').find('.psp-error-using-module').length > 0 ) {
			pspFreamwork.to_ajax_loader_close();
			return false;
		}
		
		// Datepicker (range)
		$( "#psp-filter-by-date-from" ).datepicker({
			changeMonth: true,
			numberOfMonths: 1,
			dateFormat: "yy-mm-dd",
			onClose: function( selectedDate ) {
				$( "#psp-filter-by-date-to" ).datepicker( "option", "minDate", selectedDate );
			}
		});
		
		$( "#psp-filter-by-date-to" ).datepicker({
			changeMonth: true,
			numberOfMonths: 1,
			dateFormat: "yy-mm-dd",
			onClose: function( selectedDate ) {
				$( "#psp-filter-by-date-from" ).datepicker( "option", "maxDate", selectedDate );
			}
		});
		
		loadAudience();
	}

	function make_auth_with_save( $this )
	{
		var saveform = $this.data('saveform') || 'yes';
		
		var ajaxPms = {
			'action' 		: 'pspGoogleAuthorizeApp',
			'saveform'		: saveform
		};

		pspFreamwork.to_ajax_loader( "Saving your settings ..." );

		if ( typeof saveform != 'undefined' && saveform == 'yes' ) {
			var form = $this.parents('form').eq(0),
				client_id = form.find("#client_id").val(),
				client_secret = form.find("#client_secret").val();

			// Check if user has client ID and client secret key
			if( client_id == '' || client_secret == '' ){
				swal('Please add your Client ID / Secret for authorize your app.');
				return false;
			}

			ajaxPms.params = form.serialize()		
		}
	
		$.post(ajaxurl, ajaxPms, function(response) {

			if( response.status == 'valid' )
			{
				loadAuthWizard( response.auth_url );
				pspFreamwork.to_ajax_loader_close();
			}
		}, 'json');

	}

	function googleAuthorizeApp()
	{
		$('body').on('click', ".psp-google-de-authorize-app", function(e){
			e.preventDefault();

			pspFreamwork.to_ajax_loader( "Deauthorizing your app ..." );

			jQuery.post(ajaxurl, {
				'action' 		: 'pspGoogleAPIRequest',
				'sub_action' 	: 'deAuthorize',
				'debug_level'	: debug_level
			}, function(response) {
				if( response.status == 'valid' ){
					window.location.reload();
					return true;
				}
			}, 'json');
		});

		$('body').on('click', ".psp-google-authorize-app", function(e){
			e.preventDefault();
			make_auth_with_save( $(this) );
		});
	}


	function triggers()
	{
		if( $( "#psp-filter-by-date-from" ).length > 0 ){
			createInterface();
		}
		googleAuthorizeApp();
		
		$("#psp-filter-graph-data").click(function () {
			pspFreamwork.to_ajax_loader( "Loading..." );
			
			loadAudience();
		});

		$("body").on('click', "a.psp-view-page-path, a.psp-view-page-title", function(e){
			e.preventDefault();
			var that = $(this),
				pages = $(".psp-table-row-page");

			pages.find('span').hide();

			if( that.hasClass('psp-view-page-path') ){
				pages.find('span:first-child').show();
			}else{
				pages.find('span:last-child').show();
			}

			$("a.psp-view-page-path, a.psp-view-page-title").removeClass('on');
			that.addClass('on');
		});

		$(".psp-section-header .psp-panel-body").append( '<a href="#" class="psp-tab" id="psp-clear-cache"><i class="psp-icon-setup_backup"></i> Clear Analytics Cache and Refresh</a>')

		$("body").on('click', '#psp-clear-cache', function(e){
			e.preventDefault();

			jQuery.post(ajaxurl, {
				'action' 		: 'pspGoogleAPIRequest',
				'sub_action' 	: 'clearCache',
				'debug_level'	: debug_level
			}, function(response) {
				if( response.status == 'valid' ){
					window.location.reload();
					return true;
				}
			}, 'json');
		});
	}

	// external usage
	return {
	}
})(jQuery);
