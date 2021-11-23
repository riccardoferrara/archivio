<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
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
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'OALo3XRtyF9BRfLunpeQ1eD9+S2FF0DtH5UwDO/M+lCWdMZwY+kg0wTz/ReQePIPbu0/P5Ng08I8AfjqcrH9jA==');
define('SECURE_AUTH_KEY',  'Y/lloKF/0PnYn/TBrCJMj3GQIfEBqt9i943MFX5mO2yk2oHMlqb/cS4fdk8P+pYXLx4wMG2PPqUdDgStG7UgEg==');
define('LOGGED_IN_KEY',    '6K5rVze/gZgFTJMkvySbfST92JZgkqhJneqqM3K1Xzh02tSUA3mR2YiDW0kOWTk1pk4WA7Zi9kAAjCK7k3MYOQ==');
define('NONCE_KEY',        'bk4YqA6OF6kgQuWl4RNR5t3a2gHKh5sXcMfw5gaoxhO2Y1LF2QK3kB8Ed7/ZAFSYJDpbKIBp2j0RTEXjUTT8cw==');
define('AUTH_SALT',        'z4wdcVkEDkTskBEKutgsc4RC70xrdgN2VSnm27XBi5wMZk8K/0w3l1+RTvjhOxIus7MC2bB7/D/Vn9SBw6MoNQ==');
define('SECURE_AUTH_SALT', 'C2+geCXX78CBKwmVaj7eRRMGhKeYN6suDkzMA+yS3tw0qYSZPmE2eSIM7df0FqHWla4ZW4DTT3EJm9Ta1UGV1Q==');
define('LOGGED_IN_SALT',   '2bvT0eA3Kt5ysWb0ofsK+MI4dkJA5+dm1eZsWh4MMbmHnvPBbFjasS0vnd03sbIrOTMQXy91hWE/BRSXJrcTDw==');
define('NONCE_SALT',       'OEvY0Vru97lpc4JlwMJlyzzlsJodIh2H9xLA/oeRoU+C4LuAnWzEzccK6cnpdBltcIQqH2B17f+Aor7fh4t+dg==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
