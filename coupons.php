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

	$results_per_page = COUPONS_PER_PAGE;
	$cc = 0;


	////////////////// filter  //////////////////////
		if (isset($_GET['column']) && $_GET['column'] != "")
		{
			switch ($_GET['column'])
			{
				case "added": $rrorder = "c.added"; break;
				case "visits": $rrorder = "c.visits"; break;
				case "retailer_id": $rrorder = "c.retailer_id"; break;
				case "end_date": $rrorder = "c.end_date"; break;
				default: $rrorder = "c.added"; break;
			}
		}
		else
		{
			$rrorder = "c.added";
		}

		if (isset($_GET['order']) && $_GET['order'] != "")
		{
			switch ($_GET['order'])
			{
				case "asc": $rorder = "asc"; break;
				case "desc": $rorder = "desc"; break;
				default: $rorder = "desc"; break;
			}
		}
		else
		{
			$rorder = "desc";
		}
	//////////////////////////////////////////////////

	if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
	$from = ($page-1)*$results_per_page;

	$where = " (start_date<=NOW() AND (end_date='0000-00-00 00:00:00' OR end_date > NOW())) AND status='active'";
	
	// Query for fetching data & sorting according to the previous functionality of sorting
	//$query = "SELECT c.*, DATE_FORMAT(c.end_date, '%d %b %Y')  AS coupon_end_date, UNIX_TIMESTAMP(c.end_date) - UNIX_TIMESTAMP() AS time_left, c.title AS coupon_title, r.image, r.title FROM cashbackengine_coupons c LEFT JOIN cashbackengine_retailers r ON c.retailer_id=r.retailer_id WHERE (c.start_date<=NOW() AND (c.end_date='0000-00-00 00:00:00' OR c.end_date > NOW())) AND c.status='active' AND (r.end_date='0000-00-00 00:00:00' OR r.end_date > NOW()) AND r.status='active' ORDER BY $rrorder $rorder LIMIT $from, $results_per_page";

	// Query for fetching data & sorting according to the sort order provided at the backend
	$query = "SELECT c.*, 
					DATE_FORMAT(c.end_date, '%m/%d/%Y')  AS coupon_end_date, 
					UNIX_TIMESTAMP(c.end_date) - UNIX_TIMESTAMP() AS time_left, 
					c.title AS coupon_title, 
					r.image,
					r.image_I,
					r.image_II,
					r.image_III, 
					r.title,
					r.retailer_slug,
					r.cashback AS cashback FROM cashbackengine_coupons c LEFT JOIN cashbackengine_retailers r 
					ON c.retailer_id=r.retailer_id 
					WHERE (c.start_date<=NOW() AND (c.end_date='0000-00-00 00:00:00' OR c.end_date > NOW()))
					AND (r.is_profile_completed='0') 
					AND c.status='active' AND (r.end_date='0000-00-00 00:00:00' OR r.end_date > NOW()) 
					AND r.status='active' ORDER BY sort_order ";
	
	//$total_result = smart_mysql_query("SELECT * FROM cashbackengine_coupons WHERE $where ORDER BY sort_order ASC");
	//$total = mysql_num_rows($total_result);

	$result = smart_mysql_query($query);
	$total_on_page = mysql_num_rows($result);

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_COUPONS_TITLE;
	require_once ("inc/header.inc.php");
?>
<div class="container content standardContainer blog">
    <!-- Featured Store List -->			
    
    <div class="cb"></div>
    <div class="SiteContentSection">
        <div class="SiteContentLeft">
            <div class="categoryHeadingWithMargins"><h1>Sales, Specials &#x0026; Coupons</h1></div>
            <table class="RetailerOffersTable couponTable">	
                <?php while ($row = mysql_fetch_array($result)) { ?>
                	<tr>
                    <td class="columnOne">
                        <div class="couponProviderIcon"> <!-- Exclusive or See all coupons button is commented down, uncomment to show in the website -->
                        <?php /*?><?php if ($row['exclusive'] == 1) { ?><span class="exclusive" alt="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>" title="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>"><?php echo CBE1_COUPONS_EXCLUSIVE; ?></span><?php } ?><?php */?>
                            <!-- <a href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>">-->
                            <a href="<?php echo SITE_URL.'coupons/'.$row['retailer_slug'];?>">
                             <img src="<?php echo SITE_URL."admin/upload/retailer/".$row['image_I'];?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" border="0" />
                            </a>
                            <?php /*?>
                            <br/><a class="more" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>#coupons"><?php echo CBE1_COUPONS_SEEALL; ?></a>
                            <?php */?>
                        </div>
						
						<div class="isResponsive">
							<div class="cashBackOnOffer">Cash Back: <span><?php echo $row['cashback']?></span></div>
							<div class="requirement">
								<?php if($row['code']!=""){?>
									<b><?php echo CBE1_COUPONS_CODE; ?></b>: <?php echo $row['code'];?>
								<?php } else{?>
								No Coupon Required
								<?php }?>
								<?php 
								//Description
								/* if ($row['description'] != "") { ?>
								<p><?php echo $row['description']; ?></p>
							<?php } */?>
							</div>
							<div class="offerName">
								<span class="offerExpiryDate">
									<?php if ($row['end_date'] != "0000-00-00 00:00:00") { ?>
										<span class="cashBackOnOffer">Expires: <?php echo $row['coupon_end_date']; ?></span> &nbsp; 
									<?php } ?>								
								</span>
							</div>
						</div>
						
						
                    </td>                    
                    <td class="columnTwo">
						<div class="offerName">
							<!-- <a class="retailer_title" href="<?php echo SITE_URL.$row['retailer_slug']; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>&c=<?php echo $row['coupon_id']; ?>" target="_blank"><b><?php echo $row['coupon_title']; ?></b></a>-->
						</div>
						<div class="offerDescription1"><?php echo $row['description']?></div>
						<div class="notResponsive">
							<div class="cashBackOnOffer">Plus <span><?php echo $row['cashback']?></span> Cash Back</div>
							<div class="requirement">
								<?php if($row['code']!=""){?>
								<b><?php echo CBE1_COUPONS_CODE; ?></b>: <?php echo $row['code'];?>
								<?php } else{?> No Coupon Code Required
								<?php }?>
								
								<?php 
								//Description
								/* if ($row['description'] != "") { ?>
								<p><?php echo $row['description']; ?></p>
							<?php } */?>
							</div>
							<div class="offerName">
								<span class="offerExpiryDate">
									<?php if ($row['end_date'] != "0000-00-00 00:00:00") { ?>
										<span class="cashBackOnOffer">Expires <?php echo $row['coupon_end_date']; ?></span> &nbsp; 
									<?php } ?>								
								</span>
							</div>
						</div>
						<div class="shopNowBotton siteButton isResponsive">
                            <a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>&c=<?php echo $row['coupon_id']; ?>"><span>SHOP NOW</span></a>
                        </div>
                    </td>
                    <td class="columnThree">
                        <div class="shopNowBotton siteButton notResponsive">
                            <a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>&c=<?php echo $row['coupon_id']; ?>"><span>SHOP NOW</span></a>
                        </div>
                    </td>
                </tr>
                <?php }?>
                
             <?php /*?> // NEW CSS Design - static
                <tr>
                    <td class="columnOne">
                        <div class="couponProviderIcon">
                            <img src="../img/storeLogos/sample4.jpg" alt=""/>
                        </div>
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
                        <div class="couponProviderIcon">
                            <img src="../img/storeLogos/sample1.jpg" alt=""/>
                        </div>
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
                        <div class="couponProviderIcon">
                            <img src="../img/storeLogos/sample2.jpg" alt=""/>
                        </div>
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
                        <div class="couponProviderIcon">
                            <img src="../img/storeLogos/sample3.jpg" alt=""/>
                        </div>
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
                        <div class="couponProviderIcon">
                            <img src="../img/storeLogos/sample6.jpg" alt=""/>
                        </div>
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
                        <div class="couponProviderIcon">
                            <img src="../img/storeLogos/sample4.jpg" alt=""/>
                        </div>
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
                        <div class="couponProviderIcon">
                            <img src="../img/storeLogos/sample1.jpg" alt=""/>
                        </div>
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
                        <div class="couponProviderIcon">
                            <img src="../img/storeLogos/sample2.jpg" alt=""/>
                        </div>
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
                <?php */?>
            </table>
            <div class="cb"></div>
        </div>
        
        <?php require_once('inc/right_sidebar.php')?>
        
        
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
        </div><?php */?>
        <div class="cb"></div>
    </div>
</div>
<?php require_once ("inc/footer.inc.php"); ?>

<?php /*?> // OLD Functionality DESIGN  + CODE
	<h1><?php echo CBE1_COUPONS_TITLE; ?></h1>

		<div id="tabs_container">
			<ul id="tabs">
				<li class="active"><a href="#all"><span><?php echo CBE1_COUPONS_ALL; ?></span></a></li>
				<li><a href="#top-coupons"><span><?php echo CBE1_COUPONS_POPULAR; ?></span></a></li>
				<?php if (GetExclusiveCouponsTotal() > 0) { ?><li><a href="#exclusive"><span><?php echo CBE1_COUPONS_EXCLUSIVE; ?></span></a></li><?php } ?>
				<li><a href="#latest"><span><?php echo CBE1_COUPONS_LATEST; ?></span></a></li>
				<?php if (GetExpiringCouponsTotal() > 0) { ?><li><a href="#expiring"><span><?php echo CBE1_COUPONS_EXPIRING; ?></span></a></li><?php } ?>
			</ul>
		</div>


		<div id="all" class="tab_content">
		<?php

		if ($total > 0) {

		?>
		<div class="browse_top">
			<div class="sortby">
				<form action="" id="form1" name="form1" method="get">
					<span><?php echo CBE1_SORT_BY; ?>:</span>
					<select name="column" id="column" onChange="document.form1.submit()">
						<option value="added" <?php if ($_GET['column'] == "added") echo "selected"; ?>><?php echo CBE1_COUPONS_SDATE; ?></option>
						<option value="visits" <?php if ($_GET['column'] == "visits") echo "selected"; ?>><?php echo CBE1_COUPONS_SPOPULAR; ?></option>
						<option value="retailer_id" <?php if ($_GET['column'] == "retailer_id") echo "selected"; ?>><?php echo CBE1_COUPONS_SSTORE; ?></option>
						<option value="end_date" <?php if ($_GET['column'] == "end_date") echo "selected"; ?>><?php echo CBE1_COUPONS_SEND; ?></option>
					</select>
					<select name="order" id="order" onChange="document.form1.submit()">
						<option value="desc" <?php if ($_GET['order'] == "desc") echo "selected"; ?>><?php echo CBE1_SORT_DESC; ?></option>
						<option value="asc" <?php if ($_GET['order'] == "asc") echo "selected"; ?>><?php echo CBE1_SORT_ASC; ?></option>
					</select>
					<?php if ($cat_id) { ?><input type="hidden" name="cat" value="<?php echo $cat_id; ?>" /><?php } ?>
					<input type="hidden" name="page" value="<?php echo $page; ?>" />
				</form>
			</div>
			<div class="results">
				<?php echo CBE1_RESULTS_SHOWING; ?> <?php echo ($from + 1); ?> - <?php echo min($from + $total_on_page, $total); ?> <?php echo CBE1_RESULTS_OF; ?> <?php echo $total; ?>
			</div>
		</div>

			<table align="center" width="100%" border="0" cellspacing="0" cellpadding="5">
				<?php while ($row = mysql_fetch_array($result)) { ?>
				<tr>
					<td class="td_coupon" width="125" align="center" valign="middle">
						<?php if ($row['exclusive'] == 1) { ?><span class="exclusive" alt="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>" title="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>"><?php echo CBE1_COUPONS_EXCLUSIVE; ?></span><?php } ?>
						<div class="imagebox"><a href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>"><img src="<?php if (!stristr($row['image'], 'http')) echo SITE_URL."img/"; echo $row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" border="0" /></a></div>
						<br/><a class="more" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>#coupons"><?php echo CBE1_COUPONS_SEEALL; ?></a>
					</td>
					<td class="td_coupon" align="left" valign="top">
						<a class="retailer_title" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>&c=<?php echo $row['coupon_id']; ?>" target="_blank"><b><?php echo $row['title']; ?></b></a>
						<p><?php echo $row['coupon_title']; ?></p>
						<?php if ($row['code'] != "") { ?>
							<b><?php echo CBE1_COUPONS_CODE; ?></b>: <span class="coupon_code"><?php echo (isLoggedIn()) ? $row['code'] : CBE1_COUPONS_CODE_HIDDEN; ?></span><br/><br/>
						<?php } ?>
						<a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>&c=<?php echo $row['coupon_id']; ?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>><?php echo ($row['code'] != "") ? CBE1_COUPONS_LINK : CBE1_COUPONS_LINK2; ?></a>
						<?php if ($row['end_date'] != "0000-00-00 00:00:00") { ?>
							<span class="expires"><?php echo CBE1_COUPONS_EXPIRES; ?>: <?php echo $row['coupon_end_date']; ?></span> &nbsp; 
							<span class="time_left"><?php echo CBE1_COUPONS_TIMELEFT; ?>: <?php echo GetTimeLeft($row['time_left']).""; ?></span>
						<?php } ?>
						<?php if ($row['description'] != "") { ?>
							<p><?php echo $row['description']; ?></p>
						<?php } ?>
					</td>
				</tr>
				<?php } ?>
				<tr>
				  <td valign="middle" align="center" colspan="2">
					<?php
							$params = "";
							if (isset($cat_id) && $cat_id > 0) { $params = "cat=$cat_id&"; }

							echo ShowPagination("coupons",$results_per_page,"coupons.php?".$params."column=$rrorder&order=$rorder&","WHERE ".$where);
					?>
				  </td>
			  </tr>
			</table>

			<?php }else{ ?>
				<p align="center"><?php echo CBE1_COUPONS_NO; ?></p>
				<div class="sline"></div>
			<?php } ?>
		</div>


		<div id="top-coupons" class="tab_content">
		<?php
				// show most popular coupons //
				$top_query = "SELECT c.*, DATE_FORMAT(c.end_date, '%d %b %Y') AS coupon_end_date, UNIX_TIMESTAMP(c.end_date) - UNIX_TIMESTAMP() AS time_left, c.title AS coupon_title, r.image, r.title FROM cashbackengine_coupons c LEFT JOIN cashbackengine_retailers r ON c.retailer_id=r.retailer_id WHERE (c.start_date<=NOW() AND (c.end_date='0000-00-00 00:00:00' OR c.end_date > NOW())) AND c.status='active' AND (r.end_date='0000-00-00 00:00:00' OR r.end_date > NOW()) AND r.status='active' ORDER BY c.visits DESC LIMIT $results_per_page";
				$top_result = smart_mysql_query($top_query);
				$top_total = mysql_num_rows($top_result);

				if ($top_total > 0)
				{
			?>
				<table align="center" width="100%" border="0" cellspacing="0" cellpadding="5">
				<?php while ($top_row = mysql_fetch_array($top_result)) { ?>
				<tr>
					<td class="td_coupon" width="125" align="center" valign="middle">
						<?php if ($top_row['exclusive'] == 1) { ?><span class="exclusive" alt="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>" title="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>"><?php echo CBE1_COUPONS_EXCLUSIVE; ?></span><?php } ?>
						<div class="imagebox"><a href="<?php echo GetRetailerLink($top_row['retailer_id'], $tops_row['title']); ?>"><img src="<?php if (!stristr($top_row['image'], 'http')) echo SITE_URL."img/"; echo $top_row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $top_row['title']; ?>" title="<?php echo $top_row['title']; ?>" border="0" /></a></div>
						<br/><a class="more" href="<?php echo GetRetailerLink($top_row['retailer_id'], $tops_row['title']); ?>#coupons"><?php echo CBE1_COUPONS_SEEALL; ?></a>
					</td>
					<td class="td_coupon" align="left" valign="top">
						<a class="retailer_title" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $top_row['retailer_id']; ?>&c=<?php echo $top_row['coupon_id']; ?>" target="_blank"><b><?php echo $top_row['title']; ?></b></a>
						<p><?php echo $top_row['coupon_title']; ?></p>
						<?php if ($top_row['code'] != "") { ?>
							<b><?php echo CBE1_COUPONS_CODE; ?></b>: <span class="coupon_code"><?php echo (isLoggedIn()) ? $top_row['code'] : CBE1_COUPONS_CODE_HIDDEN; ?></span><br/><br/>
						<?php } ?>
						<a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $top_row['retailer_id']; ?>&c=<?php echo $top_row['coupon_id']; ?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>><?php echo ($top_row['code'] != "") ? CBE1_COUPONS_LINK : CBE1_COUPONS_LINK2; ?></a>
						<?php if ($top_row['end_date'] != "0000-00-00 00:00:00") { ?>
							<span class="expires"><?php echo CBE1_COUPONS_EXPIRES; ?>: <?php echo $top_row['coupon_end_date']; ?></span> &nbsp; 
							<span class="time_left"><?php echo CBE1_COUPONS_TIMELEFT; ?>: <?php echo GetTimeLeft($top_row['time_left']).""; ?></span>
						<?php } ?>
						<?php if ($top_row['description'] != "") { ?>
							<p><?php echo $top_row['description']; ?></p>
						<?php } ?>
					</td>
				</tr>
				<?php } ?>
				</table>

				<?php }else{ ?>
					<p align="center"><?php echo CBE1_COUPONS_NO; ?></p>
					<div class="sline"></div>
				<?php } ?>
		</div>


		<div id="latest" class="tab_content">
		<?php
				// show latest coupons //
				$last_query = "SELECT c.*, DATE_FORMAT(c.end_date, '%d %b %Y') AS coupon_end_date, UNIX_TIMESTAMP(c.end_date) - UNIX_TIMESTAMP() AS time_left, c.title AS coupon_title, r.image, r.title FROM cashbackengine_coupons c LEFT JOIN cashbackengine_retailers r ON c.retailer_id=r.retailer_id WHERE (c.start_date<=NOW() AND (c.end_date='0000-00-00 00:00:00' OR c.end_date > NOW())) AND c.status='active' AND (r.end_date='0000-00-00 00:00:00' OR r.end_date > NOW()) AND r.status='active' ORDER BY c.added DESC LIMIT $results_per_page";
				$last_result = smart_mysql_query($last_query);
				$last_total = mysql_num_rows($last_result);

				if ($last_total > 0)
				{
			?>
				<table align="center" width="100%" border="0" cellspacing="0" cellpadding="5">
				<?php while ($last_row = mysql_fetch_array($last_result)) { ?>
				<tr>
					<td class="td_coupon" width="125" align="center" valign="middle">
						<?php if ($last_row['exclusive'] == 1) { ?><span class="exclusive" alt="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>" title="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>"><?php echo CBE1_COUPONS_EXCLUSIVE; ?></span><?php } ?>
						<div class="imagebox"><a href="<?php echo GetRetailerLink($last_row['retailer_id'], $last_row['title']); ?>"><img src="<?php if (!stristr($last_row['image'], 'http')) echo SITE_URL."img/"; echo $last_row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $last_row['title']; ?>" title="<?php echo $last_row['title']; ?>" border="0" /></a></div>
						<br/><a class="more" href="<?php echo GetRetailerLink($last_row['retailer_id'], $last_row['title']); ?>#coupons"><?php echo CBE1_COUPONS_SEEALL; ?></a>
					</td>
					<td class="td_coupon" align="left" valign="top">
						<a class="retailer_title" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $last_row['retailer_id']; ?>&c=<?php echo $last_row['coupon_id']; ?>" target="_blank"><b><?php echo $last_row['title']; ?></b></a>
						<p><?php echo $last_row['coupon_title']; ?></p>
						<?php if ($last_row['code'] != "") { ?>
							<b><?php echo CBE1_COUPONS_CODE; ?></b>: <span class="coupon_code"><?php echo (isLoggedIn()) ? $last_row['code'] : CBE1_COUPONS_CODE_HIDDEN; ?></span><br/><br/>
						<?php } ?>
						<a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $last_row['retailer_id']; ?>&c=<?php echo $last_row['coupon_id']; ?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>><?php echo ($last_row['code'] != "") ? CBE1_COUPONS_LINK : CBE1_COUPONS_LINK2; ?></a>
						<?php if ($last_row['end_date'] != "0000-00-00 00:00:00") { ?>
							<span class="expires"><?php echo CBE1_COUPONS_EXPIRES; ?>: <?php echo $last_row['coupon_end_date']; ?></span> &nbsp; 
							<span class="time_left"><?php echo CBE1_COUPONS_TIMELEFT; ?>: <?php echo GetTimeLeft($last_row['time_left']).""; ?></span>
						<?php } ?>
						<?php if ($last_row['description'] != "") { ?>
							<p><?php echo $last_row['description']; ?></p>
						<?php } ?>
					</td>
				</tr>
				<?php } ?>
				</table>

				<?php }else{ ?>
					<p align="center"><?php echo CBE1_COUPONS_NO; ?></p>
					<div class="sline"></div>
				<?php } ?>
		</div>


		<div id="exclusive" class="tab_content">
		<?php
				// show exclusive coupons //
				$ex_query = "SELECT c.*, DATE_FORMAT(c.end_date, '%d %b %Y') AS coupon_end_date, UNIX_TIMESTAMP(c.end_date) - UNIX_TIMESTAMP() AS time_left, c.title AS coupon_title, r.image, r.title FROM cashbackengine_coupons c LEFT JOIN cashbackengine_retailers r ON c.retailer_id=r.retailer_id WHERE (c.start_date<=NOW() AND (c.end_date='0000-00-00 00:00:00' OR c.end_date > NOW())) AND c.exclusive='1' AND c.status='active' AND (r.end_date='0000-00-00 00:00:00' OR r.end_date > NOW()) AND r.status='active' ORDER BY c.added DESC LIMIT $results_per_page";
				$ex_result = smart_mysql_query($ex_query);
				$ex_total = mysql_num_rows($ex_result);

				if ($ex_total > 0)
				{
			?>
				<table align="center" width="100%" border="0" cellspacing="0" cellpadding="5">
				<?php while ($ex_row = mysql_fetch_array($ex_result)) { ?>
				<tr>
					<td class="td_coupon" width="125" align="center" valign="middle">
						<div class="imagebox"><a href="<?php echo GetRetailerLink($ex_row['retailer_id'], $ex_row['title']); ?>"><img src="<?php if (!stristr($ex_row['image'], 'http')) echo SITE_URL."img/"; echo $ex_row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $ex_row['title']; ?>" title="<?php echo $ex_row['title']; ?>" border="0" /></a></div>
						<br/><a class="more" href="<?php echo GetRetailerLink($ex_row['retailer_id'], $ex_row['title']); ?>#coupons"><?php echo CBE1_COUPONS_SEEALL; ?></a>
					</td>
					<td class="td_coupon" align="left" valign="top">
						<a class="retailer_title" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $ex_row['retailer_id']; ?>&c=<?php echo $ex_row['coupon_id']; ?>" target="_blank"><b><?php echo $ex_row['title']; ?></b></a>
						<p><?php echo $ex_row['coupon_title']; ?></p>
						<?php if ($ex_row['code'] != "") { ?>
							<b><?php echo CBE1_COUPONS_CODE; ?></b>: <span class="coupon_code"><?php echo (isLoggedIn()) ? $ex_row['code'] : CBE1_COUPONS_CODE_HIDDEN; ?></span><br/><br/>
						<?php } ?>
						<a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $ex_row['retailer_id']; ?>&c=<?php echo $ex_row['coupon_id']; ?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>><?php echo ($ex_row['code'] != "") ? CBE1_COUPONS_LINK : CBE1_COUPONS_LINK2; ?></a>
						<?php if ($ex_row['end_date'] != "0000-00-00 00:00:00") { ?>
							<span class="expires"><?php echo CBE1_COUPONS_EXPIRES; ?>: <?php echo $ex_row['coupon_end_date']; ?></span> &nbsp; 
							<span class="time_left"><?php echo CBE1_COUPONS_TIMELEFT; ?>: <?php echo GetTimeLeft($ex_row['time_left']).""; ?></span>
						<?php } ?>
						<?php if ($ex_row['description'] != "") { ?>
							<p><?php echo $ex_row['description']; ?></p>
						<?php } ?>
					</td>
				</tr>
				<?php } ?>
				</table>

				<?php }else{ ?>
					<p align="center"><?php echo CBE1_COUPONS_NO; ?></p>
					<div class="sline"></div>
				<?php } ?>
		</div>


		<div id="expiring" class="tab_content">
		<?php
				// show expires in 3 days coupons //
				$exp_query = "SELECT c.*, DATE_FORMAT(c.end_date, '%d %b %Y') AS coupon_end_date, UNIX_TIMESTAMP(c.end_date) - UNIX_TIMESTAMP() AS time_left, c.title AS coupon_title, r.image, r.title FROM cashbackengine_coupons c LEFT JOIN cashbackengine_retailers r ON c.retailer_id=r.retailer_id WHERE c.end_date!='0000-00-00 00:00:00' AND (c.end_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 3 DAY)) AND c.status='active' AND (r.end_date='0000-00-00 00:00:00' OR r.end_date > NOW()) AND r.status='active' ORDER BY c.added DESC LIMIT $results_per_page";
				$exp_result = smart_mysql_query($exp_query);
				$exp_total = mysql_num_rows($exp_result);

				if ($exp_total > 0)
				{
			?>
				<table align="center" width="100%" border="0" cellspacing="0" cellpadding="5">
				<?php while ($exp_row = mysql_fetch_array($exp_result)) { ?>
				<tr>
					<td class="td_coupon" width="125" align="center" valign="middle">
						<?php if ($exp_row['exclusive'] == 1) { ?><span class="exclusive" alt="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>" title="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>"><?php echo CBE1_COUPONS_EXCLUSIVE; ?></span><?php } ?>
						<div class="imagebox"><a href="<?php echo GetRetailerLink($exp_row['retailer_id'], $exp_row['title']); ?>"><img src="<?php if (!stristr($exp_row['image'], 'http')) echo SITE_URL."img/"; echo $exp_row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $exp_row['title']; ?>" title="<?php echo $exp_row['title']; ?>" border="0" /></a></div>
						<br/><a class="more" href="<?php echo GetRetailerLink($exp_row['retailer_id'], $exp_row['title']); ?>#coupons"><?php echo CBE1_COUPONS_SEEALL; ?></a>
					</td>
					<td class="td_coupon" align="left" valign="top">
						<a class="retailer_title" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $exp_row['retailer_id']; ?>&c=<?php echo $exp_row['coupon_id']; ?>" target="_blank"><b><?php echo $exp_row['title']; ?></b></a>
						<p><?php echo $exp_row['coupon_title']; ?></p>
						<?php if ($exp_row['code'] != "") { ?>
							<b><?php echo CBE1_COUPONS_CODE; ?></b>: <span class="coupon_code"><?php echo (isLoggedIn()) ? $exp_row['code'] : CBE1_COUPONS_CODE_HIDDEN; ?></span><br/><br/>
						<?php } ?>
						<a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $exp_row['retailer_id']; ?>&c=<?php echo $exp_row['coupon_id']; ?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>><?php echo ($exp_row['code'] != "") ? CBE1_COUPONS_LINK : CBE1_COUPONS_LINK2; ?></a>
						<?php if ($exp_row['end_date'] != "0000-00-00 00:00:00") { ?>
							<span class="expires"><?php echo CBE1_COUPONS_EXPIRES; ?>: <?php echo $exp_row['coupon_end_date']; ?></span> &nbsp; 
							<span class="time_left"><?php echo CBE1_COUPONS_TIMELEFT; ?>: <span style="color: #F9682A"><?php echo GetTimeLeft($exp_row['time_left']).""; ?></span></span>
						<?php } ?>
						<?php if ($exp_row['description'] != "") { ?>
							<p><?php echo $exp_row['description']; ?></p>
						<?php } ?>
					</td>
				</tr>
				<?php } ?>
				</table>

				<?php }else{ ?>
					<p align="center"><?php echo CBE1_COUPONS_NO; ?></p>
					<div class="sline"></div>
				<?php } ?>
		</div>


	<?php

		$astores_query = "SELECT * FROM cashbackengine_retailers WHERE (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' ORDER BY title";
	
		$astores_result = smart_mysql_query($astores_query);
		$astores_total = mysql_num_rows($astores_result);

		if ($total > 0 && $astores_total > 0)
		{
			$stores_per_column = 7;
			$vv = 0;
			$b = 0;
	?>

		<h1><?php echo CBE1_COUPONS_BYSTORE; ?></h1>

		<div id="alphabet">
		<ul>
			<?php

				$numLetters = count($alphabet);
				$i = 0;

				foreach ($alphabet as $letter)
				{
					$i++;
					if ($i == $numLetters) $lilast = ' class="last"'; else $lilast = '';

					echo "<li".$lilast."><a href=\"#$letter\">".$letter."</a></li>";
				}

			?>
		</ul>
		</div>

		<ul class="stores_list">
		<?php while ($astores_row = mysql_fetch_array($astores_result)) { ?>
			<?php
					
				$first_letter = ucfirst(substr($astores_row['title'], 0, 1));

				if ($old_letter != $first_letter)
				{
					if ($b != 0 && $vv != 1) echo "</ul>";

					if (!in_array($first_letter, $alphabet))
					{
						if ($vv != 1)
						{
							echo "<li class='store2'><div class='letter'>0-9<a name='0-9'></a></div><ul>";
							$vv = 1;
						}
					}
					else
					{
						if ($vv == 1) echo "</ul>";
						echo "<li class='store2'><div class='letter'>$first_letter<a name='$first_letter'></a></div><ul>";
					}
							
					$old_letter = $first_letter;
					$b++;
					$bb = 0;
				}
			?>
				<?php if ($astores_row['featured'] == 1) { $ftag1 = "<b>"; $ftag2 = "</b>"; }else{  $ftag1 = $ftag2 = ""; } ?>

				<li><a href="<?php echo GetRetailerLink($astores_row['retailer_id'], $astores_row['title']); ?>"><?php echo $ftag1; ?><?php echo (strlen($astores_row['title']) > 75) ? substr($astores_row["title"], 0, 70)."..." : $astores_row["title"]; ?><?php echo $ftag2; ?></a> <span class="coupons"><?php echo GetStoreCouponsTotal($astores_row['retailer_id']); ?></span></li>

				<?php $bb++; if ($bb%$stores_per_column == 0) echo "</ul><ul>"; ?>
			<?php } ?>
		</ul>
	<?php } ?>

<?php */?>
<?php require_once ("inc/footer.inc.php"); ?>
