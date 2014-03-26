$( document ).ready(function() {
							 
	$(".iframe").colorbox({
						  iframe:true,
						  width:823,						  
						  height:"95%",
						  opacity:0.8,
						  closeButton:false,
						  fixed:true						  
						  });	
	
	
	$(".siteButton a").hover(
			  function() {
			    $(this).children("span").animate({
			    	backgroundColor:"#FBD2D1",
			    	color: "#373737"			    	
			    }, 180);
			  }, function() {
				  $(this).children("span").animate({
				    	backgroundColor:"#373737",
				    	color: "#fff"
				    }, 180);
			  }
			);

	
	$(".allStores span").hover(
			  function() {
			    $(this).animate({
			    	backgroundColor:"#FBD2D1",
			    	color: "#373737"
			    }, 180);
			  }, function() {
				  $(this).animate({
				    	backgroundColor:"#373737",
				    	color: "#fff"
				    }, 180);
			  }
			);
	
	$(".customButton").hover(
			  function() {
			    $(this).children("span").animate({
			    	backgroundColor:"#FBD2D1",
			    	color: "#373737"
			    }, 180);
			  }, function() {
				  $(this).children("span").animate({
				    	backgroundColor:"#373737",
				    	color: "#fff"
				    }, 180);
			  }
			);
	
	
	$('.topTrends').bxSlider({
		auto: false,
		pause: 2000,
	    speed: 800,
	    pager: true, // carasuls
	    controls: false
		});
	
	$('.contentSlider').bxSlider({
		auto: true,
		pause: 2000,
	    speed: 800,
	    pager: false, // carasuls
	    controls: false
		});
	
	$('.expertSectoinSlider').bxSlider({
		auto: false,
		pause: 1000,
	    speed: 920,
	    pager: false // carasuls	    
		});
	
	
	 $( ".searchIcon img" ).on( "click", function() {
		 $(".searchContainer .searchCriteria").animate({						  
			  right:"43"
		  }, 300);
	 });
	
});