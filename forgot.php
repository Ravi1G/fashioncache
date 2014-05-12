<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("inc/config.inc.php");


	if (isset($_POST['action']) && $_POST['action'] == "forgot")
	{
		$email = strtolower(mysql_real_escape_string(getPostParameter('email')));
		unset($errors);
		$errors = array();

		if (!($email) || $email == "")
		{
			$errors[] = CBE1_FORGOT_MSG1;
		}
		else
		{
			if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email))
			{
				$errors[] = CBE1_FORGOT_MSG2;
			}
		}
		if(count($errors)==0 )
		{
		$query = "SELECT * FROM cashbackengine_users WHERE email='$email' AND status='active' LIMIT 1";
		$result = smart_mysql_query($query);

		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			
			$newPassword = generatePassword(11);
			$update_query = "UPDATE cashbackengine_users SET password='".PasswordEncryption($newPassword)."' WHERE user_id='".(int)$row['user_id']."' LIMIT 1";
			
			if (smart_mysql_query($update_query))
			{
				////////////////////////////////  Send Message  //////////////////////////////
				$etemplate = GetEmailTemplate('forgot_password');
				$esubject = $etemplate['email_subject'];
				$emessage = $etemplate['email_message'];

				$emessage = str_replace("{first_name}", $row['fname'], $emessage);
				$emessage = str_replace("{username}", $row['username'], $emessage);
				$emessage = str_replace("{password}", $newPassword, $emessage);
				$emessage = str_replace("{login_url}", SITE_URL."signup_or_login.php", $emessage);	
				
				$to_email = $row['fname'].' '.$row['lname'].' <'.$email.'>';
				$subject = $esubject;
				$message = $emessage;			
			
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$headers .= 'From: '.SITE_TITLE.' <'.NOREPLY_MAIL.'>' . "\r\n";

				if (@mail($to_email, $subject, $message, $headers))
				{
					header("Location: forgot.php?msg=4");
					exit();
				}
			}
		}
		else
		{
			$errors[] = CBE1_FORGOT_MSG3;
		}
	}
}

	//  Page config
	$PAGE_TITLE = CBE1_FORGOT_TITLE;
	require_once "inc/header.inc.php";
?>
		<div class="container content siteInnerSection">
			<div class="userActivityContainer">
			<?php if(count($errors) > 0 ){?>
	        <div class="errorMessageContainer">
	            <div class="leftContainer errorIcon"></div>
				<div class="leftContainer">	
					<!-- For Single line error, add singleError class -->
	               <ul class="standardList errorList <?php if (isset($_GET['msg']) || count($errors)==1){ echo 'singleError'; }?>"> 	             			
	               <?php
	               	if(isset($_GET['msg']) && $_GET['msg']=='exists')
	               	{?>
	               		<li><div class="errorMessage"><?php echo CBE1_SIGNUP_ERR10;?></div></li>
	               	<?php }
	               	else if(isset($errors))
	               	{
					foreach ($errors as $err){?>
						<li><div class="errorMessage"><?php echo $err;?></div></li>               	
					<?php }	
					}
					?>
				</ul>
	            </div>              
	            <div class="cb"></div>			
	        </div>
	           <?php } ?>
        	<?php 
			if((isset($_GET['msg']) && ($_GET['msg'] == 4) && (count($errors)==0)))
				{?>
				<div class="errorMessageContainer successMessageContainer">
            <div class="leftContainer errorIcon"></div>
			<div class="leftContainer">	
				<!-- For Single line error, add singleError class -->
               <ul class="standardList errorList <?php if (isset($_GET['msg']) || count($errors)==1){ echo 'singleError'; }?>"> 	             			
               <?php
               	if(isset($_GET['msg']) && $_GET['msg']=='4')
               	{?>
               		<li><div class="errorMessage success_msg"><?php echo CBE1_FORGOT_MSG4; ?></div></li>
               	<?php }?>
			</ul>
            </div>              
            <div class="cb"></div>			
        </div>
			<?php }?>
			
				<?php //if (!(isset($_GET['msg']) && $_GET['msg'] == 4)) { ?>
				
				<div class="forgotPasswordContainer">
					<div class="heading">FORGOT YOUR PASSWORD?</div>
					<div class="body">
						<p class="forgotPasswordCaption">You can request a new password by filling in your e-mail address below. By doing so, we will send you an e-mail containing a new password which is active directly.</p>
						 <form action="" method="post" id="recoveryEmailForm">
							 <div class="standardInputBox recoveryEmailInput">
								<b>EMAIL ADDRESS:</b>
								<div><input type="text" class="textbox" name="email" size="30" required="required" value=""/></div>
							 </div>
							 <input type="hidden" name="action" value="forgot" />
							 <div class="actionCenter">
								 <div class="fl shopNowBotton siteButton leftAlign formSubmitButton recoveryEmailSubmit">
									<a onclick="document.getElementById('recoveryEmailForm').submit();return false;" href="#">
										<span>SEND EMAIL</span>
									</a>
								 </div>
								 <div class="fl backToSignIn"><a class="colorLink" href="<?php echo SITE_URL;?>signup_or_login.php">Click here for Sign In</a></div>
								 <div class="cb"></div>
							 </div>
						 </form>
 						<div class="signUpReference">Dont have an account? <a class="colorLink" href="<?php echo SITE_URL;?>signup_or_login.php">Sign Up</a></div>
					</div>										
				</div>
				<?php //} ?>
			</div>
			<?php require_once "inc/right_sidebar.php";?>	
		</div>

<?php require_once ("inc/footer.inc.php"); ?>