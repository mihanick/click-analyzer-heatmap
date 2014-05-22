<?php
/*
 * Файл возвращает список пользователей в ответ на Ajax запрос из jScript
 * Список формируется на основании запроса в базу данных,
*/

//http://www.jqwidgets.com/jquery-widgets-documentation/documentation/phpintegration/bind-jquery-combobox-to-mysql-database-using-php.htm
#Include the connect.php file
include('init.php');

//connection String
$connect = mysql_connect($server, $username, $password)
or die('Could not connect: ' . mysql_error());
//select database
mysql_select_db($database, $connect);
//Select The database
$bool = mysql_select_db($database, $connect);
if ($bool === False){
   print "can't find $database";
}
//Получаем всех уникальных пользователей из таблицы documents
$query = "SELECT DISTINCT user FROM ".$documents_table_name;
 
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
$users[] = "all";
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $users[] = $row['user'];
}
 //Возвращаем массив пользователей в виде json
echo json_encode($users);
?>
