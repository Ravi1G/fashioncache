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


	if (isset($_GET['show']) && is_numeric($_GET['show']) && $_GET['show'] > 0 && in_array($_GET['show'], $results_on_page))
	{
		$results_per_page = (int)$_GET['show'];
		if (!(isset($_GET['go']) && $_GET['go'] == 1))$page = 1;
	}
	else
	{
		$results_per_page = RESULTS_PER_PAGE;
	}

	$cc = 0;

	////////////////// filter  //////////////////////
		if (isset($_GET['column']) && $_GET['column'] != "")
		{
			switch ($_GET['column'])
			{
				case "title": $rrorder = "title"; break;
				case "added": $rrorder = "added"; break;
				case "visits": $rrorder = "visits"; break;
				case "cashback": $rrorder = "cashback"; break;
				default: $rrorder = "title"; break;
			}
		}
		else
		{
			$rrorder = "title";
		}

		if (isset($_GET['order']) && $_GET['order'] != "")
		{
			switch ($_GET['order'])
			{
				case "asc": $rorder = "asc"; break;
				case "desc": $rorder = "desc"; break;
				default: $rorder = "asc"; break;
			}
		}
		else
		{
			$rorder = "asc";
		}
	//////////////////////////////////////////////////

	if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
	
	$from = ($page-1)*$results_per_page;
	$where = "";

	if (isset($_GET['action']) && $_GET['action'] == "search")
	{
		$stext = mysql_real_escape_string(getGetParameter('searchtext'));
		$stext = substr(trim($stext), 0, 100);

		// country filter //
		if (isset($_GET['country']) && is_numeric($_GET['country']) && $_GET['country'] > 0)
		{
			$country_id = (int)$_GET['country'];

			unset($retailers_per_country);
			$retailers_per_country = array();
			$retailers_per_country[] = "111111111111111111111";

			$sql_retailers_per_country = smart_mysql_query("SELECT retailer_id FROM cashbackengine_retailer_to_country WHERE country_id='$country_id'");
			while ($row_retailers_per_country = mysql_fetch_array($sql_retailers_per_country))
			{
				$retailers_per_country[] = $row_retailers_per_country['retailer_id'];
			}

			$where .= "retailer_id IN (".implode(",",$retailers_per_country).") AND";
		}

		if ($rrorder == "cashback")
			$query = "SELECT * FROM cashbackengine_retailers WHERE $where (title LIKE '%".$stext."%' OR description LIKE '%".$stext."%') AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' ORDER BY ABS(cashback) $rorder LIMIT $from, $results_per_page";
		else
			$query = "SELECT * FROM cashbackengine_retailers WHERE $where (title LIKE '%".$stext."%' OR description LIKE '%".$stext."%') AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' ORDER BY featured DESC, $rrorder $rorder LIMIT $from, $results_per_page";

		$total_result = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE $where (title LIKE '%".$stext."%' OR description LIKE '%".$stext."%') AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' ORDER BY title ASC");
	}

	$total = mysql_num_rows($total_result);
	$result = smart_mysql_query($query);
	$total_on_page = mysql_num_rows($result);


	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_SEARCH_TITLE." ".$stext;

	require_once ("inc/header.inc.php");

?>

	

<div class="container content standardContainer blog">
    <!-- Search List -->			
    
    <div class="cb"></div>
    <div class="SiteContentSection">
        <div class="SiteContentLeft">
            <h1>Search Results for  '<?php echo $stext; ?>'</h1>
            <table class="RetailerOffersTable couponTable searchRetailer">	
                <?php while ($row = mysql_fetch_array($result)) { ?>
                	<tr>
                    <td class="columnOne">
                        <div class="couponProviderIcon"> <!-- Exclusive or See all coupons button is commented down, uncomment to show in the website -->
                        <?php /*?><?php if ($row['exclusive'] == 1) { ?><span class="exclusive" alt="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>" title="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>"><?php echo CBE1_COUPONS_EXCLUSIVE; ?></span><?php } ?><?php */?>
                            <a href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>"><img src="<?php if (!stristr($row['image'], 'http')) echo SITE_URL."img/"; echo $row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" border="0" /></a>
                            <?php /*?>
                            <br/><a class="more" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>#coupons"><?php echo CBE1_COUPONS_SEEALL; ?></a>
                            <?php */?>
                        </div>
						<div class="shareOnSocialMedia isResponsive">Share: &#x00A0;
							<a href="http://www.facebook.com/sharer.php?u=<?php echo urlencode(GetRetailerLink($row['retailer_id'], $row['title'])); ?>&t=<?php echo $row['title']; ?>" target="_blank" title="<?php echo CBE1_SHARE_FACEBOOK; ?>"><img src="<?php echo SITE_URL; ?>images/icon_facebook.png"  alt="<?php echo CBE1_SHARE_FACEBOOK; ?>" /></a> &nbsp;
							<a href="http://twitter.com/intent/tweet?source=sharethiscom&text=<?php echo $row['title']; ?>&url=<?php echo urlencode(GetRetailerLink($row['retailer_id'], $row['title'])); ?>" target="_blank" title="<?php echo CBE1_SHARE_TWITTER; ?>"><img src="<?php echo SITE_URL; ?>images/icon_twitter.png" alt="<?php echo CBE1_SHARE_TWITTER; ?>" /></a>
						</div>
                    </td>
                    <td class="columnTwo">
	                    <div class="offerName"><?php echo $row['title'];?> <span class="offerExpiryDate">(<?php echo $row['cashback'];?> Cashback)</span></div>
                        <div class="requirement">
							<div class="offerDetail"><?php
								echo substr(strip_tags($row['description']),0,161).'...';?> 
							</div>
						</div>
						<div class="isResponsive addToFav">							
							<span class="shopNowBotton siteButton searchRetailerShopNow">
								<a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>&c=<?php echo $row['coupon_id']; ?>"><span>SHOP NOW</span></a>
							</span>
							<span class="shopNowBotton siteButton searchRetailerShopNow">
								<a class="favorites" href="<?php echo SITE_URL; ?>myfavorites.php?act=add&id=<?php echo $row['retailer_id']; ?>"><span>Add to favourites</span></a>
							</span>							
						</div>
                    </td>
                    <td class="columnThree notResponsive">
						<div class="shareOnSocialMedia">
							<a href="http://www.facebook.com/sharer.php?u=<?php echo urlencode(GetRetailerLink($row['retailer_id'], $row['title'])); ?>&t=<?php echo $row['title']; ?>" target="_blank" title="<?php echo CBE1_SHARE_FACEBOOK; ?>"><img src="<?php echo SITE_URL; ?>images/icon_facebook.png"  alt="<?php echo CBE1_SHARE_FACEBOOK; ?>" /></a> &nbsp;
							<a href="http://twitter.com/intent/tweet?source=sharethiscom&text=<?php echo $row['title']; ?>&url=<?php echo urlencode(GetRetailerLink($row['retailer_id'], $row['title'])); ?>" target="_blank" title="<?php echo CBE1_SHARE_TWITTER; ?>"><img src="<?php echo SITE_URL; ?>images/icon_twitter.png" alt="<?php echo CBE1_SHARE_TWITTER; ?>" /></a>
						</div>
						<div class="shopNowBotton siteButton searchRetailerShopNow">
                            <a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>&c=<?php echo $row['coupon_id']; ?>"><span>SHOP NOW</span></a>
                        </div>
						<div class="shopNowBotton siteButton searchRetailerShopNow">
                            <a class="favorites" href="<?php echo SITE_URL; ?>myfavorites.php?act=add&id=<?php echo $row['retailer_id']; ?>"><span>Add to favourites</span></a>
                        </div>					
					</td>
                </tr>
                <?php }?>
                
            
            </table>
            <div class="cb"></div>
        </div>
        
        <?php require_once('inc/right_sidebar.php')?>
        <div class="cb"></div>
    </div>
</div>

<!-- Old Design & Functionality -->
<?php /*?>
	<div class="search_box">
	<form action="" method="get">
		<b><?php echo CBE1_SEARCH_TITLE2; ?></b>: 
		<input type="text" name="searchtext" class="textbox" value="<?php echo $stext; ?>" size="40" />
		<select name="country" class="textbox2" id="country" style="width: 150px;">
		<option value=""><?php echo CBE1_LABEL_COUNTRY_SELECT; ?></option>
		<?php
			$sql_country = "SELECT * FROM cashbackengine_countries WHERE status='active' ORDER BY name ASC";
			$rs_country = smart_mysql_query($sql_country);
			$total_country = mysql_num_rows($rs_country);

			if ($total_country > 0)
			{
				while ($row_country = mysql_fetch_array($rs_country))
				{
					if ($country_id == $row_country['country_id'])
						echo "<option value='".$row_country['country_id']."' selected>".$row_country['name']."</option>\n";
					else
						echo "<option value='".$row_country['country_id']."'>".$row_country['name']."</option>\n";
				}
			}
		?>
		</select>
		<input type="hidden" name="action" value="search" />
		<input type="submit" class="submit" value="<?php echo CBE1_SEARCH_BUTTON; ?>" />
	</form>
	</div>

	<?php

		if ($total > 0) {
	?>

	<div class="browse_top">
		<div class="sortby">
			<form action="<?php echo SITE_URL; ?>search.php?<?php echo $_SERVER['QUERY_STRING']; ?>" id="form1" name="form1" method="get">
				<span><?php echo CBE1_SORT_BY; ?>:</span>
				<select name="column" id="column" onChange="document.form1.submit()">
					<option value="title" <?php if ($_GET['column'] == "title") echo "selected"; ?>><?php echo CBE1_SEARCH_NAME; ?></option>
					<option value="visits" <?php if ($_GET['column'] == "visits") echo "selected"; ?>><?php echo CBE1_SEARCH_POPULARITY; ?></option>
					<option value="added" <?php if ($_GET['column'] == "added") echo "selected"; ?>><?php echo CBE1_SEARCH_DATE; ?></option>
					<option value="cashback" <?php if ($_GET['column'] == "cashback") echo "selected"; ?>><?php echo CBE1_SEARCH_CASHBACK; ?></option>
				</select>
				<select name="order" id="order" onChange="document.form1.submit()">
					<option value="asc" <?php if ($_GET['order'] == "asc") echo "selected"; ?>><?php echo CBE1_SORT_ASC; ?></option>
					<option value="desc" <?php if ($_GET['order'] == "desc") echo "selected"; ?>><?php echo CBE1_SORT_DESC; ?></option>
				</select>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<span><?php echo CBE1_RESULTS; ?>:</span>
				<select name="show" id="show" onChange="document.form1.submit()">
					<option value="5" <?php if ($results_per_page == "5") echo "selected"; ?>>5</option>
					<option value="10" <?php if ($results_per_page == "10") echo "selected"; ?>>10</option>
					<option value="25" <?php if ($results_per_page == "25") echo "selected"; ?>>25</option>
					<option value="50" <?php if ($results_per_page == "50") echo "selected"; ?>>50</option>
					<option value="100" <?php if ($results_per_page == "100") echo "selected"; ?>>100</option>
					<option value="111111" <?php if ($results_per_page == "111111") echo "selected"; ?>><?php echo CBE1_RESULTS_ALL; ?></option>
				</select>
				<input type="hidden" name="searchtext" value="<?php echo $stext; ?>" />
				<input type="hidden" name="page" value="<?php echo $page; ?>" />
				<input type="hidden" name="action" value="search" />
			</form>
		</div>
		<div class="results">
			<?php echo CBE1_RESULTS_SHOWING; ?> <?php echo ($from + 1); ?> - <?php echo min($from + $total_on_page, $total); ?> <?php echo CBE1_RESULTS_OF; ?> <?php echo $total; ?>
		</div>
	</div>

			<table align="center" width="100%" border="0" cellspacing="0" cellpadding="5">
			<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
				
				<tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td width="125" align="center" valign="middle">
						<a href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>">
						<?php if ($row['featured'] == 1) { ?><span class="featured" alt="<?php echo CBE1_FEATURED_STORE; ?>" title="<?php echo CBE1_FEATURED_STORE; ?>"></span><?php } ?>
						<div class="imagebox"><img src="<?php if (!stristr($row['image'], 'http')) echo SITE_URL."img/"; echo $row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="" border="0" /></div>
						</a>
						<?php echo GetStoreRating($row['retailer_id'], $show_start = 1); ?>
					</td>
					<td align="left" valign="top">
						<table width="100%" border="0" cellspacing="0" cellpadding="3">
							<tr>
								<td colspan="2" width="80%" align="left" valign="top">
									<a class="retailer_title" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>"><?php echo $row['title']; ?></a>
								</td>
								<td nowrap="nowrap" width="20%" align="right" valign="middle">
									<a class="coupons" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>#coupons" title="<?php echo $row['title']; ?> Coupons"><?php echo GetStoreCouponsTotal($row['retailer_id']); ?></a>
								</td>
							</tr>
							<tr>
								<td colspan="2" valign="middle" align="left">
									<span class="retailer_description"><?php echo $row['description']; ?></span><br/>
									<?php echo GetStoreCountries($row['retailer_id']); ?>
								</td>
								<td valign="top" align="center">
								<?php if ($row['cashback'] != "") { ?>
									<?php if ($row['old_cashback'] != "") { ?><span class="old_cashback"><?php echo DisplayCashback($row['old_cashback']); ?></span><?php } ?>
									<span class="cashback"><span class="value"><?php echo DisplayCashback($row['cashback']); ?></span> <?php echo CBE1_CASHBACK; ?></span>
								<?php } ?>
								</td>
							</tr>
							<tr>
								<td colspan="2" valign="middle" align="left">
									<a href="http://www.facebook.com/sharer.php?u=<?php echo urlencode(GetRetailerLink($row['retailer_id'], $row['title'])); ?>&t=<?php echo $row['title']; ?>" target="_blank" title="<?php echo CBE1_SHARE_FACEBOOK; ?>"><img src="<?php echo SITE_URL; ?>images/icon_facebook.png"  alt="<?php echo CBE1_SHARE_FACEBOOK; ?>" align="absmiddle" /></a> &nbsp;
									<a href="http://twitter.com/intent/tweet?source=sharethiscom&text=<?php echo $row['title']; ?>&url=<?php echo urlencode(GetRetailerLink($row['retailer_id'], $row['title'])); ?>" target="_blank" title="<?php echo CBE1_SHARE_TWITTER; ?>"><img src="<?php echo SITE_URL; ?>images/icon_twitter.png" alt="<?php echo CBE1_SHARE_TWITTER; ?>" align="absmiddle" /></a>
									&nbsp;&nbsp;
									<?php if ($row['conditions'] != "") { ?>
										<div class="cashbackengine_tooltip">
											<a class="conditions" href="#"><?php echo CBE1_CONDITIONS; ?></a> <span class="tooltip"><?php echo $row['conditions']; ?></span>
										</div>
									<?php } ?>
									<a class="favorites" href="<?php echo SITE_URL; ?>myfavorites.php?act=add&id=<?php echo $row['retailer_id']; ?>"><?php echo CBE1_ADD_FAVORITES; ?></a>
								</td>
								<td valign="middle" align="right">
									<a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>><?php echo CBE1_GO_TO_STORE; ?></a>
								</td>
							</tr>
						</table>
					</td>
				</tr>

			<?php } ?>
			</table>

			<?php
				echo ShowPagination("retailers",$results_per_page,"search.php?action=search&searchtext=$stext&column=$rrorder&order=$rorder&show=$results_per_page&go=1&", "WHERE $where (title LIKE '%".$stext."%' OR description LIKE '%".$stext."%') AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active'");
			?>

	<?php }else{ ?>
		<p align="center">
			<?php echo CBE1_SEARCH_NO; ?><br/><br/>
			<a class="goback" href="#" onclick="history.go(-1);return false;"><?php echo CBE1_GO_BACK; ?></a>
		</p>
	<?php } ?><?php */?>


<?php require_once ("inc/footer.inc.php"); ?>