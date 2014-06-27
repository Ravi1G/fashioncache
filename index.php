<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\******************************************************************/





	$is_index = 1;
	if (file_exists("./install.php"))
	{
		header ("Location: install.php");
		exit();
	}

	session_start();
	require_once("inc/config.inc.php");
 	
	// save referral id //////////////////////////////////////////////
	if (isset($_GET['ref']) && is_numeric($_GET['ref']))
	{//checking if ref variable is set
		if(isset($_GET['in_id']) && is_numeric($_GET['in_id']))
		{ // checking for the in_id variable if set then create both the cookies
			//Getting the record of invitation_id and checking whether 
			//this is the first time when the invitation id is being used
			$invitation_id =$_GET['in_id'];
			$query = "SELECT * FROM cashbackengine_invitations WHERE invitation_id = $invitation_id";
			
			$result = smart_mysql_query($query);
			$row = mysql_fetch_assoc($result);
			
			if($row['registering_user_id']==0)
			{
				//Set cookie for invitation_id if registering_user_id is not set
				setcookie("invitation_id", $_GET['in_id'], time()+(60*60*24*30));
				$ref_id = (int)$_GET['ref'];
				setReferal($ref_id);
			}
		}
		else 
		{//Or create the single cookie
			$ref_id = (int)$_GET['ref'];
			setReferal($ref_id);
		}
		
		header("Location: index.php");
		exit();
	}

	// set language ///////////////////////////////////////////////////
	if (isset($_GET['lang']) && $_GET['lang'] != "")
	{
		$site_lang	= strtolower(getGetParameter('lang'));
		$site_lang	= preg_replace("/[^0-9a-zA-Z]/", " ", $site_lang);
		$site_lang	= substr(trim($site_lang), 0, 30);
		
		if ($site_lang != "")
			setcookie("site_lang", $site_lang, time()+3600*24*365, '/');

		header("Location: index.php");
		exit();
	}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = SITE_HOME_TITLE;
	
	
	$trending_sale_coupons = GetTrendingSaleCoupons();
	$total_trending_sale_coupons = count($trending_sale_coupons);
	require_once("inc/header.inc.php");
	
	/* // Only Mobiles no Tablets.
	if( $detect->isMobile() && !$detect->isTablet()){
	
		// Getting the Status
		if(isset($_GET['show']) && $_GET['show']==1) {
			$visitStatus = $_GET['show'];
			// Setting the cookie
			setcookie("UserVisitStatus", $visitStatus, time()+3600*12);
		}		
		// Check If user is Frequent or not		
		elseif(!isset($_COOKIE["UserVisitStatus"]) || $_COOKIE["UserVisitStatus"]!='1'){
				header ("Location: pmv.php");
				exit;
		} 
		
	} */
	
	if(!isLoggedIn() && !$detect->isMobile() || ($detect->isTablet() && !isLoggedIn())){
	?>
		<script type="application/javascript">
			$.colorbox({
				iframe: true,
				width: 548,
				height: 553,
				opacity: 0.8,
				scrolling: false,
				closeButton: false,
				fixed: true,				
				href : "<?php echo SITE_URL?>popup.php"
			});
		</script>
	<?php } ?>
	
	<?php
	if(isset($_GET['msg']) && $_GET['msg']=="welcome")
	{ ?>
		<script type="application/javascript">
			$.colorbox({
				iframe      : true,
				width: 361,
				height: 238,
				opacity: 0.8,
				scrolling: false,
				closeButton: false,
				className: 'welcomePopupParent',
				fixed: true,				
				href : "<?php echo SITE_URL?>welcomeAgain.php"
			});
		</script>
	<?php } ?>
		<div class="cb"></div>
		<!-- For Responsive View -->
		<div class="isResponsive BannerSlider">
		<?php $allbanners = array(); $allbanners = BannersList(0);?>
					<ul class="contentSlider">
							<?php foreach ($allbanners as $banner){ ?>
							<li>
								<?php
								if($banner['bypass_script']==1) 
								{
									$link	=	$banner['link'];	
								}
								else {
									$link	=	SITE_URL.'go2store.php?id='.$banner['retailer_id'].'&b='.$banner['banner_id'];	
								}
								?>
								<a href="<?php echo $link?>" <?php echo "target=\"_blank\"";?> >
									<img src="<?php echo SITE_URL.'admin/'.$banner['image']?>" alt=""/>
								</a>
							</li>
							<?php 	}?>
                        </ul>
		</div>
		
		
	
		<div class="container content">
		<!-- Featured Store List  -->
			<div class="featuredStoresSection">
			  	<div class="sectionTitle">FEATURED STORES</div>
			</div>
			<div class="saleAlertSection notResponsive">
			        <div class="sectionTitle"><span>SALE ALERT!</span> 
			        <a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $sale_alert['retailer_id']; ?>&s=<?php echo $sale_alert['sale_alert_id']; ?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>><?php echo $sale_alert['title']?></a></div>
			</div>
		<div class="cb"></div>
		<div class="SiteContentSection">
		<div class="SiteContentLeft">
		
		
		    <div class="featuredStoresSection">		      
		        <div class="sectionBody">
		            <?php
					if (FEATURED_STORES_LIMIT > 0)
					{
						// show featured retailers
						$result_featured = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE featured='1' AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' AND is_profile_completed=0 ORDER BY sort_order asc LIMIT ".FEATURED_STORES_LIMIT);
						$total_fetaured = mysql_num_rows($result_featured);
					
						if ($total_fetaured > 0) { 
							$looped_featured = 0;
						?>
							<?php 
								while ($row_featured = mysql_fetch_array($result_featured)) {
									$looped_featured++;
							?>							
									<div class="store<?php if($looped_featured==$total_fetaured){?> lastItem<?php }?>">
									  <a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row_featured['retailer_id']; ?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>>
						                <span class="icon middleIconMod">						                    
						                    	<img alt="" src="<?php if (!stristr($row_featured['image_I'], 'http')) echo SITE_URL."admin/upload/retailer/"; echo $row_featured['image_I']; ?>" alt="<?php echo $row_featured['title']; ?>"/>
										</span>				
						                <span class="cashBack cashBackMod">
						                	<?php
						                	$cashback_type = GetCashbackType($row_featured['cashback']);
						                	$cashback = RemoveCashbackType($row_featured['cashback']);
						                	?>
						                    <span class="percentage"><?php echo $cashback;?><span class="percentageSymbol"><?php echo $cashback_type;?></span></span>
						                    <span class="cashBackCaption">Cash Back</span>
						                </span>
										</a>
						              <div class="cb"></div>
						            </div>
						             <!-- <div>
-										<?php echo $row_featured['cashback'];?>&nbsp;Cashback
-									</div>
-									<div class="imagebox">
-										<a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row_featured['retailer_id']; ?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>><img src="<?php if (!stristr($row_featured['image'], 'http')) echo SITE_URL."img/"; echo $row_featured['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row_featured['title']; ?>" title="<?php echo $row_featured['title']; ?>" border="0" /></a>
-									</div> -->
							<?php } ?>
						<?php
								}
						} // end featured retailers 
					?>
		            <div class="cb"></div>
		        </div>	
				<div class="isResponsive">
					<div class="allStores responsiveButton">
						<a href="<?php echo SITE_URL?>retailers">
							<span class="hoverAnim">Show All Stores</span>
						</a>
					</div>
				</div>		
		    </div>
		     <?php $allbanners = array(); $allbanners = BannersList(0);?>
		    <!-- Sale Alert Section -->
		     <div class="saleAlertSection notResponsive">		        
                <div class="sectionBody">
                    <div class="contentSection contentSectionSlides">
                        <ul class="contentSlider">
							<?php foreach ($allbanners as $banner){ ?>
							<li>
								<?php
								if($banner['bypass_script']==1) 
								{
									$link	=	$banner['link'];	
								}
								else {
									$link	=	SITE_URL.'go2store.php?id='.$banner['retailer_id'].'&b='.$banner['banner_id'];	
								}
								?>
								<a href="<?php echo $link?>" <?php echo "target=\"_blank\"";?> >
									<img src="<?php echo SITE_URL.'admin/'.$banner['image']?>" alt=""/>
								</a>
							</li>
							<?php 	}?>
                        </ul>                        
                        <!-- <div class="noTricks">No Catches, Tricks or Gimmicks.</div> -->
                        <div class="topTrendSection dealsOfWeekContainer">
                            <div class="title">TRENDING SALES</div>
                            <div class="body"> 
								<div class="notResponsiveSlider">
									<div class="slideLoader"><img src="<?php echo SITE_URL;?>img/bx_loader.gif" alt="Loading" /></div>
								</div>
								<div class="cb"></div>
								<div class="allStores"><a href="<?php echo SITE_URL?>coupons.php"><span class="hoverAnim">SEE ALL SALES &#x0026; COUPONS</span></a></div>
                            </div>
                        </div>
                    </div>                                       
                </div>
            </div>
		      <div class="cb"></div>
			 
				  <div class="fashionExpertSection notResponsive">
					<div class="titleSection">
						<div>
							<img alt="" src="<?php echo SITE_URL;?>img/FashionExpertsIcon.jpg" />
						</div>
						<div class="title">
							MEET OUR <span>FASHION</span> <span class="pink">EXPERTS</span>
						</div>
					</div>
					<div class="expertList">
						<ul class="expertSectoinSlider" id="notREsponsiveExpertSectoinSlider">
						<?php 
						// Calling function to get author list from blog/apis.php
						$authors= fc_get_users();
						fc_reconnect_db();
						$total_authors = count($authors);
						if($total_authors>0){ 
							$i = 1;
							$total_authors_processed = 0;
							foreach($authors as $author){		
								$total_authors_processed++;
								if($i==1){
									echo '<li>';
								}
						?>
						
								<div class="expertInformation">
									<div class="expertThumb">
										<a href="<?php echo BLOG_URL?>?author=<?php echo $author['ID'];?>">
											<?php //Image link if author image is present otherwise show the default image
												$img_link =isset($author['Author_Profile_Picture']) && $author['Author_Profile_Picture']!="" ? $author['Author_Profile_Picture'] : SITE_URL.'img/hangerIconSmall.png' ; 
											?>
											<img width="92" height="92" src='<?php echo $img_link;?>'/>
										</a>
									</div>
									<?php 
										if($author['first_name']!="")
											$username = $author['first_name'];
										//$username = trim($author['First_Name'].' '.substr($author['Last_name'], 0, 1));
										elseif($author['user_nicename']!=''){
											$username = $author['user_nicename'];
										}
									?>
									<!-- Information about the author: authorname,city,state -->
									<div class="expertName">
										<?php 
											if($username!="") 
												echo $username;
										?>
									</div>
									<?php 
									unset($author_address);
									if($author['author_city']!=""){
										$author_address[] = $author['author_city'];								
									}
									if($author['author_state']!=""){
										$author_address[] = $author['author_state'];								
									}
									if(isset($author_address)){
									?>
									<div class="expertLocation">
										<?php echo implode(', ', $author_address);?>
									</div>
									<?php } ?>
									<div class="expertBlog"><a href="<?php echo BLOG_URL?>?author=<?php echo $author['ID'];?>">Read Posts</a></div>
								</div>							
						<?php 			
								if($i==5 || $total_authors_processed==$total_authors){
									echo '</li>';
								}
								
								if($i==5)
									$i = 1;
								else 
									$i++;  
																	
							}
						?>							
						<?php } ?>				
						</ul>
						<div class="cb"></div>
					</div>
					<div class="cb"></div>
				</div>		     
		      
		    </div>
		    <div class="SiteContentRight notResponsive">
		     <!-- Sidebar Section -->
            <div class="sideBarsSection">
                <!-- Follow Us Section  -->				
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
                
                <?php if(!isLoggedIn()) {  ?>
                <!-- Sign Up Section -->
                <div class="signUpContainer">
                    <div class="title">SIGN UP TODAY TO<br/>EARN CASH BACK!</div>
                    <div class="body">
                        <div><a href="javascript: void(0);" onclick="fbLogin();"><img alt="" src="<?php echo SITE_URL;?>img/fbSignUp.jpg"/></a></div>
                        <div class="emailSignUp">Or- Sign up with Email</div>
                        <form method="post" action="<?php echo 'signup_or_login.php';?>">
                            <div><input type="text" name="email" placeholder="Email" class="inputBox"/></div>
                            <div><input type="password" name="password" placeholder="Password" class="inputBox"/></div>
                           <input type="hidden" name="action" id="action" value="signup" />
                            <div><button type="submit" class="customButton customButtonHomePage"><span>SIGN UP</span></button></div>
                        </form>
                    </div>
                </div>
                <?php } else {?>
                <div class="signUpContainer howItWorksContainer">
						<div class="title">
							<div>EARN CASH BACK!</div>
							<div class="subTitleSec">HOW IT WORKS!</div>
						</div>
						<div class="body">
							<div class="horizontalSteps"><img src="<?php echo SITE_URL;?>img/stepOneDark.jpg" alt=""/> Sign up <span>(it’s free)</span></div>
							<div class="horizontalSteps"><img src="<?php echo SITE_URL;?>img/stepTwoDark.jpg" alt=""/> Select a Store &#x0026; Shop</div>
							<div class="horizontalSteps"><img src="<?php echo SITE_URL;?>img/stepThreeDark.jpg" alt=""/> Get Cash Back!</div>
							<div class="learnMoreAboutSteps">
								<div class="learnMoreAlert">No Catches, Tricks or Gimmicks.</div>
								<div class="learnMoreLink"><a href="<?php echo SITE_URL;?>blog/about-fasion-smart/">Learn More &#x003E;</a></div>								
							</div>
						</div>	
					</div>
                <?php }?>
                <!-- Gift Card Section -->
                <div class="giftCardContainer">
                    <div class="title">Receive a</div>
                    <div class="giftCardAmount">
                        <span class="currency">&#x0024;</span>10
                    </div>
                    <div class="giftCardCaption">GIFT CARD</div>
                    <div class="giftCardCaption1">when you refer a friend!</div>
                    <div class="giftCardLearnMore"><a href="<?php echo SITE_URL;?>invite.php">LEARN MORE &#x003E;</a></div>
                </div>
                
                <!-- Featured Articles Section -->
                <?php /* ?><div class="heading3">FEATURED ARTICLES</div><?php */ ?>
                <a href="<?php echo SITE_URL;?>blog/" class="noUnderline">
					<span class="captionContainer readOurBlog">				
						<span><img alt="" src="<?php echo SITE_URL;?>img/blog.jpg"/></span>
						<span class="subText">READ OUR</span>
						<span class="titleText">BLOG</span>
						<span class="caption">Read More</span>
					</span>
				</a>
				<div class="homepageAdsContainer">
					<a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $advertisements[HOME_PAGE_SIDEBAR_AD1_ID]['retailer_id']; ?>&a=<?php echo $advertisements[HOME_PAGE_SIDEBAR_AD1_ID]['advertisement_id']?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>>
						<img width="195" src="<?php echo $advertisements[HOME_PAGE_SIDEBAR_AD1_ID]['image_url']!='' ? $advertisements[HOME_PAGE_SIDEBAR_AD1_ID]['image_url'] : SITE_URL.'admin/'.$advertisements[HOME_PAGE_SIDEBAR_AD1_ID]['image_name']?>">
					</a>
				</div>
				<div class="homepageAdsContainer">
					<a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $advertisements[HOME_PAGE_SIDEBAR_AD2_ID]['retailer_id']; ?>&a=<?php echo $advertisements[HOME_PAGE_SIDEBAR_AD2_ID]['advertisement_id']?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>>
						<img width="195" src="<?php echo $advertisements[HOME_PAGE_SIDEBAR_AD2_ID]['image_url']!='' ? $advertisements[HOME_PAGE_SIDEBAR_AD2_ID]['image_url'] : SITE_URL.'admin/'.$advertisements[HOME_PAGE_SIDEBAR_AD2_ID]['image_name']?>">
					</a>
				</div>
				<?php /* ?>
                <div class="captionContainer">
                    <div class="image"><img alt="" src="<?php echo SITE_URL;?>img/handBags.jpg"/></div>
                    <div class="caption">Lorem ipsum dolor</div>
                </div>
				<?php */ ?>
            </div>
            </div>
		    <div class="cb"></div>
		</div>   
		</div>
		
		
		<?php // require_once("inc/screenDetector.php"); ?>		
	
		
		
				<div class="topTrendSection dealsOfWeekContainer isResponsive">
                            <div class="title">TRENDING SALES</div>
                            <div class="body">
							<div class="responsiveSlider">
								<div class="slideLoader"><img src="<?php echo SITE_URL?>img/bx_loader.gif" alt="Loading" /></div>
							</div>							
								<div class="cb"></div>                                
								<div class="allStores"><a href="<?php echo SITE_URL?>coupons.php"><span class="hoverAnim">SEE ALL SALES &#x0026; COUPONS</span></a></div>
                            </div>
                        </div>
		
		<div class="followUs isResponsive">
                    <div class="title">------ FOLLOW US ------</div>
                    <div class="shortLinks">
                        <span>
                        	<a href="<?php echo FACEBOOK_PAGE;?>" class="fbLight"><img class="noncolor" alt="" src="<?php echo SITE_URL;?>img/fbLight.jpg"/><img class="colorful" alt="" src="<?php echo SITE_URL;?>img/fbColorLight.jpg"/></a></span>
                        	<span><a href="<?php echo TWITTER_PAGE;?>" class="twtLight"><img class="noncolor" alt="" src="<?php echo SITE_URL;?>img/twtLight.jpg"/><img class="colorful" alt="" src="<?php echo SITE_URL;?>img/twtColorLight.jpg"/></a></span>
                        	<span><a href="https://plus.google.com/109229722850645533350" class="gpLight"><img class="noncolor" alt="" src="<?php echo SITE_URL;?>img/gpLight.jpg"/><img class="colorful" alt="" src="<?php echo SITE_URL;?>img/gpColorLight.jpg"/></a></span>
                        	<span><a href="http://www.pinterest.com/thefashioncache/" class="piLight"><img class="noncolor" alt="" src="<?php echo SITE_URL;?>img/piLight.jpg"/><img class="colorful" alt="" src="<?php echo SITE_URL;?>img/piColorLight.jpg"/></a></span>
                    </div>
                </div>
				
				
		<?php if( $detect->isMobile() && !$detect->isTablet() ) { ?>
		<div class="fashionExpertSection isResponsive">
			<div class="titleSection">
					<img alt="" src="<?php echo SITE_URL;?>img/FashionExpertsIcon.jpg" />
					MEET OUR <span>FASHION</span> <span class="pink">EXPERTS</span>
			</div>
			<div class="expertList">
				<ul class="expertSectoinSlider" id="responsiveExpertSectoinSlider">
				<?php 
				// Calling function to get author list from blog/apis.php
				
				if($detect->isMobile()) {
					$authorPerRow = 2;
				}				
				else {
					$authorPerRow = 5;
				}
				
				$authors= fc_get_users();
				fc_reconnect_db();
				$total_authors = count($authors);
				if($total_authors>0){ 
					$i = 1;
					$total_authors_processed = 0;
					foreach($authors as $author){		
						$total_authors_processed++;
						if($i==1){
							echo '<li>';
						}
				?>
				
						<div class="expertInformation">
							<div class="expertThumb">
								<a href="<?php echo BLOG_URL?>?author=<?php echo $author['ID'];?>">
									<?php //Image link if author image is present otherwise show the default image
										$img_link =isset($author['Author_Profile_Picture']) && $author['Author_Profile_Picture']!="" ? $author['Author_Profile_Picture'] : SITE_URL.'img/hangerIconSmall.png' ; 
									?>
									<img width="92" height="92" src='<?php echo $img_link;?>'/>
								</a>
							</div>
							<?php 
								if($author['first_name']!="")
									$username = $author['first_name'];
								//$username = trim($author['First_Name'].' '.substr($author['Last_name'], 0, 1));
								elseif($author['user_nicename']!=''){
									$username = $author['user_nicename'];
								}
							?>
							<!-- Information about the author: authorname,city,state -->
							<div class="expertName">
								<?php 
									if($username!="") 
										echo $username;
								?>
							</div>
							<?php 
							unset($author_address);
							if($author['author_city']!=""){
								$author_address[] = $author['author_city'];								
							}
							if($author['author_state']!=""){
								$author_address[] = $author['author_state'];								
							}
							if(isset($author_address)){
							?>
							<div class="expertLocation">
								<?php echo implode(', ', $author_address);?>
							</div>
							<?php } ?>
							<div class="expertBlog"><a href="<?php echo BLOG_URL?>?author=<?php echo $author['ID'];?>">Read Posts</a></div>
						</div>							
						
						
						
						
				<?php
						
						if($i==$authorPerRow || $total_authors_processed==$total_authors){
							echo '</li>';
						}
						
						if($i==$authorPerRow)
							$i = 1;
						else 
							$i++;  
															
					}
				?>							
				<?php } ?>				
				</ul>
				<div class="cb"></div>
			</div>
			<script>
				$('#responsiveExpertSectoinSlider').bxSlider({
						auto: false,
						pause: 1000,
						speed: 920,
						pager: false // carasuls
				});
			</script>
			<div class="cb"></div>
		</div>
		<?php } ?>
				
				
		<?php /* ?>
		<div id="slider">
			<ul>
				<li><img src="<?php echo SITE_URL; ?>images/slide01.jpg" alt="" /></li>
				<li><img src="<?php echo SITE_URL; ?>images/slide02.jpg" alt="" /></li>
				<li><img src="<?php echo SITE_URL; ?>images/slide03.jpg" alt="" /></li>
			</ul>
		</div>
		<center><img src="<?php echo SITE_URL; ?>images/slider_shadow.png" /></center>
		<div style="clear: both"></div>


		<?php

			// hide welcome text from registered users
			if (!isLoggedIn())
			{
				$content = GetContent('home');
				echo $content['text'];
			}

		?>

		<?php

			if (FEATURED_STORES_LIMIT > 0)
			{
				// show featured retailers //
				$result_featured = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE featured='1' AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' ORDER BY RAND() LIMIT ".FEATURED_STORES_LIMIT);
				$total_fetaured = mysql_num_rows($result_featured);

				if ($total_fetaured > 0) { 
		?>
			<div style="clear: both;"></div>
			<h3 class="brd"><?php echo CBE1_HOME_FEATURED_STORES; ?></h3>
			<div class="featured_stores">
			<?php while ($row_featured = mysql_fetch_array($result_featured)) { ?>
				<div>
					<?php echo $row_featured['cashback'];?>&nbsp;Cashback
				</div>
				<div class="imagebox">
					<!-- <a href="<?php echo GetRetailerLink($row_featured['retailer_id'], $row_featured['title']); ?>"><img src="<?php if (!stristr($row_featured['image'], 'http')) echo SITE_URL."img/"; echo $row_featured['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row_featured['title']; ?>" title="<?php echo $row_featured['title']; ?>" border="0" /></a>-->
					<a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row_featured['retailer_id']; ?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>><img src="<?php if (!stristr($row_featured['image'], 'http')) echo SITE_URL."img/"; echo $row_featured['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row_featured['title']; ?>" title="<?php echo $row_featured['title']; ?>" border="0" /></a>
				</div>
			<?php } ?>
			</div>
		<?php
				}
			} // end featured retailers 
		?>
		
		<?php

		// show featured retailers
		$result_trending_sales = smart_mysql_query("SELECT sale.created,sale.title, sale.description, sale.trending_sale_id, retailer.title as retailer_title, retailer.retailer_id, retailer.cashback FROM cashbackengine_trending_sales as sale 
		join cashbackengine_retailers as retailer 
		on retailer.retailer_id = sale.retailer_id 
		ORDER BY sale.trending_sale_id desc LIMIT 3");
		$total_trending_sales = mysql_num_rows($result_trending_sales);

		if ($total_trending_sales > 0) { 
		?>
			<div style="clear: both;"></div>
			<h3 class="brd">Trending Sales </h3>
			<div class="featured_stores">
			<?php while ($row_sale = mysql_fetch_array($result_trending_sales)) { ?>
				<div style="float: left; width: 200px;">
					<div>
						<a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row_sale['retailer_id']; ?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>>
							<?php echo $row_sale['title']?>
						</a>
					</div>
					<div><?php echo $row_sale['description']?></div>
					<div>+</div>
					<div><?php echo $row_sale['cashback'];?>&nbsp;Cashback</div>
				</div>
			<?php } ?>
				<div style="clear: both"></div>
			</div>
		<?php
			} // end trending sales 
		?>
		<div style="clear: both;"></div>
			<h3 class="brd">Authors </h3>
		
	<?php
	 	// Calling function to get author list from blog/apis.php
	 		
		$authors= fc_get_users();
		fc_reconnect_db();
		if(count($authors) > 0)
		{
			foreach($authors as $author)
			{
				?>
				<div style="float:left">
					<div>
						<a href="<?php echo BLOG_URL?>?author=<?php echo $author['ID'];?>"><img src='<?php echo $author['Author_Profile_Picture'];?>'/></a>
					</div>
					<div>
						First Name: <?php echo $author['First_Name'];?>
					</div>
					<div>
						Last Name: <?php echo $author['Last_Name'];?>
					</div>
					<div>
						Nice Name: <?php echo $author['user_nicename'];?>
					</div>
					
				</div>
				<?php
			}
		}
		else 
		{
			echo 'No Author';
		}
	?>

		<?php
			if (TODAYS_COUPONS_LIMIT > 0)
			{
				// show today's top coupons //
				$result_todays_coupons = smart_mysql_query("SELECT c.*, DATE_FORMAT(c.end_date, '%d %b %Y') AS coupon_end_date, UNIX_TIMESTAMP(c.end_date) - UNIX_TIMESTAMP() AS time_left, c.title AS coupon_title, r.image, r.title FROM cashbackengine_coupons c LEFT JOIN cashbackengine_retailers r ON c.retailer_id=r.retailer_id WHERE (c.start_date<=NOW() AND (c.end_date='0000-00-00 00:00:00' OR c.end_date > NOW())) AND c.status='active' AND (r.end_date='0000-00-00 00:00:00' OR r.end_date > NOW()) AND r.status='active' ORDER BY RAND() LIMIT ".TODAYS_COUPONS_LIMIT);
				$total_todays_coupons = mysql_num_rows($result_todays_coupons);

				if ($total_todays_coupons > 0) { 
		?>
			<div style="clear: both;"></div>
			<h3 class="brd"><?php echo CBE1_HOME_TOP_COUPONS; ?></h3>
			<table align="center" width="100%" border="0" cellspacing="0" cellpadding="5">
			<?php while ($row_todays_coupons = mysql_fetch_array($result_todays_coupons)) { ?>
				<tr>
					<td class="td_coupon" width="125" align="center" valign="top">
						<div class="imagebox"><a href="<?php echo GetRetailerLink($row_todays_coupons['retailer_id'], $row_todays_coupons['title']); ?>"><img src="<?php if (!stristr($row_todays_coupons['image'], 'http')) echo SITE_URL."img/"; echo $row_todays_coupons['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row_todays_coupons['title']; ?>" title="<?php echo $row_todays_coupons['title']; ?>" border="0" /></a></div>
						<br/><a class="more" href="<?php echo GetRetailerLink($row_todays_coupons['retailer_id'], $row_todays_coupons['title']); ?>#coupons"><?php echo CBE1_COUPONS_SEEALL; ?></a>
					</td>
					<td class="td_coupon" align="left" valign="top">
						<a class="retailer_title" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row_todays_coupons['retailer_id']; ?>&c=<?php echo $row_todays_coupons['coupon_id']; ?>" target="_blank"><b><?php echo $row_todays_coupons['title']; ?></b></a>
						<p><?php echo $row_todays_coupons['coupon_title']; ?></p>
						<?php if ($row_todays_coupons['code'] != "") { ?>
							<b><?php echo CBE1_COUPONS_CODE; ?></b>: <span class="coupon_code"><?php echo (isLoggedIn()) ? $row_todays_coupons['code'] : CBE1_COUPONS_CODE_HIDDEN; ?></span><br/><br/>
						<?php } ?>
						<a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row_todays_coupons['retailer_id']; ?>&c=<?php echo $row_todays_coupons['coupon_id']; ?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>><?php echo ($row_todays_coupons['code'] != "") ? CBE1_COUPONS_LINK : CBE1_COUPONS_LINK2; ?></a>
						<?php if ($row_todays_coupons['end_date'] != "0000-00-00 00:00:00") { ?>
							<span class="expires"><?php echo CBE1_COUPONS_EXPIRES; ?>: <?php echo $row_todays_coupons['coupon_end_date']; ?></span> &nbsp; 
							<span class="time_left"><?php echo CBE1_COUPONS_TIMELEFT; ?>: <?php echo GetTimeLeft($row_todays_coupons['time_left']).""; ?></span>
						<?php } ?>
						<?php if ($row_todays_coupons['description'] != "") { ?>
							<p><?php echo $row_todays_coupons['description']; ?></p>
						<?php } ?>
					</td>
				</tr>
			<?php } ?>
			</table>
		<?php
				}
			} // end today's top coupons
		?>


		<?php

			if (HOMEPAGE_REVIEWS_LIMIT > 0)
			{
				// Show recent reviews //
				$reviews_query = "SELECT r.*, DATE_FORMAT(r.added, '%e/%m/%Y') AS review_date, u.user_id, u.username, u.fname, u.lname FROM cashbackengine_reviews r LEFT JOIN cashbackengine_users u ON r.user_id=u.user_id WHERE r.status='active' ORDER BY r.added DESC LIMIT ".HOMEPAGE_REVIEWS_LIMIT;
				$reviews_result = smart_mysql_query($reviews_query);
				$reviews_total = mysql_num_rows($reviews_result);

				if ($reviews_total > 0) {
		?>
			<div style="clear: both"></div>
			<h3 class="brd"><?php echo CBE1_HOME_RECENT_REVIEWS; ?></h3>
			<?php while ($reviews_row = mysql_fetch_array($reviews_result)) { ?>
            <div id="review">
                <span class="review-author"><?php echo $reviews_row['fname']; ?></span>
				<span class="review-date"><?php echo $reviews_row['review_date']; ?></span><br/><br/>
				<b><a href="<?php echo GetRetailerLink($reviews_row['retailer_id'], GetStoreName($reviews_row['retailer_id'])); ?>"><?php echo GetStoreName($reviews_row['retailer_id']); ?></a></b><br/>
				<img src="<?php echo SITE_URL; ?>images/icons/rating-<?php echo $reviews_row['rating']; ?>.gif" />&nbsp;
				<span class="review-title"><?php echo $reviews_row['review_title']; ?></span><br/>
				<div class="review-text"><?php echo $reviews_row['review']; ?></div>
                <div style="clear: both;"></div>
            </div>
			<?php } ?>
			<div style="clear: both"></div>
		<?php
				}
			}
		?>
		
		<?php $allbanners = array(); $allbanners = BannersList(0);
?>

<div id="slider">
			<ul>
				<?php foreach ($allbanners as $banner){?>
				<li>
					<a href="
					<?php 
					if($banner['link']!="")
						echo $banner['link'];
					else 
						echo $banner['url'];		
					?>" target="blank">
					<img src="<?php echo SITE_URL.'admin/'.$banner['image']?>" alt=""/></a>
				</li>
				<?php 	}?>
		<?php */ ?>
		<script>
		$(function(){
				$('.contentSlider').bxSlider({
						auto: true,
						pause: 2000,
						speed: BANNER_SPEED,
						pager: false, // carasuls
						controls: false,
						pause: 7000, // By default 4000ms
						autoHover : true<?php if(count($allbanners)<2){?>,
						infiniteLoop: false 
						<?php }?>
				});				
			})
				
		$(function(){
				$('.slideLoader').show();
					<?php if( $detect->isMobile() && !$detect-> isTablet()){ ?>				
					// For Mobile Versions				
					var noOfColumns = 2;
					<?php } else { ?>
					// For Desktops Versions		
					var noOfColumns = 3;
					<?php } ?>	
						$.ajax({
							url: "responsiveSlider.php",
							data: {
								dataColumns: noOfColumns
							}
							}).done(function (res) { 										
										<?php if( $detect->isMobile() && !$detect-> isTablet()){ ?>
											$('.slideLoader').hide();
											$('.responsiveSlider').append(res);
										<?php } else { ?>
											$('.slideLoader').hide();
											$('.notResponsiveSlider').append(res);
										<?php } ?>
							});			
				});
	</script>
<?php require_once("inc/footer.inc.php"); ?>