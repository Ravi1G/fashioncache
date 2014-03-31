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


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$retailer_id = (int)$_GET['id'];
	}
	else
	{		
		header ("Location: index.php");
		exit();
	}

	$query = "SELECT *, DATE_FORMAT(added, '%e %b %Y') AS date_added FROM cashbackengine_retailers WHERE retailer_id='$retailer_id' AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' LIMIT 1";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	if ($total > 0)
	{
		$row = mysql_fetch_array($result);

		$cashback = DisplayCashback($row['cashback']);
		
		if ($cashback != "")
			$ptitle	= $row['title'].". ".CBE1_STORE_EARN." ".$cashback." ".CBE1_CASHBACK2;
		else
			$ptitle	= $row['title'];

		$retailer_url = GetRetailerLink($row['retailer_id'], $row['title']);

		//// ADD REVIEW //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if (isset($_POST['action']) && $_POST['action'] == "add_review" && isLoggedIn())
		{
			$userid			= (int)$_SESSION['userid'];
			$retailer_id	= (int)getPostParameter('retailer_id');
			$rating			= (int)getPostParameter('rating');
			$review_title	= mysql_real_escape_string(getPostParameter('review_title'));
			$review			= mysql_real_escape_string(nl2br(trim(getPostParameter('review'))));
			$review			= ucfirst(strtolower($review));

			unset($errs);
			$errs = array();

			if (!($userid && $retailer_id && $rating && $review_title && $review))
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
				$check_review = mysql_num_rows(smart_mysql_query("SELECT * FROM cashbackengine_reviews WHERE retailer_id='$retailer_id' AND user_id='$userid'"));

				if ($check_review == 0)
				{
					(REVIEWS_APPROVE == 1) ? $status = "pending" : $status = "active";
					$review_query = "INSERT INTO cashbackengine_reviews SET retailer_id='$retailer_id', rating='$rating', user_id='$userid', review_title='$review_title', review='$review', status='$status', added=NOW()";
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
		<div class="saleAlertSection">
			<div class="sectionTitle"><span>SALE ALERT!</span> Shop the Nordstroms Half Anniversary sale going on now</div>
		</div>
		<div class="cb"></div>
		<div class="SiteContentSection">
			<div class="SiteContentLeft">
				<div class="RetailerContainer">
					<h1><?php echo $ptitle; ?></h1>
					<div class="RetailerIcon">
						<div><img alt="" src="<?php echo SITE_IMG;?>retailerLogo.png"/></div>
					</div>
					<div class="RetailerCashBack">
						<div class="percentageCount"><span class="upToText"><i></i></span> <?php echo $cashback?><span class="percentageCountSymbol">%</span></div>
						<div class="cashBackCaption">Cash Back</div>
					</div>
					<div class="cb">
						<?php /*if ($row['featured'] == 1) { ?><span class="featured" alt="<?php echo CBE1_FEATURED_STORE; ?>" title="<?php echo CBE1_FEATURED_STORE; ?>"></span><?php } */ ?>
						<div class="imagebox"><a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>><img src="<?php if (!stristr($row['image'], 'http')) echo SITE_URL."img/"; echo $row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" border="0" /></a></div>
						<?php echo GetStoreRating($row['retailer_id'], $show_start = 1); ?>
					</div>
					<div class="RetailerDescription"><?php echo $row['description']; ?></div>
					<div class="RetailerShopNow">
						<div class="shopNowLeft">
							<div class="shopNowBotton siteButton">
								<a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>>
									<span>SHOP NOW</span>
								</a>
							</div>
						</div>
						<div class="shopNowRight">
							<div class="blogSocialIcons">
								<a href=""><img src="<?php echo SITE_IMG;?>blog/fb.png" alt=""/></a>
								<a href=""><img src="<?php echo SITE_IMG;?>blog/twitter.png" alt=""/></a>
								<a href=""><img src="<?php echo SITE_IMG;?>blog/pinterest.png" alt=""/></a>
								<a href=""><img src="<?php echo SITE_IMG;?>blog/comments.png" alt=""/></a>
								<span class="countContainer">2</span> </div>
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
			<h3 class="store_coupons"> <?php echo CBE1_STORE_COUPONS; ?></h3>
			<div class="RetailerOffersHeading"><?php echo $row['title']; ?><?php echo CBE1_STORE_COUPONS; ?> &#x0026; Specials</div>
			
			<table class="RetailerOffersTable">
				<?php while ($row_coupons = mysql_fetch_array($result_coupons)) { $ee++; ?>
						<tr>
							<td class="columnOne">
								<div class="discountTitle">Discount:</div>
							</td>
							<td class="columnTwo">
								<div class="offerName"><a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>&c=<?php echo $row_coupons['coupon_id']; ?>" target="_blank"><b><?php echo $row_coupons['title']; ?></b></a> 
								<span class="offerExpiryDate"><?php if ($row_coupons['end_date'] != "0000-00-00 00:00:00") { ?>
								<span class="expires">(exp. <?php echo $row_coupons['coupon_end_date']; ?>)</span> 
								
							<?php } ?></span></div>
								<div class="cashBackOnOffer">Plus <span>5.0%</span> Cash Back</div>
								<div class="requirement">No Coupon Code Required</div>
							</td>
							<td class="columnThree">
								<div class="shopNowBotton siteButton">
									<a href="#"><span>SHOP NOW</span></a>
								</div>
							</td>
						</tr>
				
				<li class="coupon">
					<span class="scissors"></span>
					<?php if ($row_coupons['exclusive'] == 1) { ?><span class="exclusive" alt="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>" title="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>"><?php echo CBE1_COUPONS_EXCLUSIVE; ?></span><br/><br/><?php } ?>
					<a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>&c=<?php echo $row_coupons['coupon_id']; ?>" target="_blank"><b><?php echo $row_coupons['title']; ?></b></a>
					<?php if ($row_coupons['code'] != "") { ?>
						<div class="coupon_code2" id="coupon_code2"><?php echo (isLoggedIn()) ? $row_coupons['code'] : CBE1_COUPONS_CODE_HIDDEN; ?></div><br/><br/>
						<small><?php echo CBE1_COUPONS_MSG; ?></small><br/>
					<?php } ?>
					<a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>&c=<?php echo $row_coupons['coupon_id']; ?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>><?php echo ($row_coupons['code'] != "") ? CBE1_COUPONS_LINK : CBE1_COUPONS_LINK2; ?></a><br/><br/>
					<?php if ($row_coupons['end_date'] != "0000-00-00 00:00:00") { ?>
						<span class="expires"><?php echo CBE1_COUPONS_EXPIRES; ?>: <?php echo $row_coupons['coupon_end_date']; ?></span> &nbsp; 
						<span class="time_left"><?php echo CBE1_COUPONS_TIMELEFT; ?>: <?php echo GetTimeLeft($row_coupons['time_left']).""; ?></span>
					<?php } ?>
					<?php if ($row_coupons['description'] != "") { ?><p><?php echo $row_coupons['description']; ?></p><?php } ?>
				</li>
				<?php } ?>
			</ul>
			<div style="clear: both"></div>
		<?php } // end coupons // ?>
					<!-- OLD CODE ENDS -->
					
					
					
					
					
					
						<tr>
							<td class="columnOne">
								<div class="discountTitle">Discount:</div>
							</td>
							<td class="columnTwo">
								<div class="offerName">Men&#x2019;s Half-Yearly Sale Men. <span class="offerExpiryDate">(exp. 01/01/2014)</span></div>
								<div class="cashBackOnOffer">Plus <span>5.0%</span> Cash Back</div>
								<div class="requirement">No Coupon Code Required</div>
							</td>
							<td class="columnThree">
								<div class="shopNowBotton siteButton">
									<a href="#"><span>SHOP NOW</span></a>
								</div>
							</td>
						</tr>
						<tr>
							<td class="columnOne">
								<div class="discountTitle">Discount:</div>
							</td>
							<td class="columnTwo">
								<div class="offerName">Men&#x2019;s Half-Yearly Sale Men. <span class="offerExpiryDate">(exp. 01/01/2014)</span></div>
								<div class="cashBackOnOffer">Plus <span>5.0%</span> Cash Back</div>
								<div class="requirement">No Coupon Code Required</div>
							</td>
							<td class="columnThree">
								<div class="shopNowBotton siteButton">
									<a href="#"><span>SHOP NOW</span></a>
								</div>
							</td>
						</tr>
						<tr>
							<td class="columnOne">
								<div class="discountTitle">Discount:</div>
							</td>
							<td class="columnTwo">
								<div class="offerName">Men&#x2019;s Half-Yearly Sale Men. <span class="offerExpiryDate">(exp. 01/01/2014)</span></div>
								<div class="cashBackOnOffer">Plus <span>5.0%</span> Cash Back</div>
								<div class="requirement">No Coupon Code Required</div>
							</td>
							<td class="columnThree">
								<div class="shopNowBotton siteButton">
									<a href="#"><span>SHOP NOW</span></a>
								</div>
							</td>
						</tr>
						<tr class="lastRow">
							<td class="columnOne">
								<div class="discountTitle">Discount:</div>
							</td>
							<td class="columnTwo">
								<div class="offerName">Men&#x2019;s Half-Yearly Sale Men. <span class="offerExpiryDate">(exp. 01/01/2014)</span></div>
								<div class="cashBackOnOffer">Plus <span>5.0%</span> Cash Back</div>
								<div class="requirement">No Coupon Code Required</div>
							</td>
							<td class="columnThree">
								<div class="shopNowBotton siteButton">
									<a href="#"><span>SHOP NOW</span></a>
								</div>
							</td>
						</tr>
					</table>
				</div>
				<div class="cb"></div>
			</div>
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
			<div class="cb"></div>
		</div>
	</div>
	
	<!-- Old code -->		

			<div class="breadcrumbs"><a href="<?php echo SITE_URL; ?>" class="home_link"><?php echo CBE1_BREADCRUMBS_HOME; ?></a> &#155; <a href="<?php echo SITE_URL; ?>retailers.php"><?php echo CBE1_BREADCRUMBS_STORES; ?></a> &#155; <?php echo $row['title']; ?></div>

			<div id="alphabet">
			<ul>
				<li><a href="<?php echo SITE_URL; ?>retailers.php"><?php echo CBE1_STORES_ALL; ?></a></li>
				<?php

					$numLetters = count($alphabet);
					$i = 0;

					foreach ($alphabet as $letter)
					{
						$i++;
						if ($i == $numLetters) $lilast = ' class="last"'; else $lilast = '';

						$ltr = $row['title'][0];

						if (isset($ltr) && ucfirst($ltr) == $letter) $liclass = ' class="active"'; else $liclass = '';
			
						if (isset($cat_id) && is_numeric($cat_id))
							echo "<li".$lilast."><a href=\"".SITE_URL."retailers.php?cat=$cat_id&letter=$letter\" $liclass>$letter</a></li>";
						else
							echo "<li".$lilast."><a href=\"".SITE_URL."retailers.php?letter=$letter\" $liclass>$letter</a></li>";
					}

				?>
			</ul>
			</div>

			<table align="center" width="100%" border="0" cellspacing="0" cellpadding="5">
				<tr class="odd">
					<td width="125" align="center" valign="top">
						<?php if ($row['featured'] == 1) { ?><span class="featured" alt="<?php echo CBE1_FEATURED_STORE; ?>" title="<?php echo CBE1_FEATURED_STORE; ?>"></span><?php } ?>
						<div class="imagebox"><a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>><img src="<?php if (!stristr($row['image'], 'http')) echo SITE_URL."img/"; echo $row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" border="0" /></a></div>
						<a class="coupons" href="#coupons"><?php echo GetStoreCouponsTotal($row['retailer_id']); ?></a> <?php echo CBE1_STORE_COUPONS1; ?><br/><br/>
						<b><a href="#reviews" style="color: #707070"><?php echo GetStoreReviewsTotal($row['retailer_id']); ?></a> <?php echo CBE1_STORE_REVIEWS1; ?></b><br/>
						<?php echo GetStoreRating($row['retailer_id'], $show_start = 1); ?>
					</td>
					<td align="left" valign="bottom">
						<table width="100%" border="0" cellspacing="0" cellpadding="3">
							<tr>
								<td colspan="2" align="left" valign="middle">
									<a class="stitle" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>><?php echo $row['title']; ?></a>
								</td>
							</tr>
							<tr>
								<td width="80%" valign="top" align="left">
									<span class="retailer_description"><?php echo $row['description']; ?></span><br/>
									<?php echo GetStoreCountries($row['retailer_id']); ?>
								</td>
								<td width="20%" valign="top" align="center">
								<?php if ($cashback != "") { ?>
									<div class="cashback_box">
										<?php if ($row['old_cashback'] != "") { ?><span class="old_cashback"><?php echo DisplayCashback($row['old_cashback']); ?></span><?php } ?>
										<span class="bcashback"><?php echo $cashback; ?></span> <?php echo CBE1_CASHBACK; ?>
									</div>
								<?php } ?>
								</td>
							</tr>
							<tr>
								<td colspan="2" valign="middle" align="left">
								<?php if ($row['conditions'] != "") { ?>
									<div class="cashbackengine_tooltip">
										<a class="conditions" href="#"><?php echo CBE1_CONDITIONS; ?></a> <span class="tooltip"><?php echo $row['conditions']; ?></span>
									</div>
								<?php } ?>
									<a class="favorites" href="<?php echo SITE_URL; ?>myfavorites.php?act=add&id=<?php echo $row['retailer_id']; ?>"><?php echo CBE1_ADD_FAVORITES; ?></a>
									<a class="report" href="<?php echo SITE_URL; ?>report_retailer.php?id=<?php echo $row['retailer_id']; ?>"><?php echo CBE1_REPORT; ?></a>
									<?php if (SUBMIT_COUPONS == 1) { ?>
										<a class="submit_coupon" href="<?php echo SITE_URL; ?>submit_coupon.php?id=<?php echo $row['retailer_id']; ?>"><?php echo CBE1_STORE_COUPONS2; ?></a>
									<?php } ?>
								</td>
							</tr>
							<tr>
								<td colspan="2" height="50" align="center" valign="middle">
									<a class="go2store_large" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>><?php echo CBE1_GO_TO_STORE2; ?></a>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>

			<table align="center" width="100%" border="0" cellspacing="0" cellpadding="5">
			<tr>
				<?php if (SHOW_RETAILER_STATS == 1) { ?>
				<td nowrap="nowrap" style="border-right: 1px dotted #D7D7D7; border-bottom: 1px dotted #D7D7D7;" bgcolor="#F9F9F9" nowrap="nowrap">
					<div class="retailer_statistics">
						<center><b><?php echo CBE1_STORE_STATS; ?></b></center>
						<label><?php echo CBE1_STORE_COUPONS; ?>:</label> <?php echo GetStoreCouponsTotal($row['retailer_id']); ?><br/>
						<label><?php echo CBE1_STORE_REVIEWS; ?>:</label> <?php echo GetStoreReviewsTotal($row['retailer_id'], $all = 0, $word = 0); ?><br/>
						<label><?php echo CBE1_STORE_FAVORITES; ?>:</label> <?php echo GetFavoritesTotal($row['retailer_id']); ?><br/>
						<label><?php echo CBE1_STORE_DATE; ?>:</label> <?php echo $row['date_added']; ?><br/>
					 </div>
				</td>
				<?php } ?>
				<?php if (SHOW_CASHBACK_CALCULATOR == 1 && strstr($row['cashback'], '%')) { ?>
				<td nowrap="nowrap" style="border-right: 1px dotted #D7D7D7; border-bottom: 1px dotted #D7D7D7;" bgcolor="#F9F9F9" align="center">
					<center><b><?php echo CBE1_STORE_CCALCULATOR; ?></b></center>
					<table align="center" width="100%" border="0" cellspacing="0" cellpadding="2">
					<tr>
						<td width="50%" align="center"><?php echo CBE1_STORE_SPEND; ?></td>
						<td width="50%" align="center"><?php echo CBE1_CASHBACK; ?></td>
					</tr>
					<tr>
						<td align="center"><span class="calc_spend"><?php echo DisplayMoney("100", 0, 1); ?></span></td>
						<td align="center"><span class="calc_cashback"><?php echo DisplayMoney(CalculatePercentage(100, $cashback),0,1); ?></span></td>
					</tr>
					<tr>
						<td align="center"><span class="calc_spend"><?php echo DisplayMoney("500", 0, 1); ?></span></td>
						<td align="center"><span class="calc_cashback"><?php echo DisplayMoney(CalculatePercentage(500, $cashback),0,1); ?></span></td>
					</tr>
					<tr>
						<td align="center"><span class="calc_spend"><?php echo DisplayMoney("1000", 0, 1); ?></span></td>
						<td align="center"><span class="calc_cashback"><?php echo DisplayMoney(CalculatePercentage(1000, $cashback),0,1); ?></span></td>
					</tr>
					</table>
				</td>
				<?php } ?>
				<td nowrap="nowrap" style="border-bottom: 1px dotted #D7D7D7;" bgcolor="#F9F9F9" align="center">
					<div class="share_box">
						<!-- AddThis Button BEGIN -->
						<div class="addthis_toolbox" addthis:url="<?php echo $retailer_url; ?>" addthis:title="<?php echo $ptitle; ?>">
						<div class="addthis_toolbox addthis_default_style">
							<a class="addthis_button_facebook_like"></a>
							<a class="addthis_button_tweet"></a>
							<a class="addthis_button_google_plusone_share" g:plusone:size="medium"></a>
							<span class="addthis_separator">~</span>
							<a href="http://addthis.com/bookmark.php?v=250" class="addthis_button_expanded at300m" style="color: #999;"><?php echo CBE1_STORE_MORE; ?></a>
						</div>
						</div>
						<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=<?php echo ADDTHIS_ID; ?>"></script>
						<!-- AddThis Button END -->
					</div>
					<span style="color: #333"><?php echo CBE1_STORE_SHARE; ?>:</span>
					<input type="text" class="share_textbox" size="53" READONLY onfocus="this.select();" onclick="this.focus();this.select();" value="<?php echo $retailer_url; ?>" />
				</td>
				</tr>
			</table>


		<?php
				// start coupons //
				$ee = 0;
				$query_coupons = "SELECT *, DATE_FORMAT(end_date, '%d %b %Y') AS coupon_end_date, UNIX_TIMESTAMP(end_date) - UNIX_TIMESTAMP() AS time_left FROM cashbackengine_coupons WHERE retailer_id='$retailer_id' AND (start_date<=NOW() AND (end_date='0000-00-00 00:00:00' OR end_date > NOW())) AND status='active' ORDER BY sort_order, added DESC";
				$result_coupons = smart_mysql_query($query_coupons);
				$total_coupons = mysql_num_rows($result_coupons);

				if ($total_coupons > 0)
				{
		?>
			<a name="coupons"></a>
			<h3 class="store_coupons"><?php echo $row['title']; ?> <?php echo CBE1_STORE_COUPONS; ?></h3>

			<ul class="coupons-list">
				<?php while ($row_coupons = mysql_fetch_array($result_coupons)) { $ee++; ?>
				<li class="coupon">
					<span class="scissors"></span>
					<?php if ($row_coupons['exclusive'] == 1) { ?><span class="exclusive" alt="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>" title="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>"><?php echo CBE1_COUPONS_EXCLUSIVE; ?></span><br/><br/><?php } ?>
					<a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>&c=<?php echo $row_coupons['coupon_id']; ?>" target="_blank"><b><?php echo $row_coupons['title']; ?></b></a>
					<?php if ($row_coupons['code'] != "") { ?>
						<div class="coupon_code2" id="coupon_code2"><?php echo (isLoggedIn()) ? $row_coupons['code'] : CBE1_COUPONS_CODE_HIDDEN; ?></div><br/><br/>
						<small><?php echo CBE1_COUPONS_MSG; ?></small><br/>
					<?php } ?>
					<a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>&c=<?php echo $row_coupons['coupon_id']; ?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>><?php echo ($row_coupons['code'] != "") ? CBE1_COUPONS_LINK : CBE1_COUPONS_LINK2; ?></a><br/><br/>
					<?php if ($row_coupons['end_date'] != "0000-00-00 00:00:00") { ?>
						<span class="expires"><?php echo CBE1_COUPONS_EXPIRES; ?>: <?php echo $row_coupons['coupon_end_date']; ?></span> &nbsp; 
						<span class="time_left"><?php echo CBE1_COUPONS_TIMELEFT; ?>: <?php echo GetTimeLeft($row_coupons['time_left']).""; ?></span>
					<?php } ?>
					<?php if ($row_coupons['description'] != "") { ?><p><?php echo $row_coupons['description']; ?></p><?php } ?>
				</li>
				<?php } ?>
			</ul>
			<div style="clear: both"></div>
		<?php } // end coupons // ?>

		<?php
				// start expired coupons //
				$ee = 0;
				$query_exp_coupons = "SELECT *, DATE_FORMAT(end_date, '%d %b %Y') AS coupon_end_date, UNIX_TIMESTAMP(end_date) - UNIX_TIMESTAMP() AS time_left FROM cashbackengine_coupons WHERE retailer_id='$retailer_id' AND end_date != '0000-00-00 00:00:00' AND end_date < NOW() AND status='active' ORDER BY sort_order, added DESC";
				$result_exp_coupons = smart_mysql_query($query_exp_coupons);
				$total_exp_coupons = mysql_num_rows($result_exp_coupons);

				if ($total_exp_coupons > 0)
				{
		?>
			<h3 class="store_exp_coupons"><?php echo CBE1_STORE_ECOUPONS; ?></h3>

			<ul class="coupons-list">
				<?php while ($row_exp_coupons = mysql_fetch_array($result_exp_coupons)) { $ee++; ?>
				<li class="coupon">
					<b><?php echo $row_exp_coupons['title']; ?></b>
					<?php if ($row_exp_coupons['code'] != "") { ?>
						<div class="coupon_code2" id="coupon_code2"><?php echo $row_exp_coupons['code']; ?></div><br/><br/>
					<?php } ?>
					<br/><br/>
					<a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>&c=<?php echo $row_exp_coupons['coupon_id']; ?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>><?php echo ($row_exp_coupons['code'] != "") ? CBE1_COUPONS_LINK : CBE1_COUPONS_LINK2; ?></a>
					<?php if ($row_exp_coupons['description'] != "") { ?>
						<p><?php echo $row_exp_coupons['description']; ?></p>
					<?php } ?>
				</li>
				<?php } ?>
			</ul>
			<div style="clear: both"></div>
		<?php } // end expired coupons // ?>

		<?php
				// reviews //
				$results_per_page = REVIEWS_PER_PAGE;

				if (isset($_GET['cpage']) && is_numeric($_GET['cpage']) && $_GET['cpage'] > 0) { $page = (int)$_GET['cpage']; } else { $page = 1; }
				$from = ($page-1)*$results_per_page;

				$reviews_query = "SELECT r.*, DATE_FORMAT(r.added, '%e/%m/%Y') AS review_date, u.user_id, u.username, u.fname, u.lname FROM cashbackengine_reviews r LEFT JOIN cashbackengine_users u ON r.user_id=u.user_id WHERE r.retailer_id='$retailer_id' AND r.status='active' ORDER BY r.added DESC LIMIT $from, $results_per_page";
				$reviews_result = smart_mysql_query($reviews_query);
				$reviews_total = mysql_num_rows(smart_mysql_query("SELECT * FROM cashbackengine_reviews WHERE retailer_id='$retailer_id' AND status='active'"));
		?>

		<div id="add_review_link"><a id="add-review" href="javascript:void(0);"><?php echo CBE1_REVIEW_TITLE; ?></a></div>
		<a name="reviews"></a>
		<h3 class="store_reviews"><?php echo $row['title']; ?> <?php echo CBE1_STORE_REVIEWS; ?> <?php echo ($reviews_total > 0) ? "($reviews_total)" : ""; ?></h3>

		<script>
		$("#add-review").click(function () {
			$("#review-form").toggle("slow");
		});
		</script>

		<div id="review-form" class="review-form" style="<?php if (!(isset($_POST['action']) && $_POST['action'] == "add_review")) { ?>display: none;<?php } ?>">
			<?php if (isset($errormsg) && $errormsg != "") { ?>
				<div style="width: 91%;" class="error_msg"><?php echo $errormsg; ?></div>
			<?php } ?>
			<?php if (REVIEWS_APPROVE == 1 && $review_added == 1) { ?>
				<div style="width: 91%;" class="success_msg"><?php echo CBE1_REVIEW_SENT; ?></div>
			<?php } ?>
			<?php if (isLoggedIn()) { ?>
				<form method="post" action="#reviews">
					<select name="rating">
						<option value=""><?php echo CBE1_REVIEW_RATING_SELECT; ?></option>
						<option value="5" <?php if ($rating == 5) echo "selected"; ?>>&#9733;&#9733;&#9733;&#9733;&#9733; - <?php echo CBE1_REVIEW_RATING1; ?></option>
						<option value="4" <?php if ($rating == 4) echo "selected"; ?>>&#9733;&#9733;&#9733;&#9733; - <?php echo CBE1_REVIEW_RATING2; ?></option>
						<option value="3" <?php if ($rating == 3) echo "selected"; ?>>&#9733;&#9733;&#9733; - <?php echo CBE1_REVIEW_RATING3; ?></option>
						<option value="2" <?php if ($rating == 2) echo "selected"; ?>>&#9733;&#9733; - <?php echo CBE1_REVIEW_RATING4; ?></option>
						<option value="1" <?php if ($rating == 1) echo "selected"; ?>>&#9733; - <?php echo CBE1_REVIEW_RATING5; ?></option>					
					</select><br/>
					<?php echo CBE1_REVIEW_RTITLE; ?><br/>
					<input type="text" name="review_title" id="review_title" value="<?php echo getPostParameter('review_title'); ?>" size="47" class="textbox" /><br/>
					<?php echo CBE1_REVIEW_REVIEW; ?><br/>
					<textarea id="review" name="review" cols="45" rows="5" class="textbox2"><?php echo getPostParameter('review'); ?></textarea><br/>
					<input type="hidden" id="retailer_id" name="retailer_id" value="<?php echo $retailer_id; ?>" />
					<input type="hidden" name="action" value="add_review" />
					<input type="submit" class="submit" value="<?php echo CBE1_REVIEW_BUTTON; ?>" />
				</form>
			<?php }else{ ?>
				<?php echo CBE1_REVIEW_MSG; ?>
			<?php } ?>
		</div>

		<div style="clear: both"></div>
		<?php if ($reviews_total > 0) { ?>

			<?php while ($reviews_row = mysql_fetch_array($reviews_result)) { ?>
            <div id="review">
                <span class="review-author"><?php echo $reviews_row['fname']; ?></span>
				<span class="review-date"><?php echo $reviews_row['review_date']; ?></span><br/><br/>
				<img src="<?php echo SITE_URL; ?>images/icons/rating-<?php echo $reviews_row['rating']; ?>.gif" />&nbsp;
				<span class="review-title"><?php echo $reviews_row['review_title']; ?></span><br/>
				<div class="review-text"><?php echo $reviews_row['review']; ?></div>
                <div style="clear: both"></div>
            </div>
			<?php } ?>
		
			<?php echo ShowPagination("reviews",REVIEWS_PER_PAGE,"?id=$retailer_id&","WHERE retailer_id='$retailer_id' AND status='active'"); ?>
		
		<?php }else{ ?>
				<?php echo CBE1_REVIEW_NO; ?>
		<?php } ?>
		<br/>

		<?php
			// start related retailers //
			$query_like = "SELECT * FROM cashbackengine_retailers WHERE retailer_id<>'$retailer_id' AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' ORDER BY RAND() LIMIT 5";
			$result_like = smart_mysql_query($query_like);
			$total_like = mysql_num_rows($result_like);

			if ($total_like > 0)
			{
		?>
			<div style="clear: both"></div>
			<h3><?php echo CBE1_STORE_LIKE; ?></h3>
			<table align="center" width="100%" border="0" cellspacing="0" cellpadding="5">
			<tr>
				<?php while ($row_like = mysql_fetch_array($result_like)) { ?>
					<td class="like" width="125" align="center" valign="middle">
						<?php echo $row_like['title']; ?><br/>
						<a href="<?php echo GetRetailerLink($row_like['retailer_id'], $row_like['title']); ?>"><img src="<?php if (!stristr($row_like['image'], 'http')) echo SITE_URL."img/"; echo $row_like['image']; ?>" width="<?php echo IMAGE_WIDTH/2; ?>" height="<?php echo IMAGE_HEIGHT/2; ?>" alt="<?php echo $row_like['title']; ?>" title="<?php echo $row_like['title']; ?>" border="0" style="margin:5px;" class="imgs" /></a><br/>
						<?php if ($row_like['cashback'] != "") { ?><span class="cashback"><?php echo DisplayCashback($row_like['cashback']); ?></span> <?php echo CBE1_CASHBACK; ?><?php } ?>
					</td>
				<?php } ?>
			</tr>
			</table>
		<?php } // end related retailers // ?>

	<?php }else{ ?>
		<h1><?php echo $ptitle; ?></h1>
		<p align="center"><?php echo CBE1_STORE_NOT_FOUND2; ?></p>
		<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;"><?php echo CBE1_GO_BACK; ?></a></p>
	<?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>