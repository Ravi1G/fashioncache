$(document).ready(function () {
	
	$nav = $('#reponsiveNavigation');
	$nav.megakrill({
		//clone       : false,
		cloneRemove : 'li > div',
		cloneId     : false
	});

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
	
	$('#notREsponsiveExpertSectoinSlider').bxSlider({
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
	
	
	//	Submit Buttons
	
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