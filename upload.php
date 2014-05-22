<?php
/*
 * Файл отвечает за импорт xml файла с логом в базу данных
 * */
 
//Параметры подключения и другие общие переменные
require "init.php";

function ConvertDateToMysqlFormat($DateTimeSource){
	//Вспомогательная функция преобразует формат
	// даты из сохраняемого visual studio
	//в тот, который воспринимает mySQL
	
	//http://stackoverflow.com/questions/2167916/convert-one-date-format-into-another-in-php
	$myDateTime = 
		DateTime::createFromFormat('d.m.Y H:i:s', $DateTimeSource);
	return $myDateTime->format('Y-m-d H:i:s');
};


//Подключение к базе данных 
$db_handle = mysql_connect($server,$username,$password);
$db_found = mysql_select_db($database,$db_handle);

//Исходник
//http://www.w3schools.com/php/php_file_upload.asp

//Проверяем файл что он корректно загружен
if ($_FILES["file"]["error"] > 0){
	//Если что -выводим ошибку
  echo "Error: " . $_FILES["file"]["error"] . "<br>";
} else {
	//Загружаем файл в переменную xml и пишем, что он успешно загружен
	
	//http://stackoverflow.com/questions/8618372/reading-a-passed-xml-file-directly-in-php
	$doc = simplexml_load_file($_FILES['file']['tmp_name']);
	
	print "Файл: ".$_FILES['file']['name']." загружен";
	//print $_FILES['file']['tmp_name'];
};

if ($db_found){
	//Когда удалось подключиться к базе данных
	
	//Получаем все документы dwg, они хранятся в node'ах doc
	// xpath '//doc' выбирает все узлы doc независимо от их положения
	//в структуре
	$dwgnodes = $doc->xpath("//doc");
	//По каждому узлу
	foreach ($dwgnodes as $dwgnode) {
		//Формируем строчку SQL запроса вставки и перечень полей
		$docSQL = "INSERT IGNORE INTO ".$documents_table_name."
			( dwg, created, machine,user,
			platform,system,closed, edit_time_seconds)";
		$docSQL.="VALUES";
	
		//Достаем атрибуты узла
		$attributes=$dwgnode->attributes();
		if ($attributes["dwg"]!=""){
			//Если атрибуты не пустые
			$dwg=mysql_real_escape_string($attributes["dwg"]);
			$created=ConvertDateToMysqlFormat($attributes["created"]);
			$machine=mysql_real_escape_string($attributes["machine"]);
			$user=mysql_real_escape_string($attributes["user"]);
			$platform=mysql_real_escape_string($attributes["platform"]);
			$system=mysql_real_escape_string($attributes["system"]);
			$closed=ConvertDateToMysqlFormat($attributes["closed"]);
			$edit_time_seconds=$attributes["edit-time-seconds"];
			
			//То добавляем в строчку запроса одну строчку с атрибутами
			//данного узла xml
			$docSQL.="(";
				$docSQL.='"'.$dwg.'",';
				$docSQL.='"'.$created.'",';
				$docSQL.='"'.$machine.'",';
				$docSQL.='"'.$user.'",';
				$docSQL.='"'.$platform.'",';
				$docSQL.='"'.$system.'",';
				$docSQL.='"'.$closed.'",';
				$docSQL.='"'.$edit_time_seconds.'"';
			$docSQL.=")";
		};
		
		//Выполняем полученный запрос
		
		//print "DEBUG:".$docSQL;
		$doc_query = mysql_query($docSQL);
		
		//http://php.net/manual/en/function.mysql-insert-id.php
		//Получаем идентификатор добавленного документа
		$doc_id = mysql_insert_id();
		
		//print "DEBUG:".$doc_id;
		//print "DEBUG:".$docSQL;
		if ($doc_id!=0){
			if (count($dwgnode->children())!=0){
				//Если идентификатор документа не пуст, и у узла есть
				//дочерние узлы команд, то их соответственно добавляем
				//в таблицу commands
				
				
				//Достаем все дочерние узлы
				$cmdChildren = $dwgnode->children();
				//Формируем строчку sql запроса
				$cmdSQL = "INSERT IGNORE INTO ".$commands_table_name." 
				(doc_id, name, status, cursor_x, cursor_y,time)
				";
				$cmdSQL.="VALUES";
				
				//Счетчик нужен для того чтобы в последнем узле не
				//добавляь запятую в VALUES
				$j=0;
				//Общее число команд
				$cmdcount = count($cmdChildren);
				//По каждой команде
				foreach ($cmdChildren as $cmdNode){
					//Достаем ее атрибуты
					$cmdattributes = $cmdNode->attributes();
					
					$cmd_doc_id=$doc_id;
					$cmdName=$cmdattributes["name"];
					$cmdStatus=$cmdattributes["status"];
					$cursor_x=$cmdattributes["cursor-x"];
					$cursor_y=$cmdattributes["cursor-y"];
					$cmdTime=ConvertDateToMysqlFormat(
						$cmdattributes["time"]);
					
					//Формируем сточку данных
					$cmdSQL.="(";
						$cmdSQL.='"'.$cmd_doc_id.'",';
						$cmdSQL.='"'.$cmdName.'",';
						$cmdSQL.='"'.$cmdStatus.'",';
						$cmdSQL.='"'.$cursor_x.'",';
						$cmdSQL.='"'.$cursor_y.'",';
						$cmdSQL.='"'.$cmdTime.'"';
					$cmdSQL.=")";
					
					//Если строчка не последняя. то добавляем запятую
					if ($j<$cmdcount-1) $cmdSQL.=",";
					$j++;
				};
				//Добавить команды текущего документа
				//Выполняем запрос
				$cmd_query = mysql_query($cmdSQL);
			}
		};
	};
//Закрываем подключение
mysql_close($db_handle);
}
else{
	//В противном случае выдаем ошибку подключения к БД
	print "database not found";
	mysql_close($db_handle);
};

?>
