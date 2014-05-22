<!DOCTYPE html[]>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <link rel="stylesheet" href="log.css" type="text/css">
    <title>Анализ лога команд</title>
  </head>
  <body>
	  <div id="debug"></div>
	<div id="upload">
		<!--Форма загрузки данных лога в скрипт upload.php-->
		<form action="upload.php" method="post"
			enctype="multipart/form-data">
			<label for="file">Импортировать файл лога:</label><br/>
			<input type="file" name="file" id="file"/>
			<input type="submit" name="submit" value="Загрузить в базу"/><br/>
			
			<!--Контолы переключения отображения логов по 
			отдельным пользователям или месяцам
			-->
			<label for="cmbUsers">Пользователи:</label><br/>
			<select id="cmbUsers" ></select><br/>
			<label for="cmbMonths">Месяцы:</label><br/>
			<select id="cmbMonths" ></select>
		</form>
	</div>
	
    <div id="main">
      <h1>Тепловая карта кликов</h1>
      <div id="configArea">
		  <!--Раздел данных для отрисовки тепловой карты-->
        <textarea id="data">
        <?php require 'data.php'; ?>
	</textarea>
      </div>
      <div id="heatmapArea">
		  <!--Раздел для отрисовки самой тепловой карты-->
      </div>
      <div>
        <h1>Открытые файлы</h1>
      </div>
      <div id="dwg">
			<!-- Раздел для отображения списка документов -->
			<?php require 'dwg.php'; ?>
      </div>
      <div>
        <h1>Частота вызова команды</h1>
      </div>
      <div id="commands">
		<!--Раздел для отображения таблицы команд-->
		<?php require 'commands.php'; ?>
      </div>
    </div>
    <!--Скрипт отрисовки теловой карты-->
	<script type="text/javascript" src="heatmap.js"></script>
	<!--jquery - я без него ничего не умею писать на js-->
	<script src="jquery.min.js"></script> 
	<!--Основной скрипт с логикой команд-->
	<script src="main.js"></script>
  </body>
</html>
