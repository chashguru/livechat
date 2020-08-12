<?php

/**
 * Copyright © Comm100. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Comm100\LiveChat\Plugin;

/**
 * Class CartCustomAttributes.
 */
class CustomerDataCartSection
{
    protected $_cart;
    protected $_customerSession;
    protected $_sessionManager;

    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager
    ) {
        $this->_customerSession = $customerSession;
        $this->_sessionManager = $sessionManager;
        $this->_cart = $cart;
    }

    public function getMagentoCartId()
    {
        return $this->_cart->getQuote()->getId();
    }

    public function getCustomerId()
    {
        $customerId = null;
        if ($this->_customerSession->isLoggedIn()) {
            $customerId = $this->getCustomer()->getId();
        }

        return $customerId;
    }

    public function getCustomer()
    {
        $customer = null;
        if ($this->_customerSession->isLoggedIn()) {
            $customer = $this->_customerSession->getCustomer();
        }

        return $customer;
    }

    /**
     * Add quote id data to result.
     *
     * @param \Magento\Checkout\CustomerData\Cart $subject
     * @param array                               $result
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetSectionData(
        \Magento\Checkout\CustomerData\Cart $subject,
        $result
    ) {
        $result['cartId'] = $this->getMagentoCartId();
        $result['magentoCustomerId'] = $this->getCustomerId();

        return $result;
    }
}
