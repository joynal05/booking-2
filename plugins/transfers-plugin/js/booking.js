(function($){

	"use strict";
	
	$(document).ready(function () {
		transfers_booking.init();
	});
	
	String.prototype.format = function() {
		var formatted = this;
		for (var i = 0; i < arguments.length; i++) {
			var regexp = new RegExp('\\{'+i+'\\}', 'gi');
			formatted = formatted.replace(regexp, arguments[i]);
		}
		return formatted;
	};	
	
	var transfers_booking = {
	
		init: function () {
		
			if (!window.enableExtraItems) {
				transfers_booking.loadStep2();
			} else {
				transfers_booking.bindRequiredExtraItems();
			}
		
			$('select.dep_extra_item_quantity').on('change', function() {
			
				var quantity = parseInt($(this).val());
				var extraItemId = $(this).attr('id').replace('dep_extra_item_quantity_', '');
				
				transfers_booking.updateDepartureExtraItemSelection(extraItemId, quantity);
			});
			
			$('select.ret_extra_item_quantity').on('change', function() {
				
				var quantity = $(this).val();
				var extraItemId = $(this).attr('id').replace('ret_extra_item_quantity_', '');

				transfers_booking.updateReturnExtraItemSelection(extraItemId, quantity);
			});
			
			$('.step1-next').on('click', function(e) {
			
				transfers_booking.loadStep2();
			
				e.preventDefault();
			});		
		},
		updateDepartureExtraItemSelection: function(extraItemId, quantity) {
		
			var extraItemPrice = parseFloat($('#dep_extra_item_price_' + extraItemId).val());
			var extraItemTitle = $('#dep_extra_item_title_' + extraItemId).html();
			
			var extraItem = {};

			// reduce total by old item summed price.
			if (extraItemId in window.bookingRequest.departureExtras) {
				var oldItem = window.bookingRequest.departureExtras[extraItemId];
				window.bookingRequest.totalPrice -= parseFloat(oldItem.summedPrice);	
				delete window.bookingRequest.departureExtras[extraItemId];
			}

			if (quantity > 0) {
			
				// increase total by new item summer price
				extraItem.quantity = quantity;
				extraItem.id = extraItemId;
				extraItem.price = extraItemPrice;
				extraItem.summedPrice = quantity * extraItemPrice;
				extraItem.title = extraItemTitle;
				
				window.bookingRequest.totalPrice += extraItem.summedPrice;
				window.bookingRequest.departureExtras[extraItemId] = extraItem;
			}
			
			transfers_booking.setSidebarExtras();
			
			$('.total_price_sum').html(window.transfers.numberFormatI18N(window.bookingRequest.totalPrice));
		
		},
		updateReturnExtraItemSelection: function(extraItemId, quantity) {
			var extraItemPrice = $('#ret_extra_item_price_' + extraItemId).val();
			var extraItemTitle = $('#ret_extra_item_title_' + extraItemId).html();
			
			var extraItem = {};

			// reduce total by old item summed price.
			if (extraItemId in window.bookingRequest.returnExtras) {
				var oldItem = window.bookingRequest.returnExtras[extraItemId];
				window.bookingRequest.totalPrice -= oldItem.summedPrice;	
				delete window.bookingRequest.returnExtras[extraItemId];
			}

			if (quantity > 0) {
			
				// increase total by new item summer price
				extraItem.quantity = quantity;
				extraItem.id = extraItemId;
				extraItem.price = extraItemPrice;
				extraItem.summedPrice = quantity * extraItemPrice;
				extraItem.title = extraItemTitle;
				
				window.bookingRequest.totalPrice += extraItem.summedPrice;
				window.bookingRequest.returnExtras[extraItemId] = extraItem;
			}
			
			transfers_booking.setSidebarExtras();
			
			$('.total_price_sum').html(window.transfers.numberFormatI18N(window.bookingRequest.totalPrice));		
		},
		bindRequiredExtraItems: function() {
			if (typeof(window.requiredDepartureExtraItems) !== 'undefined' && window.requiredDepartureExtraItems.length > 0) {
				$.each( window.requiredDepartureExtraItems, function( index, extraItemId ){
					transfers_booking.updateDepartureExtraItemSelection(extraItemId, 1);
					$('#dep_extra_item_quantity_' + extraItemId).val('1');
				});
			}	
			if (typeof(window.requiredReturnExtraItems) !== 'undefined' && window.requiredReturnExtraItems.length > 0) {
				$.each( window.requiredReturnExtraItems, function( index, extraItemId ){
					transfers_booking.updateReturnExtraItemSelection(extraItemId, 1);
					$('#ret_extra_item_quantity_' + extraItemId).val('1');
				});
			}	
		},		
		loadStep2: function() {

			if (window.useWooCommerceForCheckout && window.wooCartPageUri.length > 0) {

				// add Product and go to WooCommerce cart
				transfers_booking.addWooProductToCart();
				
			} else {
			
				$('.step1').hide();
				$('.step2').show();
				$('.captcha_error').hide();
								
				$('#form-booking').validate({
					onkeyup: false,
					ignore: [],
					invalidHandler: function(e, validator) {
						var errors = validator.numberOfInvalids();
						if (errors) {
							var message = errors == 1 ? window.formSingleError : window.formMultipleError.format(errors);
							$("div.error div p").html(message);
							$("div.error").show();
						} else {
							$("div.error").hide();
						}
					},
					submitHandler: function() { 
						transfers_booking.processBooking(); 
					}					
				});				

				$.each(window.bookingFormFields, function(index, field) {
				
					if (field.hide !== '1' && field.id !== null && field.id.length > 0) {
						var $input = null;
						if (field.type == 'text' || field.type == 'email') {
							$input = $('#form-booking').find('input[name=' + field.id + ']');
						} else if (field.type == 'textarea') {
							$input = $('#form-booking').find('textarea[name=' + field.id + ']');
						}
						
						if ($input !== null && typeof($input) !== 'undefined') {
							if (field.required == '1') {
								$input.rules('add', {
									required: true,
									messages: {
										required: window.bookingFormRequiredError
									}
								});
							}
							if (field.type == 'email') {
								$input.rules('add', {
									email: true,
									messages: {
										required: window.bookingFormEmailError
									}
								});
							}
						}
					}
				});				
			}
		},
		addWooProductToCart : function () {
		
			var d = {};
			
			d.action = 'booking_add_to_cart_request';
			d.nonce = TransfersAjax.nonce;	
			d.peopleCount = window.bookingRequest.people;
			
			d.departureAvailabilityId = window.bookingRequest.departureAvailabilityId;
			d.departureFromId = window.bookingRequest.departureFromId;
			d.departureToId = window.bookingRequest.departureToId;
			d.departureDate = window.bookingRequest.departureDateAlt;
			d.departureIsPrivate = window.bookingRequest.departureIsPrivate;
			d.departureExtraItems = window.bookingRequest.departureExtras;
			d.departureSlotMinutesNumber = window.bookingRequest.departureSlotMinutesNumber;
			
			if (window.bookingRequest.returnAvailabilityId > 0) {
				d.returnAvailabilityId = window.bookingRequest.returnAvailabilityId;
				d.returnFromId = window.bookingRequest.returnFromId;
				d.returnToId = window.bookingRequest.returnToId;
				d.returnDate = window.bookingRequest.returnDateAlt;
				d.returnIsPrivate = window.bookingRequest.returnIsPrivate;
				d.returnExtraItems = window.bookingRequest.returnExtras;
				d.returnSlotMinutesNumber = window.bookingRequest.returnSlotMinutesNumber;
			}	
			
			$.ajax({
				url: TransfersAjax.ajaxurl,
				data: d,
				success:function(data) {
					top.location.href = window.wooCartPageUri;				
				},
				error: function(xhr, errorThrown, errorText){
					console.log(errorThrown);
				}
			});			

		},
		processBooking : function() {
			
			var d = {};
			
			d.peopleCount = window.bookingRequest.people;

			d.departureAvailabilityId = window.bookingRequest.departureAvailabilityId;
			d.departureFromId = window.bookingRequest.departureFromId;
			d.departureToId = window.bookingRequest.departureToId;
			d.departureDate = window.bookingRequest.departureDateAlt;
			d.departureIsPrivate = window.bookingRequest.departureIsPrivate;
			d.departureExtraItems = window.bookingRequest.departureExtras;
			d.departureSlotMinutesNumber = window.bookingRequest.departureSlotMinutesNumber;
			
			if (window.bookingRequest.returnAvailabilityId > 0) {
				d.returnAvailabilityId = window.bookingRequest.returnAvailabilityId;
				d.returnFromId = window.bookingRequest.returnFromId;
				d.returnToId = window.bookingRequest.returnToId;
				d.returnDate = window.bookingRequest.returnDateAlt;
				d.returnIsPrivate = window.bookingRequest.returnIsPrivate;
				d.returnExtraItems = window.bookingRequest.returnExtras;
				d.returnSlotMinutesNumber = window.bookingRequest.returnSlotMinutesNumber;
			}			
						
			if (window.addCaptchaToForms) {
				d.cValS = $('#c_val_s_book').val();
				d.cVal1 = $('#c_val_1_book').val();
				d.cVal2 = $('#c_val_2_book').val();
			}
			
			$.each(window.bookingFormFields, function(index, field) {
				if (field.hide !== '1') {
					d[field.id] = $('#' + field.id).val();
					$('.confirmation_' + field.id).html($('#' + field.id).val());
				}
			});			
			
			d.action = 'book_transfer_ajax_request';
			d.nonce = TransfersAjax.nonce;
			
			$.ajax({
				url: TransfersAjax.ajaxurl,
				data: d,
				success:function(data) {
				
					var proceed = true;					
					if (window.addCaptchaToForms) {
						if (data == 'captcha_error') {
							$('.captcha_error').show();
							proceed = false;
						} else {
							$('.captcha_error').hide();
						}
					}
					
					if (proceed) {
					
						$('.confirmation_full_name').html(d.firstName + ' ' + d.lastName);
						$('.confirmation_telephone').html(d.phone);
						$('.confirmation_email').html(d.email);
						$('.confirmation_address').html(d.address);
						$('.confirmation_city').html(d.town);
						$('.confirmation_post_code').html(d.zip);
						$('.confirmation_state').html(d.state);
						$('.confirmation_country').html(d.country);
						$('.confirmation_departure_date').html(window.bookingRequest.departureDate);
						$('.confirmation_departure_from').html(window.bookingRequest.departureFrom);
						$('.confirmation_departure_to').html(window.bookingRequest.departureTo);
						$('.confirmation_departure_transport_type').html(window.bookingRequest.departureTransportType);
						if (d.departureIsPrivate)
							$('.confirmation_departure_is_private').html(window.yesLabel);
						else
							$('.confirmation_departure_is_private').html(window.noLabel);
						
						var departureExtraItemsStr = '';
						$.each( window.bookingRequest.departureExtras, function( index, value ){
							departureExtraItemsStr += value.quantity + ' x ' + value.title + ', ';
						});
						departureExtraItemsStr = departureExtraItemsStr.trim();
						departureExtraItemsStr = transfers_booking.trimTrailingComma(departureExtraItemsStr);
						$('.confirmation_departure_extras').html(departureExtraItemsStr);
						
						if (window.bookingRequest.returnAvailabilityId > 0) {
							$('.confirmation_return_date').html(window.bookingRequest.returnDate);
							$('.confirmation_return_from').html(window.bookingRequest.returnFrom);
							$('.confirmation_return_to').html(window.bookingRequest.returnTo);
							$('.confirmation_return_transport_type').html(window.bookingRequest.returnTransportType);
							if (d.returnIsPrivate)
								$('.confirmation_return_is_private').html(window.yesLabel);
							else
								$('.confirmation_return_is_private').html(window.noLabel);
							
							var returnExtraItemsStr = '';
							$.each( window.bookingRequest.returnExtras, function( index, value ){
								returnExtraItemsStr += value.quantity + ' x ' + value.title + ', ';
							});
							returnExtraItemsStr = returnExtraItemsStr.trim();
							returnExtraItemsStr = transfers_booking.trimTrailingComma(returnExtraItemsStr);
							$('.confirmation_return_extras').html(returnExtraItemsStr);
						} else {
							$('.confirmation_return_details').hide();
						}
						
						$('.step1').hide();
						$('.step2').hide();
						$('.step3').show();
					}
				},
				error: function(errorThrown){
					console.log(errorThrown);
				}
			});
		},
		trimTrailingComma : function(str) {
			if(str.substr(-1) === ',') {
				return str.substr(0, str.length - 1);
			}
			return str;
		},
		setSidebarExtras : function() {

			var departureExtraItemsStr = '';
			$.each( window.bookingRequest.departureExtras, function( index, value ){
				departureExtraItemsStr += value.quantity + ' x ' + value.title + ', ';
			});
			departureExtraItemsStr = departureExtraItemsStr.trim();
			departureExtraItemsStr = transfers_booking.trimTrailingComma(departureExtraItemsStr);
			$('.departure_extras').html(departureExtraItemsStr);
			
			if (window.bookingRequest.returnAvailabilityId > 0) {
				var returnExtraItemsStr = '';
				$.each( window.bookingRequest.returnExtras, function( index, value ){
					returnExtraItemsStr += value.quantity + ' x ' + value.title + ', ';
				});
				returnExtraItemsStr = returnExtraItemsStr.trim();
				returnExtraItemsStr = transfers_booking.trimTrailingComma(returnExtraItemsStr);
				$('.return_extras').html(returnExtraItemsStr);
			}
		}
	};

})(jQuery);