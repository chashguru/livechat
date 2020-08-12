<?php

namespace Comm100\LiveChat\Helper;

class CustomApiResponse
{
    /**
     * @var bool ;
     */
    protected $_success;

    /**
     * @var mixed ;
     */
    protected $_data;

    public function __construct()
    {
        $this->_success = false;
        $this->_data = null;
    }

    public function setApiResponse(bool $success, $data)
    {
        $this->_success = $success;
        $this->_data = $data;
    }

    public function getApiResponse()
    {
        return ['success' => $this->_success, 'data' => $this->_data];
    }
}
