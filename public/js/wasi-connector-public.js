// Vue App for Listing and Widgets:
var wasiApp, wasiSearchApp;

// Vue App shared data:
var wasi_data = {
	loading: true,
	app_ready: false,
	properties: [],
	current_page: 1,
	total_properties: 0,
	total_pages: 0,
	properties_per_page : properties_per_page,
	property_types: null,
	filters: {
		for_type: 0,
		id_property_type: 0,
		min_bedrooms: 0,
		bathrooms: 0
	},
	contact: {
		id_country: 0,
		id_region: 0,
		id_city: 0
	},
	location: {
		regions: [],
		cities: []
	}
};
if(typeof propertyTypes !== 'undefined') {
	wasi_data.property_types = propertyTypes;
}
if(typeof wasi_order !== 'undefined') {
	wasi_data.filters.order = wasi_order;
}
if(typeof wasi_order_by !== 'undefined') {
	wasi_data.filters.order_by = wasi_order_by;
}

function wasiLoadProperties() {
	var scope = wasi_data;

	// console.log('Loading properties from API...', scope.filters);
	var params = scope.app_ready ? scope.filters : {};
	scope.loading = true;
	scope.properties = [];
	scope.total_pages = 0;

	if(scope.total_properties>0) {
		// properties_per_page is global var fomr inlinde document!
		params.skip = (scope.current_page-1) * properties_per_page;
	}
	
	if(scope.filters.order_by!=='id_property') {
		// params.filters = scope.filters.order_by;
	}

	if(typeof wasi_featured !== 'undefined' && wasi_featured===true) {
		// https://api.wasi.co/guide/es/property/status_on_page.html
		// params.id_status_on_page = 3;
	}

	// limit results:
	if(typeof wasi_limit!== 'undefined') {
		params.take = wasi_limit;
	} else {
		params.take = properties_per_page;
	}

 	getWasiAPIData('/property/search', params).done(function(data) {
 		if(data) {
 			var infoProperties = JSON.parse(data);
 			scope.total_properties = parseInt(infoProperties.total);

 			if(infoProperties.total<=0) {
 				return false;
 			}

 			if(typeof wasi_limit === 'undefined') {
 				scope.total_pages = Math.floor(scope.total_properties / properties_per_page);
 			} else {
 				scope.total_pages = 0;
 			}

 			var list = [];
 			Object.keys(infoProperties).forEach(function(index) {
 				if(!isNaN(index)) {
 					list.push( infoProperties[index] );
 				}
 			});
 			// console.log('Props:', list);
 			list.forEach(function(prop){
 				if(prop.galleries && typeof prop.galleries[0][0]!=='undefined') {
 					prop.thumbnail = {
 						backgroundImage: 'url('+prop.galleries[0][0].url+')'
 					};
 				} else {
 					prop.thumbnail = {
 						backgroundImage: 'url(/wp-content/plugins/wasi-connector/public/img/property-image-default.jpg)'
 					}
 				}
 			});

 			scope.properties = list;
 		}
 	}).fail(function(err) {
 		console.error(err);
 	}).always(function() {
 		if( !scope.app_ready ) {
 			jQuery('#wasiApp').find('.hidden').removeClass('hidden');
 			scope.app_ready = true;
 		}
 		scope.loading = false;
 	});
}

function wasiPaginate(page) {
	this.current_page = page;
	wasiLoadProperties();
}
function wasiPreviousPage() {
	if (this.current_page > 1) {
		this.current_page--;
		wasiLoadProperties();
	}
}
function wasiNextPage() {
	if (this.current_page < this.total_pages) {
		this.current_page++;
		wasiLoadProperties();
	}
}
function wasiActivePageClass(page) {
	return (page===this.current_page) ? 'active' : '';
}

function wasiSearchProperties(evt, obj) {
	// check if I'm on the properties page or if I have to redirect from another page
	if(is_properties_page) {
		this.current_page = 1;
		wasiApp.wasiLoadProperties();
	} else {
		evt.target.action = '/' + wasi_properties_page;
		evt.target.submit();
	}
}



function getWasiAPIData(endpoint, data, method) {
	if(!method) {
		method = 'POST';
	}

	var params = {action: 'wasi_api', endpoint: endpoint};
	if(typeof data!=='undefined'  && data!==null) {
		params.api_data = data;
	}

	var ajaxRequestParams = {
		type: method,
		url: ajax_url,
		data: params
	};
	if (method==='put') {
		ajaxRequestParams.headers['X-HTTP-Method-Override'] = "PUT";
	}
	return jQuery.ajax(ajaxRequestParams);
}


function initWasiPropertiesList() {
	if(jQuery('#wasiApp').length>0) {
		var regex = /[?&]([^=#]+)=([^&#]*)/g,
		    url = window.location.href,
		    url_params = {},
		    match;
		while(match = regex.exec(url)) {
		    url_params[match[1]] = match[2];
		}
		if(Object.keys(url_params).length > 0) {
			var new_filters = {
				app_ready: true,
				filters: url_params
			};
			wasi_data = Object.assign({}, wasi_data, new_filters);
		}

		// VueJS App:
		wasiApp = new Vue({
		  el: '#wasiApp',
		  data: wasi_data,
		  created: wasiLoadProperties,
		  methods: {
		  	wasiLoadProperties: wasiLoadProperties,
		  	paginate: wasiPaginate,
		  	previousPage: wasiPreviousPage,
		  	nextPage: wasiNextPage,
		  	activePageClass: wasiActivePageClass
		  },
		  filters: {
		  	formatNumber: function(value) {
		  		var num = parseInt(value);
		  		if(num>0) {
		  			return '$'+num.toLocaleString(undefined, {useGrouping:true});
		  		}

		  		return null;
		  	}
		  }
		});
	}
}


function iniiWasiSearchForm() {
	if (jQuery('#wasiSearchApp').length>0) {
		wasiSearchApp = new Vue({
		  el: '#wasiSearchApp',
		  data: wasi_data,
		  methods: {
		  	wasiSearchProperties: wasiSearchProperties
		  }
		});
	}
}


function iniiWasiContactForm() {
	if (jQuery('#wasiContactApp').length>0) {
		new Vue({
		  el: '#wasiContactApp',
		  data: wasi_data,
		  methods: {
		  	wasiContactOwner: wasiContactOwner,
		  	changeLocationCountry: changeLocationCountry,
		  	changeLocationRegion: changeLocationRegion
		  }
		});
	}

	function changeLocationRegion() {
		getWasiAPIData('/location/cities-from-region/' + wasi_data.contact.id_region).done(function(res){
			if(res) {
				var cities = JSON.parse(res);
				var list = [];
	 			Object.keys(cities).forEach(function(index) {
	 				if(!isNaN(index)) {
	 					list.push( cities[index] );
	 				}
	 			});
	 			wasi_data.location.cities = list;
			} else {
				wasi_data.location.cities = [];
			}
		}).fail(function(err) {
			wasi_data.location.cities = [];
			console.error('Load cities error:', err);
		});
	}

	function changeLocationCountry() {
		getWasiAPIData('/location/regions-from-country/' + wasi_data.contact.id_country).done(function(res){
			if(res) {
				var regions = JSON.parse(res);
				var list = [];
	 			Object.keys(regions).forEach(function(index) {
	 				if(!isNaN(index)) {
	 					list.push( regions[index] );
	 				}
	 			});
	 			wasi_data.location.regions = list;
			} else {
				wasi_data.location.regions = [];
			}
		}).fail(function(err) {
			wasi_data.location.regions = [];
			console.error('Load regions error:', err);
		});
	}

	function wasiContactOwner() {
		var div = jQuery('#wasiResponseContact');
		var btn = jQuery('#wasi-contact-btn');
		var ajaxImg = jQuery('#contact-ajax-send');
		btn.button('loading');
		ajaxImg.removeClass('hidden');
		div.removeClass('alert-success');
		div.removeClass('alert-danger');
		div.hide();

		if(typeof id_user_property!=='undefined') {
			this.contact.id_user_property = id_user_property;
			this.contact.id_property = id_property;
			ajaxContactWasi(this.contact).done(function(res){
				console.log(res);
				showResponseContactWasi(res.message);
			}).fail(function(err){
				//
			}).always(function(){
				btn.button('reset');
				ajaxImg.addClass('hidden');
			});
		} else {
			// show error because the id_property doesn't exist!
			btn.button('reset');
			ajaxImg.addClass('hidden');
		}
	}

	function showResponseContactWasi(message, isError) {
		var div = jQuery('#wasiResponseContact');
		if(isError) {
			div.addClass('alert-danger');
		} else {
			div.addClass('alert-success');
		}
		div.html(message);
		div.slideDown();
	}


	function ajaxContactWasi(data) {
		var params = {action: 'wasi_contact', data: data};
		var ajaxRequestParams = {
			type: 'POST',
			url: ajax_url,
			data: params
		};
		return jQuery.ajax(ajaxRequestParams);
	}
}


// init whne script is loaded:
(function( $ ) {
	'use strict';
	initWasiPropertiesList();
	iniiWasiSearchForm();
	iniiWasiContactForm();
	
})( jQuery );
