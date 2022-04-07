function confirmDelete (form_id, message) {
	var answer = confirm(message);
	if (answer){
		document.getElementById(form_id.replace('#', '')).submit();
		return true;
	}
	return false;  
}

(function($){

	$(document).ready(function () {
		tfAdmin.init();
	});
	
	var tfAdmin = {

		init: function () {
		
			$('#entry_type').on('change', function() {

				var entryType = $(this).val();
				
				switch (entryType) {
				
					case 'daily' :
						$('#day_of_month_row').hide();
						$('#day_of_week_row').hide();
						$('#by_minute_row').hide();
						$('#not_by_minute_row').show();
						break;
					case 'weekly' :
						$('#day_of_month_row').hide();
						$('#day_of_week_row').show();
						$('#by_minute_row').hide();
						$('#not_by_minute_row').show();
						break;
					case 'monthly' :
						$('#day_of_month_row').show();
						$('#day_of_week_row').hide();
						$('#by_minute_row').hide();
						$('#not_by_minute_row').show();
						break;
					case 'byminute' :
						$('#day_of_month_row').hide();
						$('#day_of_week_row').hide();
						$('#by_minute_row').show();
						$('#not_by_minute_row').hide();
						break;
					default :
						$('#day_of_month_row').hide();
						$('#day_of_week_row').hide();
						$('#by_minute_row').show();
						$('#not_by_minute_row').hide();
						break;
				}
				
			});

		}
	}	

})(jQuery);