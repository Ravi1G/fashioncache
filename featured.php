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


	$results_per_page = RESULTS_PER_PAGE;

	if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
	$from = ($page-1)*$results_per_page;

	$query = "SELECT * FROM cashbackengine_retailers WHERE featured='1' AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' ORDER BY title ASC LIMIT $from, $results_per_page";

	$total_result = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE featured='1' AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' ORDER BY title ASC");
	$total = mysql_num_rows($total_result);

	$result = smart_mysql_query($query);
	$total_on_page = mysql_num_rows($result);

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_FEATURED_TITLE;

	require_once ("inc/header.inc.php");

?>

	<h1><?php echo CBE1_FEATURED_TITLE; ?></h1>


	<?php if ($total > 0) { ?>

			<p><?php echo CBE1_FEATURED_TEXT; ?></p>

			<table align="center" width="100%" border="0" cellspacing="0" cellpadding="5">
			<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
				<tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td width="125" align="center" valign="middle">
						<div class="imagebox"><a href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>"><img src="<?php if (!stristr($row['image'], 'http')) echo SITE_URL."img/"; echo $row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" border="0" /></a></div>
					</td>
					<td align="left" valign="bottom">
						<table width="100%" border="0" cellspacing="0" cellpadding="3">
							<tr>
								<td width="80%" align="left" valign="top">
									<a class="retailer_title" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>"><?php echo $row['title']; ?></a>
								</td>
								<td nowrap="nowrap" width="20%" align="right" valign="middle">
									<a class="coupons" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>#coupons" title="<?php echo $row['title']; ?> Coupons"><?php echo GetStoreCouponsTotal($row['retailer_id']); ?></a>
								</td>
							</tr>
							<tr>
								<td valign="middle" align="left"><p class="retailer_description"><?php echo $row['description']; ?>&nbsp;</p></td>
								<td valign="middle" align="center">
								<?php if ($row['cashback'] != "") { ?>
									<?php if ($row['old_cashback'] != "") { ?><span class="old_cashback"><?php echo DisplayCashback($row['old_cashback']); ?></span><?php } ?>
									<span class="cashback"><span class="value"><?php echo DisplayCashback($row['cashback']); ?></span> <?php echo CBE1_CASHBACK; ?></span>
								<?php } ?>
								</td>
							</tr>
							<tr>
								<td valign="middle" align="left">
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

			<?php echo ShowPagination("retailers",$results_per_page,"featured.php?","WHERE featured='1' AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active'"); ?>

	<?php }else{ ?>
		<p align="center">
			<?php echo CBE1_FEATURED_NO; ?><br/><br/>
			<a class="goback" href="#" onclick="history.go(-1);return false;"><?php echo CBE1_GO_BACK; ?></a>
		</p>
	<?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>