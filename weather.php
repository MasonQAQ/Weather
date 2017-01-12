<?php
	require_once("dao.php");
	$data=queryCityList();
	foreach ($data as $key=>$value) {
		saveWeather($value['name']);
		sleep(2);//防止API接口报警
	}
?>