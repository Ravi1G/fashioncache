<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("inc/auth.inc.php");
	require_once("inc/config.inc.php");

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_ACCOUNT_TITLE;

	require_once ("inc/header.inc.php");

?>

	<h1><?php echo CBE1_ACCOUNT_TITLE; ?></h1>

	<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
	<div class="errorMessageContainer successMessageContainer">
		 <div class="leftContainer errorIcon">
                <img src="<?php echo SITE_URL;?>img/successIcon.png" alt="Error"/>
          </div>
          <div class="leftContainer">	
          <ul class="standardList errorList singleError">
        		<li>
        			<div class="errorMessage">
        				<?php if ($_GET['msg'] == "welcome") { ?><?php echo CBE1_ACCOUNT_MSG; ?><?php } ?>
        			</div>
        		</li>  	
          </ul>
          </div>  
		<div class="cb"></div>
	</div>
	<?php } ?>

	<table width="100%" align="center" cellpadding="3" cellspacing="0" border="0">
	<tr>
		<td align="left" valign="top">
			<p><?php echo str_replace("%username%",$_SESSION['FirstName'],CBE1_ACCOUNT_WELCOME); ?></p>
			
			<?php if (GetUserBalance($userid, 1) == 0) { ?><p><?php echo str_replace("%amount%",DisplayMoney("0.00"),CBE1_ACCOUNT_MSG2); ?></p><?php } ?>
			
			<h1><?php echo CBE1_ACCOUNT_START; ?></h1>
			<ul style="list-style:none; margin: 7px; line-height: 35px;">
				<li style="background: url('images/icons/1.png') no-repeat 0px 5px; padding-left: 35px;"><a href="<?php echo SITE_URL; ?>retailers.php"><?php echo CBE1_ACCOUNT_STEP1; ?></a></li>
				<li style="background: url('images/icons/2.png') no-repeat 0px 5px; padding-left: 35px;"><?php echo CBE1_ACCOUNT_STEP2; ?></li>
				<li style="background: url('images/icons/3.png') no-repeat 0px 5px; padding-left: 35px;"><?php echo CBE1_ACCOUNT_STEP3; ?></li>
			</ul>

			<p><?php echo CBE1_ACCOUNT_MSG3; ?></p>
  		</td>
	</tr>
	</table>

	<?php
		// show featured retailers //
		$result_featured = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE featured='1' AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' ORDER BY added DESC LIMIT ".FEATURED_STORES_LIMIT);
		$total_fetaured = mysql_num_rows($result_featured);

		if ($total_fetaured > 0) {
	?>
		<div style="clear: both;"></div>
		<h1><?php echo CBE1_ACCOUNT_FEATURED; ?></h1>
		<div class="featured_stores">
		<?php while ($row_featured = mysql_fetch_array($result_featured)) { $cc++; ?>
			<div class="imagebox"><a href="<?php echo GetRetailerLink($row_featured['retailer_id'], $row_featured['title']); ?>"><img src="<?php if (!stristr($row_featured['image'], 'http')) echo SITE_URL."img/"; echo $row_featured['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row_featured['title']; ?>" title="<?php echo $row_featured['title']; ?>" border="0" /></a></div>
		<?php } ?>
		</div>
		<div style="clear: both"></div>
	<?php } // end featured retailers ?>


<?php require_once ("inc/footer.inc.php"); ?>