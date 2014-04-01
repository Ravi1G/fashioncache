<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("inc/iflogged.inc.php");
	require_once("inc/config.inc.php");
	
	//For Session variables from pop-up of index page
	if(isset($_SESSION['action']) && $_SESSION['action']=='signup' && isset($_SESSION['email']))
	{
		$username	=	$_SESSION['email'];
		$email		=	$_SESSION['email'];
		$pwd		=	$_SESSION['password'];
		$action		=	$_SESSION['action'];
		unset($_SESSION['email']);
		unset($_SESSION['password']);
		unset($_SESSION['action']);		
		
		$action = $_POST['action'] = 'signup';
		$_POST['email'] = $email;
		$_POST['action'] = $pwd;
	}
	//For post variables from the same page
	if(isset($_POST['action']) && $_POST['action'] == "signup")
	{
		$email		= mysql_real_escape_string(getPostParameter('email'));
		$username	= mysql_real_escape_string(getPostParameter('email'));
		$pwd		= mysql_real_escape_string(getPostParameter('password'));
		$action		= $_POST['action'];
	}

	// Login
	if (isset($_POST['action']) && $_POST['action'] == "login")
	{
		$username	= mysql_real_escape_string(getPostParameter('email'));
		$pwd		= mysql_real_escape_string(getPostParameter('password'));
		$remember	= (int)getPostParameter('rememberme');
		$ip			= getenv("REMOTE_ADDR");

		if (!($username && $pwd))
		{
			$errs[] = CBE1_LOGIN_ERR;
		}
		
		if (isset($username) && $username != "" && !preg_match("/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/", $username))
		{
			$errs[] = "Please enter a valid email";	
		}
	
		if (isset($pwd) && $pwd != "")
			{
				if ((strlen($pwd)) < 6 || (strlen($pwd)) > 20 )
				{
					$errs[] = CBE1_SIGNUP_ERR7;
				}
				elseif (stristr($pwd, ' '))
				{
					$errs[] = CBE1_SIGNUP_ERR8;
				}
			}


		if(count($errs)==0 )
		{	
			$sql = "SELECT * FROM cashbackengine_users WHERE username='$username' AND password='".PasswordEncryption($pwd)."' LIMIT 1";
			$result = smart_mysql_query($sql);
			if (mysql_num_rows($result) != 0)
			{
					$row = mysql_fetch_array($result);

					if ($row['status'] == 'inactive')
					{
						header("Location: signup_or_login.php?msg=2");
						exit();
					}

					if (LOGIN_ATTEMPTS_LIMIT == 1)
					{
						unset($_SESSION['attems_'.$username."_".$ip], $_SESSION['attems_left']);
					}

					if ($remember == 1)
					{
						$cookie_hash = md5(sha1($username.$ip));
						setcookie("usname", $cookie_hash, time()+3600*24*365, '/');
						$login_sql = "login_session = '$cookie_hash', ";
					}

					smart_mysql_query("UPDATE cashbackengine_users SET ".$login_sql." last_ip='$ip', login_count=login_count+1, last_login=NOW() WHERE user_id='".(int)$row['user_id']."' LIMIT 1");

					if (!session_id()) session_start();
					$_SESSION['userid'] = $row['user_id'];
					$_SESSION['FirstName'] = $row['fname'];

					if ($_SESSION['goRetailerID'])
					{
						$goRetailerID = (int)$_SESSION['goRetailerID'];
						$redirect_url = GetRetailerLink($goRetailerID, GetStoreName($goRetailerID));
						unset($_SESSION['goRetailerID']);
					}
					else
					{
						$redirect_url = "index.php";
					}

					header("Location: ".$redirect_url);
					exit();
			}
			else
			{
				if (LOGIN_ATTEMPTS_LIMIT == 1)
				{
					$check_sql = "SELECT * FROM cashbackengine_users WHERE username='$username' AND status!='inactive' AND block_reason!='login attempts limit' LIMIT 1";
					$check_result = smart_mysql_query($check_sql);

					if (mysql_num_rows($check_result) != 0)
					{
						if (!session_id()) session_start();
						$_SESSION['attems_'.$username."_".$ip] += 1;
						$_SESSION['attems_left'] = LOGIN_ATTEMPTS - $_SESSION['attems_'.$username.'_'.$ip];

						if ($_SESSION['attems_left'] == 0)
						{ 
							// block user //
							smart_mysql_query("UPDATE cashbackengine_users SET status='inactive', block_reason='login attempts limit' WHERE username='$username' LIMIT 1"); 
							unset($_SESSION['attems_'.$username."_".$ip], $_SESSION['attems_left']);
					
							header("Location: login.php?msg=6");
							exit();
						}
						else
						{
							header("Location: login.php?msg=5");
							exit();
						}
					}
				}

				header("Location: signup_or_login.php?msg=1");
				exit();
			}
		}
	}
	else if(isset($action) && $action == "signup")
	{
		$ip			= getenv("REMOTE_ADDR");
		$ref_email	= mysql_real_escape_string(getPostParameter('referrer_email'));
		
		if (!($email && $pwd))
		{
			$errs[] = CBE1_SIGNUP_ERR;
		}
		else
		{
			if (isset($email) && $email != "" && !preg_match("/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/", $email))
			{
				$errs[] = CBE1_SIGNUP_ERR4;
			}else{
				$query = "SELECT username FROM cashbackengine_users WHERE username='$email' LIMIT 1";
				$result = smart_mysql_query($query);
	
				if (mysql_num_rows($result) != 0)
				{
					$errs[] = 'Email already exists';
				}
			}
	
			
			if (isset($pwd) && $pwd != "")
			{
				if ((strlen($pwd)) < 6 || (strlen($pwd)) > 20 )
				{
					$errs[] = CBE1_SIGNUP_ERR7;
				}
				elseif (stristr($pwd, ' '))
				{
					$errs[] = CBE1_SIGNUP_ERR8;
				}
			}

			if (isset($ref_email) && $ref_email != "" && !preg_match("/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/", $ref_email))
			{
				$errs[] = 'Please enter a valid email address of referrer';
			}
		}

	if (count($errs) == 0)
		{	
				// check referral
				if (isset($ref_email) && $ref_email!="")
				{
					$check_referral_query = "SELECT user_id FROM cashbackengine_users WHERE username='$ref_email' LIMIT 1";
					$check_referral_result = smart_mysql_query($check_referral_query);

					if (mysql_num_rows($check_referral_result) != 0)
					{
						$ref_id=mysql_fetch_assoc($check_referral_result);
						$ref_id = $ref_id['user_id'];
					}
					else
					{
						$ref_id = 0;
					}
				}

				$unsubscribe_key = GenerateKey($username);

				if (ACCOUNT_ACTIVATION == 1)
				{
					$activation_key = GenerateKey($username);
					$insert_query = "INSERT INTO cashbackengine_users SET username='$username', password='".PasswordEncryption($pwd)."', email='$email', ip='$ip', status='inactive',ref_id='$ref_id', activation_key='$activation_key', created=NOW()";
				}
				else
				{
					$insert_query = "INSERT INTO cashbackengine_users SET username='$username', password='".PasswordEncryption($pwd)."', email='$email', ip='$ip', status='active',ref_id='$ref_id',  activation_key='', unsubscribe_key='$unsubscribe_key', last_login=NOW(), login_count='1', last_ip='$ip', created=NOW()";
				}

				smart_mysql_query($insert_query);
				$new_user_id = mysql_insert_id();

				if (SIGNUP_BONUS > 0)
				{
					// save SIGN UP BONUS transaction //
					$reference_id = GenerateReferenceID();
					smart_mysql_query("INSERT INTO cashbackengine_transactions SET reference_id='$reference_id', user_id='$new_user_id', payment_type='Sign Up Bonus', amount='".SIGNUP_BONUS."', status='confirmed', created=NOW(), process_date=NOW()");
					/////////////////////////////////////
				}

				// add bonus to referral, save transaction //
				if (REFER_FRIEND_BONUS > 0 && isset($ref_id) && $ref_id > 0)
				{
					$reference_id = GenerateReferenceID();
					$ref_res = smart_mysql_query("INSERT INTO cashbackengine_transactions SET reference_id='$reference_id', user_id='$ref_id', payment_type='Refer a Friend Bonus', amount='".REFER_FRIEND_BONUS."', status='pending', created=NOW()");
				}
				//////////////////////////////////////////////


				if (ACCOUNT_ACTIVATION == 1)
				{			
					////////////////////////////////  Send Message  //////////////////////////////
					$etemplate = GetEmailTemplate('activate');
					$esubject = $etemplate['email_subject'];
					$emessage = $etemplate['email_message'];

					$activate_link = SITE_URL."activate.php?key=".$activation_key;

					$emessage = str_replace("{first_name}", $fname, $emessage);
					$emessage = str_replace("{username}", $email, $emessage);
					$emessage = str_replace("{password}", $pwd, $emessage);
					$emessage = str_replace("{activate_link}", $activate_link, $emessage);

					$to_email = $fname.' '.$lname.' <'.$email.'>';
					$subject = $esubject;
					$message = $emessage;

					$headers = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
					$headers .= 'From: '.SITE_TITLE.' <'.NOREPLY_MAIL.'>' . "\r\n";
				
					@mail($to_email, $subject, $message, $headers);
					////////////////////////////////////////////////////////////////////////////////

					header("Location: activate.php?msg=1"); // show account activation message
					exit();
				}
				else
				{
					////////////////////////////////  Send welcome message  ////////////////////////
					$etemplate = GetEmailTemplate('signup');
					$esubject = $etemplate['email_subject'];
					$emessage = $etemplate['email_message'];

					$emessage = str_replace("{first_name}", $fname, $emessage);
					$emessage = str_replace("{username}", $email, $emessage);
					$emessage = str_replace("{password}", $pwd, $emessage);
					$emessage = str_replace("{login_url}", SITE_URL."login.php", $emessage);

					$to_email = $fname.' '.$lname.' <'.$email.'>';
					$subject = $esubject;
					$message = $emessage;

					$headers = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
					$headers .= 'From: '.SITE_TITLE.' <'.NOREPLY_MAIL.'>' . "\r\n";
				
					@mail($to_email, $subject, $message, $headers);
					////////////////////////////////////////////////////////////////////////////////

					if (!session_id()) session_start();
					$_SESSION['userid']		= $new_user_id;
					$_SESSION['FirstName']	= $fname;

					header("Location: myaccount.php?msg=welcome"); // forward new user to member dashboard
					exit();
				}
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= "&#155; ".$errorname."<br/>\n";
		}
	}
	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_LOGIN_TITLE;

	require_once ("inc/header.inc.php");
?>
<div class="container content siteInnerSection">
    <div class="userActivityContainer">
        <h1>SIGN-UP OR LOG IN</h1>
        <?php if(count($errs) > 0 || isset($_GET['msg'])){?>
        <div class="errorMessageContainer">
            <div class="leftContainer errorIcon">
                <img src="../img/errorIcon.png" alt="Error"/>
            </div>
             
            <div class="leftContainer">	
				<!-- For Single line error, add singleError class -->
               <ul class="standardList errorList <?php if (isset($_GET['msg']) || count($errs)==1){ echo 'singleError'; }?>"> 	             			
               <?php
               	if(isset($_GET['msg']) && $_GET['msg']=='exists')
               	{?>
               		<li><div class="errorMessage"><?php echo CBE1_SIGNUP_ERR10;?></div></li>
               	<?php }
               	else if(isset($errs))
               	{
				foreach ($errs as $err){?>
					<li><div class="errorMessage"><?php echo $err;?></div></li>               	
				<?php }	
				}
				?>
				<?php if (isset($_GET['msg']) && $_GET['msg'] == 1 ||$_GET['msg'] == 2 || $_GET['msg'] == 3 || $_GET['msg'] == 4 || $_GET['msg'] == 5 || $_GET['msg'] == 6) { ?>
				<li><div class="errorMessage">
					<?php if ($_GET['msg'] == 1) { echo CBE1_LOGIN_ERR1; } ?>
					<?php if ($_GET['msg'] == 2) { echo CBE1_LOGIN_ERR2; } ?>
					<?php if ($_GET['msg'] == 3) { echo CBE1_LOGIN_ERR3; } ?>
					<?php if ($_GET['msg'] == 4) { echo CBE1_LOGIN_ERR4; } ?>
					<?php if ($_GET['msg'] == 5) { echo CBE1_LOGIN_ERR1." ".$_SESSION['attems_left']." ".CBE1_LOGIN_ATTEMPTS; } ?>
					<?php if ($_GET['msg'] == 6) { echo CBE1_LOGIN_ERR6; } ?>
				</div></li>
				<?php } ?>
				
					</ul>
            </div>              
            <div class="cb"></div>			
        </div>
        <?php } ?>
        <!-- Sign up form -->
        <div class="leftContainer signUpActivity rightLine">
            <div class="contentTitle">New Members</div>
            <div>
                <form action="" method="post" id="frmsignup">
                    <label class="standardLabel">Email:<sup class="manadatoryField">*</sup></label>
                    <div class="standardInputBox"><input name="email" type="text" value="<?php echo getPostParameter('email')?>"/></div>						
                    <label class="standardLabel">Password (6-12 characters):<sup class="manadatoryField">*</sup></label>
                    <div class="standardInputBox"><input name="password" type="password" value="<?php echo getPostParameter('password')?>"/></div>
                    <label class="standardLabel">Referrer's Email (optional):</label>
                    <div class="standardInputBox"><input name="referrer_email" type="text" value="<?php echo getPostParameter('referrer_email')?>"/></div>
                    <div class="shopNowBotton siteButton leftAlign formSubmitButton">
                        <a href="#" onclick="document.getElementById('frmsignup').submit();return false;">
                            <span>Sign Up &#x003E;</span>
                        </a>
                    </div>
                    <input type="hidden" name="action" value="signup">
                </form>
            </div>
            <div>By becoming a member, you agree to our <a class="colorLink" href="#">Terms &#x0026; Conditions</a>.</div>
            <h3>Benefits of an Ebates membership:</h3>
            <ul class="standardList">
                <li>Up to 25% Cash Back on all purchases</li>
                <li>Hundreds of exclusive coupons and free shipping offers</li>
                <li>TRUSTe certified to protect your privacy</li>
                <li>Excellent customer service</li>
            </ul>
        </div>			
        <div class="leftContainer loginActivity rightLine">
            <div class="contentTitle">Returning Members</div>
            <form action="" method="post" id="frmlogin">
                <label class="standardLabel">Email:<sup class="manadatoryField">*</sup></label>
                <div class="standardInputBox"><input name="email" type="text"/></div>
                <label class="standardLabel">Password:<sup class="manadatoryField">*</sup></label>
                <div class="standardInputBox"><input name="password" type="password"/></div>
                
          		  <input type="checkbox" class="checkboxx" name="rememberme" id="rememberme" value="1" <?php echo (@$rememberme == 1) ? "checked" : "" ?>/> <?php echo CBE1_LOGIN_REMEMBER; ?>
          		
          
          					
                <div class="loginActions">
                    <div class="leftContainer">
                        <div class="shopNowBotton siteButton leftAlign formSubmitButton">
                            <a href="#" onclick="document.getElementById('frmlogin').submit();return false;">
                                <span>Log In &#x003E;</span>
                            </a>
                        </div>
                    </div>
                    <div class="leftContainer">
                        <div class="forgotPassword"><a class="colorLink" href="<?php echo SITE_URL; ?>forgot.php">Forgot Password?</a></div>
                    </div>
                    <div class="cb"></div>
                </div>
                <input type="hidden" name="action" value="login" >
                
            </form>
        </div>
        <div class="rightContainer loginActivity">
            <div class="contentTitle facebookTitle">Facebook Members</div>
            <div class="haveAccount">Have a Facebook account?</div>
            <div>Signup or login here.</div>
            <div class="fbLoginApi"><a href="#"><img src="../img/fbApiLogin.jpg" alt="Login with Facebook"/></a></div>				
            <div>By becoming a member, you agree to our <a class="colorLink" href="#">Terms &#x0026; Conditions</a>.</div>				
        </div>			
        <div class="cb"></div>
    </div>
    
    <?php require_once "inc/right_sidebar.php";?>	
</div>

<?php require_once ("inc/footer.inc.php"); ?>