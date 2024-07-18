<?php
define('WP_CACHE', true);
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'tecprestige');

/** Database username */
define('DB_USER', 'root');

/** Database password */
define('DB_PASSWORD', '');

/** Database hostname */
define('DB_HOST', 'localhost');

/** Database charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The database collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

if (!defined('WP_CLI')) {
    define('WP_SITEURL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']);
    define('WP_HOME',    $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']);
}



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
define('AUTH_KEY',         'W3hT2DqB7Zx1RZ4tKrJhImgm4cslSfx0KQnIy9t9es5YqCKqQIqV5Zwa5jGxxWuH');
define('SECURE_AUTH_KEY',  'zyptoslgFtQEKaRapfLsWMDzVDbZ2x1XFKQ6kh2KFpie2xARGKwXhUMA1IAZpmFa');
define('LOGGED_IN_KEY',    'KrL6bdsZuLEWQho8Lm4t8XLOsaZxPZ9BNQsfzgSG6mfuMJ0DoUwlIJp4v4OnGDqP');
define('NONCE_KEY',        'DS34o0YOPaVT1qPt4Om0YgBlusLRxCfJezeKfNZws7vBM3QkEpHsXiPkMvsDLqHQ');
define('AUTH_SALT',        'BX1aonb5gRZ2I1J4f6Mz8kBAR38lbEHithpiJduBQyNRCShgNC0v1ACjyPludEK8');
define('SECURE_AUTH_SALT', 'y4XhoCojHz40pADEsmbZgoCQdBsPOG0SIYBPcDYGe168sJIcWPCz5Sqhk9dlFSNn');
define('LOGGED_IN_SALT',   'zNBxb9iZgzTtFB2uyrI0HcNNVsmtl8WTNEcotVR1Cbrw97TnXJoAXTBYBklMvyDU');
define('NONCE_SALT',       'MwidnyfV9w7nwWM5mXyw2fwTC8WBBMJgle157RU33uTlj4lgBJ3rj6UNk7bWryMc');

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define('WP_DEBUG', false);

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';