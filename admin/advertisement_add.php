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

$id		=	(int)trim($_GET['id']);
$isEdit =	$id==0 ? 0 : 1;
$isPost	=	count($_POST)>0 ? 1 : 0;

if($isEdit)
{
	$title = "Edit Advertisement";
}
else 
{
	$title = "Add Advertisement";
}

//first time page load
if(!$isPost){
	$select_option = 'use_url';	
}

//Run in case of edit to fetch advertisement information
if($id && !$isPost)
{
	$fetch_record 	=	mysql_fetch_assoc(smart_mysql_query("SELECT title,advertisement_id, image_url, link, image_name FROM cashbackengine_advertisements WHERE advertisement_id=$id"));
	$image_url		=	$fetch_record['image_url'];
	$link			=	$fetch_record['link'];
	$image_name		=	$fetch_record['image_name'];
	
	$select_option = $image_url!='' ? 'use_url' : 'upload_image';
	$_POST = $fetch_record;
	
	$_POST["existingAdImage"] = $image_url!='' ? $image_url : $image_name;
	
	if(empty($fetch_record)){
		header("Location: advertisements.php?msg=invalid_ad&msg_type=error");
		exit();
	}
}

if ($isPost)
{
	unset($errors);
	$errors = array();
	$select_option	=	$_POST['radiourl'];
	
	$image_url		= mysql_real_escape_string(getPostParameter('imageurl'));
	$link			= mysql_real_escape_string(getPostParameter('link'));
	$adTitle			= mysql_real_escape_string(getPostParameter('title'));
	$image_name		= $_FILES['image']['name'];

	if ($link=='' || ($image_url=='' && $image_name=='') || $adTitle=='')
	{
		$errors[] = "Please ensure that all fields marked with an asterisk are complete";
	}
	else
	{	
		//validate advertisement image
		if($select_option=='upload_image'){
			if ($_FILES["image"]["error"] > 0)
	  		{
				$error[]= $_FILES["image"]["error"];
			}else{
				$valid_mime_types = array(
					"image/gif",
					"image/jpeg",
					"image/jpg",
					"image/pjpeg",
					"image/x-png",
					"image/png",
				);
				
				$allowedExts = array("gif", "jpeg", "jpg", "png");
				$temp = explode(".", $_FILES["image"]["name"]);
				$extension = end($temp);

				if (!(in_array($extension, $allowedExts) && in_array($_FILES["image"]["type"], $valid_mime_types)))
				{
			  		$errors[] = 'Uplaod valid image.';
				}
				
				if($_FILES["image"]["size"] > 5242880){
					$errors[] = 'Allowed maximum file size is 5MB.';
				}
			}
			 
		}elseif($select_option=='use_url'){		//validate advertisement image url
			if (!preg_match("/^((http|https):\/\/)?([www]+(\.[a-zA-Z0-9]+)+(\.[a-zA-Z0-9]).*)$/", $image_url))
			{
				$errors[] = "Enter correct image url. (e.g. 'http://www.abc.com' or 'https://www.abc.com' or 'www.abc.com')";
			}			
		}
		
		//validate advertisement link
		if (!preg_match("/^((http|https):\/\/)?([www]+(\.[a-zA-Z0-9]+)+(\.[a-zA-Z0-9]).*)$/", $link))
		{
			$errors[] = "Enter correct link url. (e.g. 'http://www.abc.com' or 'https://www.abc.com' or 'www.abc.com')";
		}
	}
	
	if (count($errors) == 0 && $isPost)
	{
		if($select_option=='upload_image')
		{
			if (file_exists("upload/" . $_FILES["image"]["name"]))
			{
				//rename the file and upload - pending
				$name =	$temp[0].'1.';
				$name = $name.$extension;
				while(file_exists("upload/" . $name)) //If file with new name already exist then change the name
				{
					$temp = explode(".", $name);
					$name = $temp[0].'1.';
					$name = $name.$extension;
				}
				move_uploaded_file($_FILES["image"]["tmp_name"],
				"upload/" . $name);
		     	$loc="upload/" . $name;
			}
			else
			{
				move_uploaded_file($_FILES["image"]["tmp_name"],
				"upload/" . $_FILES["image"]["name"]);
		     	$loc="upload/" . $_FILES["image"]["name"];
			}
		}
		
		//Update record
		if($id)
		{	
			//remove image if one already there for advertisement		
			$sql=mysql_query("SELECT image_name FROM cashbackengine_advertisements WHERE advertisement_id='$id'");
			$row=mysql_fetch_assoc($sql);
			$image_loc = $row['image_name'];
			
			if($image_loc!="" )	
				$img_del_result=unlink($image_loc);	
			//remove block ends		
				
			if($select_option=='upload_image')
				$sql = "UPDATE cashbackengine_advertisements  SET title='$adTitle', image_url='', link='$link', image_name='$loc' WHERE advertisement_id='$id'";
			else 
				$sql = "UPDATE cashbackengine_advertisements  SET title='$adTitle', image_url='$image_url', link='$link', image_name='' WHERE advertisement_id='$id'";
			
			if(smart_mysql_query($sql))
			{
				header("Location: advertisements.php?msg=updated");
				exit();
			}
		}
		else
		{ 
			// For inserting new advertisement
			if($select_option=="upload_image")
			{
				$sql = "INSERT INTO cashbackengine_advertisements SET title='$adTitle', link='$link',image_name='$loc'";
			}
			else
			{
				$sql = "INSERT INTO cashbackengine_advertisements SET title='$adTitle', image_url='$image_url',link='$link'"; 
			}

			if (smart_mysql_query($sql))
			{
				header("Location: advertisements.php?msg=added");
				exit();
			}
			
			header("Location: advertisements.php?msg=exists");
			exit();
		}	
	}
	else
	{
		$errormsg = "";
		foreach ($errors as $errorname)
		{
			$errormsg .= "&#155; ".$errorname."<br/>";
		}
	}
}
require_once ("inc/header.inc.php");

?>
<h2><?php echo $title;?></h2>

<?php if (isset($errormsg) && $errormsg != "") { ?>
	<div class="error_box"><?php echo $errormsg; ?></div>
<?php } elseif (isset($_GET['msg']) && ($_GET['msg']) == "added") { ?>
	<div class="success_box">Advertisement has been successfully added.</div>
<?php } ?>
	
	<form action="" method="post" enctype="multipart/form-data">
		<table align="center" width="75%" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td colspan="2" align="right" valign="top"><font color="red">* denotes required field</font></td>
			</tr>
			<tr>
				<td width="150" nowrap="nowrap" valign="middle" align="right" class="tb1"><span class="req">* </span>Title :</td>
				<td align="left">
					<input type="text" name="title" id="title" value="<?php echo getPostParameter('title'); ?>" size="40" class="textbox" />
				</td>
			</tr>
			<tr>
				<td width="150" nowrap="nowrap" valign="middle" align="right" class="tb1"><span class="req">* </span>Advertisement Image:</td>
				<td align="left">
					<input type="radio" name="radiourl" class="radiourl" id="use_url" value="use_url" <?php if($select_option=='use_url') {?> checked <?php	}?>>Image URL&nbsp;
					<input type="radio" name="radiourl" class="radiourl" id="upload_image" value="upload_image" <?php if($select_option=='upload_image'){?> checked <?php }?>>Upload Image
				</td>
			</tr>
			<tr>
				<td width="150" nowrap="nowrap" valign="middle" align="right" class="tb1"><span class="req"> </span></td>
				<td align="left">
					<input type="text" name="imageurl" id="imageurl" value="<?php echo $image_url; ?>" size="40" class="textbox" />
					<input type="file" name="image" id="image" />
					<?php if($id && $_POST['existingAdImage']!=''){ ?>
						<input type="hidden" name="existingAdImage" value="<?php echo getPostParameter('existingAdImage')?>"/>
						<a target="_blank" href="<?php echo $_POST['existingAdImage']?>"><img style="width:50%" src="<?php echo $_POST['existingAdImage']?>" /></a>
					<?php } ?>
				</td>
			</tr>
	
			<tr>
				<td width="150" nowrap="nowrap" valign="middle" align="right" class="tb1"><span class="req">* </span>Link :</td>
				<td align="left">
					<input type="text" name="link" id="link" value="<?php echo $link; ?>" size="40" class="textbox" />
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td valign="middle" align="left">
					<input type="hidden" name="action" id="action" value="add" />
					<input type="submit" name="add" id="add" class="submit" value="<?php echo $title;?>" />
				</td>
			</tr>
		</table>
	</form>

<script>
$(function(){
	$("#image").hide();
	$("#imageurl").hide();
	
	var radioImageChecked = $('#upload_image').is(':checked');
	var radioUrlChecked = $('#use_url').is(':checked');
	
	if(radioImageChecked){
		$("#image").show();
		$("#imageurl").hide();	
	}else if(radioUrlChecked){
		$("#imageurl").show();
		$("#image").hide();
	}
		
	$('.radiourl').click(function()	{
		var value = $(this).val();
		if(value=="use_url"){
			$("#imageurl").show();
			$("#image").hide();
		}
		else if(value="upload_image"){
			$("#image").show();
			$("#imageurl").hide();
		}
	});
});
</script>
<?php require_once ("inc/footer.inc.php"); ?>