<?php

use pssPack\pdosingle\DBConnect;

require_once('../lib/DBConnect.php');

$dsn      = 'mysql:dbname=db_pdo_3;host=127.0.0.1';
$user     = 'root';
$password = '';

$dbh = DBConnect::connect($dsn, $user, $password);
$dbh2 = DBConnect::connect('mysql:dbname=db_pdo_1;host=127.0.0.1', 'root', '');
$dbh3 = DBConnect::connect('mysql:dbname=db_pdo_3;host=127.0.0.1', 'root', '');
$dbh4 = DBConnect::connect('mysql:dbname=db_pdo_2;host=127.0.0.1', 'root', '');
$dbh5 = DBConnect::connect('mysql:dbname=db_pdo_2;host=127.0.0.1', 'ink', 'ink');
$dbh5->reconnect();
$dbh2->close();
//
print_r($dbh->getConnectionConfig());
echo "\ndbh1 -- pdo\n";
print_r($dbh->getPdoInstance());
echo "\ndbh1 -- config\n";
print_r($dbh->getCurrentConfiguration());
echo "\n-------------------------------------------\n";
print_r($dbh2->getConnectionConfig());
echo "\ndbh2 -- pdo\n";
print_r($dbh2->getPdoInstance());
echo "\ndbh2 -- config\n";
print_r($dbh2->getCurrentConfiguration());
echo "\n-------------------------------------------\n";
print_r($dbh3->getConnectionConfig());
echo "\ndbh3 -- pdo\n";
print_r($dbh3->getPdoInstance());
echo "\ndbh3 -- config\n";
print_r($dbh3->getCurrentConfiguration());
echo "\n-------------------------------------------\n";
print_r($dbh4->getConnectionConfig());
echo "\ndbh4 -- pdo\n";
print_r($dbh4->getPdoInstance());
echo "\ndbh4 -- config\n";
print_r($dbh4->getCurrentConfiguration());
echo "\n-------------------------------------------\n";
print_r($dbh5->getConnectionConfig());
echo "\ndbh5 -- pdo\n";
print_r($dbh5->getPdoInstance());
echo "\ndbh5 -- config\n";
print_r($dbh5->getCurrentConfiguration());
echo "\n-------------------------------------------\n";