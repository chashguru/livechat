<?php

namespace Comm100\LiveChat\Model\Observer\Customer;

use Comm100\LiveChat\Model\Comm100LiveChatFactory;
use Comm100\LiveChat\Model\Comm100LiveChatStoreCampaignsFactory;
use Comm100\LiveChat\Model\Observer\WebhookAbstract;
use Magento\Framework\Event\Observer;
use Magento\Framework\HTTP\Adapter\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Payment\Model\Cart;
use Magento\Checkout\Model\Session as CheckoutSession;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 * Class CollectItemsAndAmounts.
 */
class CustomerObserver extends WebhookAbstract
{
    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var Cart
     * */
    protected $_cart;

    /**
     * @var CheckoutSession
     * */
    protected $_checkoutSession;

    protected $_sessionManager;

    protected $_customerSession;

    /**
     * @var Comm100LiveChatStoreCampaignsFactory _$comm100LiveChatStoreCampaignsFactory
     * */
    protected $_comm100LiveChatStoreCampaignsFactory;

    /**
     * @var CollectionFactory
     */
    private $orderCollectionFactory;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        CheckoutSession $checkoutSession,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        Comm100LiveChatFactory $comm100LiveChatFactory,
        CollectionFactory $orderCollectionFactory,
        //Others to be transferesd to the parent class.
        \Psr\Log\LoggerInterface $logger,
        Json $jsonHelper,
        Curl $curlAdapter,
        Comm100LiveChatStoreCampaignsFactory $comm100LiveChatStoreCampaignsFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_sessionManager = $sessionManager;
        $this->_logger = $logger;
        $this->_checkoutSession = $checkoutSession;
        $this->_comm100LiveChatStoreCampaignsFactory = $comm100LiveChatStoreCampaignsFactory;

        return parent::__construct(
            $logger,
            $jsonHelper,
            $curlAdapter,
            $comm100LiveChatFactory,
            $storeManager
        );
    }

    protected function _getWebhookEvent(Observer $observer)
    {
        // return $observer->getEventName();
        $this->_logger->debug(
            'xxx Event fired: ' . $observer->getEvent()->getName()
        );

        return $observer->getEvent()->getName(); // 'payment_cart_collect_items_and_amounts_comm100_webhook_observer';
    }

    public function getVisitorId()
    {
        $visitorId = null;
        if ($this->_customerSession->isLoggedIn() == false) {
            $visitor = $this->_sessionManager->getVisitorData();

            return $visitor['visitor_id'];
        }

        return $visitorId;
    }

    public function getCustomerId()
    {
        $customerId = null;
        if ($this->_customerSession->isLoggedIn()) {
            $customerId = $this->_customerSession->getCustomer()->getId();
        }

        return $customerId;
    }

    /**
     * @return array
     */
    public function getCustomerOrder($customerId)
    {
        $customerOrder = $this->orderCollectionFactory
            ->create()
            ->addFieldToFilter('customer_id', $customerId);

        return $customerOrder->getData();
    }

    protected function _getWebhookData(Observer $observer)
    {
        $visitorId = $this->getVisitorId();

        $customer = $observer->getEvent()->getCustomer();
        $customerId = $this->getCustomerId();
        $orders = $this->getCustomerOrder($customerId);

        $return = [
            'visitorId' => $visitorId,
            'customerId' => $customerId,
            'customer' => $customer,
            'orders' => $orders,
        ];

        return $return;
    }
}
