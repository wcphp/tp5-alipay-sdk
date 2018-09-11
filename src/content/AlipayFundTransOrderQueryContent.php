<?php
// +----------------------------------------------------------------------
// | 单笔转账
// +----------------------------------------------------------------------
// | Author: wk <weika@wcphp.com>
// +----------------------------------------------------------------------
namespace Alipay\content;


class AlipayFundTransOrderQueryContent extends AlipayContentBase
{
    //必传参数
    protected $mustContentArr = [

    ];

    protected $bizContentArr = [
        'order_id',//支付宝转账单据号
        'out_biz_no'//商户转账订单号
    ];
    
    public function setOutBizNo($outBizNo)
    {
        $this->bizContentArr['out_biz_no'] = $outBizNo;
    }

    public function setOrderId($orderId)
    {
        $this->bizContentArr['order_id'] = $orderId;
    }

}

?>