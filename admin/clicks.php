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
	require_once("../inc/pagination.inc.php");
	require_once("./inc/admin_funcs.inc.php");


	// results per page
	if (isset($_GET['show']) && is_numeric($_GET['show']) && $_GET['show'] > 0)
		$results_per_page = (int)$_GET['show'];
	else
		$results_per_page = 10;


		$where = "1=1";

		////////////////// filter  //////////////////////
			if (isset($_GET['column']) && $_GET['column'] != "")
			{
				switch ($_GET['column'])
				{
					case "added": $rrorder = "added"; break;
					case "retailer_id": $rrorder = "retailer_id"; break;
					case "user_id": $rrorder = "user_id"; break;
					default: $rrorder = "added"; break;
				}
			}
			else
			{
				$rrorder = "added";
			}

			if (isset($_GET['order']) && $_GET['order'] != "")
			{
				switch ($_GET['order'])
				{
					case "asc": $rorder = "asc"; break;
					case "desc": $rorder = "desc"; break;
					default: $rorder = "desc"; break;
				}
			}
			else
			{
				$rorder = "desc";
			}
		///////////////////////////////////////////////////////

		if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
		$from = ($page-1)*$results_per_page;

		if (isset($_GET['store']) && is_numeric($_GET['store']))
		{
			$store = (int)$_GET['store'];
			$where .= " AND retailer_id='$store' ";
			$title2 = GetStoreName($store);
		}

		if (isset($_GET['user']) && is_numeric($_GET['user']))
		{
			$user = (int)$_GET['user'];
			$where .= " AND user_id='$user' ";
			$title2 = GetUsername($user)."'s";
		}

		$query = "SELECT *, DATE_FORMAT(added, '%e %b %Y %h:%i:%s %p') AS click_date FROM cashbackengine_clickhistory WHERE $where ORDER BY $rrorder $rorder LIMIT $from, $results_per_page";

		$result = smart_mysql_query($query);
		$total_on_page = mysql_num_rows($result);

		$query2 = "SELECT * FROM cashbackengine_clickhistory WHERE ".$where;
		$result2 = smart_mysql_query($query2);
        $total = mysql_num_rows($result2);

		$cc = 0;

		// clear clicks history
		if (isset($_GET['act']) && $_GET['act'] == "delete_clicks")
		{
			smart_mysql_query("DELETE FROM cashbackengine_clickhistory");
			header("Location: clicks.php?msg=clicks_deleted");
			exit();
		}


		$title = $title2." Click History";
		require_once ("inc/header.inc.php");

?>

		<div id="addnew"><a href="#" onclick="if (confirm('Are you sure you really want to clear clicks history?') )location.href='clicks.php?act=delete_clicks'" title="Delete"><img src="images/idelete.png" align="absmiddle" /> Clear clicks history</a></div>

		<h2><?php echo $title2; ?> Click History</h2>

		<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
		<div class="success_box">
			<?php

				switch ($_GET['msg'])
				{
					case "deleted": echo "Click has been successfully deleted"; break;
					case "clicks_deleted": echo "Click history has been emptied"; break;
				}

			?>
		</div>
		<?php } ?>

		<?php if (!empty($store)) { ?>
		<div style="width: 100%; background: #F7F7F7; border-bottom: 2px solid #EEE; padding: 10px 5px; text-align: right;">
			Total store visits: <span style="color: #FFF; background: #D67208; padding: 2px 4px;"><?php echo GetVisitsTotal($store); ?></span> &nbsp;&nbsp;&nbsp;&nbsp;
			Members visits: <span style="color: #FFF; background: #61E236; padding: 2px 4px;"><?php echo GetMembersVisitsTotal($store); ?></span> &nbsp;&nbsp;&nbsp;&nbsp;
			Guests visits: <span style="color: #FFF; background: #8C8C8C; padding: 2px 4px;"><?php echo GetGuestsVisitsTotal($store); ?></span>
		</div>
		<?php } ?>


		<form id="form1" name="form1" method="get" action="">
		<table bgcolor="#F9F9F9" align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
		<tr>
		<td nowrap="nowrap" width="47%" valign="middle" align="left">
           Sort by: 
          <select name="column" id="column" onChange="document.form1.submit()">
			<option value="added" <?php if ($_GET['column'] == "added") echo "selected"; ?>>Date</option>
			<option value="retailer_id" <?php if ($_GET['column'] == "retailer_id") echo "selected"; ?>>Retailer</option>
			<option value="user_id" <?php if ($_GET['column'] == "user_id") echo "selected"; ?>>User</option>
          </select>
          <select name="order" id="order" onChange="document.form1.submit()">
			<option value="desc" <?php if ($_GET['order'] == "desc") echo "selected"; ?>>Descending</option>
			<option value="asc" <?php if ($_GET['order'] == "asc") echo "selected"; ?>>Ascending</option>
          </select>
		  &nbsp;&nbsp;View: 
          <select name="show" id="show" onChange="document.form1.submit()">
			<option value="10" <?php if ($_GET['show'] == "10") echo "selected"; ?>>10</option>
			<option value="50" <?php if ($_GET['show'] == "50") echo "selected"; ?>>50</option>
			<option value="100" <?php if ($_GET['show'] == "100") echo "selected"; ?>>100</option>
			<option value="111111111" <?php if ($_GET['show'] == "111111111") echo "selected"; ?>>ALL</option>
          </select>
			</td>
			<td nowrap="nowrap" width="30%" valign="middle" align="left">
				<div style="background: #F7F7F7; padding: 7px 15px; border-radius: 7px;">
				Store: 
				<select name="store" id="store" onChange="document.form1.submit()" style="width: 150px;" class="textbox2">
				<option value="">--- all stores ---</option>
				<?php
					$sql_retailers = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE status='active' ORDER BY title ASC");
					if (mysql_num_rows($sql_retailers) > 0)
					{
						while ($row_retailers = mysql_fetch_array($sql_retailers))
						{
							if ($store == $row_retailers['retailer_id']) $selected = " selected=\"selected\""; else $selected = "";
							if ($row_retailers['visits'] == 0) $vletter = ""; elseif ($row_retailers['visits'] == 1) $vletter = " visit"; else $vletter = " visits";
							echo "<option value=\"".$row_retailers['retailer_id']."\"".$selected.">".$row_retailers['title']." (".number_format($row_retailers['visits']).$vletter.")</option>";
						}
					}
				?>
				</select>
				<?php if ($store > 0) { ?><a href="clicks.php"><img align="absmiddle" src="images/icons/delete_filter.png" border="0" alt="Delete Filter" /></a><?php } ?>
			</td>
			<td nowrap="nowrap" width="35%" valign="middle" align="right">
				<?php if ($total > 0) { ?>
					Showing <?php echo ($from + 1); ?> - <?php echo min($from + $total_on_page, $total); ?> of <?php echo $total; ?>
				<?php } ?>
			</td>
			</tr>
			</table>
			</form>


		<?php if ($total > 0) { ?>

			<form id="form2" name="form2" method="post" action="">
			<table align="center" class="tbl" width="100%" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<th width="10%"><b>Click ID</b></th>
				<th width="20%"><b>Date/Time</b></th>
				<th width="35%"><b>Store</b></th>
				<th width="20%"><b>User</b></th>
				<th width="10%"><b>Actions</b></th>
			</tr>
			<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>				  
				  <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td nowrap="nowrap" align="center" valign="middle"><?php echo $row['click_id']; ?></td>
					<td align="center" valign="middle"><?php echo $row['click_date']; ?></td>
					<td align="left" valign="middle" class="row_title"><a href="retailer_details.php?id=<?php echo $row['retailer_id']; ?>"><?php echo GetStoreName($row['retailer_id']); ?></a></td>
					<td align="center" valign="middle"><a href="user_details.php?id=<?php echo $row['user_id']; ?>" class="user"><?php echo GetUsername($row['user_id']); ?></a></td>
					<td nowrap="nowrap" align="center" valign="middle"><a href="click_details.php?id=<?php echo $row['click_id']; ?>" title="View"><img src="images/view.png" border="0" alt="View" /></a></td>
				  </tr>
			<?php } ?>
				  <tr>
				  <td colspan="5" align="center">
					<?php echo ShowPagination("clickhistory",$results_per_page,"clicks.php?column=$rrorder&order=$rorder&show=$results_per_page&", "WHERE ".$where); ?>
				  </td>
				  </tr>
            </table>
			</form>

          <?php }else{ ?>
				<?php if (isset($filter)) { ?>
					<div class="info_box">No results found. <a href='clicks.php'>Search again &#155;</a></div>
				<?php }else{ ?>
					<div class="info_box">There are no clicks at this time.</div>
				<?php } ?>
          <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>