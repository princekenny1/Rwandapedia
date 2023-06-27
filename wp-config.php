<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'odpunbms_wp_aiwex' );

/** Database username */
define( 'DB_USER', 'odpunbms_wp_icpeh' );

/** Database password */
define( 'DB_PASSWORD', '36cY#9jCf7ywLi^&' );

/** Database hostname */
define( 'DB_HOST', 'localhost:3306' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', '(26Nn)!9@2MJp5M#lRj/b4amv:218+7ip)yzS4[b+Lu)E34@P6][1Hqab3_!(o%v');
define('SECURE_AUTH_KEY', 'bim@A3!;H1h1c06]!08~cRpM0lt4iCRJ&c86[+V9-@4WE4yT;R9C)UAW082AEdAE');
define('LOGGED_IN_KEY', '64Ww&k)&%qi8)O@*Mrv:Z&9k[4V#Irw%W(Y%Z[#9#43Mg;43i7e|+BI3pm~0T7&3');
define('NONCE_KEY', 'zipsqK7pO90)q904[818Qk0nPI&Kje3XOgQ217nLqx4g80|IPb)U-|q_8e-oBF0t');
define('AUTH_SALT', 'X49C2|[07S95tbbB~9G)]2M1rVNv48~X]]68+-N&yUZG!0sp~UL_jfH//K7T*[yo');
define('SECURE_AUTH_SALT', 'K|LsVEn5/P_G%B961H|3:&6(u8y[2c|Q1Pc0orz)!c_u10Wp_CYJgrH9mz/6m(jo');
define('LOGGED_IN_SALT', 'nRUQmh&90C4_tKSXyQqf[R4SG)z+75!SS71*g2Y_2m8k+2RPXX7m(%CSl|b&H5J|');
define('NONCE_SALT', 'Dr)79du(wcj)0eEJm9Rm8ih%6hibWP1dD71[tiBD386hlaTtF0;zKS0#63#q2vZc');


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'FNc3elVS_';


/* Add any custom values between this line and the "stop editing" line. */

define('WP_ALLOW_MULTISITE', true);
/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
set_time_limit(300);