<?php

namespace Space48\ConversantDataLayer\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper {

    const XML_PATH = 's48_gtm_datalayer/conversant/';


    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isEnabled() {
        return $this->scopeConfig->isSetFlag(self::XML_PATH."active", ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $field
     *
     * @return mixed
     */
    public function getConfig($field) {
        return $this->scopeConfig->getValue(self::XML_PATH.$field, ScopeInterface::SCOPE_STORE);
    }

}
