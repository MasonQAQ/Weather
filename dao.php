<?php
require_once("conn.php");
require_once("api.php");
	/*
		获取数据库连接
		param: null
		return: conn
	*/
	function getConn(){
		global $db_host,$db_user,$db_pwd,$db_name;
		//面向对象方式
		$mysqli = new mysqli($db_host, $db_user, $db_pwd, $db_name);
		//面向对象的昂视屏蔽了连接产生的错误，需要通过函数来判断
		if(mysqli_connect_error()){
		    echo mysqli_connect_error();
		}else{
		}
		//设置编码
		$mysqli->set_charset("utf8");//或者 $mysqli->query("set names 'utf8'");
		return $mysqli;
	}

	/*
		数据库查询接口，查询城市列表city
		param: null
		return: array
	*/
	function queryCityList(){
		$mysqli = getConn();
		$sql = "select name from city";
		$stmt = $mysqli->prepare($sql);
		//执行预处理
		$stmt->execute();
		$result = $stmt->get_result();
		$data=$result->fetch_all(MYSQLI_ASSOC); //返回的结果

		$stmt->close();
		$mysqli->close();
		
		return $data;
	}

	/*
		数据库插入接口，将查询的数据保存到数据库中
		param: cityName 城市名
		return: json 是否成功
	*/
	function saveWeather($cityName){
		$apiResJson=json_decode(weatherAPI($cityName));
		$mysqli = getConn();
		//var_dump($apiResJson);
		if ($apiResJson->{'code'}==0) {
			$dateToday=$apiResJson->{'data'}[0]->{'date'};//今日日期
			// echo "today date: $dateToday <br />";
			$weather7String="";
			foreach ($apiResJson->{'data'} as $key => $value) {
				// echo $value->{'date'}."<br />";
				//天气简述：多云 or 晴转多云 问题
				$cond=($value->{'cond'}->{'txt_d'}==$value->{'cond'}->{'txt_n'})?$value->{'cond'}->{'txt_d'}:($value->{'cond'}->{'txt_d'}."转".$value->{'cond'}->{'txt_n'});
				// echo $cond."<br />";
				//最高温度
				$tempMax=$value->{'tmp'}->{'max'};
				//最低温度
				$tempMin=$value->{'tmp'}->{'min'};
				//风力
				$wind=$value->{'wind'}->{'sc'};
				//拼接sql
				$tempString = ",'$cond',$tempMax,$tempMin,'$wind'";
				//echo $tempString;
				$weather7String.=$tempString;
			}
			$insertSQL="INSERT INTO ".$cityName." (date,1_weather,1_maxTemp,1_minTemp,1_wind,2_weather,2_maxTemp,2_minTemp,2_wind,3_weather,3_maxTemp,3_minTemp,3_wind,4_weather,4_maxTemp,4_minTemp,4_wind,5_weather,5_maxTemp,5_minTemp,5_wind,6_weather,6_maxTemp,6_minTemp,6_wind,7_weather,7_maxTemp,7_minTemp,7_wind) VALUES ('$dateToday'".$weather7String.")";
			$createSQL="create table if not exists ".$cityName." (location varchar(32) not null default '".$cityName."',date varchar(32) not null,1_weather varchar(32) not null,1_maxTemp int(8) not null,1_minTemp int(8) not null,1_wind varchar(32) not null,2_weather varchar(32) not null,2_maxTemp int(8) not null,2_minTemp int(8) not null,2_wind varchar(32) not null,3_weather varchar(32) not null,3_maxTemp int(8) not null,3_minTemp int(8) not null,3_wind varchar(32) not null,4_weather varchar(32) not null,4_maxTemp int(8) not null,4_minTemp int(8) not null,4_wind varchar(32) not null,5_weather varchar(32) not null,5_maxTemp int(8) not null,5_minTemp int(8) not null,5_wind varchar(32) not null,6_weather varchar(32) not null,6_maxTemp int(8) not null,6_minTemp int(8) not null,6_wind varchar(32) not null,7_weather varchar(32) not null,7_maxTemp int(8) not null,7_minTemp int(8) not null,7_wind varchar(32) not null,primary key (date)) engine=InnoDB DEFAULT CHARSET=utf8";
			// echo $insertSQL;
			// echo "<br />";
			// echo $createSQL;
			//错误报告
			$result =$mysqli->query($createSQL);
			if($result === false){//创建表执行失败
			   	$errorMsg=date("Y-m-d H:i:s")."    "."创建数据表 $cityName 失败".$mysqli->error."\n";
			    error_log($errorMsg, 3, "errors.log");
			}
			$result =$mysqli->query($insertSQL);
			if($result === false){//插入数据执行失败
			    $errorMsg=date("Y-m-d H:i:s")."    "."插入数据到 $cityName 失败".$mysqli->error."\n";
			    error_log($errorMsg, 3, "errors.log");
			}
		}else{
			# error 
			# the error is handled by function weatherAPI
		}
		$mysqli->close();
	}
