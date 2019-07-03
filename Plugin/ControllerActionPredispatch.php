<?php

namespace Space48\ConversantDataLayer\Plugin\Plugin;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Space48\GtmDataLayer\Observer\Frontend\ControllerActionPredispatchObserver;

/**
 * Class ControllerActionPredispatch
 * @package Space48\ConversantDataLayer\Plugin\Plugin
 */
class ControllerActionPredispatch
{
    /**
     * @var CurrentCustomer
     */
    private $currentCustomer;

    /**
     * ControllerActionPredispatch constructor.
     * @param CurrentCustomer $currentCustomer
     */
    public function __construct(
        CurrentCustomer $currentCustomer
    ) {
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * @param ControllerActionPredispatchObserver $subject
     */
    public function beforeGetCustomerSession(ControllerActionPredispatchObserver $subject)
    {
        $customer = $this->currentCustomer;
        if ($customer->getCustomerId() && $customer->getCustomer()->getEmail()) {
            $email = $customer->getCustomer()->getEmail();
            $data = hash('sha256', strtolower(trim($email)));
            $subject->setCustomerSession('conversant_customer_id', $data);
        }
    }
}