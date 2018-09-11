<?php
namespace Alipay\content;;


class AlipayContentBase
{
    //必传参数
    protected $mustContentArr = [];

    protected $bizContentArr = [];

    protected $bizContent = NULL;


    public function getBizContent()
    {
        if(!empty($this->bizContentArr)){


            $this->bizContent = json_encode(array_merge($this->mustContentArr,array_filter($this->bizContentArr)),JSON_UNESCAPED_UNICODE);
        }
        return $this->bizContent;
    }
}