<div class="leftAligned sidebar2">
	<div class="selfSection">
		<h2>My Fashion Cache</h2>
		<div class="secondaryNavigation">
			<div>Account Info</div>
			<div <?php if($pageURL=='/myprofile.php'){?>class="current"<?php }?>><a href="<?php echo SITE_URL.'myprofile.php';?>">My Profile</a></div>			
			<div>Purchase History</div>
			<div <?php if(($pageURL=='/cashback_method.php') || ($pageURL=='/cashback_method.php?msg=1') ){?>class="current"<?php }?>><a href="<?php echo SITE_URL.'cashback_method.php';?>">Cash Back Method</a></div>
			<div <?php if($pageURL=='/invite.php'){?>class="current"<?php }?>><a href="<?php echo SITE_URL.'invite.php';?>">Invite Friends &#x0026; Earn $</a></div>
			<div <?php if($pageURL=='/change_pwd.php'){?>class="current"<?php }?>><a href="<?php echo SITE_URL.'change_pwd.php';?>">Change Password</a></div>
		</div>
	</div>
	<?php /*?>
	<div class="selfSection">
		<h2>Cash Back Summary</h2>
		<div class="secondaryNavigation">
			<div>Pending Cash Back</div>
			<div>Recently Added</div>
			<div>Big Fat Payments</div>
			<div>Total Cash Back</div>						
		</div>
	</div>
	<?php */?>
</div>