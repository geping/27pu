<?php
/**
 * 淘宝开放平台数据接口文件
 *
 * @package crontab
 * @version $Id$
 * @copyright 2011 27pu.com
 * @license Commercial
 * =================================================================
 * 版权所有 (C) 2011 27pu.com，并保留所有权利。
 * 网站地址:http://www.27pu.com/
 * -----------------------------------------------------------------
 * 您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * =================================================================
 */
 
class TaobaoOpenClient
{
	public $appkey;

	public $secretKey;

	public $gatewayUrl = "http://gw.api.taobao.com/router/rest";

	public $format = "xml";

    protected $signMethod = "md5";

    protected $apiVersion = "2.0";


	protected function generateSign($params)
	{
		ksort($params);

		$stringToBeSigned = $this->secretKey;
		foreach ($params as $k => $v)
		{
			if("@" != substr($v, 0, 1))
			{
				$stringToBeSigned .= "$k$v";
			}
		}
		unset($k, $v);
		$stringToBeSigned .= $this->secretKey;
		return strtoupper(md5($stringToBeSigned));
	}

	protected function curl($url, $postFields = null)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		if (is_array($postFields) && 0 < count($postFields))
		{
			$postBodyString = "";
			$postMultipart = false;
			foreach ($postFields as $k => $v)
			{
				if("@" != substr($v, 0, 1))//判断是不是文件上传
				{
					$postBodyString .= "$k=" . urlencode($v) . "&"; 
				}
				else//文件上传用multipart/form-data，否则用www-form-urlencoded
				{
					$postMultipart = true;
				}
			}
			unset($k, $v);
			curl_setopt($ch, CURLOPT_POST, true);
			if ($postMultipart)
			{
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
			}
			else
			{
				curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString,0,-1));
			}
		}
		$reponse = curl_exec($ch);
		
		if (curl_errno($ch))
		{
			throw new Exception(curl_error($ch),0);
		}
		else
		{
			$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if (200 !== $httpStatusCode)
			{
				throw new Exception($reponse,$httpStatusCode);
			}
		}
		curl_close($ch);
		return $reponse;
	}
    
    
	protected function logCommunicationError($apiName, $requestUrl, $errorCode, $responseTxt)
	{
        str_replace("\n","",$responseTxt);
        $logName = 'top_comm_err_'.date("Y-m-d").'.log';
        $logData = $apiName."\t".'errorCode:'.$errorCode."\t".'responseTxt:'.$responseTxt;
		$this->ilog($logName, $logData);
	}
    
    protected function logBizError($apiName, $code, $msg, $sub_code='', $sub_msg='')
    {
        str_replace("\n","",$msg);
        $logName = 'top_biz_err_'.date("Y-m-d").'.log';
        $logData = $apiName."\t".'[code] '.$code."\t".'[msg] '.$msg;
        $logData .= '[sub_code] '.$sub_code."\t".'[sub_msg] '.$sub_msg;
        $this->ilog($logName, $logData);
    }
    
    protected function ilog($filename, $str) {

        $path = dirname(__FILE__) . '/logs/';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $filename = dirname(__FILE__) . '/logs/' . $filename;
        $fp = fopen($filename, 'a');
        fwrite($fp, date('Y-m-d H:i:s') . ' : ' . $str . "\n");
        fclose($fp);
    }

	public function execute($method,$params,$session=null)
	{
		//组装系统参数
		$sysParams["app_key"] = $this->appkey;
        $sysParams["v"] = $this->apiVersion;
		$sysParams["format"] = $this->format;
        $sysParams["sign_method"] = $this->signMethod;
		$sysParams["method"] = $method;
        $sysParams["timestamp"] = date("Y-m-d H:i:s");
        if (null != $session){
            $sysParams["session"] = $session;
        }


		//获取业务参数
		$apiParams = $params;

		//签名
		$sysParams["sign"] = $this->generateSign(array_merge($apiParams, $sysParams));

		//系统参数放入GET请求串
		$requestUrl = $this->gatewayUrl . "?";
		foreach ($sysParams as $sysParamKey => $sysParamValue)
		{
			$requestUrl .= "$sysParamKey=" . urlencode($sysParamValue) . "&";
		}
		$requestUrl = substr($requestUrl, 0, -1);
        
		//发起HTTP请求
		try
		{
			$resp = $this->curl($requestUrl, $apiParams);
		}
		catch (Exception $e)
		{
			$this->logCommunicationError($sysParams["method"],$requestUrl,"HTTP_ERROR_" . $e->getCode(),$e->getMessage());
			return false;
		}

		//解析TOP返回结果
		$respWellFormed = false;
		if ("json" == $this->format)
		{
			$respObject = json_decode($resp);
			if (null !== $respObject)
			{
				$respWellFormed = true;
				foreach ($respObject as $propKey => $propValue)
				{
					$respObject = $propValue;
				}
			}
		}
		else if("xml" == $this->format)
		{
			$respObject = @simplexml_load_string($resp);
			if (false !== $respObject)
			{
				$respWellFormed = true;
			}
		}

		//返回的HTTP文本不是标准JSON或者XML，记下错误日志
		if (false === $respWellFormed)
		{
			$this->logCommunicationError($sysParams["method"],$requestUrl,"HTTP_RESPONSE_NOT_WELL_FORMED",$resp);
			return false;
		}

		//如果TOP返回了错误码，记录到业务错误日志中
		if (isset($respObject->code))
		{
            $this->logBizError($sysParams["method"],$respObject->code,$respObject->msg,$respObject->sub_code,$respObject->sub_msg);
            //echo('errcode: '.$respObject->code.', errmsg: '.$respObject->msg);
            //print_r("\n");
            /*
			$logger = new LtLogger;
			$logger->conf["log_file"] = rtrim(TOP_SDK_WORK_DIR, '\\/') . '/' . "logs/top_biz_err_" . $this->appkey . "_" . date("Y-m-d") . ".log";
			$logger->log(array(
				date("Y-m-d H:i:s"),
				$resp
			));
            */
		}
		return $respObject;
	}
}
