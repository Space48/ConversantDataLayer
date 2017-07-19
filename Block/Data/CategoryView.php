<?php

namespace Space48\ConversantDataLayer\Block\Data;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Space48\ConversantDataLayer\Helper\Data as ConversantHelper;

class CategoryView extends Template
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
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * ConversantDataLayer Helper
     *
     * @var \Space48\ConversantDataLayer\Helper\Data
     */
    protected $conversantHelper = null;

    /**
     * @var Data
     */
    protected $jsonHelper;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var Integer
     */
    protected $categoryLevel;

    /**
     * CategoryView constructor.
     *
     * @param Context            $context
     * @param Data               $jsonHelper
     * @param Registry           $registry
     * @param ConversantHelper   $conversantHelper
     * @param CategoryRepository $categoryRepository
     * @param array              $data
     */
    public function __construct(
        Context $context,
        Data $jsonHelper,
        Registry $registry,
        ConversantHelper $conversantHelper,
        CategoryRepository $categoryRepository,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->jsonHelper = $jsonHelper;
        $this->conversantHelper = $conversantHelper;
        $this->categoryRepository = $categoryRepository;

        parent::__construct($context, $data);
    }

    /**
     * @return Category
     */
    private function getCategory()
    {
        if (!$this->_category) {
            $this->_category = $this->_coreRegistry->registry('current_category');
        }

        return $this->_category;
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
        $parentCategories = $this->getParentCategoryIds();

        if ($this->getCategoryLevel() <= self::DEPARTMENT) {
            $json['promo_id'] = (string) self::DEPARTMENT;
            $json['department'] = $this->getCategory()->getName();

        } elseif ($this->getCategoryLevel() == self::CATEGORY) {
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

    /**
     * @return mixed
     */
    private function getParentCategoryIds()
    {
        $explodedPath = explode("/", $this->getCategory()->getPath());
        $pathArray = array_reverse($explodedPath);
        $pathArrayCount = count($pathArray);

        $categories['department'] = $pathArray[$pathArrayCount - 3];
        $categories['parent'] = $pathArray[1];

        return $categories;
    }

    /**
     * @param $categoryId
     *
     * @return \Magento\Catalog\Api\Data\CategoryInterface|mixed
     */
    private function getCategoryById($categoryId)
    {
        return $this->categoryRepository->get($categoryId);
    }

    /**
     * @return int
     */
    private function getCategoryLevel()
    {
        if(! $this->categoryLevel){
            $this->categoryLevel = $this->getCategory()->getLevel();
        }

        return $this->categoryLevel;
    }
}
