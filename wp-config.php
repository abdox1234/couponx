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
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'couponx_db' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'tkY7F@y;e`bk~S?2efvkPRSYMI?/Fx^d+ELc,zj^%KSFn`WUqq0x#UW;9Aa=1o%T' );
define( 'SECURE_AUTH_KEY',  'xjo[C/3!eXc/B7S8,E2bBye[6!o3k/<k<2L_P|X:76[i&:*w@j&Ij!M.BM5cdXs#' );
define( 'LOGGED_IN_KEY',    'e3y$<1q&KQ%?XA;$=?_*VK;yqlsQ9>B_r&Asng/>0Cs|=OFAc~W<cj8Usq12oX:u' );
define( 'NONCE_KEY',        ']uoJ_)jR%.VeG>#BYzRm5)%p-Q)pD=WW8HMBjAj&kFk,oVvh<+Fb=IHoEzZ[A)0{' );
define( 'AUTH_SALT',        '@`yY<Iz3,#WepLFp7_9.d5,0!t:C72.P]j&Oe6rzT7&n(~VyPR?-)W;vP[&XX?(O' );
define( 'SECURE_AUTH_SALT', '5ne!&g,ut}[+RzCHGf=bt7*Nt4Q.~h0+6a9{>.ioztxg(|JriS<Os64:-;eeL(UO' );
define( 'LOGGED_IN_SALT',   'T6f*Xy}PFQgLxhUhXiM;u3dQ()R~@=osSB{yNY0$!:rblr>B]Ej,tNVe.{B[+9Hj' );
define( 'NONCE_SALT',       '~Z!}N8c0h1(DaGqEECkz}5AMc-<=jZaGYWZ0D!F;Jpik;LY^@H1ka&195&%/>%Xa' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
