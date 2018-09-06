<?php
namespace Alipay;

use Alipay\AlipayTradeService;
use Alipay\aop\AopClient;
use Alipay\aop\request\AlipayCommerceCityfacilitatorDepositQueryRequest;
use Alipay\aop\request\AlipayTradeAppPayRequest;
use Alipay\content\AlipayDataDataserviceBillDownloadurlQueryContent;
use Alipay\content\AlipayTradeAppPayContent;
use Alipay\content\AlipayTradeCloseContent;
use Alipay\content\AlipayTradeFastpayRefundQueryContent;
use Alipay\content\AlipayTradeQueryContent;
use Alipay\content\AlipayTradeRefundContent;
use Alipay\content\AlipayTradeWapPayContent;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/9 0009
 * Time: 上午 9:12
 */
class AopSdk{
    private $config = [];
    private $tradeResponse;

    public function __construct($config='')
    {
         if(is_string($config) && !empty($config = config('alipay.'.$config))){
             $this->config = array_merge($this->config,$config);
         }elseif(is_array($config) && !empty($config)){
             $this->config = array_merge($this->config,$config);
         }

        $this->tradeResponse = new AlipayTradeService($this->config);
    }

    /**
     * app生成订单
     * @access public
     * @param string $subject 订单名称，必填
     * @param string $outTradeNo 商户订单号，商户网站订单系统中唯一订单号，必填
     * @param string $totalAmount 付款金额，必填
     * @param string $notifyPathInfo  支付结果异步回掉路径信息
     * @param string $body 商品描述
     * @param int $timeoutExpress 超时时间
     * @return
     * @throws \Exception
     */
    public function appOrder($subject, $outTradeNo, $totalAmount,$notifyPathInfo, $body="", $timeoutExpress="30m")
    {
        $contentObj = new AlipayTradeAppPayContent();
        $contentObj->setBody($body);
        $contentObj->setSubject($subject);
        $contentObj->setOutTradeNo($outTradeNo);
        $contentObj->setTotalAmount($totalAmount);
        $contentObj->setTimeExpress($timeoutExpress);

        $result=$this->tradeResponse->appOrder($contentObj,$this->config['notify_domain'].$notifyPathInfo);
        return $result;
    }

    /**
     * 手机网站支付接口
     * @param $subject 订单名称，必填
     * @param $outTradeNo 商户订单号，商户网站订单系统中唯一订单号，必填
     * @param $totalAmount 付款金额，必填
     * @param $body 商品描述
     * @param int $timeoutExpress 超时时间
     * @return mixed
     */
    public function wapPay($subject, $outTradeNo, $totalAmount, $body="", $timeoutExpress="5m")
    {
        $payRequestBuilder = new AlipayTradeWapPayContent();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setOutTradeNo($outTradeNo);
        $payRequestBuilder->setTotalAmount($totalAmount);
        $payRequestBuilder->setTimeExpress($timeoutExpress);

        $result=$this->tradeResponse->wapPay($payRequestBuilder,$this->config['return_url'],$this->config['notify_url']);
        return $result;
    }

    /**
     * 交易查询接口
     * @param $tradeNo 支付宝交易号，和商户订单号二选一
     * @param $outTradeNo 商户订单号，和支付宝交易号二选一
     * @return [] 查询结果
     */
    public function query($tradeNo = "", $outTradeNo = "")
    {
        if (empty($tradeNo) && empty($outTradeNo)) {
            return [];
        }
        $RequestBuilder = new AlipayTradeQueryContent();
        $RequestBuilder->setTradeNo($tradeNo);
        $RequestBuilder->setOutTradeNo($outTradeNo);

        $result=$this->tradeResponse->Query($RequestBuilder);
        return $result;
    }

    /**
     * 退款接口
     * @param $tradeNo 支付宝交易号，和商户订单号二选一
     * @param $outTradeNo 商户订单号，和支付宝交易号二选一
     * @param $refundAmount 退款金额，不能大于订单总金额
     * @param $refundReason 退款的原因说明
     * @param $outRequestNo 标识一次退款请求，同一笔交易多次退款需要保证唯一，如需部分退款，则此参数必传。
     * @return [] 退款结果
     */
    public function refund($tradeNo, $outTradeNo, $refundAmount, $refundReason = "", $outRequestNo = "")
    {
        if (empty($tradeNo) && empty($outTradeNo)) {
            return [];
        }
        $RequestBuilder = new AlipayTradeRefundContent();
        $RequestBuilder->setTradeNo($tradeNo);
        $RequestBuilder->setOutTradeNo($outTradeNo);
        $RequestBuilder->setRefundAmount($refundAmount);
        $RequestBuilder->setRefundReason($refundReason);
        $RequestBuilder->setOutRequestNo($outRequestNo);

        $result=$this->tradeResponse->Refund($RequestBuilder);
        return $result;
    }

    /**
     * 退款详情查询接口
     * @param $tradeNo 支付宝交易号，和商户订单号二选一
     * @param $outTradeNo 商户订单号，和支付宝交易号二选一
     * @param $outRequestNo 请求退款接口时，传入的退款请求号，如果在退款请求时未传入，则该值为创建交易时的外部交易号
     * @return [] 查询结果
     */
    public function refundQuery($tradeNo, $outTradeNo, $outRequestNo = "")
    {
        if (empty($tradeNo) && empty($outTradeNo)) {
            return [];
        }
        $RequestBuilder = new AlipayTradeFastpayRefundQueryContent();
        $RequestBuilder->setTradeNo($tradeNo);
        $RequestBuilder->setOutTradeNo($outTradeNo);
        $RequestBuilder->setOutRequestNo($outRequestNo);

        $result = $this->tradeResponse->refundQuery($RequestBuilder);
        return $result;
    }

    /**
     * 关闭交易接口
     * @param $tradeNo 支付宝交易号，和商户订单号二选一
     * @param $outTradeNo 商户订单号，和支付宝交易号二选一
     * @return [] 请求结果
     */
    public function closePay($tradeNo, $outTradeNo)
    {
        if (empty($tradeNo) && empty($outTradeNo)) {
            return [];
        }
        $RequestBuilder = new AlipayTradeCloseContent();
        $RequestBuilder->setTradeNo($tradeNo);
        $RequestBuilder->setOutTradeNo($outTradeNo);

        $result=$this->tradeResponse->Close($RequestBuilder);
        return $result;
    }

    /**
     * 账单下载接口
     * @param $billType trade指商户基于支付宝交易收单的业务账单；signcustomer是指基于商户支付宝余额收入及支出等资金变动的帐务账单；
     * @param $billDate 账单时间：日账单格式为yyyy-MM-dd，月账单格式为yyyy-MM。
     * @return bool|mixed|aop\提交表单HTML文本|\SimpleXMLElement|\SimpleXMLElement[]|string
     */
    public function dataDownload($billType, $billDate)
    {
        $RequestBuilder = new AlipayDataDataserviceBillDownloadurlQueryContent();
        $RequestBuilder->setBillType($billType);
        $RequestBuilder->setBillDate($billDate);

        $result = $this->tradeResponse->downloadurlQuery($RequestBuilder);
        return $result;
    }

    /**
     * 校验签名
     * @param   $post 接收到的post请求参数
     * @return bool
     */
    public function checkSign($post)
    {
        $result = $this->tradeResponse->check($post);
        return $result;
    }

    /**
     * app支付宝授权登陆请求参数字符串
     * @return bool
     */
    public function appOpenAuthStr()
    {
        return $this->tradeResponse->openAuthStr();

    }


}