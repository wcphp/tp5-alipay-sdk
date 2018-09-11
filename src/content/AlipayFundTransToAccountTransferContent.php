<?php
// +----------------------------------------------------------------------
// | 单笔转账
// +----------------------------------------------------------------------
// | Author: wk <weika@wcphp.com>
// +----------------------------------------------------------------------
namespace Alipay\content;


class AlipayFundTransToAccountTransferContent extends AlipayContentBase
{
    //必传参数
    protected $mustContentArr = [
        'out_biz_no'=>'',
        'payee_type'=>'',
        'payee_account'=>'',
        'amount'=>'',
    ];

    protected $bizContentArr = [
        'out_biz_no'=>'',//商户订单号
        'payee_type'=>'',//收款方账户类型。可取值：1、ALIPAY_USERID：支付宝账号对应的支付宝唯一用户号2、ALIPAY_LOGONID：支付宝登录号，支持邮箱和手机号格式。
        'payee_account'=>'',//收款方账户
        'amount'=>'',//转账金额，单位：元
        'payer_show_name'=>'',//付款方姓名
        'payee_real_name'=>'',//收款方真实姓名，存在支付宝就校验不存在不校验
        'remark'=>'',//转账备注
    ];


    public function setOutBizNo($outBizNo)
    {
        $this->bizContentArr['out_biz_no'] = $outBizNo;
    }

    public function setPayeeType($payeeType)
    {
        $this->bizContentArr['payee_type'] = $payeeType;
    }
    public function setPayeeAccount($payeeAccount)
    {
        $this->bizContentArr['payee_account'] = $payeeAccount;
    }
    public function setAmount($amount)
    {
        $this->bizContentArr['amount'] = $amount;
    }
    public function setPayerShowName($payerShowName)
    {
        $this->bizContentArr['payer_show_name'] = $payerShowName;
    }
    public function setPayeeRealName($payeeRealName)
    {
        $this->bizContentArr['payee_real_name'] = $payeeRealName;
    }
    public function setRemark($remark)
    {
        $this->bizContentArr['remark'] = $remark;
    }
}

?>