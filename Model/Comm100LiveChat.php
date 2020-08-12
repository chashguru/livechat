<?php

namespace Comm100\LiveChat\Model;

use Comm100\LiveChat\Helper\Constants;

class Comm100LiveChat extends \Magento\Framework\Model\AbstractModel implements
    \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = Constants::PARENT_TABLE_NAME;

    protected $_cacheTag = Constants::PARENT_TABLE_NAME;

    protected $_eventPrefix = Constants::PARENT_TABLE_NAME;

    protected function _construct()
    {
        $this->_init('Comm100\LiveChat\Model\ResourceModel\Comm100LiveChat');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
