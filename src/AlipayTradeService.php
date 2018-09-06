<?php
namespace Alipay;

use Alipay\aop\AopClient;
use Alipay\aop\request\AlipayTradeAppPayRequest;
use Alipay\aop\request\AlipayTradeCloseRequest;
use Alipay\aop\request\AlipayTradeFastpayRefundQueryRequest;
use Alipay\aop\request\AlipayTradeRefundRequest;
use Alipay\aop\request\AlipayTradeWapPayRequest;
use Alipay\aop\request\AlipayTradeQueryRequest;
use Alipay\aop\request\AlipayDataDataserviceBillDownloadurlQueryRequest;


class AlipayTradeService {

	//支付宝网关地址
	public $gatewayUrl = "https://openapi.alipay.com/gateway.do";

	//支付宝公钥
	public $alipayPublicKey;

	//商户应用私钥
	public $appPrivateKey;

	//应用id
	public $appId;

    //应用pid
    public $pid;

	//编码格式
	public $charset = "UTF-8";

	//返回数据格式
	public $format = "json";

	//签名方式
	public $signType = "RSA2";

	//是否开启DEBUG
	private $debug;

    public function __construct($alipayConfig)
    {
		$this->gatewayUrl = isset($alipayConfig['gateway_url']) ? $alipayConfig['gateway_url'] : $this->gatewayUrl;
		$this->appId = isset($alipayConfig['app_id']) ? $alipayConfig['app_id'] : $this->appId;
		$this->appPrivateKey = isset($alipayConfig['app_private_key']) ? $alipayConfig['app_private_key'] : $this->appPrivateKey;
		$this->alipayPublicKey = isset($alipayConfig['alipay_public_key']) ? $alipayConfig['alipay_public_key'] : $this->alipayPublicKey;
		$this->charset = isset($alipayConfig['charset']) ? $alipayConfig['charset'] : $this->charset;
		$this->signType = isset($alipayConfig['sign_type']) ? $alipayConfig['sign_type'] : $this->signType;
		$this->debug = isset($alipayConfig['debug']) ? $alipayConfig['debug'] : false;
        $this->pid = isset($alipayConfig['pid']) ? $alipayConfig['pid'] : '';

		if(empty($this->appId)||trim($this->appId)==""){
			throw new \Exception("appId should not be NULL!");
		}
		if(empty($this->appPrivateKey)||trim($this->appPrivateKey)==""){
			throw new \Exception("app_private_key should not be NULL!");
		}
		if(empty($this->alipayPublicKey)||trim($this->alipayPublicKey)==""){
			throw new \Exception("alipay_public_key should not be NULL!");
		}
		if(empty($this->charset)||trim($this->charset)==""){
			throw new \Exception("charset should not be NULL!");
		}
		if(empty($this->gatewayUrl)||trim($this->gatewayUrl)==""){
			throw new \Exception("gateway_url should not be NULL!");
		}

	}

    /**
     * alipay.trade.wap.pay
     * @param $bizContent 业务参数，使用content中的对象生成。
     * @param $notifyUrl 异步通知地址，公网可以访问
     * @return $response 生成用于调用收银台SDK的字符串
     */
    public function appOrder($bizContentObj,$notifyUrl)
    {
        
        $bizContent=$bizContentObj->getBizContent();

        $request = new AlipayTradeAppPayRequest();

        $request->setNotifyUrl($notifyUrl);
        $request->setBizContent ( $bizContent );

        // 首先调用支付api
        $response = $this->aopclientRequestSdkExecute ($request,false);
        return $response;
    }


	/**
	 * alipay.trade.wap.pay
	 * @param $bizContent 业务参数，使用content中的对象生成。
	 * @param $return_url 同步跳转地址，公网可访问
	 * @param $notify_url 异步通知地址，公网可以访问
	 * @return $response 支付宝返回的信息
 	*/
    public function wapPay($bizContentObj,$returnUrl,$notifyUrl)
    {
	
		$biz_content=$bizContentObj->getBizContent();

		$request = new AlipayTradeWapPayRequest();
	
		$request->setNotifyUrl($notify_url);
		$request->setReturnUrl($return_url);
		$request->setBizContent ( $biz_content );
	
		// 首先调用支付api
		$response = $this->aopclientRequestExecute ($request,true);
		// $response = $response->alipay_trade_wap_pay_response;
		return $response;
	}

    private function aopclientRequestExecute($request,$ispage=false)
    {
		$aop = new AopClient();
		$aop->gatewayUrl = $this->gatewayUrl;
		$aop->appId = $this->appId;
		$aop->rsaPrivateKey =  $this->appPrivateKey;
		$aop->alipayrsaPublicKey = $this->alipayPublicKey;
		$aop->apiVersion ="1.0";
		$aop->postCharset = $this->charset;
		$aop->format= $this->format;
		$aop->signType=$this->signType;
		// 开启页面信息输出
		$aop->debugInfo=$this->debug;
		if($ispage)
		{
			$result = $aop->pageExecute($request,"post");
			echo $result;
		}
		else 
		{
			$result = $aop->Execute($request);
		}

		return $result;
	}

    private function aopclientRequestSdkExecute($request,$ispage=false)
    {
        $aop = new AopClient();
        $aop->gatewayUrl = $this->gatewayUrl;
        $aop->appId = $this->appId;
        $aop->rsaPrivateKey =  $this->appPrivateKey;
        $aop->alipayrsaPublicKey = $this->alipayPublicKey;
        $aop->apiVersion ="1.0";
        $aop->postCharset = $this->charset;
        $aop->format= $this->format;
        $aop->signType=$this->signType;

        return $aop->sdkExecute($request);
    }

	/**
	 * alipay.trade.query (统一收单线下交易查询)
	 * @param $bizContentObj 业务参数，使用content中的对象生成。
	 * @return $response 支付宝返回的信息
 	*/
    public function Query($bizContentObj)
    {
		$biz_content=$bizContentObj->getBizContent();
		$request = new AlipayTradeQueryRequest();
		$request->setBizContent ( $biz_content );

		// 首先调用支付api
		$response = $this->aopclientRequestExecute ($request);
		$response = $response->alipay_trade_query_response;
		return json_decode(json_encode($response),true);
	}
	
	/**
	 * alipay.trade.refund (统一收单交易退款接口)
	 * @param $bizContentObj 业务参数，使用content中的对象生成。
	 * @return $response 支付宝返回的信息
	 */
    public function Refund($bizContentObj)
    {
		$biz_content=$bizContentObj->getBizContent();
		$request = new AlipayTradeRefundRequest();
		$request->setBizContent ( $biz_content );
	
		// 首先调用支付api
		$response = $this->aopclientRequestExecute ($request);
		$response = $response->alipay_trade_refund_response;
		return json_decode(json_encode($response),true);
	}

	/**
	 * alipay.trade.close (统一收单交易关闭接口)
	 * @param $bizContentObj 业务参数，使用content中的对象生成。
	 * @return $response 支付宝返回的信息
	 */
    public function Close($bizContentObj)
    {
		$biz_content=$bizContentObj->getBizContent();

		$request = new AlipayTradeCloseRequest();
		$request->setBizContent ( $biz_content );
	
		// 首先调用支付api
		$response = $this->aopclientRequestExecute ($request);
		$response = $response->alipay_trade_close_response;
		return json_decode(json_encode($response),true);
	}
	
	/**
	 * 退款查询   alipay.trade.fastpay.refund.query (统一收单交易退款查询)
	 * @param $bizContentObj 业务参数，使用content中的对象生成。
	 * @return $response 支付宝返回的信息
	 */
    public function refundQuery($bizContentObj)
    {
		$bizContent=$bizContentObj->getBizContent();

		$request = new AlipayTradeFastpayRefundQueryRequest();
		$request->setBizContent ( $bizContent );
	
		// 首先调用支付api
		$response = $this->aopclientRequestExecute ($request);
		return json_decode(json_encode($response->alipay_trade_fastpay_refund_query_response),true);
	}
	/**
	 * alipay.data.dataservice.bill.downloadurl.query (查询对账单下载地址)
	 * @param $bizContentObj 业务参数，使用content中的对象生成。
	 * @return $response 支付宝返回的信息
	 */
    public function downloadurlQuery($bizContentObj)
    {
        $bizContent=$bizContentObj->getBizContent();
		$request = new AlipayDataDataserviceBillDownloadurlQueryRequest();
		$request->setBizContent ( $bizContent );
	
		// 首先调用支付api
		$response = $this->aopclientRequestExecute ($request);
		$response = $response->alipay_data_dataservice_bill_downloadurl_query_response;
		return $response;
	}

	/**
	 * 验签方法
	 * @param $arr 验签支付宝返回的信息，使用支付宝公钥。
	 * @return boolean
	 */
    public function check($arr)
    {
		$aop = new AopClient();
		$aop->alipayrsaPublicKey = $this->alipayPublicKey;
		$result = $aop->rsaCheckV1($arr, $this->alipayPublicKey, $this->signType);
		return $result;
	}

    /**
     * app支付宝授权登陆请求参数字符串
     * @return bool
     */
    public function openAuthStr()
    {
        $aop = new AopClient();

        $aop->appId = $this->appId;
        $aop->pid = $this->pid;
        $aop->rsaPrivateKey =  $this->appPrivateKey;
        $aop->alipayrsaPublicKey = $this->alipayPublicKey;
        $aop->postCharset = $this->charset;
        $aop->format= $this->format;
        $aop->signType=$this->signType;


        return $aop->strExecute([
            'apiname'=>'com.alipay.account.auth',
            'method'=>'alipay.open.auth.sdk.code.get',
            'app_name'=>'mc',
            'biz_type'=>'openservice',
            'product_id'=>'APP_FAST_LOGIN',
            'scope'=>'kuaijie',
            'auth_type'=>'AUTHACCOUNT',
            'target_id'=>uniqid(mt_rand(0,99))
        ]);

    }


}

?>