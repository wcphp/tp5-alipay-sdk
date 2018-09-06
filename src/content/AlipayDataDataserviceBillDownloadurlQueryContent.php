<?php
namespace Alipay\content;

class AlipayDataDataserviceBillDownloadurlQueryContent extends AlipayContentBase
{

    // 账单类型
    private $billType;

    // 	账单时间
    private $billDate;

    public function getBillType()
    {
        return $this->billType;
    }

    public function setBillType($billType)
    {
        $this->billType = $billType;
        $this->bizContentarr['bill_type'] = $billType;
    }

    public function getBillDate()
    {
        return $this->billDate;
    }

    public function setBillDate($billDate)
    {
        $this->billDate = $billDate;
        $this->bizContentarr['bill_date'] = $billDate;
    }
}

?>