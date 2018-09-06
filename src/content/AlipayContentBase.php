<?php
namespace Alipay\content;;


class AlipayContentBase
{
    protected $bizContentarr = array();

    protected $bizContent = NULL;

    public function getBizContent()
    {
        if(!empty($this->bizContentarr)){
            $this->bizContent = json_encode($this->bizContentarr,JSON_UNESCAPED_UNICODE);
        }
        return $this->bizContent;
    }
}