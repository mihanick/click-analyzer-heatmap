
function DrawHeatMap(){
	//Функция вывода тепловой карты для jScript'а heatmap.js
	//взята из примеров с сайта этого js (см в комментах этого скрипта)
	
	//Перед отрисовкой очищаем данные предыдущей отрисовки
	jQuery("#heatmapArea").html("");
	
	var xx = h337.create({"element":document.getElementById("heatmapArea"), "radius":10, "visible":true});

	var el = document.getElementById("data").value;
	var obj = eval('('+el+')');

	// call the heatmap's store's setDataSet method in order to set static data
	xx.store.setDataSet(obj);
}

window.onload = function(){
	//На загрузке страницы сразу отрисовываем тепловую карту
	DrawHeatMap();
	//И заполняем списки пользователей и месяцев
	getData();
};

function getData(){
	//Функция заполняет комбо-боксы #cmbUsers данными на основе php
	//запроса к users.php, который возвращает для нее json
	jQuery.getJSON('users.php',function(items){
		jQuery.each(items,function(index,value){
			jQuery('#cmbUsers').append('<option>'+value+'</option>');
		});
	});
	
	//Тоже заполняет комбо-бокс #cmbMonths данными json,
	//возвращаемыми months.php
	jQuery.getJSON('months.php',function(items){
		jQuery.each(items,function(index,value){
			jQuery('#cmbMonths').append('<option>'+value+'</option>');
		});
	});
};

function AjaxUpdate(div, urlname){
	//Функия обновляет соответствующий раздел странички div данными
	//из urlname
	
	//Данные для отправки получаются из глобальных переменных usr, month
	adata = {user: usr, month: month};
	//jQuery("#debug").html(adata.month+" "+adata.user);
	jQuery.ajax({		//Выполняем ajax запрос
		url: urlname,
		type:"POST",
		cache: false,
		data: adata,	//С необходимыми данными

		// callback handler that will be called on success
		success: function(html){
			//заполняем необходимы div данными 
			jQuery(div).html(html);
		},
		// callback handler that will be called on error
		error: function( textStatus, errorThrown){
			// log the error to the console
			console.log(
				"The following error occured: "+
				textStatus, errorThrown
			);
		}
	}); 
};


function UpdateData(){
	//Функция обновления данных, случается по событиям изменения
	//в комбо-боксах
	
	//Выполняем обновления данных heatmap, dwg, commands
	AjaxUpdate("#dwg","dwg.php");
	AjaxUpdate("#commands","commands.php");
	AjaxUpdate("#data","data.php");

	//Перерисовываем тепловую карту
	DrawHeatMap();
}


jQuery(document).ready(function(){
	jQuery("#cmbUsers").change(function() { 
		//Если случается изменение в одном или втором комбо-боксе,
		//то назначаем глобальные переменные usr, month 
		//соответствующими значениями, выбранными в этих комбо
		usr=escape(jQuery("#cmbUsers option:selected").text());
		month=escape(jQuery("#cmbMonths option:selected").text());
		
		//И обновляем данные
		UpdateData();
	});
	jQuery("#cmbMonths").change(function() { 
		month=escape(jQuery("#cmbMonths option:selected").text());
		usr=escape(jQuery("#cmbUsers option:selected").text());
		
		UpdateData();
	});
 })
