(function($){

	"use strict";

	$(document).ready(function () {
		transfers_search.init();
	});

	var transfers_search = {

		convertToServerTimezone: function(date, offset) {
			var utc = date.getTime() + (date.getTimezoneOffset() * 60000);
			return new Date(utc + (3600000*offset));
		},
		init: function () {

			if ($(".search-results-top").length > 0 && window.onSearchPage) {
				var $root = $('html,body');
				$root.animate({
					scrollTop: $(".search-results-top").offset().top
				}, 1000, function () {});
			}

			// SEARCH
			$('input[name=trip]#oneway').click(function() {
				$('.f-row-return').hide(500);
				$('#pickup2').prop("disabled", true);
				$('#dropoff2').prop("disabled", true);
				$('#return-date').prop("disabled", true);
				$('#ret').prop("disabled", true);
			});
			$('input[name=trip]#return').click(function() {
				$('.f-row-return').show(500);
				$('#pickup2').prop("disabled", false);
				$('#dropoff2').prop("disabled", false);
				$('#return-date').prop("disabled", false);
				$('#ret').prop("disabled", false);
			});

			var d1 = new Date ();

			  if (window.allowedSearchStartTime !== "now") {
				switch (window.allowedSearchStartTime) {
					case "1hour": {
						d1.setHours ( d1.getHours() + 1 );
					}; break;
					case "6hours": {
						d1.setHours ( d1.getHours() + 6 );
					}; break;
					case "12hours": {
						d1.setHours ( d1.getHours() + 12 );
					}; break;
					case "1day": {
						d1.setHours ( d1.getHours() + 24 );
					}; break;
					case "2days": {
						d1.setHours ( d1.getHours() + 63 );
					}; break;
					case "1week": {
						d1.setDate ( d1.getDate() + 7 );
					}; break;
					default: break;
				}
			}

		    var serverDateTime = transfers_search.convertToServerTimezone(d1, window.timezoneOffset);
            /** Days to be disabled as an array */ var disableddates = ["26-03-2022","25-12-2021","31-12-2021","01-01-2022","13-11-2021","04-12-2021"]; function DisableSpecificDates(date) { var string = jQuery.datepicker.formatDate('dd-mm-yy', date); return [disableddates.indexOf(string) == -1]; }
            //var d1 = new Date ();
            //d1.setHours ( d1.getHours() + 36 );
			// DATE & TIME PICKER
			$('.departure-date').datetimepicker({
			    minDate: serverDateTime,
                beforeShowDay: DisableSpecificDates,
			  	dateFormat: window.datepickerDateFormat,
			  	altFormat: window.datepickerAltFormat,
				altFieldTimeOnly: false,
				showMillisec: false,
				showMicrosec: false,
				showTimezone: false,
				numberOfMonths: 1,
				altField: "#dep",
				controlType: 'select',
				onSelect: function(text, inst) {
					var currentDate = new Date();
					var zeroedDate = new Date(inst.selectedYear, inst.selectedMonth, parseInt(inst.selectedDay), 0, 0, 0, 0);
					if (zeroedDate > currentDate) {
						$('.departure-date').datetimepicker("setDate", new Date(inst.selectedYear, inst.selectedMonth, parseInt(inst.selectedDay), 0, 0, 0, 0));
						$('.return-date').datetimepicker("setDate", new Date(inst.selectedYear, inst.selectedMonth, parseInt(inst.selectedDay), 0, 0, 0, 0));
					} else {
						$('.return-date').datetimepicker("setDate", currentDate);
					}
				}
			});

		   	if (typeof(window.datepickerDepartureDateValue) != 'undefined' && window.datepickerDepartureDateValue.length > 0) {
		   		$('.departure-date').datetimepicker("setDate", new Date(window.datepickerDepartureDateValue.replace(/-/g, '/')));
		   	} /*else {
		   		$('.departure-date').datetimepicker("setDate", new Date());
		   	}*/

			$('.return-date').datetimepicker({
				minDate: serverDateTime,
                beforeShowDay: DisableSpecificDates,
				dateFormat: window.datepickerDateFormat,
				altFormat: window.datepickerAltFormat,
				altFieldTimeOnly: false,
				showMillisec: false,
				showMicrosec: false,
				showTimezone: false,
				numberOfMonths: 1,
				altField: "#ret",
				controlType: 'select'
			});

			if (typeof(window.datepickerReturnDateValue) != 'undefined' && window.datepickerReturnDateValue.length > 0) {
				$('.return-date').datetimepicker("setDate", new Date(window.datepickerReturnDateValue.replace(/-/g, '/')));
			} /*else {
				$('.return-date').datetimepicker("setDate", new Date());
			}*/

			$('.select-avail-slot').on('click', function(e) {

				var $root = null;

				if ($(this).hasClass('selected')) {

					$(this).removeClass('selected');
					$(this).removeClass('color');
					$(this).addClass('grey');

					if ($(this).hasClass('select-avail-dep-slot')) {
						window.bookingRequest.departureAvailabilityId = 0;
						window.bookingRequest.departureIsPrivate = false;
						window.bookingRequest.departureSlotMinutesNumber = -1;
					} else {
						window.bookingRequest.returnAvailabilityId = 0;
						window.bookingRequest.returnIsPrivate = false;
						window.bookingRequest.returnSlotMinutesNumber = -1;
					}
				} else {
					var availId = $(this).attr('id').replace('select-avail-slot-', '');

					var slotMinutesNumber = $.grep($(this).attr('class').split(" "), function(v, i){
					   return v.indexOf('select-avail-slot-time-') === 0;
					}).join();

					slotMinutesNumber = slotMinutesNumber.replace('select-avail-slot-time-', '');

					if ($(this).hasClass('select-avail-dep-slot')) {

						if (availId > 0) {
							$('.proceed-to-booking').show();
						} else {
							$('.proceed-to-booking').hide();
						}

						if($("#returnHeading").length > 0) {
							$root = $('html,body');
							$root.animate({
								scrollTop: $("#returnHeading").offset().top
							}, 1000, function () {});
						} else {
							$root = $('html,body');
							$root.animate({
								scrollTop: $("#book-transfers").offset().top - $("#book-transfers").height()
							}, 1000, function () {});
						}

						$('.select-avail-dep-slot').removeClass('selected');
						$('.select-avail-dep-slot').removeClass('color');
						$('.select-avail-dep-slot').addClass('grey');

						$(this).addClass('selected');
						$(this).addClass('color');
						$(this).removeClass('grey');

						window.bookingRequest.departureSlotMinutesNumber = slotMinutesNumber;
						window.bookingRequest.departureAvailabilityId = availId;
						if ($(this).hasClass('select-avail-slot-private')) {
							window.bookingRequest.departureIsPrivate = true;
						} else {
							window.bookingRequest.departureIsPrivate = false;
						}
					} else {

						if (availId > 0) {
							$('.proceed-to-booking').show();
						} else {
							$('.proceed-to-booking').hide();
						}

						$root = $('html, body');
						$root.animate({
							scrollTop: $("#book-transfers").offset().top - $("#book-transfers").height()
						}, 1000, function () {});


						$('.select-avail-ret-slot').removeClass('selected');
						$('.select-avail-ret-slot').removeClass('color');
						$('.select-avail-ret-slot').addClass('grey');

						$(this).addClass('selected');
						$(this).addClass('color');
						$(this).removeClass('grey');

						window.bookingRequest.returnSlotMinutesNumber = slotMinutesNumber;
						window.bookingRequest.returnAvailabilityId = availId;
						if ($(this).hasClass('select-avail-slot-private')) {
							window.bookingRequest.returnIsPrivate = true;
						} else {
							window.bookingRequest.returnIsPrivate = false;
						}
					}
				}

				e.preventDefault();
			});

			$('.proceed-to-booking').on('click', function(e) {

				if (window.bookingRequest.departureAvailabilityId) {

					var redirectUri = '';

					redirectUri = window.bookingFormPageUrl + '?' +
					'depavid=' + window.bookingRequest.departureAvailabilityId +
					'&depslot=' + window.bookingRequest.departureSlotMinutesNumber +
					'&dep=' + window.bookingRequest.departureDateAlt +
					'&depp=' + (window.bookingRequest.departureIsPrivate ? '1' : '0') +
					'&p1=' + (window.bookingRequest.departureFromId) +
					'&d1=' + (window.bookingRequest.departureToId);

					if (window.bookingRequest.returnAvailabilityId > 0) {
						redirectUri +=
							'&retavid=' + window.bookingRequest.returnAvailabilityId +
							'&retslot=' + window.bookingRequest.returnSlotMinutesNumber +
							'&ret=' + window.bookingRequest.returnDateAlt +
							'&retp=' + (window.bookingRequest.returnIsPrivate ? '1' : '0') +
							'&p2=' + (window.bookingRequest.returnFromId) +
							'&d2=' + (window.bookingRequest.returnToId);
					}

					redirectUri += '&ppl=' + window.bookingRequest.people;

					window.location.href = redirectUri;
				}

				e.preventDefault();
			});
		}
	};

})(jQuery);