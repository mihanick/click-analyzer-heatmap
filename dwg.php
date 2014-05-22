<?php
require "init.php";
/*
 * Этот файл заполняет таблицу документов

 * Пременные $user_to_select, $month_to_select, $BeginMonth, $EndMonth
 * Устанавливаются в init.php, при зачитывании из $_REQUEST
 * 
 * Данные для установки соединения с БД также устанавливаются в init.php

 */

if ($db_found){
	//Простой запрос всех строчек
	$select_query = "SELECT * from ".$documents_table_name;
	
	//Ограничение строчек по выбранному имени пользователя 
	if (isset($user_to_select) AND $user_to_select!="all"){
		$select_query.= ' WHERE (user = "'.
			mysql_escape_string($user_to_select).'")';
		//print "DEBUG:".$select_query;
	};
	//Дополнительное ограничение по датам (месяцам)
	if (isset($month_to_select) AND $month_to_select!="all") {

		if ($BeginMonth!=false AND $EndMonth!=false) {
			if (isset($user_to_select) AND $user_to_select!="all"){
				$select_query.=" AND ";
			} else {
				$select_query.=" WHERE ";
			};
			$select_query.= ' (closed BETWEEN "'. 
				$BeginMonth->format('Y-m-d H:i:s').'"'.
				' AND "'.$EndMonth->format('Y-m-d H:i:s').'")';
			//print "DEBUG:".$select_query;
		};
	};
	//Выполняем запрос
	$rows=mysql_query($select_query);
	//Формируем шапку таблицы
	$out= "<table>";
	$out.= "<tr>";
	$out.= "<td>dwg</td>";
	$out.= "<td>edit-time-seconds</td>";
	$out.= "<td>created</td>";
	$out.= "<td>user</td>";
	$out.= "<td>closed</td>";
	$out.= "</tr>";
	
	//Обрабатываем полученные данные по запросу - формируем html строчку
	while ($row = mysql_fetch_assoc($rows)){

		$out.="<tr>";
		$out.="<td>".$row["dwg"]."</td>";
		$out.="<td>".$row["edit_time_seconds"]."</td>";
		$out.="<td>".$row["created"]."</td>";
		$out.="<td>".$row["user"]."</td>";
		$out.="<td>".$row["closed"]."</td>";
		$out.="</tr>";
	};
	//Закрываем соединение
	mysql_close($db_handle);
	//Закрываем тэг таблицы
	$out.="</table>";
	//Выводим результат
	echo $out;
};

/* expected output something like this:
        <table>
          <tr>
            <td>dwg</td>
            <td>edit-time-seconds</td>
            <td>created</td>
            <td>user</td>
            <td>closed</td>
          </tr>
          <tr>
            <td>Без имени0</td>
          </tr>
          <tr>
            <td>D:\svn\Технологические решения\Административные помещения\Таблицы ТХ по адм зданиям.dwg</td>
          </tr>
          <tr>
            <td>c:\Temp\Без имени0(10-41-06_10.04.2013).autosave</td>
          </tr>
          <tr>
            <td>c:\Temp\Таблицы ТХ по адм зданиям.dwg(10-42-11_10.04.2013).autosave</td>
          </tr>
          <tr>
            <td>D:\svn\Технологические решения\Детский сад на 80 мест.Стадия Р - Standard\Детский сад на 80 мест.Стадия Р.dwg</td>
          </tr>
          <tr>
            <td>c:\Temp\Детский сад на 80 мест.Стадия Р.dwg(11-16-19_11.04.2013).autosave</td>
          </tr>
          <tr>
            <td>C:\Documents and Settings\nikanorov\Рабочий стол\таблица.dwg</td>
          </tr>
        </table>

	 */
?>

