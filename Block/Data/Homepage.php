<?php

namespace Space48\ConversantDataLayer\Block\Data;

use Magento\Framework\Json\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Space48\ConversantDataLayer\Helper\Data as ConversantHelper;

class Homepage extends Template {

    /**
     * @var Data
     */
    protected $jsonHelper;

    /**
     * ConversantDataLayer Helper
     *
     * @var \Space48\ConversantDataLayer\Helper\Data
     */
    protected $conversantHelper = null;

    /**
     * @param Context          $context
     * @param Data             $jsonHelper
     * @param ConversantHelper $conversantHelper
     * @param array            $data
     *
     */
    public function __construct(
        Context $context,
        Data $jsonHelper,
        ConversantHelper $conversantHelper,
        array $data = []
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->conversantHelper = $conversantHelper;

        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->conversantHelper->isEnabled()) {
            return '';
        }

        return $this->getOutput();
    }

    /**
     * @return string
     */
    private function getOutput()
    {
        $json = $result = array();
        $json['promo_id'] = "1";
        $result[] = 'dataLayer.push(' . $this->jsonHelper->jsonEncode($json) . ");\n";

        return implode("\n", $result);
    }
}
