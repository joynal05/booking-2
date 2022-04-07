(function($){

	$(window).load(function () {
		transfers_contact.load();
	});

	$(document).ready(function () {
		transfers_contact.init();
	});

	var transfers_contact = {

		load : function () {

			var latLong = new google.maps.LatLng(window.contactAddressLatitude, window.contactAddressLongitude);

			var myMapOptions = {
				zoom: 15,
				center: latLong,
				scrollwheel: false,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};

			var theMap = new google.maps.Map(document.getElementById("map_canvas"), myMapOptions);

			var marker = new google.maps.Marker({
				map: theMap,
				position: new google.maps.LatLng(window.contactAddressLatitude, window.contactAddressLongitude),
				visible:false
			});

			var boxText = document.createElement("div");
			boxText.innerHTML = '<span>' + window.contactCompanyName + '</span><br>' + window.contactAddress;

			var myOptions = {
				content: boxText,
				disableAutoPan: false,
				maxWidth: 0,
        // pixelOffset: new google.maps.Size(-140, -120),
        pixelOffset: new google.maps.Size(window.contactAddressLatitude - 100,  window.contactAddressLongitude - 7),
				zIndex: null,
				closeBoxURL: "",
				infoBoxClearance: new google.maps.Size(1, 1),
				isHidden: false,
				pane: "floatPane",
				enableEventPropagation: false
			};

			google.maps.event.addListener(marker, "click", function (e) {
				ib.open(theMap, this);
			});

			var ib = new InfoBox(myOptions);
			ib.open(theMap, marker);
		},
		init : function() {

			$('#form-contact').validate({
				onkeyup: false,
				ignore: [],
				rules: {
					contact_name: "required",
					contact_email: {
						required: true,
						email: true
					},
					contact_message: "required",
				},
				invalidHandler: function(e, validator) {
					var errors = validator.numberOfInvalids();
					if (errors) {

					} else {

					}
				},
				messages: {
					contact_name: '',
					contact_email: '',
					contact_message: '',
					c_val_s: '',
				},
				submitHandler: function() {
					transfers_contact.processSubmission();
				}
			});
		},
		processSubmission : function () {

			$('#submit-contact')
				.after('<span class="loader">...</span>')
				.attr('disabled','disabled');

			var contact_name = $('#contact_name').val();
			var contact_email = $('#contact_email').val();
			var contact_message = $('#contact_message').val();

			var c_val_s = $('#c_val_s_con').val();
			var c_val_1 = $('#c_val_1_con').val();
			var c_val_2 = $('#c_val_2_con').val();

			var $root = $('html, body');

			$.ajax({
				url: TransfersAjax.ajaxurl,
				data: {
					'action':'contact_form_ajax_request',
					'contact_name' : contact_name,
					'contact_email' : contact_email,
					'contact_message' : contact_message,
					'c_val_s' : c_val_s,
					'c_val_1' : c_val_1,
					'c_val_2' : c_val_2,
					'nonce' : TransfersAjax.nonce
				},
				success:function(data) {

					$('#form-contact span.loader').fadeOut('slow', function(){
						$(this).remove();
					});
					$('#submit-contact').removeAttr('disabled');
					// This outputs the result of the ajax request
					if (data == 'captcha_error') {
						$("input#c_val_s_con").addClass('error');
						$("div.error").show();
					} else {

						$("div.error").hide();
						$("#form-contact").hide();
						$("div.thankyou").fadeIn();

						$root.animate({
							scrollTop: $('#thankyou').offset().top - 100
						}, 500, function () {});
					}
				},
				error: function(errorThrown){
					console.log(errorThrown);
				}
			});
		}
	};

})(jQuery);
