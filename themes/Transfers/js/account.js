(function($){
	
	var $root = $('html, body');
	
	$(window).load(function () {
		transfers_account.load();
	});
	
	$(document).ready(function () {
		transfers_account.init();
	});
	
	var transfers_account = {	
	
		load : function () {
			
		},
		init : function() {		
		
			$('#form-account-general').validate({
				onkeyup: false,
				rules: { 
					user_email: {
						required: true,
						email: true
					}
				},
				messages: {
					user_email: '',
				},
				submitHandler: function() { 
					transfers_account.processGeneralSubmission(); 
				}
			});
			
			$('#form-account-security').validate({
				onkeyup: false,
				rules: { 
					old_password: { required: true },
					new_password: { required: true },
					confirm_password: { required: true },
				},
				messages: {
					old_password: '',
					new_password: '',
					confirm_password: '',					
				},
				submitHandler: function() { 
					transfers_account.processSecuritySubmission(); 
				}
			});
			
			$('#form-account-billing').validate({
				onkeyup: false,
				rules: { 
					billing_email : {
						required: true,
						email: true
					}
				},
				messages: {
					billing_email: ''
				},
				submitHandler: function() { 
					transfers_account.processBillingSubmission(); 
				}
			});
		
		},
		processGeneralSubmission : function () {
		
			$("div.success").hide();
			$("div.error").hide();		
		
			$('#save-general')
				.after('<span class="loader">...</span>')
				.attr('disabled','disabled');
		
			var dataObj = {
				'action'		: 'account_ajax_save_general_request',
				'user_id'		: window.currentUserId,
				'user_login'	: window.currentUserLogin,
				'nonce' 		: TransfersAjax.nonce,
				'user_email'	: $('#user_email').val(),
				'first_name'	: $('#first_name').val(),
				'last_name'		: $('#last_name').val(),
				'user_url'		: $('#user_url').val(),
				'nickname'		: $('#nickname').val(),
				'googleplus'	: $('#googleplus').val(),
				'twitter'		: $('#twitter').val(),
				'facebook'		: $('#facebook').val(),
				'description'	: $('#description').val()
			};
				
			jQuery.ajax({
				url: TransfersAjax.ajaxurl,
				data: dataObj,
				success:function(data) {	
				
					if (data == '1') {
						$('#form-account-general span.loader').fadeOut('slow',function(){$(this).remove();});
						$('#save-general').removeAttr('disabled'); 
					
						$("div.success").fadeIn();
						
						$root.animate({
							scrollTop: $('.success').offset().top - 100
						}, 500, function () {});
					} else {
						$('#form-account-general span.loader').fadeOut('slow',function(){$(this).remove();});
						$('#save-general').removeAttr('disabled'); 
					
						$("div.error").fadeIn();
						$root.animate({
							scrollTop: $('.error').offset().top - 100
						}, 500, function () {});
					}
					
				},
				error: function(errorThrown){
					console.log(errorThrown);
				}
			}); 

		},
		processSecuritySubmission : function () {
			
			$("div.success").hide();
			$("div.error").hide();
			
			$('#save-security')
				.after('<span class="loader">...</span>')
				.attr('disabled','disabled');
		
			var dataObj = {
				'action'			: 'account_ajax_save_security_request',
				'user_id'			: window.currentUserId,
				'user_login'		: window.currentUserLogin,
				'nonce' 			: TransfersAjax.nonce,
				'old_password'		: $('#old_password').val(),
				'new_password'		: $('#new_password').val(),
				'confirm_password'	: $('#confirm_password').val(),
			};
				
			jQuery.ajax({
				url: TransfersAjax.ajaxurl,
				data: dataObj,
				success:function(data) {		

					if (data == '1') {
						$('#form-account-security span.loader').fadeOut('slow',function(){$(this).remove();});
						$('#save-security').removeAttr('disabled'); 
				
						$("div.success").fadeIn();
						
						$root.animate({
							scrollTop: $('.success').offset().top - 100
						}, 500, function () {});
					} else {
						$('#form-account-security span.loader').fadeOut('slow',function(){$(this).remove();});
						$('#save-security').removeAttr('disabled'); 
				
						$("div.error").fadeIn();					
						$root.animate({
							scrollTop: $('.error').offset().top - 100
						}, 500, function () {});
					}
				},
				error: function(errorThrown){
					console.log(errorThrown);
				}
			}); 
		},
		processBillingSubmission : function () {
		
			$("div.success").hide();
			$("div.error").hide();		
		
			$('#save-billing')
				.after('<span class="loader">...</span>')
				.attr('disabled','disabled');
		
			var dataObj = {
				'action'				: 'account_ajax_save_billing_request',
				'user_id'				: window.currentUserId,
				'user_login'			: window.currentUserLogin,
				'nonce' 				: TransfersAjax.nonce,
				'billing_first_name'	: $('#billing_first_name').val(),
				'billing_last_name'		: $('#billing_last_name').val(),
				'billing_company'		: $('#billing_company').val(),
				'billing_email'			: $('#billing_email').val(),
				'billing_address_1'		: $('#billing_address_1').val(),
				'billing_postcode'		: $('#billing_postcode').val(),
				'billing_phone'			: $('#billing_phone').val(),
				'billing_city'			: $('#billing_city').val(),
				'billing_state'			: $('#billing_state').val(),
				'billing_country'		: $('#billing_country').val(),
			};
				
			jQuery.ajax({
				url: TransfersAjax.ajaxurl,
				data: dataObj,
				success:function(data) {

					if (data == '1') {
						$('#form-account-billing span.loader').fadeOut('slow',function(){$(this).remove();});
						$('#save-billing').removeAttr('disabled'); 
					
						$("div.success").fadeIn();
						
						$root.animate({
							scrollTop: $('.success').offset().top - 100
						}, 500, function () {});
					} else {
						$('#form-account-billing span.loader').fadeOut('slow',function(){$(this).remove();});
						$('#save-billing').removeAttr('disabled'); 
					
						$("div.error").fadeIn();
						
						$root.animate({
							scrollTop: $('.error').offset().top - 100
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