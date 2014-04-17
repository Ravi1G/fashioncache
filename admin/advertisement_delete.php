<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

exit;
  
session_start();
require_once("../inc/adm_auth.inc.php");
require_once("../inc/config.inc.php");
require_once("./inc/admin_funcs.inc.php");

if(isset($_GET['id']) && $_GET['id']!="")
{
	$id	= $_GET['id'];
	
	//Delete the image file if exists in the record
	$sql	= mysql_query("SELECT image_name FROM cashbackengine_advertisements WHERE advertisement_id='$id'");
	$row	= mysql_fetch_assoc($sql);
	$image_loc	= $row['image_name'];
	
	if($image_loc != "" && file_exists($image_loc))	
		$img_del_result = unlink($image_loc);
	$sql=mysql_query("DELETE FROM cashbackengine_advertisements WHERE advertisement_id='$id'");
	
	if($sql > 0)
	{
		header("Location: advertisements.php?msg=deleted");
		exit();
	}
}