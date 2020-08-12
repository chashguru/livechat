<?php

namespace Comm100\LiveChat\Helper;

use Comm100\LiveChat\Model\Comm100LiveChatFactory;
use Comm100\LiveChat\Model\ResourceModel\Comm100LiveChat;
use Magento\Framework\App\Bootstrap;

class Methods
{

    // /**
    //  * Comm100LiveChatFactory factory.
    //  *
    //  * @var Comm100LiveChatFactory
    //  */
    // protected $_comm100LiveChatFactory;

    // public function __construct(
    //     Comm100LiveChatFactory $comm100LiveChatFactory
    // ) {
    //     $this->_comm100LiveChatFactory = $comm100LiveChatFactory;
    // }

    public static function   getMagentoAppBaseUrl()
    {
        $bootstrap = Bootstrap::create(BP, $_SERVER);
        $objectManager = $bootstrap->getObjectManager();
        
        $comm100LiveChatFactory = $objectManager->get('Comm100\LiveChat\Model\Comm100LiveChatFactory');
        $comm100LiveChatFactoryParent =  $comm100LiveChatFactory->create();
        // $comm100LiveChatFactoryParent = $this->_comm100LiveChatFactory->create();
        $item = $comm100LiveChatFactoryParent->getCollection()->getFirstItem();
        $magentoAppBaseUrl = $item->getData()['MagentoAppBaseURL'];
        if ($magentoAppBaseUrl != null  && $magentoAppBaseUrl != "") {
            return $magentoAppBaseUrl;
        } else {
            return Constants::MAGENTO_APP_BASE_URL;
        }
    }

    public static function getMagentoAppWebhookUrl(){
        return Methods::getMagentoAppBaseUrl().Constants::WEBHOOK;
    }
    public static function getDefaultPageUrl(){
        return Methods::getMagentoAppBaseUrl().Constants::DEFAULT_PAGE;
    }
    public static function getUninstallUrl(){
        return Methods::getMagentoAppBaseUrl().Constants::UNINSTALL;
    }
    public static function getSaveVisitorUrl(){
        return Methods::getMagentoAppBaseUrl().Constants::SAVE_VISITOR;
    }
}
