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
	
	$query	= "SELECT * FROM cashbackengine_users WHERE user_id='$userid' AND status='active' LIMIT 1";
	$result = smart_mysql_query($query);

	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
	}
	else
	{
		header ("Location: logout.php");
		exit();
	}
?>

<?php 
if (isset($_POST['action']) && $_POST['action'] == "changepwd")
	{
		$pwd		= mysql_real_escape_string(getPostParameter('password'));
		$newpwd		= mysql_real_escape_string(getPostParameter('newpassword'));
		$newpwd2	= mysql_real_escape_string(getPostParameter('newpassword2'));

		$errs2 = array();

		if (!($pwd && $newpwd && $newpwd2))
		{
			$errs2[] = CBE1_MYPROFILE_ERR0;
		}
		else
		{
			if (PasswordEncryption($pwd) !== $row['password'])
			{
				$errs2[] = CBE1_MYPROFILE_ERR2;
			}

			if ($newpwd !== $newpwd2)
			{
				$errs2[] = CBE1_MYPROFILE_ERR3;
			}
			elseif ((strlen($newpwd)) < 6 || (strlen($newpwd2) < 6) || (strlen($newpwd)) > 20 || (strlen($newpwd2) > 20))
			{
				$errs2[] = CBE1_MYPROFILE_ERR4;
			}
			elseif (stristr($newpwd, ' '))
			{
				$errs2[] = CBE1_MYPROFILE_ERR5;
			}
		}

		if (count($errs2) == 0)
		{
			$upp_query = "UPDATE cashbackengine_users SET password='".PasswordEncryption($newpwd)."' WHERE user_id='$userid' LIMIT 1";
		
			if (smart_mysql_query($upp_query))
			{
				header("Location: change_pwd.php?msg=2");
				exit();
			}	
		}
	}
	
	$PAGE_TITLE = "Cash Back Method";
	require_once ("inc/header.inc.php");
?>
<div class="container standardContainer innerRegularPages">
		<?php 
		/* Left SideBar Content */
		if(isLoggedIn())
		{
			require_once("inc/left_sidebar.php");				
		}
		?>			
		<div class="rightAligned flowContent1">
			<h1>CASH BACK METHOD</h1>			
				<form action="" method="post">
					<div class="customTable cashBackMethod">
						<div class="cashBackWays">
							<h3>Select Method: <span class="data"><label>Venmo</label> <input class="radio" type="radio"/> <label>Pay Pal</label> <input class="radio" type="radio"/> <label>Check</label> <input class="radio" type="radio"/></span></h3>
						</div>
						
						<!--  Check Section  -->
						
						<div class="cashBackContent">								
							<div class="row locationPlate">
								<div class="label">Address</div>
								<div class="data"><input type="text"/></div>
							</div>
							<div class="row locationPlate">
								<div class="label">City</div>
								<div class="data"><input type="text"/></div>
							</div>							
							<div class="row locationPlate">
								<div class="label">State</div>
								<div class="data">
									<select>
										<option>New York</option>
										<option>Albama</option>
										<option>Grev</option>
										<option>Sauthern</option>
									</select>
								</div>
							</div>
							<div class="row locationPlate">
								<div class="label">Country</div>
								<div class="data">
									<select>
										<option>Canada</option>
										<option>America</option>
										<option>Russia</option>
										<option>China</option>
									</select>
								</div>
							</div>
							<div class="row locationPlate">
								<div class="label">Zip Code</div>
								<div class="data"><input type="text"/></div>
							</div>
							<div class="allStores forSignUp">
								<span id="updateForm">SAVE</span><?php /* &#x00A0;&#x00A0;&#x00A0;<span id="cancelForm">CANCEL</span> */ ?>
							</div>
						</div>						
					
				
						<!--  Venmo Section  -->
				
					
						<div class="cashBackContent">
							<div class="row locationPlate">
								<div class="label">User Name</div>
								<div class="data"><input type="text"/></div>
							</div>
							<div class="allStores forSignUp">
								<span id="updateForm">SAVE</span><?php /* &#x00A0;&#x00A0;&#x00A0;<span id="cancelForm">CANCEL</span> */ ?>
							</div>
							<div class="cb"></div>
						</div>						
					
					
						<!--  Paypal Section  -->
					
					
						<div class="cashBackContent">								
							<div class="row locationPlate">
								<div class="label">Email</div>
								<div class="data"><input type="text"/></div>
							</div>
							<div class="allStores forSignUp">
								<span id="updateForm">SAVE</span><?php /* &#x00A0;&#x00A0;&#x00A0;<span id="cancelForm">CANCEL</span> */ ?>
							</div>
							<div class="cb"></div>
						</div>						
					</div>
				</form>
			</div>
		<div class="cb"></div>
	</div>
<?php require_once ("inc/footer.inc.php"); ?>