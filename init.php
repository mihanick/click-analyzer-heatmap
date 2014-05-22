<?php
/*
 * Файл производит необходимые инициализации и зачитывается пред каждым
 * файлом data.php, dwg.php, commands.php чтобы один раз задавать 
 * необходимые переменные
 * */

//Без этой опции будет неправильная кодировка при сохранении в sql
header('Content-Type: text/html; charset=utf-8');
//Без этой опции длинные xml-ки не будут зачитываться
ini_set('memory_limit','-1');

//Данные подключения к БД
$username = "root";
$password="csoft";
$database="test";
$server="127.0.0.1";

//Подключение к БД mysql
$db_handle = mysql_connect($server,$username,$password);
//Переменная удачного подключения
$db_found = mysql_select_db($database,$db_handle);
//Устанавливаем кодировку для сохранения в mySQL
mysql_set_charset("utf8");

//Имена таблицы документов и команд
$documents_table_name="documents";
$commands_table_name="commands";

//Получаем $_REQUEST переменную пользователя, 
//которую нам будет отдавать jScript по Ajax
if (isset($_REQUEST["user"])){
	$user_to_select=urldecode($_REQUEST["user"]);
	
};
//И получаем переменную месяца, также отдаваемую jScript по Ajax
if (isset($_REQUEST["month"])){
	//Если месяц известен, то нужно произвести вычисления начала и конца
	//этого месяца
	//Преобразуем jScript кодированные данные
	$month_to_select=urldecode($_REQUEST["month"]);
	//Начало месяца (добавляем 1е число и время 00:00:00
	//Здесь нужно читать мануал по DateTime:createFormat,
	//чтобы понять что строчка парсится в соотвествие с закодированным 
	//форматом 'd F Y H:i:s'
	//Мы добавляем 1е число, чтобы у нас конечный месяц получался не от 
	//наименьшей даты, а от 1го числа месяца наименьшей даты
	$BeginMonth = 
			DateTime::createFromFormat('d F Y H:i:s',"1 ".
				$month_to_select."00:00:00");
	//Конечная дата месяца получается таким же образом, 
	//но к нему добавляется интервал в 1 месяц.
	$EndMonth = 
			DateTime::createFromFormat('d F Y H:i:s',
				"1 ".$month_to_select."00:00:00")->add(
						new DateInterval('P1M'));
};

//print "DEBUG: init: ".$user_to_select;
//print "DEBUG: init: ".$month_to_select;

?>
