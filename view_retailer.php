<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("inc/config.inc.php");
	require_once("inc/pagination.inc.php");

	$slug = $_GET['r'];

	//$query = "SELECT *, DATE_FORMAT(added, '%e %b %Y') AS date_added FROM cashbackengine_retailers WHERE retailer_id='$retailer_id' AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' LIMIT 1";
	$query = "SELECT *, DATE_FORMAT(added, '%e %b %Y') AS date_added FROM cashbackengine_retailers WHERE retailer_slug='$slug' AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' LIMIT 1";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	if ($total > 0)
	{
		$row = mysql_fetch_array($result);

		//$cashback = DisplayCashback($row['cashback']);

		$cashback_type = GetCashbackType($row['cashback']);
	  	$cashback = RemoveCashbackType($row['cashback']);
		
	  	if ($cashback != "")
			$ptitle	= $row['title'].". ".CBE1_STORE_EARN." ".$cashback." ".CBE1_CASHBACK2;
		else
			$ptitle	= $row['title'];

		$retailer_url = GetRetailerLink($row['retailer_id'], $row['title']);
		$retailer_id = $row['retailer_id'];
		//// ADD REVIEW //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if (isset($_POST['action']) && $_POST['action'] == "add_review" && isLoggedIn())
		{
			$userid			= (int)$_SESSION['userid'];
			//$retailer_id	= (int)getPostParameter('retailer_id');
			$rating			= (int)getPostParameter('rating');
			$review_title	= mysql_real_escape_string(getPostParameter('review_title'));
			$review			= mysql_real_escape_string(nl2br(trim(getPostParameter('review'))));
			$review			= ucfirst(strtolower($review));

			$retailer_id = $row['retailer_id'];
			
			unset($errs);
			$errs = array();

			if (!($userid && $retailer_id && $slug && $rating && $review_title && $review))
			{
				$errs[] = CBE1_REVIEW_ERR;
			}
			else
			{
				$number_lines = count(explode("<br />", $review));
				
				if (strlen($review) > MAX_REVIEW_LENGTH)
					$errs[] = str_replace("%length%",MAX_REVIEW_LENGTH,CBE1_REVIEW_ERR2);
				else if ($number_lines > 5)
					$errs[] = CBE1_REVIEW_ERR3;
				else if (stristr($review, 'http'))
					$errs[] = CBE1_REVIEW_ERR4;
			}

			if (count($errs) == 0)
			{
				$review = substr($review, 0, MAX_REVIEW_LENGTH);
				$check_review = mysql_num_rows(smart_mysql_query("SELECT * FROM cashbackengine_reviews WHERE retailer_slug='$slug' AND user_id='$userid'"));

				if ($check_review == 0)
				{
					(REVIEWS_APPROVE == 1) ? $status = "pending" : $status = "active";
					$review_query = "INSERT INTO cashbackengine_reviews SET retailer_slug='$slug', rating='$rating', user_id='$userid', review_title='$review_title', review='$review', status='$status', added=NOW()";
					$review_result = smart_mysql_query($review_query);
					$review_added = 1;

					// send email notification //
					if (NEW_REVIEW_ALERT == 1) 
					{
						SendEmail(SITE_ALERTS_MAIL, CBE1_EMAIL_ALERT2, CBE1_EMAIL_ALERT2_MSG);
					}
					/////////////////////////////
				}
				else
				{
					$errormsg = CBE1_REVIEW_ERR5;
				}

				unset($_POST['review']);
			}
			else
			{
				$errormsg = "";
				foreach ($errs as $errorname)
					$errormsg .= "&#155; ".$errorname."<br/>";
			}
		}
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	}
	else
	{
		$ptitle = CBE1_STORE_NOT_FOUND;
	}


	///////////////  Page config  ///////////////
	$PAGE_TITLE			= $ptitle;
	$PAGE_DESCRIPTION	= $row['meta_description'];
	$PAGE_KEYWORDS		= $row['meta_keywords'];

	require_once ("inc/header.inc.php");

?>	

	<?php

		if ($total > 0) {

	?>
	
	<div class="container content standardContainer blog">
		<!-- Featured Store List -->			
		
		<div class="cb"></div>
		<div class="SiteContentSection">
			<div class="SiteContentLeft">
				<div class="RetailerContainer">
					<h1>RETAILER</h1>
					<div class="RetailerIcon">
						<div>
							<?php /*if ($row['featured'] == 1) { ?><span class="featured" alt="<?php echo CBE1_FEATURED_STORE; ?>" title="<?php echo CBE1_FEATURED_STORE; ?>"></span><?php } */ ?>
							<div class="imagebox"><a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>><img src="<?php if (!stristr($row['image_II'], 'http')) echo SITE_URL."admin/upload/retailer/"; echo $row['image_II']; ?>" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" border="0" /></a></div>
							<?php echo GetStoreRating($row['retailer_id'], $show_start = 1); ?>
						</div>
					</div>
					<div class="RetailerCashBack">
						<div class="percentageCount"><span class="upToText"><i></i></span>
							<?php echo $cashback?>					
							<span class="percentageCountSymbol"><?php echo $cashback_type;?></span></div>
						<div class="cashBackCaption">Cash Back</div>
					</div>
					<div class="cb"></div>
					<div class="RetailerDescription notResponsive">
						<?php echo $row['description'];?>
					</div>
					<div class="RetailerShopNow RetailerShopNowClear">
						<div class="shopNowLeft">
							<div class="shopNowBotton siteButton">
								<a href="<?php echo SITE_URL;?>go2store.php?id=<?php echo $row['retailer_id'];?>&rURL=<?php echo $row['url'];?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>>
									<span>SHOP NOW</span>
								</a>
							</div>
						</div>
						<div class="shopNowRight">
							<div class="blogSocialIcons">
								<!-- <a href=""><img src="<?php echo SITE_URL;?>/img/blog/fb.png" alt=""/></a>
								<a href=""><img src="<?php echo SITE_URL;?>/img/blog/twitter.png" alt=""/></a>
								<a href=""><img src="<?php echo SITE_URL;?>/img/blog/pinterest.png" alt=""/></a>
								<a href=""><img src="<?php echo SITE_URL;?>/img/blog/comments.png" alt=""/></a>
								<span class="countContainer">2</span>-->
							 </div>
						</div>
						<div class="cb"></div>
					</div>					
					<!-- OLD CODE STARTS -->
		<?php
				// start coupons //
				$ee = 0;
				$query_coupons = "SELECT *, DATE_FORMAT(end_date, '%m/%d/%Y') AS coupon_end_date, UNIX_TIMESTAMP(end_date) - UNIX_TIMESTAMP() AS time_left FROM cashbackengine_coupons WHERE retailer_id='$retailer_id' AND (start_date<=NOW() AND (end_date='0000-00-00 00:00:00' OR end_date > NOW())) AND status='active' ORDER BY sort_order, added DESC";
				$result_coupons = smart_mysql_query($query_coupons);
				$total_coupons = mysql_num_rows($result_coupons);

				if ($total_coupons > 0)
				{
		?>
			<a name="coupons"></a>
			<!--<h3 class="store_coupons"><?php echo CBE1_STORE_COUPONS; ?></h3>-->
			<div class="RetailerOffersHeading"><?php echo $row['title']; ?> <?php echo CBE1_STORE_COUPONS; ?> &#x0026; Specials</div>
			
			<table class="RetailerOffersTable oneColumnTableResponse">
				<?php while ($row_coupons = mysql_fetch_array($result_coupons)) { $ee++; ?>
						<tr>
							<td class="columnOne">
								<div class="discountTitle">Discount:</div>
							</td>
							<td class="columnTwo">
								<div class="offerName">
									<!-- <a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>&c=<?php echo $row_coupons['coupon_id']; ?>" target="_blank"><b><?php echo $row_coupons['title']; ?></b></a>-->
								<span class="offerExpiryDate"><?php if ($row_coupons['end_date'] != "0000-00-00 00:00:00") { ?>
							<?php } ?></span></div>
								<div class="offerDescription"><?php echo $row_coupons['description'];?></div>
								<div class="cashBackOnOffer">Plus <span><?php echo $cashback.$cashback_type;?></span> Cash Back</div>
								<div class="requirement">
									<?php if ($row_coupons['code'] != "") { ?>
									<?php echo (isLoggedIn()) ? $row_coupons['code'] : CBE1_COUPONS_CODE_HIDDEN; ?>
										<?php } else {?>
										No Coupon Code Required
									<?php }?>
									<br/><span class="cashBackOnOffer">Expiers <?php echo $row_coupons['coupon_end_date']; ?></span>
								</div>
								<div class="shopNowBotton siteButton isResponsive">
									<a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>&c=<?php echo $row_coupons['coupon_id']; ?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>><span>SHOP NOW</span></a>
								</div>
							</td>
							<td class="columnThree notResponsive">
								<div class="shopNowBotton siteButton">
									<a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>&c=<?php echo $row_coupons['coupon_id']; ?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>><span>SHOP NOW</span></a>
								</div>
							</td>
						</tr>

				<?php } ?>
			
		<?php } // end coupons // ?>
			
					</table>
					
				<div class="RetailerDescription RetailerDescriptionDown isResponsive">
					<?php echo $row['description'];?>
				</div>

					
					
				</div>
				<!-- Old code -->		
				<div class="cb"></div>
			</div>
		<!-- Right side bar -->	
		<?php require_once ("inc/right_sidebar.php"); ?>
			
			
			<?php /*?>
			<div class="SiteContentRight">
				<!-- Sidebar Section -->
				<div>
					<!-- Follow Us Section -->				
					<div class="followUs">
						<div class="title">------ FOLLOW US ------</div>
						<div class="shortLinks">
							<span><a href="#" class="fbLight"><img class="noncolor" alt="" src="<?php echo SITE_IMG;?>fbLight.jpg"/><img class="colorful" alt="" src="<?php echo SITE_IMG;?>fbColorLight.jpg"/></a></span><span><a href="#" class="twtLight"><img class="noncolor" alt="" src="<?php echo SITE_IMG;?>twtLight.jpg"/><img class="colorful" alt="" src="<?php echo SITE_IMG;?>twtColorLight.jpg"/></a></span><span><a href="#" class="gpLight"><img class="noncolor" alt="" src="<?php echo SITE_IMG;?>gpLight.jpg"/><img class="colorful" alt="" src="<?php echo SITE_IMG;?>gpColorLight.jpg"/></a></span><span><a href="#" class="piLight"><img class="noncolor" alt="" src="<?php echo SITE_IMG;?>piLight.jpg"/><img class="colorful" alt="" src="<?php echo SITE_IMG;?>piColorLight.jpg"/></a></span>
						</div>
					</div>
					<!-- Sign Up Section -->
					<div class="signUpContainer standardSignUpContainer">
						<div class="title titleOf14">SHOP &#x0026; EARN CASH BACK!</div>
						<div class="body standardBody">
							<div class="howItWorks">HOW IT WORKS</div>
							<div class="howItWorksSteps">
								<div class="stepSection howItWorksStepOne">
									<div><img src="<?php echo SITE_IMG;?>stepOneDark.jpg" alt=""/></div>
									<div class="stepTitle">Sign up<br/><span>(it&#x2019;s free)</span></div>
								</div>
								<div class="stepSection howItWorksStepTwo">
									<div><img src="<?php echo SITE_IMG;?>stepTwoDark.jpg" alt=""/></div>
									<div class="stepTitle">Select a Store &#x0026; Shop</div>
								</div>
								<div class="stepSection last howItWorksStepThree">
									<div><img src="<?php echo SITE_IMG;?>stepThreeDark.jpg" alt=""/></div>
									<div class="stepTitle">Get Cash Back!</div>
								</div>
								<div class="cb"></div>
							</div>						
							<div class="allStores forSignUp">
								<a href="#">
									<span>SIGN UP</span> </a> </div>
						</div>
					</div>
					<div class="advertisement300">
						<img src="<?php echo SITE_IMG;?>ad300.jpg" alt=""/>
					</div>
				</div>
			</div>
			<?php */?>
			
			<div class="cb"></div>
		</div>
	</div>	
	
	

	<?php }else{ ?>
		<h1><?php echo $ptitle; ?></h1>
		<p align="center"><?php echo CBE1_STORE_NOT_FOUND2; ?></p>
	<?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>