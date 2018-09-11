<?php
namespace Alipay\content;


class AlipayTradeFastpayRefundQueryContent extends AlipayContentBase
{

    // 商户订单号.
    private $outTradeNo;
    // 支付宝交易号
    private $tradeNo;  
    // 请求退款接口时，传入的退款请求号，如果在退款请求时未传入，则该值为创建交易时的外部交易号
    private $outRequestNo;
    


    public function getTradeNo()
    {
        return $this->tradeNo;
    }

    public function setTradeNo($tradeNo)
    {
        $this->tradeNo = $tradeNo;
        $this->bizContentArr['trade_no'] = $tradeNo;
    }

    public function getOutTradeNo()
    {
        return $this->outTradeNo;
    }

    public function setOutTradeNo($outTradeNo)
    {
        $this->outTradeNo = $outTradeNo;
        $this->bizContentArr['out_trade_no'] = $outTradeNo;
    }
    public function getOutRequestNo()
    {
    	return $this->outRequestNo;
    }
    public function setOutRequestNo($outRequestNo)
    {
    	$this->outRequestNo = $outRequestNo;
    	$this->bizContentArr['out_request_no'] = $outRequestNo;
    }
}

?>