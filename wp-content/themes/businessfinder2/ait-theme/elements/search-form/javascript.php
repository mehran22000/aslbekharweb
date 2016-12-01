<script id="{$htmlId}-script">
jQuery(document).ready(function(){

	// load cities
	jQuery.ajax({
        url: 'https://buyoriginal.herokuapp.com/services/v1/dev/cities/',
        type: 'GET',
        beforeSend: function (request)
        {
        	request.setRequestHeader("Content-Type", "application/json");
        	request.setRequestHeader("token", "emFuYmlsZGFyYW5naGVybWV6DQo=");
        },
        success: function(result) {
        	result.forEach(function(item, index, array){
        		jQuery('#city-select').append('<option data-lat="'+item.centerLat+'" data-lon="'+item.centerLon+'" value="' + item.areaCode + '">' + item.cityNameFa + '</option>')
        	});
        	jQuery('#city-select').val('021');
        	jQuery('#city-select').select2("val", "021");
        	jQuery("#city-select").trigger('change');
        	jQuery('#city-select').prop("disabled", false);
        	
        },
        error: function(result){
        	console.log(result);
        	debugger;
        }
    });

	jQuery("#city-select").on('change', function() {
		var center = {
			lat: parseFloat(jQuery('option:selected', this).attr('data-lat')),
			lng: parseFloat(jQuery('option:selected', this).attr('data-lon'))
		}
		globalMaps.headerMap.map.setCenter(center);
		globalMaps.headerMap.map.setZoom(12);
		var userLocImageStatic = {
		    url: '/wp-content/themes/businessfinder2/design/img/user-location.png',
		    // This marker is 20 pixels wide by 32 pixels high.
		    size: new google.maps.Size(40, 73),
		    // The origin for this image is (0, 0).
		    origin: new google.maps.Point(0, 0),
		    anchor: new google.maps.Point(20, 73)
		  };
		var userLocImageMoving = {
		    url: '/wp-content/themes/businessfinder2/design/img/user-location-moving.png',
		    // This marker is 20 pixels wide by 32 pixels high.
		    size: new google.maps.Size(40, 73),
		    // The origin for this image is (0, 0).
		    origin: new google.maps.Point(0, 0),
		    anchor: new google.maps.Point(20, 73)
		  };
		if (globalMaps.headerMap.userPositionMarker) {
			globalMaps.headerMap.userPositionMarker.setPosition(center);
		} else {
			var userPositionMarker = new google.maps.Marker({
				icon: userLocImageStatic,
				position: center,
				map: globalMaps.headerMap.map,
				title: 'Drag me!',
				draggable: true
	        });
        	globalMaps.headerMap.userPositionMarker = userPositionMarker;
			
		}
        jQuery('#user-latitude').html(center.lat);
    	jQuery('#user-longitude').html(center.lng);
        userPositionMarker.addListener('position_changed', function() {
        	var pos = userPositionMarker.getPosition();
        	jQuery('#user-latitude').html(pos.lat());
        	jQuery('#user-longitude').html(pos.lng());
        });
        userPositionMarker.addListener('dragstart', function() {
        	userPositionMarker.setIcon(userLocImageMoving);
        });
        userPositionMarker.addListener('dragend', function() {
        	userPositionMarker.setIcon(userLocImageStatic);
        });

		var aCode = this.value;
    	jQuery('#category-select').empty();
    	jQuery('#category-select').append('<option value="">&nbsp;</option>');
    	jQuery('#brand-select').empty();
    	jQuery('#brand-select').append('<option value="">&nbsp;</option>');
    	jQuery("#brand-select").val('').trigger('change');
    	jQuery("#category-select").val('').trigger('change');
		jQuery.ajax({
	        url: 'https://buyoriginal.herokuapp.com/services/v1/dev/categories/areacode/'+aCode,
	        type: 'GET',
	        beforeSend: function (request)
	        {
	        	request.setRequestHeader("Content-Type", "application/json");
	        	request.setRequestHeader("token", "emFuYmlsZGFyYW5naGVybWV6DQo=");
	        },
	        success: function(result) {
	        	result.forEach(function(item, index, array){
	        		jQuery('#category-select').append('<option value="' + item.bCategoryId + '">' + item.bCategory + '</option>')
	        	});
	        	jQuery('#category-select').prop("disabled", false);
	        	
	        },
	        error: function(result){
	        	console.log(result);
	        	debugger;
	        }
	    });
	});
	jQuery("#category-select").on('change', function() {
		var catId = this.value;
		if (catId != '') {
			var aCode = jQuery('#city-select').val();
	    	jQuery('#brand-select').empty();
	    	jQuery('#brand-select').append('<option value="">&nbsp;</option>');
			jQuery("#brand-select").val('').trigger('change');
			jQuery.ajax({
		        url: 'https://buyoriginal.herokuapp.com/services/v1/dev/brands/areacode/'+aCode+'/category/'+catId,
		        type: 'GET',
		        beforeSend: function (request)
		        {
		        	request.setRequestHeader("Content-Type", "application/json");
		        	request.setRequestHeader("token", "emFuYmlsZGFyYW5naGVybWV6DQo=");
		        },
		        success: function(result) {
		        	result.forEach(function(item, index, array){
		        		jQuery('#brand-select').append('<option value="' + item.bId + '">' + item.bName + '</option>')
		        	});
		        	jQuery('#brand-select').prop("disabled", false);
		        },
		        error: function(result){
		        	console.log(result);
		        	debugger;
		        }
		    });
		}
	});


	{if $options->theme->general->progressivePageLoading}
		if(!isResponsive(1024)){
			jQuery("#{!$htmlId}-main").waypoint(function(){
				jQuery("#{!$htmlId}-main").addClass('load-finished');
			}, { triggerOnce: true, offset: "95%" });
		} else {
			jQuery("#{!$htmlId}-main").addClass('load-finished');
		}
	{else}
		jQuery("#{!$htmlId}-main").addClass('load-finished');
	{/if}


	var select2Settings = {
		dropdownAutoWidth : true
	};


	jQuery('#{!$htmlId}').find('select').select2(select2Settings).on("select2-loaded", function() {
		// fired to the original element when the dropdown closes
		jQuery('#{!$htmlId}').find('.select2-container').removeAttr('style');
	});

	jQuery('#{!$htmlId}').find('select').select2(select2Settings).on("select2-open", function() {
		var selectPosition = jQuery('#{!$htmlId}').find('.select2-dropdown-open').parent().attr('data-position');
		jQuery('.select2-drop').addClass('select-position-'+selectPosition);
	});

	if(isMobile()){
		jQuery('#{!$htmlId} .category-search-wrap').find('select').select2(select2Settings).on("select2-selecting", function(val, choice) {
			if(val != ""){
				jQuery('#{!$htmlId}').find('.category-clear').addClass('clear-visible');
			}
		});
		jQuery('#{!$htmlId} .location-search-wrap').find('select').select2(select2Settings).on("select2-selecting", function(val, choice) {
			if(val != ""){
				jQuery('#{!$htmlId}').find('.location-clear').addClass('clear-visible');
			}
		});

		jQuery('#{!$htmlId} .category-search-wrap').find('select').select2(select2Settings).on("select2-selecting", function(val, choice) {
			if(val != ""){
				// add class
				jQuery('#{!$htmlId} .category-search-wrap').addClass('option-selected');
			}
		});
		jQuery('#{!$htmlId} .location-search-wrap').find('select').select2(select2Settings).on("select2-selecting", function(val, choice) {
			if(val != ""){
				jQuery('#{!$htmlId} .location-search-wrap').addClass('option-selected');
			}
		});
	} else {
		jQuery('#{!$htmlId} .category-search-wrap').find('select').select2(select2Settings).on("select2-selecting", function(val, choice) {
			if(val != ""){
				// add class
				jQuery('#{!$htmlId} .category-search-wrap').addClass('option-selected');
			}
		});
		jQuery('#{!$htmlId} .location-search-wrap').find('select').select2(select2Settings).on("select2-selecting", function(val, choice) {
			if(val != ""){
				jQuery('#{!$htmlId} .location-search-wrap').addClass('option-selected');
			}
		});

		jQuery('#{!$htmlId}').find('.category-search-wrap').hover(function(){
			if(jQuery(this).find('select').select2("val") != ""){
				jQuery(this).find('.category-clear').addClass('clear-visible');
			}
		},function(){
			if(jQuery(this).find('select').select2("val") != ""){
				jQuery(this).find('.category-clear').removeClass('clear-visible');
			}
		});

		jQuery('#{!$htmlId}').find('.location-search-wrap').hover(function(){
			if(jQuery(this).find('select').select2("val") != ""){
				jQuery(this).find('.location-clear').addClass('clear-visible');
			}
		},function(){
			if(jQuery(this).find('select').select2("val") != ""){
				jQuery(this).find('.location-clear').removeClass('clear-visible');
			}
		});
	}

	jQuery('#{!$htmlId}').find('.select2-chosen').each(function(){
		jQuery(this).html(jQuery(this).html().replace(new RegExp("&nbsp;", "g"), ''));
	});


	if(isMobile()){
		jQuery('#{!$htmlId}').find('.radius').on('click', function(){
			jQuery(this).find('.radius-clear').addClass('clear-visible');
		});
	} else {
		jQuery('#{!$htmlId}').find('.radius').hover(function(){
			jQuery(this).find('.radius-clear').addClass('clear-visible');
		},function(){
			jQuery(this).find('.radius-clear').removeClass('clear-visible');
		});
	}

	jQuery('#{!$htmlId}').find('.category-clear').click(function(){
		jQuery('#{!$htmlId}').find('.category-search-wrap select').select2("val", "");
		jQuery(this).removeClass('clear-visible');
		// remove class selected
		jQuery('#{!$htmlId} .category-search-wrap').removeClass('option-selected');
	});
	jQuery('#{!$htmlId}').find('.location-clear').click(function(){
		jQuery('#{!$htmlId}').find('.location-search-wrap select').select2("val", "");
		jQuery(this).removeClass('clear-visible');
		// remove class selected
		jQuery('#{!$htmlId} .location-search-wrap').removeClass('option-selected');
	});


	/* RADIUS SCRIPT */


	{if $type == 4}
		var $radiusContainer = jQuery('#{!$htmlId} .radius');
		initRadius($radiusContainer);


	{else}
	var $headerMap = jQuery("#{!$elements->unsortable[header-map]->getHtmlId()}-container");

	var $radiusContainer = jQuery('#{!$htmlId} .radius');
	var $radiusToggle = $radiusContainer.find('.radius-toggle');
	var $radiusDisplay = $radiusContainer.find('.radius-display');
	var $radiusPopup = $radiusContainer.find('.radius-popup-container');

	$radiusToggle.click(function(){
		jQuery(this).removeClass('radius-input-visible').addClass('radius-input-hidden');
		$radiusContainer.find('input').each(function(){
			jQuery(this).removeAttr('disabled');
		});
		$radiusDisplay.removeClass('radius-input-hidden').addClass('radius-input-visible');
		$radiusDisplay.trigger('click');

		$radiusDisplay.find('.radius-value').html($radiusPopup.find('input').val());
		$radiusPopup.find('.radius-value').html($radiusPopup.find('input').val());
	});

	$radiusDisplay.click(function(){
		$radiusPopup.removeClass('radius-input-hidden').addClass('radius-input-visible');

		if($headerMap.length != 0){
			$headerMap.gmap3({
				getgeoloc: {
					callback: function(latLng){
						if(latLng){
							jQuery("#latitude-search").attr('value', latLng.lat());
							jQuery("#longitude-search").attr('value', latLng.lng());
							jQuery(".elm-header-map ").removeClass('deactivated');
						}
					}
				}
			});
		} else {
			navigator.geolocation.getCurrentPosition(function(position){
				jQuery("#latitude-search").attr('value', position.coords.latitude);
				jQuery("#longitude-search").attr('value', position.coords.longitude);
				jQuery(".elm-header-map ").removeClass('deactivated');
			}, function(){
				// error callback
			});
		}
	});
	$radiusDisplay.find('.radius-clear').click(function(e){
		e.stopPropagation();
		$radiusDisplay.removeClass('radius-input-visible').addClass('radius-input-hidden');
		$radiusContainer.find('input').each(function(){
			jQuery(this).attr('disabled', true);
		});
		$radiusPopup.find('.radius-popup-close').trigger('click');
		$radiusToggle.removeClass('radius-input-hidden').addClass('radius-input-visible');
		$radiusContainer.removeClass('radius-set');
	});
	$radiusPopup.find('.radius-popup-close').click(function(e){
		e.stopPropagation();
		$radiusPopup.removeClass('radius-input-visible').addClass('radius-input-hidden');
	});
	$radiusPopup.find('input').change(function(){
		$radiusDisplay.find('.radius-value').html(jQuery(this).val());
		$radiusPopup.find('.radius-value').html(jQuery(this).val());
	});

	{if $selectedRad}
	$radiusToggle.trigger('click');
	{/if}
	{/if}

	/* RADIUS SCRIPT */

	{if $type == 2}
	/* KEYWORD INPUT HACK */
	var $keywordContaier = jQuery('#{!$htmlId} #searchinput-text');
	var $keywordWidthHack = jQuery('#{!$htmlId} .search-input-width-hack');

	if($keywordContaier.val() != ""){
		$keywordWidthHack.html($keywordContaier.val());
	} else {
		$keywordWidthHack.html($keywordWidthHack.attr('data-defaulttext'));
	}
	$keywordContaier.width($keywordWidthHack.width());

	$keywordContaier.on('keyup', function(){
		if(jQuery(this).val() != ""){
			$keywordWidthHack.html(jQuery(this).val());
		} else {
			$keywordWidthHack.html($keywordWidthHack.attr('data-defaulttext'));
		}

		if($keywordWidthHack.width() <= 150){
			if(jQuery(this).val() != ""){
				$keywordContaier.width($keywordWidthHack.outerWidth(true));
			} else {
				$keywordContaier.width($keywordWidthHack.width());
			}
		}
	});
	/* KEYWORD INPUT HACK */
	{/if}

	{if $type == 3}
	jQuery('#{!$htmlId} .category-search-wrap .category-icon').on('click', function(){
		jQuery(this).parent().find('select').select2('open');
	});
	jQuery('#{!$htmlId} .location-search-wrap .location-icon').on('click', function(){
		jQuery(this).parent().find('select').select2('open');
	});
	{/if}

	jQuery('.searchsubmit2').click(function(){
		var userLat = jQuery('#user-latitude').html();
		var userLng = jQuery('#user-longitude').html();
		var city = jQuery('#city-select').val();
		var category = jQuery('#category-select').val();
		if (category == '') {
			category = 'all';
		}
		var brand = jQuery('#brand-select').val();
		if (brand == '') {
			brand = 'all';
		}
		var onlyVerified = jQuery('#verification-checkbox').is(':checked');
		var onlyDiscount = jQuery('#sales-checkbox').is(':checked');
		var distance = jQuery('.radius-value').html();

		jQuery.ajax({
            url: 'http://buyoriginal.herokuapp.com/services/v1/dev/stores/search/' + city + '/' + category + '/' + brand + '/' + onlyDiscount + '/' + onlyVerified + '/' + distance + '/' + userLat + '/' + userLng,
            type: 'GET',
            beforeSend: function (request)
            {
            	request.setRequestHeader("Content-Type", "application/json");
            	request.setRequestHeader("token", "emFuYmlsZGFyYW5naGVybWV6DQo=");
            },
            success: function(result) {
            	// Remove previous searched markers
            	if (typeof globalMaps.headerMap.ourMarkers !== 'undefined') {
					for (var i = 0; i < globalMaps.headerMap.ourMarkers.length; i++ ) {
						globalMaps.headerMap.ourMarkers[i].setMap(null);
					}
					globalMaps.headerMap.ourMarkers.length = 0;
					// for (var i = 0; i < globalMaps.headerMap.ourInfoWindows.length; i++ ) {
					// 	globalMaps.headerMap.ourInfoWindows[i].setMap(null);
					// }
					// globalMaps.headerMap.ourInfoWindows.length = 0;
				}
				globalMaps.headerMap.ourMarkers = [];
				globalMaps.headerMap.ourInfoWindows = [];
            	//var infowindow = new google.maps.InfoWindow;
            	var itemHtml = '';
            	var imageName = '';
            	jQuery('.elements-area .stores-list').empty();
            	var processedItems = 0;
            	var bounds = new google.maps.LatLngBounds();
                result.forEach(function(item, index, array){
                	var myLatLng = {};
                	myLatLng.lat = parseFloat(item.sLat);
                	myLatLng.lng = parseFloat(item.sLong);
      //           	var marker = new google.maps.Marker({
						// position: myLatLng,
						// map: globalMaps.headerMap.map,
						// title: item.sName
			   //      });
			        imageName = item.bName.toLowerCase().replace(/ /g, '');

			        var pictureLabel = document.createElement("img");
					pictureLabel.src = '/wp-content/themes/businessfinder2/design/img/logos/'+imageName+'.png';
					var image = {
					    url: '/wp-content/themes/businessfinder2/design/img/base-marker.png',
					    // This marker is 20 pixels wide by 32 pixels high.
					    size: new google.maps.Size(80, 104),
					    // The origin for this image is (0, 0).
					    origin: new google.maps.Point(0, 0),
					    // The anchor for this image is the base of the flagpole at (0, 32).
					    anchor: new google.maps.Point(0, 104)
					  };
					  var labelStyle = {
					  	width: '69px', 
					  	height: '69px',
					  	borderRadius: '50%',
					  	zIndex: 200
					  };
					var marker = new MarkerWithLabel({
						position: myLatLng,
						icon: image,
						map: globalMaps.headerMap.map,
						draggable: false,
						raiseOnDrag: false,
						labelContent: pictureLabel,
						labelAnchor: new google.maps.Point(-5, 98),
						labelClass: "marker-labels",
						labelStyle: labelStyle
					});

			        bounds.extend(marker.getPosition());
			        globalMaps.headerMap.map.fitBounds(bounds);
			        marker.addListener('click', function() {
			        	var obj = {
			        		offset: -110,
			        	};
						jQuery('html').scrollTo(jQuery('.stores-list'), 800, obj);
			        	jQuery('.stores-list').scrollTo(jQuery('#elem-'+item._id), 800);
			        });
			        globalMaps.headerMap.ourMarkers.push(marker);
			        //globalMaps.headerMap.ourInfoWindows.push(infowindow);
			  //       jQuery.ajax({
					//             url: 'https://buyoriginal.herokuapp.com/services/v1/dev/brands/verification/'+item.bId,
					//             type: 'GET',
					//             beforeSend: function (request)
					//             {
					//             	request.setRequestHeader("Content-Type", "application/json");
					//             	request.setRequestHeader("token", "emFuYmlsZGFyYW5naGVybWV6DQo=");
					//             },
					// success: function(result){
					// console.log(result)

					// }
					// });
					itemHtml = '<li class="store-elem" id="elem-'+item._id+'">\
									<div class="store-item">\
										<div class="store-image">\
											<img src="/wp-content/themes/businessfinder2/design/img/logos/'+imageName+'.png" />\
										</div>\
										<div class="store-info">\
											<h4>'+item.sName+'</h4>\
											<p>\
												<span>آدرس: </span><span>'+item.sAddress+'</span>\
											</p>\
											<p>\
												<span>تلفن: </span><span>'+item.sTel1+(item.hasOwnProperty('sTel2') && item.sTel2 != '' ? ' - '+item.sTel2 : '')+'</span>\
											</p>\
											<p>'+(item.hasOwnProperty('sHours') && item.sHours != '' ? '<span>ساعت کار: </span><span>'+item.sHours+'</span>' : '')+
											'</p><p class="store-icons">' +  
											(item.sVerified == 'YES' ? '<i class="fa fa-check "></i>' : '') + (item.hasOwnProperty('dPrecentage') ? '<i class="fa fa-percent"></i>' : '') +
										'</p></div>\
										<div class="store-contact">\
											<a>نکات اصل و تقلبی</a>\
											<p>'+(item.hasOwnProperty('dNote') ? item.dNote : '')+'</p>\
										</div>\
									</div>\
									<div class="clearboth"></div>\
								</li>';
			        jQuery('.elements-area .stores-list').append(itemHtml);
			        processedItems++;

                });
            },
            error: function(result){
            	console.log(result);
            	debugger;
            }
        });
	});
});



function updateRadiusText(context) {
	var value = context.value;
	jQuery(context).closest('.radius').find('.radius-value').text(value);
}

function toggleRadius(context) {
	var $container = jQuery(context).parent('.radius');
	if ($container.hasClass('radius-set')) {
		// disable radius and geolocation
		$container.find('input').each(function(){
			jQuery(this).attr('disabled', true);
		});
		$container.removeClass('radius-set');
	} else {
		// enable radius and geolocation
		$container.find('input').each(function(){
			jQuery(this).attr('disabled', false);
		});
		$container.addClass('radius-set');
		setGeoData();
	}
}

function initRadius($container) {
	if ($container.hasClass('radius-set')) {
		$container.find('input').each(function(){
			jQuery(this).attr('disabled', false);
		});
		setGeoData();
	} else {
		$container.find('input').each(function(){
			jQuery(this).attr('disabled', true);
		});
	}
}

function setGeoData() {
	if(navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
			var pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
			jQuery("#latitude-search").attr('value', pos.lat());
			jQuery("#longitude-search").attr('value', pos.lng());
		});
	}

}

</script>
