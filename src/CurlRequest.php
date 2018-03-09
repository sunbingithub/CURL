<?php 

namespace sungithub;

 class CurlRequest {

  public $headerArr = ["Content-type: application/json;charset='utf-8'"];
 	/**
   * 
     * 模拟GET请求
     * @param string $url 请求地址
     * @param array $date 请求数据
     * @param array $header 请求头 可选参数，必须为数组，key和value与请求头对应
     *        例如：[ 'Content-type' => "application/json;charset='utf-8'" ]
     * @return mixed 
     */
    public function curl_get($url,$date = array(),$header = array()){
        if (!empty($header)) {
            $headers = array();
            foreach ($header as $key => $value) {
                $headers[] = "$key:$value";
            }
            $this->headerArr = $headers;
        }
        if (!empty($date) && (is_array($date) && count($date) == count($date, 1)))
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
    	curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArr);
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
     * @param array $header 请求头 可选参数，必须为数组，key和value与请求头对应
     *        例如：[ 'Content-type' => "application/json;charset='utf-8'" ]
     * @return mixed
     */
    public function curl_post($url,$date = array(),$header = array()){

      if (!empty($header)) {
          $headers = array();
          foreach ($header as $key => $value) {
            $headers[] = "$key:$value";
          }
          $this->headerArr = $headers;
      }
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
   		curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArr);
   		curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
   		curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
   		curl_setopt($curl, CURLOPT_POSTFIELDS, $date); // Post提交的数据包
   		curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
   		curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
   		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
   		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20); 
   		$tmpInfo = json_decode(curl_exec($curl)); // 执行操作
   		if (curl_errno($curl)) {
   		    echo 'Errno'.curl_error($curl);//捕抓异常
   		}
   		curl_close($curl); // 关闭CURL会话
   		return $tmpInfo; // 返回数据
	}
}