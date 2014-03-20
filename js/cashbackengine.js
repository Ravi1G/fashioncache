//--------(autocomplete)-----------------------------------------------------------------------------------------------

function formatItem(row) {
		return '<div style=position:relative;float:left>' + row[0] + '</div>';
}

function seacrhSubmit() {
	document.forms['searchfrm'].submit();
}

function facebook_login() {
	FB.login(function(response) {
		window.location = '../fblogin.php';
		// Log.info('FB.login callback', response);
		if (response.session) {
			// Log.info('User is logged in');
		} else {
			// Log.info('User is logged out');
		}
	}, {scope: 'email,offline_access,publish_stream,user_birthday,user_location'});
}

$(document).ready(function() {
	$('#searchtext').autocomplete('../autocomplete.php', {
	width: 234,
	minChars: 2,
	formatItem: formatItem,		
	highlight: false
	}).result(function(event, item) {
		seacrhSubmit();
	});
});

///---------(tooltip)--------------------------------------------------------------------------------------------------

$(document).ready(function() {
	    $(".cashbackengine_tooltip").hover(
	        function() { $(this).contents("span:last-child").css({ display: "block" }); },
	        function() { $(this).contents("span:last-child").css({ display: "none" }); }
	    );
	    $(".cashbackengine_tooltip").mousemove(function(e) {
	        var mousex = e.pageX + 10;
	        var mousey = e.pageY + 10;
	        $(this).contents("span:last-child").css({  top: mousey, left: mousex });
	    });
});

///---------(slider)-------------------------------------------------------------------------------------------------------

$(document).ready(function(){	
	$("#slider").easySlider({
		auto: true, 
		continuous: true,
		numeric: true
	});
});	

///---------(top)-------------------------------------------------------------------------------------------------------

 $(document).ready(function() {
	$(window).scroll(function() {
		if ($(this).scrollTop() > 100) {
			$('.scrollup').fadeIn();
		} else {
			$('.scrollup').fadeOut();
		}
		});
 
		$('.scrollup').click(function() {
			$("html, body").animate({ scrollTop: 0 }, 600);
			return false;
		});
});

///---------(tabs)------------------------------------------------------------------------------------------------------

$(document).ready(function(){

	$(".tab_content").hide(); // Hide all content
	$("#tabs li:first").addClass("active").show(); // Activate first tab
	$(".tab_content:first").show(); // Show first tab content

	$("#tabs li").click(function() {
		//	First remove class "active" from currently active tab
		$("#tabs li").removeClass('active');

		//	Now add class "active" to the selected/clicked tab
		$(this).addClass("active");

		//	Hide all tab content
		$(".tab_content").hide();

		//	Here we get the href value of the selected tab
		var selected_tab = $(this).find("a").attr("href");

		//	Show the selected tab content
		$(selected_tab).fadeIn();
		return false;
	});
});

///---------(scroll)------------------------------------------------------------------------------------------------------

$(document).ready(function() {
	$('#scrollstores').jsCarousel({ autoscroll: true, circular: true, masked: false, itemstodisplay: 5, orientation: 'h' });
});
