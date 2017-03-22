<?php

namespace Space48\ConversantDataLayer\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    const XML_PATH = 's48_gtm_datalayer/conversant/';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context, \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_objectManager = $objectManager;
        parent::__construct($context);
    }

    public function isEnabled() {
        return $this->scopeConfig->isSetFlag(self::XML_PATH."active", \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getConfig($field) {
        return $this->scopeConfig->getValue(self::XML_PATH.$field, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

}