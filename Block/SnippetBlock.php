<?php

namespace Comm100\LiveChat\Block;

use Comm100\LiveChat\Helper\Constants;
use Comm100\LiveChat\Helper\Data;
use Comm100\LiveChat\Model\Comm100LiveChatFactory;
use Comm100\LiveChat\Model\Comm100LiveChatStoreCampaignsFactory;
use Magento\Framework\View\Element\Template;
use Magento\Integration\Model\Oauth\TokenFactory;

class SnippetBlock extends Template
{
    /**
     * @var UrlInterface
     */
    private $urlinterface;

    protected $_customerSession;
    protected $_storeManager;
    protected $_urlInterface;
    protected $_quote;
    protected $_cart;
    protected $_sessionManager;

    /**
     * Comm100LiveChat factory.
     *
     * @var Comm100LiveChatFactory
     */
    protected $_comm100LiveChat;

    /**
     * Comm100LiveChat factory.
     *
     * @var Comm100LiveChatStoreCampaignsFactory
     */
    protected $_comm100LiveChatStoreCampaigns;

    protected $_tokenModelFactory;

    protected $_helperBackend;

    /**
     * Comm100LiveChatFactory factory.
     *
     * @var Comm100LiveChatFactory
     */
    protected $_comm100LiveChatFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        Comm100LiveChatFactory $comm100LiveChat,
        Comm100LiveChatStoreCampaignsFactory $comm100LiveChatStoreCampaigns,
        TokenFactory $tokenModelFactory,
        \Magento\Backend\Helper\Data $helperBackend,
        Comm100LiveChatFactory $comm100LiveChatFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->urlinterface = $context->getUrlBuilder();
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->_quote = $quote;
        $this->_cart = $cart;
        $this->_sessionManager = $sessionManager;
        $this->_comm100LiveChat = $comm100LiveChat;
        $this->_comm100LiveChatStoreCampaigns = $comm100LiveChatStoreCampaigns;
        $this->_tokenModelFactory = $tokenModelFactory;
        $this->_helperBackend = $helperBackend;
        $this->_comm100LiveChatFactory = $comm100LiveChatFactory;
    }

    public function customerIsLoggedIn()
    {
        if ($this->_customerSession->isLoggedIn()) {
            return 'Yes';
        } else {
            return 'No';
        }
    }

    public function getAdminUrl()
    {
        return $this->_helperBackend->getHomePageUrl();
    }

    public function getCustomerId()
    {
        $customerId = 0;
        if ($this->_customerSession->isLoggedIn()) {
            $customerId = $this->getCustomer()->getId();
        }

        return $customerId;
    }

    public function getOrderAddUrl()
    {
        $returnVal = 'null';
        if ($this->_customerSession->isLoggedIn()) {
            $returnVal =
                "'" .
                $this->_helperBackend->getUrl('*/sales_order_create/start/', [
                    'customer_id' => $this->getCustomerId(),
                    'store_id' => $this->getStoreId(),
                ]) .
                "'";
        }

        return $returnVal;
    }

    public function getMagentoCartId()
    {
        return $this->_cart->getQuote()->getId();
    }

    public function getStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * Method to get the customer id and if not available get the vistor id which will be treated as magento customer id;.
     */
    public function getMagentoCustomerId()
    {
        $magentoCustomerId = 0;
        if (!$this->_customerSession->isLoggedIn()) {
            $visitor = $this->_sessionManager->getVisitorData();
            $magentoCustomerId = $visitor['visitor_id'] ?? 0;
        } else {
            $magentoCustomerId = $this->getCustomer()->getId();
        }

        return $magentoCustomerId;
    }

    public function getCustomerEmail()
    {
        $customerId = null;
        if ($this->_customerSession->isLoggedIn()) {
            $customerId = $this->getCustomer()->getEmail();
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

    public function getVisitorId()
    {
        $visitorId = null;
        if (!$this->_customerSession->isLoggedIn()) {
            $visitor = $this->_sessionManager->getVisitorData();

            return $visitor['visitor_id'];
        }

        return $visitorId;
    }

    public function getCustomerToken()
    {
        $customerToken = null;
        if ($this->_customerSession->isLoggedIn()) {
            $customerId = $this->_customerSession->getCustomer()->getId();
            $tokenFactory = $this->_tokenModelFactory->create();
            $customerToken = $tokenFactory
                ->createCustomerToken($customerId)
                ->getToken();
        }

        return "'" . $customerToken . "'";
    }

    public function getCustomVariables()
    {
        return '<div id="' .
            Constants::MAGENTO_CUSTOMER_ID .
            '">' .
            $this->getCustomerId() .
            '</div>';
    }

    private function getComm100MagentoDbDetails()
    {
        $comm100LiveChatObj = $this->_comm100LiveChatFactory->create();
        $collection = $comm100LiveChatObj->getCollection();
        $magentoDbDetails = $collection->getFirstItem();
        return $magentoDbDetails;
    }

    public function getSaveVisitorUrl(){
        $comm100LiveChatData = $this->getComm100MagentoDbDetails();
        $magentoAppBaseUrl = $comm100LiveChatData["MagentoAppBaseURL"];
        if($magentoAppBaseUrl == null || $magentoAppBaseUrl == ""){
            $magentoAppBaseUrl = Constants::MAGENTO_APP_BASE_URL;
        }
        return $magentoAppBaseUrl.Constants::SAVE_VISITOR;
    }

    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    public function getChatBoxData()
    {
        $currentMagentoStoreId = $this->_storeManager->getStore()->getId();

        //Get comm100 site and campaign id from magento db.
        $comm100LiveChatStoreCampaigns = $this->_comm100LiveChatStoreCampaigns->create();
        $collection = $comm100LiveChatStoreCampaigns->getCollection();
        $siteId = null;
        $campaignId = null;

        foreach ($collection as $item) {
            if ($currentMagentoStoreId == $item->getData()['MagentoStoreId']) {
                $siteId = $item->getData()['Comm100SiteID'];
                $campaignId = $item->getData()['Comm100CampaignID'];
            }
        }

        return ['siteId' => $siteId, 'campaignId' => $campaignId];
    }
}
