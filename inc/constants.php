<?php
/// Database Settings ///
define('FC_DB_NAME', 'fashion_cache');			// MySQL database name
define('FC_DB_USER', 'root');					// MySQL database user
define('FC_DB_PASSWORD', '');					// MySQL database password
define('FC_DB_HOST', 'localhost');				// MySQL database host name (in most cases, it's localhost)

define('FC_WP_DB_NAME', 'fashion_cache_blog');	// MySQL database name
define('FC_WP_DB_USER', 'root');				// MySQL database user
define('FC_WP_DB_PASSWORD', '');				// MySQL database password
define('FC_WP_DB_HOST', 'localhost');			// MySQL database host name (in most cases, it's localhost)

define('BLOG_DIR_NAME', 'blog');

define('PUBLIC_HTML_PATH', $_SERVER['DOCUMENT_ROOT']);
define('DOCS_ROOT', $_SERVER['DOCUMENT_ROOT']);
define('CBengine_ROOT', dirname(__FILE__) . '/');
define('CBengine_PAGE', TRUE);