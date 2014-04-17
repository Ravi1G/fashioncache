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

if(isset($_GET['id']) && $_GET['id']!="")
{
	$id	= $_GET['id'];
	
	//Delete the image file if exists in the record
	$sql	= mysql_query("SELECT image FROM cashbackengine_banners WHERE banner_id='$id'");
	$row	= mysql_fetch_assoc($sql);
	$image_loc	= $row['image'];
	
	if($image_loc != "" && file_exists($image_loc))	
	{	
		$img_del_result = unlink($image_loc);
	}
	$sql=mysql_query("DELETE FROM cashbackengine_banners WHERE banner_id='$id'");
	
	if($sql > 0)
	{
		header("Location: banners.php?msg=deleted");
		exit();
	}
}