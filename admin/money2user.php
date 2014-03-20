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


if (isset($_POST["action"]) && $_POST["action"] == "addmoney")
{
		unset($errors);
		$errors = array();

		$username		= mysql_real_escape_string(getPostParameter('username'));
		$amount			= mysql_real_escape_string(getPostParameter('amount'));
		$payment_type	= mysql_real_escape_string(getPostParameter('payment_type'));
		$status			= mysql_real_escape_string(getPostParameter('status'));

		if (!($username && $amount && $payment_type && $status))
		{
			$errors[] = "Please fill in all fields";
		}
		else
		{
			if (!is_numeric($amount)) //if (!(is_numeric($amount) && $amount > 0))
			{
				$errors[] = "Please enter correct amount";
				$amount = "";
			}
		}

	if (count($errors) == 0)
	{
		if (is_numeric($username) && $username > 0)
			$ures = smart_mysql_query("SELECT user_id FROM cashbackengine_users WHERE user_id='$username' LIMIT 1");
		else
			$ures = smart_mysql_query("SELECT user_id FROM cashbackengine_users WHERE username='$username' OR email='$username' LIMIT 1");

		if (mysql_num_rows($ures) != 0)
		{
			$urow = mysql_fetch_array($ures);

			$userid			= (int)$urow['user_id'];
			$reference_id	= GenerateReferenceID();

			switch ($status)
			{
				case "confirmed":	$status="confirmed"; break;
				case "pending":		$status="pending"; break;
				case "declined":	$status="declined"; break;
				default:			$status="unknown"; break;
			}
	
			$sql = "INSERT INTO cashbackengine_transactions SET reference_id='$reference_id', user_id='$userid', payment_type='$payment_type', amount='$amount', status='$status', created=NOW(), process_date=NOW()";
			$result = smart_mysql_query($sql);

			header("Location: money2user.php?msg=added");
			exit();
		}
		else
		{
			header("Location: money2user.php?msg=notfound");
			exit();
		}

	}
	else
	{
		$errormsg = "";
		foreach ($errors as $errorname)
			$errormsg .= "&#155; ".$errorname."<br/>";
	}

}


if (isset($_GET['id']) && is_numeric($_GET['id']))
{
	$id = (int)$_GET['id'];

	$squery = "SELECT username FROM cashbackengine_users WHERE user_id='$id'";
	$sresult = smart_mysql_query($squery); 

	if (mysql_num_rows($sresult) != 0)
	{
		$srow = mysql_fetch_array($sresult);
		$username = $srow['username'];
	}
}


	$title = "Credit Member";
	require_once ("inc/header.inc.php");

?>
    
    <h2>Manual Credit</h2>

	<p align="center"><img src="images/icons/transfer.gif" /></p>
	<p align="center">Here you have the ability to credit/withdraw funds to any account.</p>

	<?php if (isset($_GET['msg']) && ($_GET['msg']) == "added") { ?>
		<div style="width:650px;" class="success_box">Transaction has been successfully complete</div>
	<?php }elseif(isset($_GET['msg']) && ($_GET['msg']) == "notfound") { ?>
		<div style="width:650px;" class="error_box">Sorry, member not found</div>
	<?php } ?>

	<?php if (isset($errormsg)) { ?>
		<div style="width:650px;" class="error_box"><?php echo $errormsg; ?></div>
	<?php } ?>

		<form action="" method="post" name="form1">
        <table width="650" style="border-bottom: 1px solid #DCEAFB;" align="center" cellpadding="3" cellspacing="0" border="0">
		<tr>
			<th>Amount</th>
			<th>UserID or Username or Email</th>
			<th>Payment Type</th>
			<th>Status</th>
			<th>Action</th>
		</tr>
		<tr>
			<td nowrap="nowrap" align="center" valign="middle">
				<?php echo SITE_CURRENCY; ?><input type="text" class="textbox" name="amount" value="<?php echo $amount; ?>" size="6" />
			</td>
            <td nowrap="nowrap" align="center" valign="middle">
				<input type="text" class="textbox" name="username" value="<?php echo $username; ?>" size="37" />
			</td>
            <td nowrap="nowrap" align="center" valign="middle">
				<input type="text" class="textbox" name="payment_type" value="Credit Account" size="35" />
			</td>
            <td nowrap="nowrap" align="center" valign="middle">
				<select name="status">
					<option value="confirmed">confirmed</option>
					<option value="pending">pending</option>
					<option value="declined">declined</option>
				</select>
			</td>
			<td align="center" valign="middle">
		  		<input type="hidden" name="action" value="addmoney" />
				<input type="submit" class="submit" name="addmoney" id="addmoney" value="Add Money" />
			</td>
		</tr>
        </table>
		</form>

<?php require_once ("inc/footer.inc.php"); ?>