<?php
namespace Alipay;

use Alipay\AlipayTradeService;
use Alipay\aop\AopClient;
use Alipay\aop\request\AlipayCommerceCityfacilitatorDepositQueryRequest;
use Alipay\aop\request\AlipayTradeAppPayRequest;
use Alipay\content\AlipayDataDataserviceBillDownloadurlQueryContent;
use Alipay\content\AlipayFundTransOrderQueryContent;
use Alipay\content\AlipayFundTransToAccountTransferContent;
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
        $requestBuilder = new AlipayTradeQueryContent();
        $requestBuilder->setTradeNo($tradeNo);
        $requestBuilder->setOutTradeNo($outTradeNo);

        $result=$this->tradeResponse->Query($requestBuilder);
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
        $requestBuilder = new AlipayTradeRefundContent();
        $requestBuilder->setTradeNo($tradeNo);
        $requestBuilder->setOutTradeNo($outTradeNo);
        $requestBuilder->setRefundAmount($refundAmount);
        $requestBuilder->setRefundReason($refundReason);
        $requestBuilder->setOutRequestNo($outRequestNo);

        $result=$this->tradeResponse->Refund($requestBuilder);
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
        $requestBuilder = new AlipayTradeFastpayRefundQueryContent();
        $requestBuilder->setTradeNo($tradeNo);
        $requestBuilder->setOutTradeNo($outTradeNo);
        $requestBuilder->setOutRequestNo($outRequestNo);

        $result = $this->tradeResponse->refundQuery($requestBuilder);
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
        $requestBuilder = new AlipayTradeCloseContent();
        $requestBuilder->setTradeNo($tradeNo);
        $requestBuilder->setOutTradeNo($outTradeNo);

        $result=$this->tradeResponse->Close($requestBuilder);
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
        $requestBuilder = new AlipayDataDataserviceBillDownloadurlQueryContent();
        $requestBuilder->setBillType($billType);
        $requestBuilder->setBillDate($billDate);

        $result = $this->tradeResponse->downloadurlQuery($requestBuilder);
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


    /**
     * 单笔转账到支付宝账户
     * @param string $tradeNo 商家支付交易号
     * @param string $amount  支付金额（分）
     * @param string $payeeAccount 收款账户
     * @param string $remark  转账备注
     * @param string $payerShowName 付款方姓名
     * @param string $userType 收款方账户类型：1、ALIPAY_USERID：支付宝账号对应的支付宝唯一 2、ALIPAY_LOGONID：支付宝登录号，支持邮箱和手机号格式
     * @param string $payeeRealName 收款方真实姓名 不为空则会校验该账户在支付宝登记的实名是否与收款方真实姓名一致
     * @return array
     */
    public function  singleToAccountTransfer($tradeNo, $amount,$payeeAccount,$remark='',$payerShowName='',$userType='ALIPAY_USERID', $payeeRealName='')
    {
        $requestBuilder = new AlipayFundTransToAccountTransferContent();
        $requestBuilder->setOutBizNo($tradeNo);
        $requestBuilder->setAmount($amount);
        $requestBuilder->setPayeeAccount($payeeAccount);
        $requestBuilder->setRemark($remark);
        $requestBuilder->setPayerShowName($payerShowName);
        $requestBuilder->setPayeeType($userType);
        $requestBuilder->setPayeeRealName($payeeRealName);

        return $this->tradeResponse->singleToAccount($requestBuilder);
    }

    /**
     * 单笔转账到支付宝账户查询
     * @param string $tradeNo 商家支付交易号
     * @param string $orderId 支付宝交易号
     * @return array
     */
    public function  singlePayQuery($tradeNo, $outTradeNo='')
    {
        $requestBuilder = new AlipayFundTransOrderQueryContent();
        $requestBuilder->setOutBizNo($tradeNo);
        $requestBuilder->setOrderId($outTradeNo);

        return $this->tradeResponse->singlePayQuery($requestBuilder);
    }


}