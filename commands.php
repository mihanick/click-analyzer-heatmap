<?php
require "init.php";
/*
 * Этот файл заполняет таблицу команд и частоты их использования

 * Пременные $user_to_select, $month_to_select, $BeginMonth, $EndMonth
 * Устанавливаются в init.php, при зачитывании из $_REQUEST
 * 
 * Данные для установки соединения с БД также устанавливаются в init.php

 */

//Шапка таблицы
$out = "<table>
	<tr>
	<td>Command</td>
	<td>Count</td>
	</tr>";

//Массив команд и их частот - пока пустой
$cmds=array();
if ($db_found){
	//Если база найдена, то создаем строчку SQL запроса
	$select_query = "SELECT * from ".$commands_table_name;
	//Дополнительно ограничиваем выборку по пользователям (см. data.php)
	if (isset($user_to_select))
		if ($user_to_select!="all"){
			$select_query.=' WHERE (doc_id IN';
			$select_query.=' (SELECT id FROM '.$documents_table_name;
			$select_query.= ' WHERE user = "'.
				mysql_escape_string($user_to_select).'"))';
			//print "DEBUG:".$select_query;
		};
	//Ограничиваем выборку по месяцам
	if (isset($month_to_select) AND $month_to_select!="all") {
		if ($BeginMonth!=false AND $EndMonth!=false) {
			if (isset($user_to_select) AND $user_to_select!="all"){
				$select_query.=" AND ";
			} else {
				$select_query.=" WHERE ";
			};
			$select_query.= ' (time BETWEEN "'. 
				$BeginMonth->format('Y-m-d H:i:s').'"'.
				' AND "'.$EndMonth->format('Y-m-d H:i:s').'")';
			//print "DEBUG:".$select_query;
		};
	};
	/*
	 Пример SQL запроса:
		SELECT * from commands 
		 WHERE doc_id IN 
		 	(
				SELECT id FROM documents 
				WHERE 
					user = "MCAD\\mihanick"
			) 
		 	AND  
		 	(
				time 
				 BETWEEN "2012-04-01 00:00:00" 
				 AND "2012-05-01 00:00:00"
			)
	 */
	 
	 //Выполняем запрос
	$rows=mysql_query($select_query);
	
	//Пробегаемся по результатам запроса
	while ($row = mysql_fetch_assoc($rows)){
		$commandName= (string)$row["name"];
		if (!empty($commandName)){ //Если что-то есть в запросе
			if (!empty($cmds[$commandName]))
				//Если команда уже есть в массиве команд, то увеличиваем
				//количество ее использований
				$cmds[$commandName]++;
			else
				//В противном случае назначаем количество = 1
				$cmds[$commandName]=1;
		};
	};
	//Закрываем подключение
	mysql_close($db_handle);
	//Сортируем массив в обратном порядке, т.к. нам интересно узнать
	//какие наиболее востребованные команды
	arsort($cmds);
	
	//Заполняем html таблицу - одна строчка - одна команда и ее частота
	foreach ($cmds as $key => $value){
		$out.="<tr>";
		$out.="<td>".$key."</td>";
		$out.="<td>".$value."</td>";
		$out.="</tr>";
	}
};

$out.="</table>";
//Выводим результат
echo $out;

/* Sample output
        <table>
          <tr>
            <td>command</td>
            <td>count</td>
          </tr>
          <tr>
            <td>grip_stretch</td>
            <td>210</td>
          </tr>
          <tr>
            <td>Paste</td>
            <td>194</td>
          </tr>
          <tr>
            <td>dedit</td>
            <td>190</td>
          </tr>
        </table>
*/
?>
