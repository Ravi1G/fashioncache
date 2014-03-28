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


	if (isset($_POST['action']) && $_POST['action'] == "login")
	{
		$username	= mysql_real_escape_string(getPostParameter('username'));
		$pwd		= mysql_real_escape_string(getPostParameter('password'));
		$remember	= (int)getPostParameter('rememberme');
		$ip			= getenv("REMOTE_ADDR");

		if (!($username && $pwd))
		{
			$errormsg = CBE1_LOGIN_ERR;
		}
		else
		{
			$sql = "SELECT * FROM cashbackengine_users WHERE username='$username' AND password='".PasswordEncryption($pwd)."' LIMIT 1";
			$result = smart_mysql_query($sql);

			if (mysql_num_rows($result) != 0)
			{
					$row = mysql_fetch_array($result);

					if ($row['status'] == 'inactive')
					{
						header("Location: login.php?msg=2");
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
						$redirect_url = "myaccount.php";
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

				header("Location: login.php?msg=1");
				exit();
			}
		}
	}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_LOGIN_TITLE;

	require_once ("inc/header.inc.php");

?>

<div class="container content siteInnerSection">
    <div class="userActivityContainer">
        <h1>SIGN-UP OR LOG IN</h1>
        <div class="errorMessageContainer">
            <div class="leftContainer errorIcon">
                <img src="../img/errorIcon.png" alt="Error"/>
            </div>
            <div class="leftContainer">	
                <ul class="standardList errorList">			
                    <li><div class="errorMessage">Please enter a valid Email.</div></li>
                    <li><div class="errorMessage">Password you have entered is ver weak.</div></li>
                    <li><div class="errorMessage">Email is required.</div></li>
                </ul>
            </div>
            <div class="cb"></div>			
        </div>
        
        <div class="leftContainer signUpActivity rightLine">
            <div class="contentTitle">New Members</div>
            <div>
                <form action="" method="post">
                    <label class="standardLabel">Email:</label>
                    <div class="standardInputBox"><input name="" type="text"/></div>						
                    <label class="standardLabel">Password (6-12 characters):</label>
                    <div class="standardInputBox"><input name="" type="password"/></div>
                    <label class="standardLabel">Referrer's Email (optional):</label>
                    <div class="standardInputBox"><input name="" type="text"/></div>
                    <div class="shopNowBotton siteButton leftAlign formSubmitButton">
                        <a href="#">
                            <span>Sign Up &#x003E;</span>
                        </a>
                    </div>
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
            <form action="" method="post">
                <label class="standardLabel">Email:</label>
                <div class="standardInputBox"><input name="" type="text"/></div>
                <label class="standardLabel">Password:</label>
                <div class="standardInputBox"><input name="" type="password"/></div>					
                <div class="loginActions">
                    <div class="leftContainer">
                        <div class="shopNowBotton siteButton leftAlign formSubmitButton">
                            <a href="#">
                                <span>Log In &#x003E;</span>
                            </a>
                        </div>
                    </div>
                    <div class="leftContainer">
                        <div class="forgotPassword"><a class="colorLink" href="#">Forgot Password?</a></div>
                    </div>
                    <div class="cb"></div>
                </div>
                
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