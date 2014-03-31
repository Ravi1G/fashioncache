<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/
	// Error Reporting
	@error_reporting(E_ALL ^ E_NOTICE);
	
	require_once('constants.php');
	require_once(CBengine_ROOT."db.inc.php");
	require_once(CBengine_ROOT."functions.inc.php");
	require_once(DOCS_ROOT."/admin/inc/admin_funcs.inc.php");
	
	if (!defined('is_Setup'))
	{
		require_once(CBengine_ROOT."siteconfig.inc.php");

		$lang = $_COOKIE['site_lang'];

		if (!empty($lang) && file_exists(DOCS_ROOT."/language/".$lang.".inc.php"))
		{
			define('USER_LANGUAGE', $lang);
			require_once(DOCS_ROOT."/language/".$lang.".inc.php");
		}
		else
		{
			define('USER_LANGUAGE', SITE_LANGUAGE);
			require_once(DOCS_ROOT."/language/".SITE_LANGUAGE.".inc.php");
		}
	}

	// maintenance mode //
	if (SITE_MODE == 'maintenance' && !$admin_panel)
	{
		require_once(DOCS_ROOT."/maintenance.php");
		die();
	}
	
	//BLOG URL
	define('BLOG_URL', SITE_URL.BLOG_DIR_NAME.'/');
	require_once PUBLIC_HTML_PATH.'/blog/apis.php';
?>