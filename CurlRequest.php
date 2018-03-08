<?php 

 class CurlRequest {
 	/**
     * 模拟GET请求
     *
     * @param string $url
     * @param array $date
     * @param array $header 请求头 可选参数
     * @return mixed
     */
 	private function curl_get_https($url,$date = array(),$header,$timeout=5){

 		if (is_object($date)) {
            $date = self::objToArr($date);
        }
        if (!empty($date) && (is_array($date) && self::isAssoc($date) && self::isOneDimenArr($date)))
        {
            $queryStr = http_build_query($date);
            $firstChar = (strpos($url, '?') == false) ? '?' : '&';
            $queryStr = $firstChar . $queryStr;

            $charPos = strpos($url, '#');
            if ($charPos !== false) {
                $url = substr_replace($url, $queryStr, $charPos, 0);
            } else {
                $url .= $queryStr;
            }
        } 
    	$curl = curl_init(); // 启动一个CURL会话
    	curl_setopt($curl, CURLOPT_URL, $url);
    	curl_setopt($curl, CURLOPT_HEADER, 0);
    	curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
    	$tmpInfo = json_decode(curl_exec($curl));     //返回请求结果
    	if (curl_errno($curl)) {
   		    echo 'Errno'.curl_error($curl);//捕抓异常
   		}
    	//关闭URL请求
    	curl_close($curl);
    	return $tmpInfo;    //返回结果
	}
	/**
     * 模拟POST请求
     * @param string $url
     * @param array $date
     * @param array $header 请求头 可选参数
     * @return mixed
     */
	private function curl_post_https($url,$date = array(),$header,$timeout=5){
		if (!empty($date)) {
            if (is_array($date) || is_object($date)) {
                $date = json_encode($date, JSON_UNESCAPED_UNICODE);
            } else {
                $date = (string)$date;
            }
        }
   		$curl = curl_init(); // 启动一个CURL会话
   		curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
   		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
   		curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
   		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
   		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
   		curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
   		curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
   		curl_setopt($curl, CURLOPT_POSTFIELDS, $date); // Post提交的数据包
   		curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
   		curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
   		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
   		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout); 
   		$tmpInfo = json_decode(curl_exec($curl)); // 执行操作
   		if (curl_errno($curl)) {
   		    echo 'Errno'.curl_error($curl);//捕抓异常
   		}
   		curl_close($curl); // 关闭CURL会话
   		return $tmpInfo; // 返回数据
	}
	/**
     * 外部调用接口
     * @param string $type 请求类型（POST,GET）此接口只支持POST和GET请求方式
     * @param string $url 
     * @param array $header 请求头 可选参数，必须为数组，key和value与请求头对应
     *        例如：[ 'Content-type' => "application/json;charset='utf-8'" ]
     * @param array $date
     * @return string
     */
	public function Request($type,$url,$date = array(),$header = array()){
		$type = strtolower($type);
		if($type == '$_post' || $type == '$post'){
			$type = 'post';
		}
		if($type == '$_get' || $type == '$post'){
			$type = 'get';
		}
		if (empty($header)) {
            $headerArr = ["Content-type: application/json;charset='utf-8'"];
        } else {
            foreach ($header as $key => $value) {
                $headerArr[] = "$key:$value";
            }
        }
		if($type == 'get'){
			return self::curl_get_https($url,$date,$headerArr);
		} else if($type == 'post'){
			return self::curl_post_https($url,$date,$headerArr);
		} else {
			return '请输入正确的请求方式';
		}
	}
	/**
     * 将对象转换为数组
     * @param object $obj
     * @return array
     */
    public static function objToArr($obj)
    {
        return is_object($obj) ? json_decode(json_encode($obj), true) : [];

    }
    /**
     * 判断数组是否为关联数组，关联数组=true，索引数组=false
     * @param $array
     * @return bool
     */
    public static function isAssoc($array)
    {
        if (is_array($array)) {
            $keys = array_keys($array);
            return $keys !== array_keys($keys);
        }
        return false;
    }
    /**
     * 判断是否一维数组
     * @param $arr
     * @return bool
     */
    public static function isOneDimenArr($arr)
    {
        return count($arr) == count($arr, 1);
    }
 }