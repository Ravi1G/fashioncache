<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $title; ?> | CashbackEngine Admin Panel</title>
<link href="css/cashbackengine.css" rel="stylesheet" type="text/css" />
<link href="<?php echo SITE_URL;?>css/colorbox.css" rel="stylesheet"/>
<script src="js/jquery-1.10.1.min.js" type="text/javascript"></script>
<script type="text/javascript" src="js/jquery.calendrical.js"></script>
<script language="javascript" src="js/scripts.js" type="text/javascript"></script>
<script src="<?php echo SITE_URL;?>js/jquery.colorbox-min.js"></script>
</head>
<body>

<div id="wrapper">

	<div id="header">
		<div id="logo"><a href="index.php"><img src="./images/logo.gif" border="0" /></a></div>
		<div id="right_header">
			Welcome, Admin! <a href="<?php echo SITE_URL; ?>" target="_blank">View Site</a> | <a class="logout" href="logout.php">Logout</a>
		</div>
	</div>

	<div id="content-wrapper">

		<div id="sidebar">
			<ul>
				<li><a href="index.php">Home</a></li>
				<li><a href="users.php">Members</a></li>
				<li><a href="advertisements.php">Advertisements</a></li>
				<li><a href="banners.php">Banners</a></li>
				<li><a href="retailers.php">Retailers</a></li>
				<?php if (GetRetailerReportsTotal() > 0) { ?>
					<li><a href="retailer_reports.php">Retailer Reports <span class="new_count" style="background:#F76E25;"><?php echo GetRetailerReportsTotal(); ?></span></a></li>
				<?php } ?>
				<li><a href="clicks.php">Click History</a></li>
				<li><a href="coupons.php">Coupons <?php if (GetNewCouponsTotal() > 0) { ?> <span class="new_count"><?php echo GetNewCouponsTotal(); ?></span><?php } ?></a></li>
				<li><a href="categories.php">Categories</a></li>
				<li><a href="trending_sales.php">Trending Sales</a></li>
				<li><a href="sale_alert_add_edit.php">Sale Alert</a></li>
				<!--<li><a href="countries.php">Countries</a></li>-->
				<!--<li><a href="reviews.php">Reviews</a></li>-->
				<?php if (GetInvitationsTotal() > 0) { ?>
					<li><a href="invitations.php">Invitations</a></li>
				<?php } ?>
				<li><a href="affnetworks.php">Affiliate Networks</a></li>
				<li><a href="payments.php">Payments</a></li>
				<li><a href="list_cashback.php">User Transactions</a></li>
				<?php if (GetRequestsTotal() > 0) { ?>
					<li><a href="cashout_requests.php">Cash Out Requests <span class="new_count"><?php echo GetRequestsTotal(); ?></span></a></li>
				<?php } ?>
				<li><a href="money2user.php">Manual Credit</a></li>
				<li><a href="csv_import.php">Upload CSV-Report</a></li>
				<?php /* <li><a href="messages.php">Messages <?php if (GetMessagesTotal() > 0) { ?> <span class="new_count"><?php echo GetMessagesTotal(); ?></span><?php } ?></a></li> */ ?>
				<!--<li><a href="pmethods.php">Payment Methods</a></li>-->
				<!--<li><a href="news.php">News</a></li>-->
				<!--<li><a href="content.php">Content</a></li>-->
				<li><a href="etemplates.php">Email Templates</a></li>
				<li><a href="email2users.php">Email Members</a></li>
				<li><a href="settings.php">Settings</a></li>
				<li><a href="logout.php" class="last">Log Out</a></li>
			</ul>
		</div>

		<div id="content">
