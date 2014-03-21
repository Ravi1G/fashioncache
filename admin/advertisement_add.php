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
if($id)
{
	$msg = "updated";
	$fetch_record 	=	mysql_fetch_assoc(smart_mysql_query("SELECT advertisement_id, image_url, link, image_name FROM cashbackengine_advertisements WHERE advertisement_id=$id"));
	$image_url		=	$fetch_record['image_url'];
	$link			=	$fetch_record['link'];
	$image_name		=	$fetch_record['image_name'];
}
if ($isPost)
{
	unset($errors);
	$errors = array();
	$select_option	=	$_POST['radiourl'];
	
	$image_url		= mysql_real_escape_string(getPostParameter('imageurl'));
	$link			= mysql_real_escape_string(getPostParameter('link'));
	$image_name		= $_FILES['image']['name'];
		

	if (!($image_name || $image_url && $link))
	{
		$errors[] = "Please ensure that all fields marked with an asterisk are complete";
	}
	else
	{	
		if(isset($image_url) && $image_url!="")
		{
			if (isset($image_url) && $image_url != "" && !preg_match("/^((http|https):\/\/)?([www]+(\.[a-zA-Z0-9]+)+(\.[a-zA-Z0-9]).*)$/", $image_url))
			{
				$errors[] = "Enter correct url format, enter the 'http://www.abc.com' or 'https://www.abc.com' statement before your link";
			}
		}

		if (isset($link) && $link == "")
		{
			$errors[] = "Please ensure that all fields marked with an asterisk are complete";
		}
		if (isset($link) && $link != "")
		{
			if (isset($link) && $link != "" && !preg_match("/^((http|https):\/\/)?([www]+(\.[a-zA-Z0-9]+)+(\.[a-zA-Z0-9]).*)$/", $link))
			{
				$errors[] = "Enter correct link url format, enter the 'http://www.abc.com' or 'https://www.abc.com' statement before your link";
			}
		}

			
	}
	if (count($errors) == 0)
	{
		if(isset($image_name) && $image_name!="")
		{
			$allowedExts = array("gif", "jpeg", "jpg", "png");
			$temp = explode(".", $_FILES["image"]["name"]);
			$extension = end($temp);
			
			
			if ((($_FILES["image"]["type"] == "image/gif")|| ($_FILES["image"]["type"] == "image/jpeg")|| ($_FILES["image"]["type"] == "image/jpg")	|| ($_FILES["image"]["type"] == "image/pjpeg")|| ($_FILES["image"]["type"] == "image/x-png")|| ($_FILES["image"]["type"] == "image/png"))&& ($_FILES["image"]["size"] < 20000)&& in_array($extension, $allowedExts))
			{
		  		if ($_FILES["image"]["error"] > 0)
		  		{
					$error[]= $_FILES["image"]["error"];
				}
				else
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
			}
			else
			{
				$errors[] = "Invalid file";
			}
		}
		
		if($id)//For updation of record while edit
		{			
			if($image_name)
			{
				$sql=mysql_query("SELECT image_name FROM cashbackengine_advertisements WHERE advertisement_id='$id'");
				$row=mysql_fetch_assoc($sql);
				$image_loc=$row['image_name'];
				
				if($image_loc!="" )	
					$img_del_result=unlink($image_loc);	
						
				$sql = mysql_query("UPDATE cashbackengine_advertisements  SET image_url='', link='$link', image_name='$loc' WHERE advertisement_id='$id'");
			}
			else if($image_url ||$image_name && $image_url)
			{
				$sql=mysql_query("SELECT image_name FROM cashbackengine_advertisements WHERE advertisement_id='$id'");
				$row=mysql_fetch_assoc($sql);
				$image_loc=$row['image_name'];
				
				if($image_loc!="")	
					$img_del_result=unlink($image_loc);	
					
				$sql = mysql_query("UPDATE cashbackengine_advertisements  SET image_url='$image_url', link='$link', image_name='' WHERE advertisement_id='$id'");
			}
			if($sql>0)
			{
				header("Location: advertisements.php?msg=updated");
				exit();
			}
		}
		else{ // For inserting new advertisement
			if($image_name!="")
			{
				$image_name='upload/'.$image_name;
				$check_query = smart_mysql_query("SELECT * FROM cashbackengine_advertisements WHERE image_name= '.$image_name.'");		
			}
			else if($image_url!="")
			{
				$check_query = smart_mysql_query("SELECT * FROM cashbackengine_advertisements WHERE image_url='.$image_url.'"); 
			}
			if (mysql_num_rows($check_query) == 0)
			{
				if($image_url!="")
				{
					$sql = "INSERT INTO cashbackengine_advertisements SET image_url='$image_url',link='$link'"; 
				}
				else if($loc!="")
				{
					$sql = "INSERT INTO cashbackengine_advertisements SET link='$link',image_name='$loc'";
				}

				if (smart_mysql_query($sql))
				{
					header("Location: advertisements.php?msg=added");
					exit();
				}
			
			}
			else
			{
				header("Location: advertisements.php?msg=exists");
				exit();
			}
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
<script>
		<!--
			function hiddenDiv(id,showid){
				if(document.getElementById(id).value != ""){
					document.getElementById(showid).style.display = ""
				}else{
					document.getElementById(showid).style.display = "none"
				}
			}
		-->
</script>

	<?php if (isset($errormsg) && $errormsg != "") { ?>
		<div class="error_box"><?php echo $errormsg; ?></div>
	<?php } elseif (isset($_GET['msg']) && ($_GET['msg']) == "added") { ?>
		<div class="success_box">Advertisement has been successfully added</div>
	<?php } ?>
	
		  <form action="" method="post" enctype="multipart/form-data">
		  <table align="center" width="75%" border="0" cellpadding="3" cellspacing="0">
          <tr>
            <td colspan="2" align="right" valign="top"><font color="red">* denotes required field</font></td>
          </tr>
          <tr>
            <td width="150" nowrap="nowrap" valign="middle" align="right" class="tb1"><span class="req">* </span>Please select one:</td>
			<td align="left">
				<input type="radio" name="radiourl" class="radiourl" id="use_url" value="use_url" <?php if(isset($_POST['radiourl']) && $_POST['radiourl']=='use_url' || (isset($image_url)&& $image_url !="")) {?> checked <?php	}?>>Use URL&nbsp;
				<input type="radio" name="radiourl" class="radiourl" id="upload_image" value="upload_image" <?php if(isset($_POST['radiourl']) && $_POST['radiourl']=='upload_image'|| (isset($image_name)&& $image_name!="")){?> checked <?php }?>>Upload Image
			</td>
          </tr>
          <tr>
             <td width="150" nowrap="nowrap" valign="middle" align="right" class="tb1"><span class="req"> </span></td>
			<td align="left">
				<input type="text" name="imageurl" id="imageurl" value="<?php echo $image_url; ?>" size="40" class="textbox" />
				<input type="file" name="image" id="image" />
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
  $(function()
  {
	$("#image").hide();
	$("#imageurl").hide();
  	var radioImageChecked = $('#upload_image').is(':checked');
  	var radioUrlChecked = $('#use_url').is(':checked');
	if(radioImageChecked)
	{
		$("#image").show();
		$("#imageurl").hide();	
	}		
	else if(radioUrlChecked)
	{
		$("#imageurl").show();
		$("#image").hide();
	}	
	$('.radiourl').click(function()
	{
		var value = $(this).val();
		if(value=="use_url")
		{
			$("#imageurl").show();
			$("#image").hide();
		}
		else if(value="upload_image")
		{
			$("#image").show();
			$("#imageurl").hide();
		}
  	});
  });
  </script>
<?php require_once ("inc/footer.inc.php"); ?>