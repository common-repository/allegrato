(function($) {
	$( "#allegrato_test_remote_system_allegro").click( function(){
		var that = $(this);	
		
	 $.ajax({
		   type: "POST",
		   dataType: "json",
		   url: $("#allegrato_admin_url").text(),
		   data: {
		 		_ajax_nonce: $("#allegrato_admin_ajax_nonce").text(),
		 		action: 'allegrato_test_remote_system_allegro',	
		 		allegrato_webapi_key_allegro: $("#allegrato_webapi_key_allegro").val(),
	 			allegrato_webapi_login_allegro:	$("#allegrato_webapi_login_allegro").val(),
	 			allegrato_webapi_password_allegro: $("#allegrato_webapi_password_allegro").val(),
	 			allegrato_webapi_country_allegro: $("#allegrato_webapi_country_allegro").val()
	 		},
		   success: function(json){
	 		   $("#allegrato_options_form1").fadeTo( 'fast', 1, function(){});
	 	       $(that).css('cursor', 'default');
			   $('body').css('cursor', 'default');
			   $("#setting-error-settings_updated").remove();
			   $("#allegrato_ajax_test_result").empty().append( "<div class='updated settings-error' id='setting-error-settings_updated'><p><strong>" + json.text + "</strong></p></div>" );
	 		   
		   },
		   beforeSend: function(){
			   $("#allegrato_options_form1").fadeTo( 'slow', 0.3, function(){});
			   $(that).css('cursor', 'wait');
			   $('body').css('cursor', 'wait');
			   $("#allegrato_test_remote_system_allegro").css("style", "cursor:wait;");
		   }
		 });		
	});
})(jQuery);