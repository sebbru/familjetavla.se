<?php
/**
 * Baskonfiguration för WordPress.
 *
 * Denna fil används av wp-config.php-genereringsskript under installationen.
 * Du behöver inte använda webbplatsen, du kan kopiera denna fil direkt till
 * "wp-config.php" och fylla i värdena.
 *
 * Denna fil innehåller följande konfigurationer:
 *
 * * Inställningar för MySQL
 * * Säkerhetsnycklar
 * * Tabellprefix för databas
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL-inställningar - MySQL-uppgifter får du från ditt webbhotell ** //
/** Namnet på databasen du vill använda för WordPress */
define('DB_NAME', 'familjetavla');

/** MySQL-databasens användarnamn */
define('DB_USER', 'root');

/** MySQL-databasens lösenord */
define('DB_PASSWORD', '123qwe');

/** MySQL-server */
define('DB_HOST', 'localhost');

/** Teckenkodning för tabellerna i databasen. */
define('DB_CHARSET', 'utf8mb4');

/** Kollationeringstyp för databasen. Ändra inte om du är osäker. */
define('DB_COLLATE', '');

/**#@+
 * Unika autentiseringsnycklar och salter.
 *
 * Ändra dessa till unika fraser!
 * Du kan generera nycklar med {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * Du kan när som helst ändra dessa nycklar för att göra aktiva cookies obrukbara, vilket tvingar alla användare att logga in på nytt.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'Mvv_CF;dhEaCR|YC]1p{_b;n=?:W^WU#m3NNdr=|_ _LG*eY.dLdP&].-mAY2B.i');
define('SECURE_AUTH_KEY',  'I-*f.oRI?&m`DGkcyk+R~=#.fzSXT!E_Eu@8GJ.C} +_O5$PM#?}1b;Q+ rPBLNt');
define('LOGGED_IN_KEY',    'F_%FfnZ-EUh)U#d?mkfg+a.r6g**kvjkJDd#zd)w,x&J^7Yh6[6AtuR{xybFj?Mm');
define('NONCE_KEY',        '^?;GE4^XRN#an>oee)y.{NU-pf:eSH}@^9f{VhZ Hxv;,2Vv~omi[<X!UKx/]2t;');
define('AUTH_SALT',        '0:iwUjxo/QR!r~%tmT%}H5ISOx&U[DRUergR]@$p cqn},Nk_4`ZtdVBa32fEKD{');
define('SECURE_AUTH_SALT', 'cuAi(WfQO{q#)v83IDJ[tl=pYwQb5iX~0E0nvvRAAyYK?+P63l5rv<K8wRQ&>H{4');
define('LOGGED_IN_SALT',   'Q&D%!Y^N-u&dRQ)ihZyJ`HD~PAP~GNLSzP=iE5^X1k4_?3FL -q6Gh#2=A|?pML8');
define('NONCE_SALT',       '~*(8xmX_q921wpbxOb*8<Q8C@|knLI=,:0G->dwqmn$!SfHODDLhJM__nsF-v3n=');

/**#@-*/

/**
 * Tabellprefix för WordPress Databasen.
 *
 * Du kan ha flera installationer i samma databas om du ger varje installation ett unikt
 * prefix. Endast siffror, bokstäver och understreck!
 */
$table_prefix  = 'wp_';

/** 
 * För utvecklare: WordPress felsökningsläge. 
 * 
 * Ändra detta till true för att aktivera meddelanden under utveckling. 
 * Det är rekommderat att man som tilläggsskapare och temaskapare använder WP_DEBUG 
 * i sin utvecklingsmiljö. 
 *
 * För information om andra konstanter som kan användas för felsökning, 
 * se dokumentationen. 
 * 
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */ 
define('WP_DEBUG', false);

/* Det var allt, sluta redigera här! Blogga på. */

/** Absoluta sökväg till WordPress-katalogen. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Anger WordPress-värden och inkluderade filer. */
require_once(ABSPATH . 'wp-settings.php');