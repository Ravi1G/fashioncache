<?php 
global $advertisements;
$sale_alert =  getSaleAlert(1);
$advertisements = GetAdvertisements(array(HOME_PAGE_HEADER_AD_ID, HOME_PAGE_FOOTER_AD_ID,SIDEBAR_TOP_IMAGE, SIDEBAR_BOTTOM_IMAGE,HOME_PAGE_SIDEBAR_AD1_ID,HOME_PAGE_SIDEBAR_AD2_ID));
$menu_categories = GetMenuCategories();
require_once 'mobile_detect.php';
$detect = new Mobile_Detect;
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js">
<!--<![endif]-->
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<?php if ($detect->isMobile() && !$detect->isTablet()) {?>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"/>
<?php } else if ($detect->isMobile() && $detect->isTablet()) {?>
		<meta name="viewport" content="width=device-width, initial-scale=0.7"/>
<?php } else {?>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
<?php }?>
<title><?php echo $PAGE_TITLE." | ".SITE_TITLE; ?></title>
<?php if ($PAGE_DESCRIPTION != "") { ?><meta name="description" content="<?php echo $PAGE_DESCRIPTION; ?>" /><?php } ?>
<?php if ($PAGE_KEYWORDS != "") { ?><meta name="keywords" content="<?php echo $PAGE_KEYWORDS; ?>" /><?php } ?>
<?php if (FACEBOOK_CONNECT == 1 && FACEBOOK_APPID != "" && FACEBOOK_SECRET != "") { ?>
	<script type="text/javascript" src="http://connect.facebook.net/en_US/all.js#appId=<?php echo FACEBOOK_APPID; ?>&amp;xfbml=1"></script>
<?php } ?>

<!-- Cashback engine items -->
<script type="text/javascript" src="<?php echo SITE_URL; ?>js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL; ?>js/jsCarousel.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL; ?>js/cashbackengine.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL; ?>js/easySlider1.7.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL; ?>js/jquery.tools.tabs.min.js"></script>
<link rel="shortcut icon" href="<?php echo SITE_URL; ?>img/favicon.png" />
<link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>img/favicon.png" />
<!-- Cashback engine items ends -->



<!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

<link rel="stylesheet" href="<?php echo SITE_URL; ?>css/normalize.css"/>
<link rel="stylesheet" href="<?php echo SITE_URL; ?>css/jquery.megakrill.min.css"/>
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic,,300,600,800' rel='stylesheet' type='text/css'/>
<link rel="stylesheet" href="<?php echo SITE_URL; ?>css/fonts.css"/>
<link rel="stylesheet" href="<?php echo SITE_URL; ?>css/main.css"/>
<link href="<?php echo SITE_URL;?>css/jquery.bxslider.css" rel="stylesheet"/>
<link href="<?php echo SITE_URL;?>css/colorbox.css" rel="stylesheet"/>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.2.min.js"><\/script>')</script>
<script src="<?php echo SITE_URL;?>js/jquery.bxslider.js"></script>
<script src="<?php echo SITE_URL;?>js/vendor/modernizr-2.6.2.min.js"></script>
<script src="<?php echo SITE_URL;?>js/jquery.c.js"></script>
<script src="<?php echo SITE_URL;?>js/jquery.colorbox-min.js"></script>
<script src="<?php echo SITE_URL; ?>js/jquery.dataTables.js"></script>
<script src="<?php echo SITE_URL; ?>js/fb.js" type="text/javascript"></script>
 <?php if ($detect->isMobile() && !$detect->isTablet()) {?>
	 <script type="text/javascript">
	 	$('html').addClass("mobile");
	 </script>	
<?php } if ($detect->isMobile() && $detect->isTablet()) {?>
	 <script type="text/javascript">
	 	$('html').addClass("tablet");
	 </script>	
<?php }?>
<?php /* ?> 
         <script type="text/javascript" src="<?php echo SITE_URL?>js/fb.js"></script>
         <script type="text/javascript" src="//use.typekit.net/aym2cwi.js"></script>
<?php */ ?> 
<script>
	var BANNER_SPEED = <?php echo BANNER_SPEED;?>;
	var FACEBOOK_APPID= "<?php echo FACEBOOK_APPID;?>";
</script>
</head>
<body>
	<!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
<?php CheckCookieLogin();?>
	<div class="header">
		<div class="container">
			<div class="saleCouponsSection">
				<!-- <a href="<?php echo SITE_URL; ?>coupons.php">SEE ALL SALES &#x0026; COUPONS</a>--> <!-- <img alt="" src="<?php echo SITE_URL;?>img/downArrow.jpg"/>-->
				<a href="<?php echo SITE_URL; ?>retailers">SEE ALL STORES</a>
			</div>						
			<div class="userActivitySection">
				<?php if (isLoggedIn()) { ?>
				<div class="welcomeContainer1">
			      <div class="myAccount notResponsive">
			       <ul class="menuDropDown">       
			        <li class="drop">
			         <img alt="" src="<?php echo SITE_URL;?>img/login.jpg"/> <a href="<?php echo SITE_URL;?>myprofile.php" class="menuItem">my account</a>
			         <div class="dropdownContain">          
			          <div class="dropOut">
			           <div class="triangle"></div>
			           <?php 
				           	$query_paid ="SELECT SUM(amount) AS total FROM cashbackengine_transactions WHERE user_id='".(int)$_SESSION['userid']."' AND payment_type='cashback' AND status='confirmed'";
							$result_paid = smart_mysql_query($query_paid);
							$total_paid = mysql_fetch_assoc($result_paid);
							$total_paid = round($total_paid['total'],2);
							
							$query_pending = "SELECT SUM(amount) AS total FROM cashbackengine_transactions WHERE user_id='".(int)$_SESSION['userid']."' AND payment_type='cashback' AND status='pending'";
							$result_pending = smart_mysql_query($query_pending);
							$total_pending = mysql_fetch_assoc($result_pending);
							$total_pending = round($total_pending['total'],2);
			           ?>
			           <ul>
							<li><b>Pending Amount:</b> <?php echo DisplayMoney($total_pending);?></li>
							<!-- <li><a href="<?php echo SITE_URL; ?>invite.php#referrals"><span class="referrals"><b>My Referrals:</b> <?php echo GetReferralsTotal($_SESSION['userid']); ?></span></a></li> -->
							<li><span class="referrals"><b>Paid Amount:</b> <?php echo DisplayMoney($total_paid);?></span></li>             
							<li class="logoutTab">
								<b>
									<a href="<?php echo SITE_URL; ?>logout.php"><?php echo CBE_LOGOUT; ?></a>
								</b>
							</li>
			           </ul>
			           <div class="cb"></div>
			          </div>
			         </div>       
			        </li>       
			       </ul>
			      </div>
			      <div class="welcomeSection1">
			       <div class="welcomeText">welcome:</div>
			       <div class="userName"><a href="<?php echo SITE_URL; ?>myprofile.php"><span class="member"><?php echo $_SESSION['FirstName']; ?></span></a></div>
			      </div>
			     </div>
				
				
				
				<?php /*?>	<?php echo CBE_WELCOME; ?>, <a href="<?php echo SITE_URL; ?>myprofile.php"><span class="member"><?php echo $_SESSION['FirstName']; ?></span></a><!-- | <a href="<?php echo SITE_URL; ?>myaccount.php"><?php echo CBE_ACCOUNT ?></a>--> | <?php echo CBE_BALANCE; ?>: <span class="mbalance"><?php echo GetUserBalance($_SESSION['userid']); ?></span> | <?php echo CBE_REFERRALS; ?>: <a href="<?php echo SITE_URL; ?>invite.php#referrals"><span class="referrals"><?php echo GetReferralsTotal($_SESSION['userid']); ?></span></a> | <a href="<?php echo SITE_URL; ?>logout.php"><?php echo CBE_LOGOUT; ?></a><?php */?>
				<?php }else{ ?>
					<div class="signUpSection notResponsive">
						<div><span>New to Fashion-Cache?</span></div>
						<div class="signUpCaption"><a href="<?php echo SITE_URL; ?>signup_or_login.php"><?php echo CBE_SIGNUP; ?></a></div>
					</div>
					<div class="LoginSection">
						<div><img alt="" src="<?php echo SITE_URL;?>img/login.jpg"/> <span><a href="<?php echo SITE_URL; ?>signup_or_login.php"><?php echo CBE_LOGIN; ?></a></span></div>
					</div>
				<?php } ?>
				<div class="cb"></div>
			</div>
			<div class="cb"></div>
		</div>
	</div>
		<div class="container">	
			<div class="siteTitleSection notResponsive">
				<div class="siteTitle">
					<a href="<?php echo SITE_URL;?>">
						<img alt="Fashion Cache" src="<?php echo SITE_URL;?>img/logo.jpg"/>
					</a>
				</div>
				<div class="siteSubTitle">
					<a href="<?php echo SITE_URL;?>">SHOP &#x0026; EARN CASH BACK</a>
				</div>
			</div>
			<div class="advertisementOf480 notResponsive">				
				<div class="sampleAdvertisement">
					<a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $advertisements[HOME_PAGE_HEADER_AD_ID]['retailer_id']; ?>&a=<?php echo $advertisements[HOME_PAGE_HEADER_AD_ID]['advertisement_id']?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>>
						<img height="60" width="480" src="<?php echo $advertisements[HOME_PAGE_HEADER_AD_ID]['image_url']!='' ? $advertisements[HOME_PAGE_HEADER_AD_ID]['image_url'] : SITE_URL.'admin/'.$advertisements[HOME_PAGE_HEADER_AD_ID]['image_name']?>">
					</a>
				</div>
				<div class="cb"></div>
			</div>
			<div class="cb"></div>
			<div class="navigationSection notResponsive">
			<?php 
				$pageURL = $_SERVER["REQUEST_URI"];
				$page = explode("/" , $pageURL);
				$cat_name =$page[2];
				$cat_name = urldecode($cat_name);
				
				$slug_value = $page[2];
				$slug_value = urldecode($slug_value);
				$slug_value = str_replace("&","&amp;",$slug_value);
				
				//$cat_name = str_replace("&","&amp;",$cat_name);
				
				//echo $cat_name = str_replace("-"," ",$cat_name);
				
				//$cat_name = mysql_real_escape_string($cat_name);
				//echo $cat_name= str_replace("%20",' ', $cat_name);
			?>
				<ul>
					<li <?php if($pageURL=="/retailers"){?>class="active"<?php }?>><a href="<?php echo SITE_URL; ?>retailers">ALL STORES</a></li>
					<?php 
					if($menu_categories){ 
						foreach($menu_categories as $menu_category){
					?>
						<li <?php
						if($menu_category['slug']==$slug_value)
							{?>class="active"<?php }?>>
							<!-- <a href="<?php echo SITE_URL;?>retailers.php?cat=<?php echo $menu_category['category_id']?>&show=111111"><?php echo $menu_category['name']?></a>-->
							<!-- <a href="<?php echo SITE_URL;?>retailers/<?php $cat_name = str_replace(" ","-",$menu_category['name']); echo $cat_name;?>"><?php echo $menu_category['name'];?></a>-->
							<a href="<?php echo SITE_URL;?>retailers/<?php echo $menu_category['slug'];?>"><?php echo $menu_category['name'];?></a>
							</li>
					<?php 
						}
					}
					?>
					<li <?php if($pageURL=='/blog/'){?>class="active last"<?php } else {?>class="last"<?php }?>><a href="<?php echo BLOG_URL; ?>">BLOG</a></li>
				</ul>
				<?php /* <div class="searchContainer">
					<form action="<?php echo SITE_URL; ?>search.php" method="get" id="searchfrm" name="searchfrm" onSubmit="if(searchtext.value==searchtext.defaultValue) return false" >
						<div class="searchIcon"><img alt="" src="<?php echo SITE_URL;?>img/search.jpg"/></div>
						<div>
							<input type="text" name="searchtext" placeholder="Search for stores..." class="searchCriteria" />
							<input type="hidden" name="action" value="search" />
							<!-- <input type="submit" class="search_button" value="" /> -->
						</div>
					</form>
				</div> */ ?>
				<div class="cb"></div>
			</div>
			
			<div class="isResponsive responsiveMenu">
				<div class="siteLogoView">
					<a href="<?php echo SITE_URL;?>"><span class="virtualLink">&#x00A0;</span></a>
					<span class="titleLogoFashionCache"><img alt="Fashion Cache" src="<?php echo SITE_URL;?>img/logo.jpg"/></span>
					<span class="subTitleFashionCache">SHOP &#x0026; EARN CASH BACK</span>
				</div>
								
				
				<ul id="reponsiveNavigation" class="demo-ul">
						<li class="demo-li <?php if($pageURL=="/retailers"){?>active<?php }?>">
							<a class="demo-a" href="<?php echo SITE_URL; ?>retailers">ALL STORES</a>
						</li>
					<?php 
					if($menu_categories){ 
						foreach($menu_categories as $menu_category){
					?>
						<li class="demo-li <?php
						if($menu_category['slug']==$slug_value)
							{?>active<?php }?>">
							
							<!-- <a href="<?php echo SITE_URL;?>retailers.php?cat=<?php echo $menu_category['category_id']?>&show=111111"><?php echo $menu_category['name']?></a>-->
							<!-- <a href="<?php echo SITE_URL;?>retailers/<?php $cat_name = str_replace(" ","-",$menu_category['name']); echo $cat_name;?>"><?php echo $menu_category['name'];?></a>-->
							<a  class="demo-a" href="<?php echo SITE_URL;?>retailers/<?php echo $menu_category['slug'];?>"><?php echo $menu_category['name'];?></a>
							</li>
					<?php 
						}
					}
					?>
					<li class="demo-li <?php if($pageURL=='/blog/'){?>active<?php }?>">
						<a class="demo-a" href="<?php echo BLOG_URL; ?>">BLOG</a>
					</li>
					<?php if (isLoggedIn()) { ?>
					<li class="demo-li">
							<a class="demo-a statusTab" href="<?php echo SITE_URL;?>myprofile.php">My Account
								<span class="statusTab1">Pending Amount: <span><?php echo DisplayMoney($total_pending);?></span></span>								
								<span class="statusTab2">Paid Amount: <span><?php echo DisplayMoney($total_paid);?></span></span>
							</a>           
					</li>
					<li class="demo-li">
						<a class="demo-a logoutLink" href="<?php echo SITE_URL; ?>logout.php"><?php echo CBE_LOGOUT; ?></a>
					</li>					
					<?php }  ?>
					
				</ul>
				<div class="cb"></div>
			</div>
			<?php /* ?>
			<?php if(!isset($is_index)){?>
			<div class="saleAlertSection siteInnerBanner">
			  <div class="sectionTitle"><span>SALE ALERT!</span><a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $sale_alert['retailer_id']; ?>&s=<?php echo $sale_alert['sale_alert_id']; ?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>><?php echo $sale_alert['title']?></a></div>
			</div>
			<?php } ?>
			<?php */ ?>
		</div>
		<!-- No record functionality is pending -->
<?php 
/*
?>

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
<?php 
*/
?>
