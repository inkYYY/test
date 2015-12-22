<?php

use pssPack\pdosingle\DBConnect,
    pssPack\pdosingle\DBQuery;

require_once('../lib/DBConnect.php');
require_once('../lib/DBQuery.php');


$dsn      = 'mysql:dbname=db_pdo_3;host=127.0.0.1';
$user     = 'root';
$password = '';

$dbh = DBConnect::connect($dsn, $user, $password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$query = new DBQuery($dbh);

echo "\n queryAll -------------------------------------------------\n";
print_r($query->queryAll('SELECT * FROM users'));

echo "\n queryRow -------------------------------------------------\n";
print_r($query->queryRow('SELECT * FROM users limit 1'));

echo "\n queryColumn ----------------------------------------------\n";
print_r($query->queryColumn('SELECT email FROM users'));

echo "\n queryScalar ----------------------------------------------\n";
echo $query->queryScalar('SELECT email FROM users');
echo "\n";


echo "\n reconnect ------------------------------------------------\n";
$dbh2 = DBConnect::connect('mysql:dbname=db_pdo_3;host=127.0.0.1', 'root', '');
$dbh2->reconnect();
$query = new DBQuery($dbh2);
/**
 * -----------------------------------------------------------------
 */

$data = [
    'email' => 'qwerty+' . rand(1,99999) . '@groupbwt.com',
    'password' => password_hash('qwerty' . time() ,PASSWORD_DEFAULT)
];

$rowCount = $query->execute("INSERT INTO `users` (`email`, `password`) VALUES (:email, :password)", $data);

echo "\ncount inserts row -> " . $rowCount . "\n";

$lastId = $dbh2->getLastInsertID();
echo "\nlast ins id \t {$lastId} \n";
print_r($query->queryRow('SELECT * FROM users where id = :id', ['id' => $lastId]));

/**
 * -----------------------------------------------------------------
 */
$updateData = [
    'password' => password_hash('qwerty' . time() ,PASSWORD_DEFAULT),
    'id' => $lastId
];

$rowCountUpdate = $query->execute("Update `users` SET password = :password where id = :id", $updateData);

echo "\ncount update row -> " . $rowCountUpdate . "\n";
echo "\nlast ins id \t {$lastId} \n";
$rowCountDelete = $query->execute("DELETE FROM `users` where id = :id", ['id' => --$lastId]);

echo "\ncount delete row -> " . $rowCountDelete . "\n";
echo "\nlast ins id \t {$dbh2->getLastInsertID()} \n";

echo "\nlast query execution time -> ";
var_dump($query->getLastQueryTime());

