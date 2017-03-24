<?php
namespace Space48\ConversantDataLayer\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultJsonFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }
    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            return $result->setData($this->customerSession->getCustomer()->getId());
        }
    }
}