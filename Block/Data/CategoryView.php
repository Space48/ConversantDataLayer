<?php

namespace Space48\ConversantDataLayer\Block\Data;

use Space48\ConversantDataLayer\Helper\Data as ConversantHelper;

class CategoryView extends \Magento\Framework\View\Element\Template
{
    const TOP_LEVEL_CATEGORY = "toplevel";
    const SUBCATEGORY = "subcategory";

    /**
     * @var Category
     */
    protected $_category = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * ConversantDataLayer Helper
     *
     * @var \Space48\ConversantDataLayer\Helper\Data
     */
    protected $conversantHelper = null;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $categoryRepository;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Registry $registry,
        ConversantHelper $conversantHelper,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->jsonHelper = $jsonHelper;
        $this->conversantHelper = $conversantHelper;
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context, $data);
    }

    public function getCategory()
    {
        if (!$this->_category) {
            $this->_category = $this->_coreRegistry->registry('current_category');
        }
        return $this->_category;
    }

    protected function _toHtml()
    {
        if (!$this->conversantHelper->isEnabled()) {
            return '';
        }

        return $this->getOutput();
    }

    public function getParentCategoryIds()
    {
        $explodedPath = explode("/", $this->getCategory()->getPath());
        $pathArray = array_reverse($explodedPath);
        $pathArrayCount = count($pathArray);

        $categories['department'] = $pathArray[$pathArrayCount-3];
        $categories['parent'] = $pathArray[1];

        return $categories;
    }

    public function getCategoryType()
    {
        return $this->getCategory()->getLevel() <= 2
            ? self::TOP_LEVEL_CATEGORY
            : self::SUBCATEGORY;
    }

    public function getOutput()
    {
        $json = $result = array();

        if ($this->getCategoryType() == self::TOP_LEVEL_CATEGORY) {
            $json['promo_id'] = "3";
            $json['category'] = $this->getCategory()->getName();
        }

        if ($this->getCategoryType() == self::SUBCATEGORY) {
            $parentCategories = $this->getParentCategoryIds();
            $json['promo_id'] = "4";
            $json['sub_category'] = $this->getCategory()->getName();
            $json['category'] = $this->getCategoryById($parentCategories['parent'])->getName();
            $json['department'] = $this->getCategoryById($parentCategories['department'])->getName();
        }

        $result[] = 'dataLayer.push(' . $this->jsonHelper->jsonEncode($json) . ");\n";

        return implode("\n", $result);
    }

    public function getCategoryById($categoryId)
    {
        return $this->categoryRepository->get($categoryId);
    }
}