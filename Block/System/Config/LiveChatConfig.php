<?php

namespace Comm100\LiveChat\Block\System\Config;

use Comm100\LiveChat\Helper\Constants;
use Comm100\LiveChat\Helper\Methods;
use Comm100\LiveChat\Model\Comm100LiveChatFactory;
use Magento\User\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;
use Magento\Authorization\Model\UserContextInterface;

class LiveChatConfig extends \Magento\Framework\View\Element\Template
{
    /**
     * Path to block template.
     */
    const CHECK_TEMPLATE = 'system/config/LiveChatConfigTemplate.phtml';

    protected $_storeManager;

    /**
     * Comm100LiveChat factory.
     *
     * @var Comm100LiveChatFactory
     */
    protected $_comm100LiveChat;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        UserContextInterface $userContext,
        UserCollectionFactory $userCollectionFactory,
        Comm100LiveChatFactory $comm100LiveChat,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->urlinterface = $context->getUrlBuilder();
        $this->_storeManager = $storeManager;
        $this->_comm100LiveChat = $comm100LiveChat;
        $this->_userContext = $userContext;
        $this->_userCollectionFactory = $userCollectionFactory;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::CHECK_TEMPLATE);
        }

        return $this;
    }
    

    public function getMagentoAppBaseUrl(){

    }

    private function getMagentoAccountEmail()
    {
        $collection = $this->_userCollectionFactory->create();
        $userId = $this->_userContext->getUserId();
        $collection->addFieldToFilter('main_table.user_id', $userId);
        $userData = $collection->getFirstItem();

        return $userData->getData()['email'];
    }

    private function getComm100MagentoDbDetails()
    {
        $comm100LiveChatObj = $this->_comm100LiveChat->create();
        $collection = $comm100LiveChatObj->getCollection();
        $magentoDbDetails = $collection->getFirstItem();
        return $magentoDbDetails;
    }

    public function getDefaultPage()
    {
        $baseUrl = urlencode($this->_storeManager->getStore()->getBaseUrl());
        $magnetoAccountEmail = urlencode($this->getMagentoAccountEmail());
        $comm100LiveChatData = $this->getComm100MagentoDbDetails();
        $consumerKey = urlencode($comm100LiveChatData['ConsumerKey']);
        $consumerSecret = urlencode($comm100LiveChatData['ConsumerSecret']);
        $oAuthVerifier = urlencode($comm100LiveChatData['OAuthVerifier']);
        $magentoAppBaseUrl = $comm100LiveChatData["MagentoAppBaseURL"];

        if($magentoAppBaseUrl == null || $magentoAppBaseUrl == ""){
            $magentoAppBaseUrl = Constants::MAGENTO_APP_BASE_URL;
        }
        return sprintf(
            $magentoAppBaseUrl.Constants::DEFAULT_PAGE,
            $baseUrl,
            $consumerKey,
            $consumerSecret,
            $oAuthVerifier,
            $magnetoAccountEmail
        );
    }
}
