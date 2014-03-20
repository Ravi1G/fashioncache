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

	$userid = (int)$_SESSION['userid'];


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
		
		$cashback		= DisplayCashback($row['cashback']);
		$website_url	= str_replace("{USERID}", $userid, $row['url']);
	}
	else
	{
		header ("Location: index.php");
		exit();
	}

	
	if (isset($_GET['c']) && is_numeric($_GET['c']) && $_GET['c'] > 0)
	{
		$coupon_id = (int)$_GET['c'];

		$coupon_query = "SELECT * FROM cashbackengine_coupons WHERE coupon_id='$coupon_id' LIMIT 1";
		$coupon_result = smart_mysql_query($coupon_query);

		if (mysql_num_rows($coupon_result) > 0)
		{
			$coupon_row = mysql_fetch_array($coupon_result);
			$coupon_link = $coupon_row['link'];

			if ($coupon_link != "")
			{
				$website_url = str_replace("{USERID}", $userid, $coupon_link);
			}
		}
	}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Visit <?php echo $row['title']; ?> and Earn <?php echo $cashback; ?> Cashback - <?php echo SITE_TITLE; ?></title>
<meta http-equiv="refresh" content="2; url=<?php echo $website_url; ?>" />
<link href="http://fonts.googleapis.com/css?family=Open+Sans+Condensed:700" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="<?php echo SITE_URL; ?>favicon.ico" />
<link rel="icon" type="image/ico" href="<?php echo SITE_URL; ?>favicon.ico" />
<style type="text/css">
<!--
body {
	background: #5B5B5B;
	font-family: Tahoma, Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #000000;
	margin: 0;
	padding: 0;
}

.container {
	width: 100%;
	height: 100%;
	position: absolute;
	top: 25%;
	left: 35%;
	width: 350px;
}

.box {
	float: left;
	height: 220px;
	width: 450px;
	padding: 0 20px 20px 20px;
	text-align: left;
	border: 1px solid #DDD;
	background: #FFFFFF;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
	position: relative;
	-moz-box-shadow: 0 0 5px 5px #333333;
	-webkit-box-shadow: 0 0 5px 5px #333333;
	box-shadow: 0 0 5px 5px #333333;
}

.msg {
	font-family: 'Open Sans Condensed', Times, "Lucida Grande", "Lucida Sans Unicode", Arial, Verdana, sans-serif;
	font-size: 20px;
	font-weight: 600;
	color: #777777;
	line-height: 30px;
	text-align: left;
}

.msg .username {
	color:#000;
}

.cashback {
	font-family: 'Open Sans Condensed', Times, "Lucida Grande", "Lucida Sans Unicode", Arial, Verdana, sans-serif;
	font-size: 26px;
	font-weight: 600;
	color: #84E028;
	line-height: 30px;
	text-align: left;
}

.store-name {
	line-height: 30px;
	font-family: 'Open Sans Condensed', Times, "Lucida Grande", "Lucida Sans Unicode", Arial, Verdana, sans-serif;
	font-size: 22px;
	font-weight: 500;
	color: #62AAF7;
	text-align: left;
}

.logo {
	position: absolute;
	float: right;
	top: 105px;
	right: 15px;
	border: 5px solid #EEEEEE;
}
-->
</style>
</head>
<body>
<div class="container">
	<div class="box">
		<p align="center"><?php echo CBE1_REDIRECT_TEXT; ?><br/><br/><img src="<?php echo SITE_URL; ?>images/loading.gif"></p>
		<div class="msg">
			<span class="username"><?php echo $_SESSION['FirstName']; ?></span>, <?php echo CBE1_REDIRECT_TEXT2; ?>:
			<br/>
			<span class="cashback"><?php echo CBE1_REDIRECT_TEXT3; ?> <?php echo $cashback; ?> CASHBACK</span>
			<br/><?php echo CBE1_REDIRECT_TEXT4; ?>
		</div>
		<div class="store-name"><?php echo $row['title']; ?></div>
		<?php if ($row['image'] != "noimg.gif") { ?>
			<img src="<?php if (!stristr($row['image'], 'http')) echo SITE_URL."img/"; echo $row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" border="0" class="logo" />
		<?php } ?>
	</div>
</div>     
</body>
</html>