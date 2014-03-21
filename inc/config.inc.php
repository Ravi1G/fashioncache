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


	/// Database Settings ///
	define('DB_NAME', 'fashion_cache');				// MySQL database name
	define('DB_USER', 'root');				// MySQL database user
	define('DB_PASSWORD', '');			// MySQL database password
	define('DB_HOST', 'localhost');		// MySQL database host name (in most cases, it's localhost)


	define('PUBLIC_HTML_PATH', $_SERVER['DOCUMENT_ROOT']);
	define('DOCS_ROOT', $_SERVER['DOCUMENT_ROOT']);
	define('CBengine_ROOT', dirname(__FILE__) . '/');
	define('CBengine_PAGE', TRUE);
	require_once(CBengine_ROOT."db.inc.php");
	require_once(CBengine_ROOT."functions.inc.php");

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

?>