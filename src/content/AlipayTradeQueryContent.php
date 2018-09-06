<?php
namespace Alipay\content;


class AlipayTradeQueryContent extends AlipayContentBase
{

    // 商户订单号.
    private $outTradeNo;

    // 支付宝交易号
    private $tradeNo;
    


    public function getTradeNo()
    {
        return $this->tradeNo;
    }

    public function setTradeNo($tradeNo)
    {
        $this->tradeNo = $tradeNo;
        $this->bizContentarr['trade_no'] = $tradeNo;
    }

    public function getOutTradeNo()
    {
        return $this->outTradeNo;
    }

    public function setOutTradeNo($outTradeNo)
    {
        $this->outTradeNo = $outTradeNo;
        $this->bizContentarr['out_trade_no'] = $outTradeNo;
    }
}

?>