<?php
/*
 * Файл возвращает список месяцев в ответ на Ajax запрос из jScript
 * Список формируется на основании запроса в базу данных,
 * из которой извлекается минимальная и максимальная дата выполнения
 * команд
*/
 
//Исходники:
//http://www.jqwidgets.com/jquery-widgets-documentation/documentation/phpintegration/bind-jquery-combobox-to-mysql-database-using-php.htm

//Подключаем общие переменные 
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
// get data and store in a json array

//Получаем минимальную дату - это просто первая строчка из записей,
//отсортированных по времени.
//http://stackoverflow.com/questions/8815030/how-to-get-a-first-and-last-date-in-a-mysql-database-table
$queryMinDate = "SELECT time FROM ".$commands_table_name;
$queryMinDate.= " ORDER BY time ASC LIMIT 1";

$minDateResult = mysql_query($queryMinDate) or die("SQL Error 1: " . mysql_error());
while ($row = mysql_fetch_array($minDateResult, MYSQL_ASSOC)) {
    $mindate = $row['time'];
};

//получаем максимальную дату аналогично, но сортируем в обратном порядке
$queryMaxDate = "SELECT time FROM ".$commands_table_name;
$queryMaxDate.= " ORDER BY time DESC LIMIT 1";

$maxDateResult = mysql_query($queryMaxDate) or die("SQL Error 1: " . mysql_error());
while ($row = mysql_fetch_array($maxDateResult, MYSQL_ASSOC)) {
    $maxdate = $row['time'];
};

//Получаем список месяцев в промежутке между минимальными 
//и максимальными датами
//http://stackoverflow.com/questions/11944684/delicate-way-to-get-a-list-of-years-and-month-between-two-given-dates
    $begin = new DateTime( $mindate );
    $begin->modify('first day of this month');
    $end = new DateTime( $maxdate);
    $interval = new DateInterval('P1M'); // 1 month interval

    $period = new DatePeriod($begin, $interval, $end);
    
    $months[]="all";
    foreach ( $period as $dt )
        $months[] = (string)$dt->format( "F" ).
			" ".(string)$dt->format( "Y" );
//возвращаем данные json по месяцам, которые будут восприниматься jScript
echo json_encode($months);
 
?>
