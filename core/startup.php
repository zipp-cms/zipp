<?php
/*
@package: Zipp
@version: 0.2 <2019-07-01>
*/

// Base Constants
define( 'DS', DIRECTORY_SEPARATOR );
define( 'EOL', PHP_EOL );
define( 'DIR', dirname(__DIR__). DS );

// CORE STATES
define( 'DEBUG_FILE', DIR. 'debug.zipp' );
define( 'DEBUG', is_file( DEBUG_FILE ) );
define( 'HTACCESS_FILE', DIR. '.htaccess' );
define( 'INSTALLING', !is_file( HTACCESS_FILE ) );

// CORE
define( 'CORE_LIB', DIR. 'core'. DS. 'lib'. DS );
define( 'CORE_BUILD', 1 );

require_once( CORE_LIB. 'helperfunctions.php' );
require_once( CORE_LIB. 'autoloader.php' );

Core\Autoloader::addDirective( 'Core', CORE_LIB );

// PATHS
define( 'USER_PATH', DIR. 'user'. DS );
define( 'TMP_PATH', DIR. 'tmp'. DS );
if ( !is_dir( TMP_PATH ) )
	mkdir( TMP_PATH );