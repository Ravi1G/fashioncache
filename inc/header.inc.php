<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $PAGE_TITLE." | ".SITE_TITLE; ?></title>
	<?php if ($PAGE_DESCRIPTION != "") { ?><meta name="description" content="<?php echo $PAGE_DESCRIPTION; ?>" /><?php } ?>
	<?php if ($PAGE_KEYWORDS != "") { ?><meta name="keywords" content="<?php echo $PAGE_KEYWORDS; ?>" /><?php } ?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="http://fonts.googleapis.com/css?family=Open+Sans+Condensed:700" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>css/style.css" />
	<script type="text/javascript" src="<?php echo SITE_URL; ?>js/jquery-1.4.2.min.js"></script>
	<?php if (FACEBOOK_CONNECT == 1 && FACEBOOK_APPID != "" && FACEBOOK_SECRET != "") { ?>
		<script type="text/javascript" src="http://connect.facebook.net/en_US/all.js#appId=<?php echo FACEBOOK_APPID; ?>&amp;xfbml=1"></script>
	<?php } ?>
	<script type="text/javascript" src="<?php echo SITE_URL; ?>js/jquery.autocomplete.js"></script>
	<script type="text/javascript" src="<?php echo SITE_URL; ?>js/jsCarousel.js"></script>
	<script type="text/javascript" src="<?php echo SITE_URL; ?>js/cashbackengine.js"></script>
	<script type="text/javascript" src="<?php echo SITE_URL; ?>js/easySlider1.7.js"></script>
	<script type="text/javascript" src="<?php echo SITE_URL; ?>js/jquery.tools.tabs.min.js"></script>
	<link rel="shortcut icon" href="<?php echo SITE_URL; ?>favicon.ico" />
	<link rel="icon" type="image/ico" href="<?php echo SITE_URL; ?>favicon.ico" />
</head>
<body>

<div id="container">

	<div id="header">
		<a href="#" class="scrollup">Top</a>
		<div id="logo"><a href="<?php echo SITE_URL; ?>"><img src="<?php echo SITE_URL; ?>images/logo.png" alt="<?php echo SITE_TITLE; ?>" title="<?php echo SITE_TITLE; ?>" border="0" /></a></div>
		<div id="links">
			<?php if (MULTILINGUAL == 1 && count($languages) > 0) { ?>
				<div id="languages">
				<?php foreach ($languages AS $language_code => $language) { ?>
					<a href="<?php echo SITE_URL; ?>?lang=<?php echo $language; ?>"><img src="<?php echo SITE_URL; ?>images/flags/<?php echo $language_code; ?>.png" alt="<?php echo $language; ?>" border="0" /></a>&nbsp;
				<?php } ?>
				</div>
			<?php } ?>
			<?php if (isLoggedIn()) { ?>
				<?php echo CBE_WELCOME; ?>, <a href="<?php echo SITE_URL; ?>myprofile.php"><span class="member"><?php echo $_SESSION['FirstName']; ?></span></a><!-- | <a href="<?php echo SITE_URL; ?>myaccount.php"><?php echo CBE_ACCOUNT ?></a>--> | <?php echo CBE_BALANCE; ?>: <span class="mbalance"><?php echo GetUserBalance($_SESSION['userid']); ?></span> | <?php echo CBE_REFERRALS; ?>: <a href="<?php echo SITE_URL; ?>invite.php#referrals"><span class="referrals"><?php echo GetReferralsTotal($_SESSION['userid']); ?></span></a> | <a href="<?php echo SITE_URL; ?>logout.php"><?php echo CBE_LOGOUT; ?></a>
			<?php }else{ ?>
				<a class="signup" href="<?php echo SITE_URL; ?>signup.php"><?php echo CBE_SIGNUP; ?></a> <a class="login" href="<?php echo SITE_URL; ?>login.php"><?php echo CBE_LOGIN; ?></a>
			<?php } ?>
		</div>
		<div id="searchbox">
			<form action="<?php echo SITE_URL; ?>search.php" method="get" id="searchfrm" name="searchfrm" onsubmit="if(searchtext.value==searchtext.defaultValue) return false">
				<input type="text" id="searchtext" name="searchtext" class="search_textbox" value="<?php echo (isset($stext)) ? $stext : CBE_SEARCH_MSG; ?>" onclick="if (this.defaultValue==this.value) this.value=''" onkeydown="this.style.color='#000000'" onblur="if (this.value=='') this.value=this.defaultValue" />
				<input type="hidden" name="action" value="search" />
				<input type="submit" class="search_button" value="" />
			</form>
		</div>	
	</div>

	<div id="menu">
		<a href="<?php echo SITE_URL; ?>"><?php echo CBE_MENU_HOME; ?></a>
		<a href="<?php echo SITE_URL; ?>retailers.php"><?php echo CBE_MENU_STORES; ?></a>
		<a href="<?php echo SITE_URL; ?>coupons.php"><?php echo CBE_MENU_COUPONS; ?></a>
		<a href="<?php echo SITE_URL; ?>featured.php"><?php echo CBE_MENU_FEATURED; ?></a>
		<a href="<?php echo SITE_URL; ?>myaccount.php" rel="nofollow"><?php echo CBE_MENU_ACCOUNT; ?></a>
		<a href="<?php echo SITE_URL; ?>myfavorites.php" rel="nofollow"><?php echo CBE_MENU_FAVORITES; ?></a>
		<a href="<?php echo SITE_URL; ?>howitworks.php"><?php echo CBE_MENU_HOW; ?></a>
		<a href="<?php echo SITE_URL; ?>help.php"><?php echo CBE_MENU_HELP; ?></a>
		<?php echo ShowTopPages(); ?>
	</div>

<div id="column_left">

	<?php if (isset($_SESSION['userid']) && is_numeric($_SESSION['userid'])) { ?>
		<?php require_once ("inc/usermenu.inc.php"); ?>
	<?php }else{ ?>
		<div class="box">
			<div class="top"><?php echo CBE1_BOX_LOGIN; ?></div>
			<div class="middle">
				<form action="<?php echo SITE_URL; ?>login.php" method="post">
					<table border="0" cellspacing="0" cellpadding="1">
					<tr><td align="left" valign="top"><?php echo CBE1_LOGIN_EMAIL; ?>:<br/><input type="text" class="textbox" name="username" value="" size="23" /></td></tr>	<tr><td align="left" valign="top"><?php echo CBE1_LOGIN_PASSWORD; ?>:<br/><input type="password" class="textbox" name="password" value="" size="23" /></td></tr>
					<tr><td align="left" valign="top"><input type="checkbox" class="checkboxx" name="rememberme" id="rememberme" value="1" <?php echo (@$rememberme == 1) ? "checked" : "" ?>/> <?php echo CBE1_LOGIN_REMEMBER; ?></td></tr>
					<tr>
						<td align="left" valign="top">
							<input type="hidden" name="action" value="login" />
							<input type="submit" class="submit" name="login" id="login" value="<?php echo CBE1_LOGIN_BUTTON; ?>" />
						</td>
					</tr>
					</table>
					<?php if (FACEBOOK_CONNECT == 1 && FACEBOOK_APPID != "" && FACEBOOK_SECRET != "") { ?>
						<p align="center"><a href="javascript: void(0);" onclick="facebook_login();" class="connect-f"><img src="<?php echo SITE_URL; ?>images/facebook_connect.png" /></a></p>
					<?php } ?>
					<a href="<?php echo SITE_URL; ?>forgot.php"><?php echo CBE1_LOGIN_FORGOT; ?></a><br/>
					
				</form>
			</div>
			<div class="bottom">&nbsp;</div>
		</div>
		<div class="box">
			<div class="top"><?php echo CBE1_SIGNUP_TITLE; ?></div>
			<div class="middle">
				<form action="<?php echo SITE_URL; ?>shortsignup.php" method="post">
					<table border="0" cellspacing="0" cellpadding="1">
					<tr><td align="left" valign="top"><?php echo CBE1_LOGIN_EMAIL; ?>:<br/><input type="text" class="textbox" name="email" value="" size="23" /></td></tr>	<tr><td align="left" valign="top"><?php echo CBE1_LOGIN_PASSWORD; ?>:<br/><input type="password" class="textbox" name="password" value="" size="23" /></td></tr>
					
          <tr>
					<tr>
						<td align="left" valign="top">
						<?php if (isset($_COOKIE['referer_id']) && is_numeric($_COOKIE['referer_id'])) { ?>
							<input type="hidden" name="referer_id" id="referer_id" value="<?php echo (int)$_COOKIE['referer_id']; ?>" />
						<?php } ?>
							<input type="hidden" name="action" value="signup" />
							<input type="submit" class="submit" name="signup" id="signup" value="<?php echo CBE1_SIGNUP_BUTTON; ?>" />
						</td>
					</tr>
					</table>
				</form>
			</div>
			<div class="bottom">&nbsp;</div>
		</div>
	  <?php } ?>

		<?php if (GetStoresTotal() > 0) { ?>
		<div class="box">
			<div class="top"><?php echo CBE1_BOX_SBS; ?></div>
			<div class="middle">

				<form name="rform" id="rform" method="get" action="<?php echo SITE_URL; ?>view_retailer.php">
				<select name="id" id="id" onChange="document.rform.submit()" style="width: 160px;">
				<option value=""><?php echo str_replace("%total%",GetStoresTotal(),CBE1_BOX_SBS_SELECT); ?></option>
				<?php
					$select_allstores = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' ORDER BY title ASC");
					while ($srow_allstores = mysql_fetch_array($select_allstores))
					{
						$s_first_letter = ucfirst(substr($srow_allstores['title'], 0, 1));
						if ($s_old_letter != $s_first_letter) { echo "<option disabled=\"disabled\" class=\"sletter\">$s_first_letter</option>"; $s_old_letter = $s_first_letter; }
						echo "<option value=\"".$srow_allstores['retailer_id']."\">".$srow_allstores['title']." ".DisplayCashback($srow_allstores['cashback'])."</option>";
					}
				?>
				</select>
				</form>

			</div>
			<div class="bottom">&nbsp;</div>
		</div>
		<?php } ?>
	
       <div class="box">
			<div class="top"><?php echo CBE1_BOX_SBC; ?></div>
			<div class="middle">
				<ul id="categories">
					<li><a href="<?php echo SITE_URL; ?>retailers.php"><?php echo CBE1_BOX_ALLSTORES; ?></a></li>
					<?php ShowCategories(0); ?>
				</ul>
			</div>
			<div class="bottom">&nbsp;</div>
		</div>

       <?php if (SHOW_SITE_STATS == 1) { ?>
       <div class="box">
			<div class="top"><?php echo CBE1_BOX_STATS; ?></div>
			<div class="middle">
				<div class="statistics">
					<?php echo CBE1_BOX_STATS_TITLE1; ?><br/>
					<span><?php echo GetStoresTotal(); ?></span><br/>
					<?php echo CBE1_BOX_STATS_TITLE2; ?><br/>
					<span><?php echo GetCouponsTotal(); ?></span><br/>
					<?php echo CBE1_BOX_STATS_TITLE3; ?><br/>
					<span><?php echo GetUsersTotal(); ?></span><br/>
					<?php echo CBE1_BOX_STATS_TITLE4; ?>
					<span class="allcashback"><?php echo GetCashbackTotal(); ?></span>
				</div>
			</div>
			<div class="bottom">&nbsp;</div>
		</div>
		<?php } ?>

</div>

<div id="column_center">
