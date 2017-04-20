<?php
class wechat_control extends control{

	public function _initialize() {
		parent::_initialize();
			$_setting = model('notify/notify','service')->get_fech_all();
			$this->config = $_setting['wechat']['configs'];
		}

	public function index(){
		if(!isset($_GET['echostr'])){
			$this->responseMsg();
		}else{
			$options = array(
					'token'=>$this->config['token'], //填写你设定的key
					'appid'=>$this->config['appid'],
					'appsecret'=>$this->config['appsecret'], //填写高级调用功能的密钥
					'encodingaeskey'=>$this->config['encodingaeskey'], //填写加密用的EncodingAESKey
				);
			$weObj = new we($options);
			header('Content-type:text');
			ob_clean();
			echo $weObj->valid($_GET);
		}
	}

	//接收事件推送并回复
	public function responseMsg(){
		//获得到微信推送过来的post数据（xml格式）
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		if (!empty($postStr)){
			//日志记录
			$this->logger("R ".$postStr);
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);  //把 XML 字符串载入对象中
			$RX_TYPE = trim($postObj->MsgType);
			//消息类型分离
			switch ($RX_TYPE)
			{
				case "event":
					$result = $this->receiveEvent($postObj);
					break;
				case "text":
					$result = $this->receiveText($postObj);
					break;
				case "image":
					$result = "unknown msg type: ".$RX_TYPE;
					break;
				case "location":
					$result = "unknown msg type: ".$RX_TYPE;
					break;
				case "voice":
					$result = "unknown msg type: ".$RX_TYPE;
					break;
				case "video":
					$result = "unknown msg type: ".$RX_TYPE;
					break;
				case "link":
					$result = "unknown msg type: ".$RX_TYPE;
					break;
				default:
					$result = "unknown msg type: ".$RX_TYPE;
					break;
				}
				$this->logger("T ".$result);
				ob_clean();
				echo $result;
				exit;
			}else {
			echo "";
			exit;
		}
	}

	//接收事件消息
	private function receiveEvent($object)
	{
		$setting = model('admin/setting','service')->get();
		$content = "";
		switch ($object->Event)
		{
			case "subscribe":
				$openid = $object->FromUserName;
				$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
				$url = $protocol.$_SERVER['HTTP_HOST'];
				$url = $url.'/index.php?m=member&c=public&a=wechat_bind&openid='.$openid;
				$content = "欢迎关注".$setting['site_name']."商城公众号，"."<a href=\"$url\">立即点去绑定</a>"."，没有绑定的输入“立即绑定”执行去绑定，"."已经绑定的输入“解除绑定”可以解除绑定。";
				break;
			case "unsubscribe":
				$openid = "$object->FromUserName";
				$this->cancel_weixin($openid);
				$content = "取消关注";
				break;
			default:
				$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
				$url = $protocol.$_SERVER['HTTP_HOST'];
				$url = $url.'/index.php?m=member&c=public&a=wechat_bind&openid='.$openid;
				$content ="<a href=\"$url\">点击去绑定</a>";
				$content = "欢迎关注".$setting['site_name']."商城公众号，"."<a href=\"$url\">立即点去绑定</a>"."，没有绑定的输入“立即绑定”执行去绑定，"."已经绑定的输入“解除绑定”可以解除绑定。";
				break;
			}

			if(is_array($content)){
				if (isset($content[0]['PicUrl'])){
					$result = $this->transmitNews($object, $content);
				}else if (isset($content['MusicUrl'])){
					$result = $this->transmitMusic($object, $content);
				}
			}else{
			$result = $this->transmitText($object, $content);
		}
		return $result;
	}

	//接收文本消息
	private function receiveText($object)
	{
		$keyword = trim($object->Content);
		//多客服人工回复模式
		if (strstr($keyword, "您好") || strstr($keyword, "你好") || strstr($keyword, "在吗")){
			$result = $this->transmitService($object);
		}
		//自动回复模式
		else{
			if(strstr($keyword, "立即绑定")){
				$openid = $object->FromUserName;
				$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
				$url = $protocol.$_SERVER['HTTP_HOST'];
				$url = $url.'/index.php?m=member&c=public&a=wechat_bind&openid='.$openid;
				$content ="<a href=\"$url\">点击去绑定</a>";
			}else if(strstr($keyword, "解除绑定")){
				$openid = "$object->FromUserName";
				if($this->cancel_weixin($openid)){
					$content = "解除成功";
				}else{
					$content = "解除失败";
				}
			}else{
			
			}
			if(is_array($content)){
				if (isset($content[0]['PicUrl'])){
					$result = $this->transmitNews($object, $content);
				}else if (isset($content['MusicUrl'])){
					$result = $this->transmitMusic($object, $content);
				}
			}else{
				$result = $this->transmitText($object, $content);
			}
		}

		return $result;
	}


	//回复文本消息
	private function transmitText($object, $content)
	{
		$xmlTpl = "<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[text]]></MsgType>
			<Content><![CDATA[%s]]></Content>
			</xml>";
		$result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
		return $result;
	}


	/**
	* 请求接口返回内容
	* @param  string $url [请求的URL地址]
	* @param  string $params [请求的参数]
	* @param  int $ipost [是否采用POST形式]
	* @return  string
	*/
	public function juhecurl($url,$params=false,$ispost=0){
		$httpInfo = array();
		$ch = curl_init();

		curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
		curl_setopt( $ch, CURLOPT_USERAGENT , 'JuheData' );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 60 );
		curl_setopt( $ch, CURLOPT_TIMEOUT , 60);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		if( $ispost )
		{
			curl_setopt( $ch , CURLOPT_POST , true );
			curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
			curl_setopt( $ch , CURLOPT_URL , $url );
		}
		else
		{
			if($params){
				curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
			}else{
				curl_setopt( $ch , CURLOPT_URL , $url);
			}
		}
		$response = curl_exec( $ch );
		if ($response === FALSE) {
			//echo "cURL Error: " . curl_error($ch);
			return false;
		}
		$httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
		$httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
		curl_close( $ch );
		return $response;
	}


	//日志记录
	public function logger($log_content)
	{
		if(isset($_SERVER['HTTP_APPNAME'])){   //SAE
			sae_set_display_errors(false);
			sae_debug($log_content);
			sae_set_display_errors(true);
		}else if($_SERVER['REMOTE_ADDR'] != "127.0.0.1"){ //LOCAL
			$max_size = 10000;
			$log_filename = "log.xml";
			if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
			file_put_contents($log_filename, date('H:i:s')." ".$log_content."\r\n", FILE_APPEND);
		}
	}
	

	//取消关注
	public function cancel_weixin($openid){
		return	model('member/member','service')->cancel_weixin($openid);
	}

}