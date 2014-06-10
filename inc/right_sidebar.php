<?php 
global $advertisements;
?>
<div class="sidebarWithAds">
        <!-- Follow Us Section -->				
        <div class="followUs">
            <div class="title">------ FOLLOW US ------</div>
            <div class="shortLinks">
                <span>
                	<a href="<?php echo FACEBOOK_PAGE;?>" class="fbLight"><img class="noncolor" alt="" src="<?php echo SITE_URL;?>img/fbLight.jpg"/><img class="colorful" alt="" src="<?php echo SITE_URL;?>img/fbColorLight.jpg"/></a></span>
                	<span><a href="<?php echo TWITTER_PAGE;?>" class="twtLight"><img class="noncolor" alt="" src="<?php echo SITE_URL;?>img/twtLight.jpg"/><img class="colorful" alt="" src="<?php echo SITE_URL;?>img/twtColorLight.jpg"/></a></span>
                	<span><a href="https://plus.google.com/109229722850645533350" class="gpLight"><img class="noncolor" alt="" src="<?php echo SITE_URL;?>img/gpLight.jpg"/><img class="colorful" alt="" src="<?php echo SITE_URL;?>img/gpColorLight.jpg"/></a></span>
                	<span><a href="http://www.pinterest.com/thefashioncache/" class="piLight"><img class="noncolor" alt="" src="<?php echo SITE_URL;?>img/piLight.jpg"/><img class="colorful" alt="" src="<?php echo SITE_URL;?>img/piColorLight.jpg"/></a></span>
            </div>
        </div>
        
        <!-- Sign Up Section -->
        <div class="signUpContainer standardSignUpContainer notResponsive">
            <div class="title titleOf14">SHOP &#x0026; EARN CASH BACK!</div>
            <div class="body standardBody">
                <div class="howItWorks">HOW IT WORKS</div>
                <div class="howItWorksSteps">
                    <div class="stepSection howItWorksStepOne">
                        <div><img src="<?php echo SITE_URL;?>img/stepOneDark.jpg" alt=""/></div>
                        <div class="stepTitle">Sign up<br/><span>(it&#x2019;s free)</span></div>
                    </div>
                    <div class="stepSection howItWorksStepTwo">
                        <div><img src="<?php echo SITE_URL;?>img/stepTwoDark.jpg" alt=""/></div>
                        <div class="stepTitle">Select a Store &#x0026; Shop</div>
                    </div>
                    <div class="stepSection last howItWorksStepThree">
                        <div><img src="<?php echo SITE_URL;?>img/stepThreeDark.jpg" alt=""/></div>
                        <div class="stepTitle">Get Cash Back!</div>
                    </div>
                    <div class="cb"></div>
                </div>						
                <div class="allStores forSignUp">
                    <a href="<?php echo SITE_URL?>signup_or_login.php">
                	<span>SIGN UP</span></a></div>
            </div>
        </div>
        <div class="advertisement300 notResponsive">
            <a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $advertisements[SIDEBAR_TOP_IMAGE]['retailer_id']; ?>&a=<?php echo $advertisements[HOME_PAGE_HEADER_AD_ID]['advertisement_id']?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>>
						<img height="250" width="300" src="<?php echo $advertisements[SIDEBAR_TOP_IMAGE]['image_url']!='' ? $advertisements[HOME_PAGE_HEADER_AD_ID]['image_url'] : SITE_URL.'admin/'.$advertisements[SIDEBAR_TOP_IMAGE]['image_name']?>">
			</a>
        </div>
        <div class="advertisement300 notResponsive">
           <a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $advertisements[SIDEBAR_BOTTOM_IMAGE]['retailer_id']; ?>&a=<?php echo $advertisements[SIDEBAR_BOTTOM_IMAGE]['advertisement_id']?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>>
						<img height="250" width="300" src="<?php echo $advertisements[SIDEBAR_BOTTOM_IMAGE]['image_url']!='' ? $advertisements[SIDEBAR_BOTTOM_IMAGE]['image_url'] : SITE_URL.'admin/'.$advertisements[SIDEBAR_BOTTOM_IMAGE]['image_name']?>">
			</a>
        </div>
    </div>				
    <div class="cb"></div>	