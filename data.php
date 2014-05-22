<?php

/*
 * Этот файл заполняет раздел данных
 * для создания тепловой карты мыши
 * с помощью скрипта heatmap.js
 * Данные отображаются в textarea #data
 * Пременные $user_to_select, $month_to_select, $BeginMonth, $EndMonth
 * Устанавливаются в init.php, при зачитывании из $_REQUEST
 * 
 * Данные для установки соединения с БД также устанавливаются в init.php
 * 
 * 
  Sample data output:
  {max:80,data:[{x: 0, y: 20, count: 1},
  {x: 0, y: 30, count: 1},
  {x: 0, y: 90, count: 1}}
  
 */
 
require "init.php";
////Подключение к БД mysql
////$db_handle = mysql_connect($server,$username,$password);
////$db_found = mysql_select_db($database,$db_handle); //Если найдена БД

if ($db_found){
	//Если база найдена, то создаем строчку SQL запроса
	$select_query = "SELECT * from ".$commands_table_name;
	//Если задан пользователь, то уточняем SQL запрос - выбираем
	//только строчки, с таким пользователем
	//Выбранное значение 'all' означает также, что дополнительной отборки
	//по пользователям не будет
	
	//Поскольку пользователи хранятся в таблице documents, то нужно 
	//выбрать такие записи, в которых выполнено условие
	//commands!doc_id = documents!id(где documents!user=$user_to_select)
	if (isset($user_to_select) AND $user_to_select!="all"){
			$select_query.=' WHERE doc_id IN';
			$select_query.=' (SELECT id FROM '.$documents_table_name;
			$select_query.= ' WHERE user = "'.
				mysql_escape_string($user_to_select).'")';
			//print "DEBUG:".$select_query;
		};
	//Тоже самое по месяцам - если он задан, то выбираем
	//из базы только те записи, которые находятся в промежутке между
	//$BeginMonth и $EndMonth
	if (isset($month_to_select) AND $month_to_select!="all") {
		//Дополнительно проверяем - если задано условие по пользователю,
		//то условие по месяцам будет AND, в противном случае условие
		//по месяцам будет обычным WHERE
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
	
	//Переменная вывода
	$Output="";

	//Массив плиток 10х10 пикселей
	$tiles = array(array(),array());
	//Заполняем массив нулями (считаем макс. разрешение 1920х1080
	for ($ii=0;$ii<192;$ii++){
		for ($jj=0;$jj<108;$jj++){
			$tiles[$ii][$jj]=0;
		}
	};
	
	//Пробегаем по полученным данным запроса, последовательно зачитывая
	//строчки
	while ($row = mysql_fetch_assoc($rows)){
		//Достаем поля имя команды и положения курсора
		$cmdName=$row["name"];
		$cursor_x=$row["cursor_x"];
		$cursor_y=$row["cursor_y"];
		
		//Округляем координаты до 10 пикселей
		$ii = (int)($cursor_x/10); 
		$jj = (int)($cursor_y/10);
		
		//Увеличиваем число попаданий мыши в данную область
		$tiles[$ii][$jj]++;
	};
	
	//закрываем запрос
	mysql_close($db_handle);
	
	//Находим максимальное значение в массиве плиток попаданий
	$max_count = $tiles[1][1];
	for ($ii=0;$ii<192;$ii++){
		for ($jj=0;$jj<108;$jj++){
			if ($tiles[$ii][$jj]>=$max_count) 
				$max_count=$tiles[$ii][$jj];
		}
	};

	//Присваиваем максимальное значение в вывод
	$Output.="{max:".$max_count.",data:[";

	//Записываем в вывод данные в необходимом формате х,у, count
	for ($ii=0;$ii<192;$ii++){
		for ($jj=0;$jj<108;$jj++){
			if ($tiles[$ii][$jj]!=0){
				$Output.="{x: ".($ii*10);
				$Output.=", y: ".($jj*10);
				$Output.=", count: ".$tiles[$ii][$jj];
				$Output.="},";
			}
		}
	}	
	//Закрываем скобки
	$Output.="]}";
	//Выводим полученное
	echo $Output;
};
?>
