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
	
	$a = isset($_GET['a']) && is_numeric($_GET['a']) && $_GET['a']>0 ? $_GET['a'] : 0;
	$b = isset($_GET['b']) && is_numeric($_GET['b']) && $_GET['b'] > 0 ? $_GET['b'] : 0;
	$s = isset($_GET['s']) && is_numeric($_GET['s']) && $_GET['s'] > 0 ? $_GET['s'] : 0;

	if ($a || $b || $s)
	{
		
		$table_name = '';
		if($a)
		{
			$table_name = 'cashbackengine_advertisements';
			$other_pk = 'advertisement_id';
			$other_field = 'a';	
			$other_id = $a;
		}
		elseif($b)
		{	
			$table_name = 'cashbackengine_banners';
			$other_pk = 'banner_id';
			$other_field = 'b';
			$other_id = $b;
		}
		elseif($s)
		{
			$table_name = 'cashbackengine_sale_alert';
			$other_pk = 'sale_alert_id';
			$other_field = 's';
			$other_id = $s;	
		}
			
		if($table_name)
		{
			$other_result = smart_mysql_query("SELECT * FROM $table_name WHERE $other_pk=$other_id");
			if (mysql_num_rows($other_result) > 0)
			{
				$other_row = mysql_fetch_array($other_result);
				$other_link = $other_row['link'];
	
				if ($other_link != "")
				{
					$website_url = str_replace("{USERID}", $userid, $other_link);
				}
				
			}
		}
	}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="refresh" content="2; url=<?php echo $website_url; ?>" />
<link href="http://fonts.googleapis.com/css?family=Open+Sans+Condensed:700" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="<?php echo SITE_URL; ?>favicon.ico" />
<link rel="icon" type="image/ico" href="<?php echo SITE_URL; ?>favicon.ico" />
<?php 
session_start();
include_once 'inc/config.inc.php';?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js">
    <!--<![endif]-->
    <head>
          <title>Visit <?php echo $row['title']; ?> and Earn <?php echo $cashback; ?> Cashback - <?php echo SITE_TITLE; ?></title>
		  <meta http-equiv="refresh" content="2; url=<?php echo $website_url; ?>" />
          <?php include 'inc/common.php'; ?>          
    </head>
	<body>

<style type="text/css">
body {
	background-color:#EBEBEB;
	position:relative;
}
</style>
</head>
<body>
		<div class="transitionPopup">
			<div class="themePink">
				<div class="loadingLine"><?php echo CBE1_REDIRECT_TEXT; ?> <img src="<?php echo SITE_URL;?>img/3dot.gif" alt="Loading"/></div>
				<div class="descriptionMessage">Congratulations <?php echo $_SESSION['FirstName']; ?>,<br/>You&#x2019;re on your way to <?php echo $cashback; ?> Cash Back<br/>at <?php echo $row['title']; ?></div>
			</div>
			<div class="themeWhite">
				<div class="webIcon"><img src="<?php echo SITE_URL;?>img/logo.png" alt="Fashion Cache"/></div>
				<div>Thank You for using Fashion Cache.</div>
			</div>
		</div>
<?php /* ?>
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
<?php */ ?>

<?php echo GOOGLE_ANALYTICS; ?>
<!-- Google Code for Sign Up Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 1008044803;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "gZ5MCI2fowgQg5bW4AM";
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/1008044803/?label=gZ5MCI2fowgQg5bW4AM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
</body>
</html>