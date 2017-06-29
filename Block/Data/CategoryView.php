<?php

namespace Space48\ConversantDataLayer\Block\Data;

use Space48\ConversantDataLayer\Helper\Data as ConversantHelper;

class CategoryView extends \Magento\Framework\View\Element\Template
{
    const DEPARTMENT = 2;
    const CATEGORY = 3;
    const SUBCATEGORY = 4;

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

    /**
     * @var Integer
     */
    protected $categoryLevel;

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
        $this->categoryLevel = $this->getCategory()->getLevel();

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

    public function getOutput()
    {
        $json = $result = array();
        $parentCategories = $this->getParentCategoryIds();

        if ($this->categoryLevel <= self::DEPARTMENT) {
            $json['promo_id'] = (string) self::DEPARTMENT;
            $json['department'] = $this->getCategory()->getName();

        } elseif ($this->categoryLevel == self::CATEGORY) {
            $json['promo_id'] = (string) self::CATEGORY;
            $json['category'] = $this->getCategory()->getName();
            $json['department'] = $this->getCategoryById($parentCategories['department'])->getName();

        } else {
            $json['promo_id'] = (string) self::SUBCATEGORY;
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
