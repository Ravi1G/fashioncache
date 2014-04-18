$(document).ready(function () {

    $(".siteButton a").hover(
        function () {
            $(this).children("span").animate({
                backgroundColor: "#FBD2D1",
                color: "#373737"
            }, 180);
        }, function () {
			$(this).children("span").stop(20);
            $(this).children("span").animate({
                backgroundColor: "#373737",
                color: "#fff"
            }, 180);
        }
    );


    $(".allStores span").hover(
        function () {
            $(this).animate({
                backgroundColor: "#FBD2D1",
                color: "#373737"
            }, 180);
        }, function () {
			$(this).stop(20);
            $(this).animate({
                backgroundColor: "#373737",
                color: "#fff"
            }, 180);
        }
    );

    $(".customButton").hover(
        function () {
            $(this).children("span").animate({
                backgroundColor: "#FBD2D1",
                color: "#373737"
            }, 180);
        }, function () {
			$(this).children("span").stop(20);
            $(this).children("span").animate({
                backgroundColor: "#373737",
                color: "#fff"
            }, 180);
        }
    );
	$(".customCommentArea .comment-respond .comment-form #submit").hover(
        function () {
            $(this).animate({
                backgroundColor: "#FBD2D1",
                color: "#373737"
            }, 180);
        }, function () {
			$(this).stop(20);
            $(this).animate({
                backgroundColor: "#373737",
                color: "#fff"
            }, 180);
        }
    );	
	$(".wpcf7-submit").hover(
        function () {
            $(this).animate({
                backgroundColor: "#FBD2D1",
                color: "#373737"
            }, 180);
        }, function () {
			$(this).stop(20);
            $(this).animate({
                backgroundColor: "#373737",
                color: "#fff"
            }, 180);
        }
    );	

    $('.topTrends').bxSlider({
        auto: false,
        pause: 2000,
        speed: 800,
		responsive: false,
        pager: true, // carasuls
        controls: true,
		onSliderLoad: function(){
			$('.bx-pager').prepend('<span id="pg-prev" class="prevSlideIcon"></span>');
			$('.bx-pager').append('<span id="pg-next" class="nextSlideIcon"></span>');			
			$('#pg-next').click(function(){
				$('.dealsOfWeekContainer .bx-next').trigger('click');
			})
			
			$('#pg-prev').click(function(){
				$('.dealsOfWeekContainer .bx-prev').trigger('click');
			})
		}
    });  
	

	//var slider = $('.topTrends').bxSlider();	
	/*$('div.customControls a.bx-next').on("click", function () {
    	slider.goToNextSlide();
		var current = slider.getCurrentSlide();
		alert(current)
		$('.bx-pager-item').find('a').removeClass('active');
		$('[data-slide-index="'+current+'"]').addClass('active');
		
    });
	$('div.customControls a.bx-prev').on("click", function () {
    	slider.goToPrevSlide();		
    });*/
	
	
	

    $('.expertSectoinSlider').bxSlider({
        auto: false,
        pause: 1000,
        speed: 920,
        pager: false // carasuls	    
    });


    $(".iframe").colorbox({
        iframe: true,
        width: 795,
        height: 875,
        opacity: 0.8,
        scrolling: false,
        closeButton: false,
        fixed: false,
        top: 20
    });



    $(".searchIcon img").on("click", function () {
        $(".searchContainer .searchCriteria").animate({
            right: "43"
        }, 300);
    });
	
	
	$(".sendInvitationButton span").on("click", function () {
		$( "#Send" ).trigger( "click" );
	});	
	$("#updateForm").on("click", function () {
		$( "#Update" ).trigger( "click" );
	});	
	$("#cancelForm").on("click", function () {
		$( "#CancelIt" ).trigger( "click" );
	});		
	
	
});