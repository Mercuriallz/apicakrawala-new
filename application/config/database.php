<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$active_group = 'default';
$query_builder = FALSE;

$tnsQA = '(DESCRIPTION =
(ADDRESS_LIST =
  (ADDRESS = (PROTOCOL = TCP)(HOST = 10.90.1.120)(PORT = 1521))
)
(CONNECT_DATA = (SERVICE_NAME = css.japfacomfeed.co.id))
)';

$tnsPROD = '(DESCRIPTION =
(ADDRESS_LIST =
  (ADDRESS = (PROTOCOL = TCP)(HOST = 10.90.0.119)(PORT = 1521))
)
(CONNECT_DATA = (SERVICE_NAME = css.japfacomfeed.co.id))
)';

$db['default'] = array(
	'dsn'	=> 'mysql:host=localhost;dbname=traxesid_globaldata',
	'hostname' => 'localhost',

	'username' => 'traxesid',
	'password' => 'Orangila#2023',
	'database' => 'traxesid_globaldata',

	// 'username' => 'siprama',
	// 'password' => '86raG5RmWjth',
	// 'database' => 'siprama_globaldata',

	'dbdriver' => 'pdo',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => FALSE
);
