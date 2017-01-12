<?php
	/*
		查询未来七天天气接口
		param: cityName  城市名
		return: json
	*/
	function weatherAPI($cityName)
	{
		global $api_key;

		$ch = curl_init();
    	$url = "http://apis.baidu.com/heweather/weather/free?city=$cityName";
    	$header = array(
        	"apikey: ".$api_key,
    	);
    	// 添加apikey到header
    	curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	// 执行HTTP请求
	    curl_setopt($ch , CURLOPT_URL , $url);
	    $res = curl_exec($ch);
	    $re = curl_getinfo($ch,CURLINFO_HTTP_CODE); 
	    if ($re==200) {
	    	$resJson = json_decode($res);
		    if ($resJson->{"HeWeather data service 3.0"}[0]->{'status'} == "ok") {
		    	return SUCCESS($resJson->{"HeWeather data service 3.0"}[0]->{'daily_forecast'});
		    }else{
		    	$errorMsg=date("Y-m-d H:i:s")."    "."API出错".$resJson->{"HeWeather data service 3.0"}[0]->{'status'}."\n";
				error_log($errorMsg, 3, "errors.log");
		    	return ERROR("API接口出现异常，请求的城市为$cityName");
		    }
	    }else{
	    	//记录错误信息
	    	$errorMsg=date("Y-m-d H:i:s")."    "."API请求出错，城市：$cityName，状态码：$re"."\n";
			error_log($errorMsg, 3, "errors.log");
	    }    
	}

	/*
		错误处理函数
		param: errorMsg 错误信息描述
		return: json 错误信息JSON格式
	*/
	function ERROR($errorMsg)
	{
		$error = new stdClass();
		$error->code = 1;
		$error->data = $errorMsg;
		return json_encode($error);
	}
	
	/*
		成功处理函数
		param: errorMsg 错误信息描述
		return: json 错误信息JSON格式
	*/
	function SUCCESS($data)
	{
		$error = new stdClass();
		$error->code = 0;
		$error->data = $data;
		return json_encode($error);
	}