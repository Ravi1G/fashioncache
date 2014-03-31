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
            $(this).children("span").animate({
                backgroundColor: "#373737",
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
        speed: BANNER_SPEED,
        pager: false, // carasuls
        controls: false,
        autoHover : true
    });

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

});