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


	if (isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 0)
	{
		$uid = (int)$_GET['id'];

		$query = "SELECT *, DATE_FORMAT(created, '%e %b %Y %h:%i %p') AS signup_date FROM cashbackengine_users WHERE ref_id='$uid' ORDER BY created DESC";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>User's Referrals</title>
<link href="css/cashbackengine.css" rel="stylesheet" type="text/css" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body style="background: #FFFFFF">
<table width="100%" bgcolor="#FFFFFF" align="center" border="0" cellpadding="3" cellspacing="0">
<tr>
 <td colspan=valign="top" align="left">

	<h2>User's Referrals</h2>

	  <?php if ($total > 0) { ?>

            <table align="center" width="98%" border="0" cellspacing="0" cellpadding="3">
              <tr>
				<th width="24%">Name</th>
				<th width="24%">Username</th>              
                <th width="20%">Country</th>
				<th width="20%">Signup Date</th>
                <th width="17%">Status</th>
              </tr>
				<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
                <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
                  <td valign="middle" align="center"><?php echo $row['fname']." ".$row['lname']; ?></td>
				  <td valign="middle" align="center"><?php echo $row['username']; ?></td>
				  <td valign="middle" align="center"><?php echo GetCountry($row['country']); ?></td>
                  <td valign="middle" align="center"><?php echo $row['signup_date']; ?></td>
				  <td valign="middle" align="center">
					<?php if ($row['status'] == "inactive") echo "<span class='inactive_s'>".$row['status']."</span>"; else echo "<span class='active_s'>".$row['status']."</span>"; ?>
				  </td>
                </tr>
				<?php } ?>
           </table>
	  
	  <?php }else{ ?>
			<div class="info_box">User has not received any referrals at this time.</div>
      <?php } ?>

	<hr size="1" color="#EEEEEE">
	<div align="right"><a onclick="window.close(); return false;" href="#">x Close this window</a></div>

 </td>
</tr>
</table>
</body>
</html>
<?php } ?>