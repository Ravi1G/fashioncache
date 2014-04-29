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


	$query = "SELECT * FROM cashbackengine_advertisements ORDER BY advertisement_id";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);
	$cc = 0;
	$title = "Categories";
	require_once ("inc/header.inc.php");
?>

		<div id="addnew"><!-- Uncomment to show add advertisement page -->
			<!-- <a class="addnew" href="advertisement_add.php">Add Advertisement</a>-->
		</div>

		<h2>Advertisements</h2>

        <?php if ($total > 0) { ?>

			<?php         	
        	$msg_type = isset($_GET['msg_type']) ? $_GET['msg_type'] : 'success';
			if (isset($_GET['msg']) && $_GET['msg'] && $msg_type=='success') { 
			?>
			<div style="width:60%;" class="success_box">
				<?php

					switch ($_GET['msg'])
					{
						case "added":	echo "Advertisement was successfully added"; break;
						case "exists":	echo "Sorry, same advertisement exists"; break;
						case "updated": echo "Advertisement has been successfully edited"; break;
						case "deleted": echo "Advertisement has been successfully deleted"; break;
					}

				?>
			</div>
			<?php } ?>
			
			<?php if(isset($_GET['msg']) && $_GET['msg'] && $msg_type=='error') { ?>
			<div style="width:60%;" class="error_box">
				<?php
					switch ($_GET['msg'])
					{
						case "invalid_ad":	echo "No such advertisement exists."; break;
					}
				?>
			</div>
			<?php } ?>

			<table align="center" class="tbl" width="100%" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<th class="noborder" width="5%">&nbsp;</th>
				<th width="30%">Title </th>
				<th width="50%">Link</th>
				<th width="15%">Actions</th>
			</tr>
             <?php $alladvertisements = array(); $alladvertisements = AdvertisementsList(0); 
             	
             foreach ($alladvertisements as $advertisement_id => $advertisement) { $cc++; ?>
				  <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td align="center"></td>
					<td align="left" valign="middle" class="row_title"><?php echo $advertisement['title']?></td>
					<td align="left" valign="middle" class="row_title"><?php echo $advertisement['link']?></td>
					<td nowrap="nowrap" align="center" valign="middle">
						<a href="advertisement_add.php?id=<?php echo $advertisement['advertisement_id']; ?>" title="Edit"><img border="0" alt="Edit" src="images/edit.png" /></a>
						<!-- <a href="#" onclick="if (confirm('Are you sure you really want to delete this advertisement?') )location.href='advertisement_delete.php?id=<?php echo $advertisement['advertisement_id']; ?>'" title="Delete"><img border="0" alt="Delete" src="images/delete.png" /></a> -->
					</td>
				  </tr>
			<?php } ?>
            </table>
          
		  <?php }else{ ?>
				<div class="info_box">There are no advertisements at this time.</div>
          <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>