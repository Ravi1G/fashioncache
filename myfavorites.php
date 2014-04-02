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

	$retailer_id = (int)$_GET['id'];
	$cc = 0;


	if (isset($_GET['act']) && $_GET['act'] == "add")
	{
		$check_query = smart_mysql_query("SELECT * FROM cashbackengine_favorites WHERE user_id='$userid' AND retailer_id='$retailer_id'");

		if (mysql_num_rows($check_query) == 0)
		{
			smart_mysql_query("INSERT INTO cashbackengine_favorites SET user_id='$userid', retailer_id='$retailer_id', added=NOW()");
		}

		header("Location: myfavorites.php?msg=added");
		exit();
	}


	if (isset($_GET['act']) && $_GET['act'] == "del")
	{
		$del_query = "DELETE FROM cashbackengine_favorites WHERE user_id='$userid' AND retailer_id='$retailer_id'";
		if (smart_mysql_query($del_query))
		{
			header("Location: myfavorites.php?msg=deleted");
			exit();
		}
	}

	
	$query = "SELECT cashbackengine_favorites.*, cashbackengine_retailers.* FROM cashbackengine_favorites cashbackengine_favorites, cashbackengine_retailers cashbackengine_retailers WHERE cashbackengine_favorites.user_id='$userid' AND cashbackengine_favorites.retailer_id=cashbackengine_retailers.retailer_id AND (cashbackengine_retailers.end_date='0000-00-00 00:00:00' OR cashbackengine_retailers.end_date > NOW()) AND cashbackengine_retailers.status='active' ORDER BY cashbackengine_retailers.title ASC";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);


	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_MYFAVORITES_TITLE;

	require_once ("inc/header.inc.php");
	
?>


<div class="container content standardContainer blog">
    <!-- Search List -->			
    <div class="SiteContentSection">
        <div class="SiteContentLeft">
           <h1><?php echo CBE1_MYFAVORITES_TITLE; ?></h1>
              <?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
			
				<div class="errorMessageContainer successMessageContainer">
				 <div class="leftContainer errorIcon">
		                <img src="<?php echo SITE_URL;?>img/successIcon.png" alt="Error"/>
		          </div>
		          <div class="leftContainer">	
		          <ul class="standardList errorList singleError">
		        		<li><div class="errorMessage">
		        			<?php if ($_GET['msg'] == "added") { ?><?php echo CBE1_MYFAVORITES_MSG1 ?><?php } ?>
		        			<?php if ($_GET['msg'] == "deleted") { ?><?php echo CBE1_MYFAVORITES_MSG2 ?><?php } ?>
		        			</div>
		        		</li>  	
		          </ul>
		          </div>  
				<div class="cb"></div>
				</div>
			
		<?php } ?>
		
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
                    </td>
                    
                    <td class="columnTwo">
						 <div class="offerName"><?php echo $row['title'];?> <span class="offerExpiryDate">(<?php echo $row['cashback'];?> Cashback)</span></div>                        
                        <div class="requirement">
							<div class="offerDetail"><?php
								echo substr(strip_tags($row['description']),0,161).'...';?> 
							</div>
						</div>
                    </td>
                    <td class="columnThree">
                        <div class="shareOnSocialMedia">
                    	<a href="http://www.facebook.com/sharer.php?u=<?php echo urlencode(GetRetailerLink($row['retailer_id'], $row['title'])); ?>&t=<?php echo $row['title']; ?>" target="_blank" title="<?php echo CBE1_SHARE_FACEBOOK; ?>"><img src="<?php echo SITE_URL; ?>images/icon_facebook.png"  alt="<?php echo CBE1_SHARE_FACEBOOK; ?>" /></a> &nbsp;
						<a href="http://twitter.com/intent/tweet?source=sharethiscom&text=<?php echo $row['title']; ?>&url=<?php echo urlencode(GetRetailerLink($row['retailer_id'], $row['title'])); ?>" target="_blank" title="<?php echo CBE1_SHARE_TWITTER; ?>"><img src="<?php echo SITE_URL; ?>images/icon_twitter.png" alt="<?php echo CBE1_SHARE_TWITTER; ?>" /></a>
					</div>
						<div class="shopNowBotton siteButton searchRetailerShopNow">
                            <a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>&c=<?php echo $row['coupon_id']; ?>"><span>SHOP NOW</span></a>
                        </div>
						<div class="shopNowBotton siteButton searchRetailerShopNow">
                            <a href="#" onclick="if (confirm('<?php echo CBE1_MYFAVORITES_DELETE; ?>') )location.href='<?php echo SITE_URL; ?>myfavorites.php?act=del&id=<?php echo $row['retailer_id']; ?>'" title="<?php echo CBE1_MYFAVORITES_DEL; ?>">
                            	<span>Remove Favourite</span>
                            </a>
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





	<!-- OLD DESIGN & FUNCTIONALITY -->

<?php /*?>
		  <?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
			<div class="success_msg">
				<?php
					switch ($_GET['msg'])
					{
						case "added": echo CBE1_MYFAVORITES_MSG1; break;
						case "deleted": echo CBE1_MYFAVORITES_MSG2; break;
					}
				?>
			</div>
		<?php } ?>


	<?php

		if ($total > 0) {
 
	?>
			<p align="center"><?php echo CBE1_MYFAVORITES_TEXT; ?></p>
			<div class="sline"></div><br/>

			<table align="center" width="100%" border="0" cellspacing="0" cellpadding="5">
			<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
				<tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td width="125" align="center" valign="middle">
						<a href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>">
						<div class="imagebox"><img src="<?php if (!stristr($row['image'], 'http')) echo SITE_URL."img/"; echo $row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" border="0" /></div>
						</a>
						<a href="#" onclick="if (confirm('<?php echo CBE1_MYFAVORITES_DELETE; ?>') )location.href='<?php echo SITE_URL; ?>myfavorites.php?act=del&id=<?php echo $row['retailer_id']; ?>'" title="<?php echo CBE1_MYFAVORITES_DEL; ?>"><img src="<?php echo SITE_URL; ?>images/delete.gif" border="0" alt="<?php echo CBE1_MYFAVORITES_DEL; ?>" /></a>
					</td>
					<td align="left" valign="bottom">
						<table width="100%" border="0" cellspacing="0" cellpadding="3">
							<tr>
								<td width="80%" align="left" valign="middle">
									<a class="retailer_title" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>"><?php echo $row['title']; ?></a>
								</td>
								<td nowrap="nowrap" width="20%" align="right" valign="midle">
								<?php if ($row['cashback'] != "") { ?>
									<?php if ($row['old_cashback'] != "") { ?><span class="old_cashback"><?php echo DisplayCashback($row['old_cashback']); ?></span><?php } ?>
									<span class="cashback"><?php echo DisplayCashback($row['cashback']); ?> <?php echo CBE1_CASHBACK; ?></span>
								<?php } ?>
								</td>
							</tr>
							<tr>
								<td colspan="2" valign="middle" align="left"><p class="retailer_description"><?php echo $row['description']; ?>&nbsp;</p></td>
							</tr>
							<tr>
								<td valign="middle" align="left">
								<?php if ($row['conditions'] != "") { ?>
									<div class="cashbackengine_tooltip">
										<a class="conditions" href="#"><?php echo CBE1_CONDITIONS; ?></a> <span class="tooltip"><?php echo $row['conditions']; ?></span>
									</div>
								<?php } ?>
								</td>
								<td valign="middle" align="right">
									<a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>" target="_blank"><?php echo CBE1_GO_TO_STORE; ?></a>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			<?php } ?>
           </table>

    <?php }else{ ?>
			<p align="center">
				<?php echo CBE1_MYFAVORITES_NO; ?><br/><br/>
				<a class="button" href="<?php echo SITE_URL; ?>retailers.php"><?php echo CBE1_MYFAVORITES_ADD; ?></a>
			</p>
     <?php } */?>

<?php require_once ("inc/footer.inc.php"); ?>