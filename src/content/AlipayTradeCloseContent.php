<?php
namespace Alipay\content;


class AlipayTradeCloseContent extends AlipayContentBase
{

    // 商户订单号.
    private $outTradeNo;

    // 支付宝交易号
    private $tradeNo;
    //卖家端自定义的的操作员 ID
    private $operatorId;


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
    public function getOperatorId()
    {
    	return $this->operatorId;
    }
    
    public function setOperatorId($operatorId)
    {
    	$this->operatorId = $operatorId;
    	$this->bizContentArr['operator_id'] = $operatorId;
    }

}

?>