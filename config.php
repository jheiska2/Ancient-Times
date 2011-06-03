<?php
// config.php
// Setting for the database connection
    define('DB_HOST', '********');
    define('DB_USER',  '*******' );
    define('DB_PASSWORD',  '*******' );
    define('DB_DATABASE', '*******'); 

// Table/view prefix in front of table names and views
define('DB_PREFIX', 'pe_');

// Settings for this website (WS), used as default values in CHTMLPage.php
define('WS_SITELINK', 'http://www.student.bth.se/~jahb08/me/forumRomanum/'); // Used for redirect 
define('WS_TITLE', ' '); // The h1 label of this site
define('WS_STYLESHEET', 'style/plain/stylesheet_liquid.css');    // Default stylesheet of the site.
define('WS_IMAGES',            WS_SITELINK . 'images/');
define('WS_FAVICON',         WS_IMAGES . 'favicon.ico');                     // Small icon to display in browser  
define('WS_FOOTER', '&copy; Jari Heiskanen, all right reserved:..'); // Footer at the end of the page. 
define('WS_VALIDATORS', TRUE);                // Show links to w3c validators tools.
define('WS_TIMER',         TRUE);              // Time generation of a page and display in footer.
define('WS_CHARSET',     'utf-8');           // Use this charset
define('WS_LANGUAGE',     'en');              // Default language
define('WS_JAVASCRIPT',    WS_SITELINK . '/js/');    // JavaScript code   


// Define the application navigation menu.
$menuApps = Array (
    'BTH'                 => 'http://www.bth.se/',
    'Drupal'                 => 'http://www.drupal.org/',
    'GitHub' => 'http://github.com/mosbth/'

);
define('MENU_APPLICATION',         serialize($menuApps));

 // Define the navigation menu.
// MOVE THIS TO CHTMLPAGE OR OTHER CONFIG-FILE OR LEAVE IT AS IT IS?
$menuNavBar = Array (
    'Home'                 => '?p=home',
    'Forum' => '?m=rom&amp;p=home',
    'Blog' => '?m=blog&amp;p=index',
    'Features' => '?p=features',  
    'About' => '?p=about', 
    'Download' => '?p=download',   
    'Install'             => '?p=install'
);
define('MENU_NAVBAR',         serialize($menuNavBar)); 

$menuFooter = Array (
    'Home'                 => '?p=home',
    'About'             => '?p=about',
    'BTH'                 => 'http://www.bth.se/',
    'Drupal'                 => 'http://www.drupal.org/',
    'GitHub' => 'http://github.com/mosbth/'
);
define('WS_FOOTER_MENU',         serialize($menuFooter));

// -------------------------------------------------------------------------------------------
// Choose the hashing algoritm to use for storing new passwords. Can be changed during
// execution since various methods is simoultaneously supported by the database.
//
// Changing to PLAIN may imply writing an own function in PHP to encode the passwords. 
// Storing is then done as plaintext in the database, withouth using a salt.
//
// This enables usage of more complex hashing and encryption algoritms that are currently not
// supported within MySQL.
#define('DB_PASSWORDHASHING', 'MD5');
define('DB_PASSWORDHASHING', 'SHA-1');
#define('DB_PASSWORDHASHING', 'PLAIN');
 // -------------------------------------------------------------------------------------------
// Server keys for reCAPTCHA. Get your own keys for your server.
// http://recaptcha.net/whyrecaptcha.html
//
// dev.phpersia.org
//define('reCAPTCHA_PUBLIC',    '6LcswbkSAAAAAN4kRL5qcAdiZLRo54fhlCVnt880');    
//define('reCAPTCHA_PRIVATE',    '6LcswbkSAAAAACFVN50SNO6lOC8uAlIB2cJwxknl');    

// www.student.bth.se
define('reCAPTCHA_PUBLIC',    '6LeUxbkSAAAAADjelI32xn2VdBwsMJLLiBO2umtO');    
define('reCAPTCHA_PRIVATE',    '6LeUxbkSAAAAAPRDQ8cAvEOgXMJZwb1rY2C5XauB');    


// Set the default email adress to be used as from in mail sent from the system to 
// account users. Be sure to set a valid domain to avoid spamfilters.
define('WS_MAILFROM',                 'Compuli Development Team <compuli@compuli.se>');
define('WS_MAILSUBJECTLABEL', '[Persia] ');
define('WS_MAILSIGNATURE',     
    "\n\nBest regards,\n" .
    "The Development Team Of Persia\n" .
    "http://www.compuli.se\n"
);


// Display the following actions if they are enabled.
// Set true to enable, false to disable.
define('LOCAL_LOGIN', true);
define('CREATE_NEW_ACCOUNT', true);
define('FORGOT_PASSWORD', true);

// User Control Panel
define('USER_CHANGE_PASSWORD', true);
define('USER_AVATAR', true);
define('USER_GRAVATAR', true);


// -------------------------------------------------------------------------------------------
//
// Settings for LDAP and LDAP authentication.
//
//define('LDAP_AUTH_SERVER', 'ldap.dbwebb.se');
//define('LDAP_AUTH_BASEDN', 'dc=dbwebb,dc=se');


// -------------------------------------------------------------------------------------------
//
// Settings for Google Analytics.
// http://www.google.com/analytics/
//
//define('GA_DOMAIN', '.phpersia.org');
//define('GA_TRACKERID', 'UA-6902244-4');


// -------------------------------------------------------------------------------------------
//
// Settings for file upload and file archive.
//
//define('FILE_ARCHIVE_PATH', '/usr/home/mos/archive/'); // Must be writable by webserver
//define('FILE_MAX_SIZE', 30000); // Filesize in bytes


//define('WS_MENU',         serialize($wsMenu));        // The menu
?>