<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */
@include_once('../../inc/constants.php');
@include_once('../inc/constants.php');

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', FC_WP_DB_NAME);

/** MySQL database username */
define('DB_USER', FC_WP_DB_USER);

/** MySQL database password */
define('DB_PASSWORD', FC_WP_DB_PASSWORD);

/** MySQL hostname */
define('DB_HOST', FC_WP_DB_HOST);

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '+;zcG8)w$0W+9q` <Vr:)|;[zp)&|Vu0Sa5C*k7Fmy:pbU2et @L@1=|svts5iFH');
define('SECURE_AUTH_KEY',  '0xj^pSh(?0_&_F%|5@7GxVMAMdx>Cx:}}(B-(T3-f~s+JzLC+sE;3UA5tqyNMqU&');
define('LOGGED_IN_KEY',    'a9n3Vm4eaDb:ox/s8r$p+g|8JW4zRTH>T/efA`3dz@iZ-5-=;pDK{=[?j<A9b|o[');
define('NONCE_KEY',        'a!q8ei=Ld3=y,w0ye_PCD&Q82T[~1t;x-P|Xac4i0g}u1/O|N+&n 1q;OW-[fJxn');
define('AUTH_SALT',        'R1 E6$W#!skr=NEe#|nz>-NMlOr8ji3,xp+@3^D/U(F,/M*W39N1]]-5+L3$2Ji|');
define('SECURE_AUTH_SALT', 'W5 ZzZW4*sVDIDNVk%{FVi;BQh+B|;@2+&2D~>c%RDs[Q]HcNiUWui$OB)B$)5zr');
define('LOGGED_IN_SALT',   '*m]PA)P(g1a%[ia*i>@e{`=|/pA[H{<,A(@|?BLf^Z(:/$Y9uUDsMc =1i+}5t#i');
define('NONCE_SALT',       '|ae[e|`7|u2ICQ`:XH+k$fmtVb><._ZnlmLe^-3]y4y-tYFB^!. j/_:@v&pfDbq');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
