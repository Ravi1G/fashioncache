<?php 
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("../inc/adm_auth.inc.php");
	require_once("../inc/config.inc.php");
	require_once("./inc/admin_funcs.inc.php");


	$query = "SELECT * FROM cashbackengine_banners ORDER BY banner_id";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);
	$cc = 0;
	$title = "Categories";
	require_once ("inc/header.inc.php");
?>

		<div id="addnew"><a class="addnew" href="banner_add_edit.php">Add Banner</a></div>

		<h2>banners</h2>

        <?php if ($total > 0) { ?>

			<?php         	
        	$msg_type = isset($_GET['msg_type']) ? $_GET['msg_type'] : 'success';
			if (isset($_GET['msg']) && $_GET['msg'] && $msg_type=='success') { 
			?>
			<div style="width:60%;" class="success_box">
				<?php

					switch ($_GET['msg'])
					{
						case "added":	echo "Banner was successfully added"; break;
						case "exists":	echo "Sorry, same Banner exists"; break;
						case "updated": echo "Banner has been successfully edited"; break;
						case "deleted": echo "Banner has been successfully deleted"; break;
					}

				?>
			</div>
			<?php } ?>
			
			<?php if(isset($_GET['msg']) && $_GET['msg'] && $msg_type=='error') { ?>
			<div style="width:60%;" class="error_box">
				<?php
					switch ($_GET['msg'])
					{
						case "invalid_ad":	echo "No such Banner exists."; break;
					}
				?>
			</div>
			<?php } ?>

			<table align="center" class="tbl" width="100%" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<th class="noborder" width="5%">&nbsp;</th>
				<th width="30%">Store </th>
				<th width="30%">Link</th>
				<th width="20%">Image</th>
				<th width="15%">Actions</th>
			</tr>
             <?php $allbanners = array(); $allbanners = BannersList(0); 
             foreach ($allbanners as $banner) { $cc++; ?>
				  <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td align="center"></td>
					<td align="left" valign="middle" class="row_title"><?php echo $banner['retailer_title']?></td>
					<td align="left" valign="middle" class="row_title"><?php echo $banner['link']?></td>
					<td align="left" valign="middle" class="row_title"><img src="<?php echo $banner['image']?>" style="max-height:50px; max-width:100px"></td>
					<td nowrap="nowrap" align="center" valign="middle">
						<a href="banner_add_edit.php?id=<?php echo $banner['banner_id']; ?>" title="Edit"><img border="0" alt="Edit" src="images/edit.png" /></a>
						<a href="#" onclick="if (confirm('Are you sure you really want to delete this banner?') )location.href='banner_delete.php?id=<?php echo $banner['banner_id']; ?>'" title="Delete"><img border="0" alt="Delete" src="images/delete.png" /></a>
					</td>
				  </tr>
			<?php } ?>
            </table>
          
		  <?php }else{ ?>
				<div class="info_box">There are no banners at this time.</div>
          <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>