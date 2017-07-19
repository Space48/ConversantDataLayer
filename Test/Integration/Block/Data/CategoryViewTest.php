<?php
/**
 * CategoryViewTest.php
 *
 * @Date        07/2017
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @author      @diazwatson
 */

declare(strict_types=1);

namespace Space48\ConversantDataLayer\Block\Data;

use Magento\Catalog\Model\Category;
use Magento\Framework\View\Element\Template;
use Magento\TestFramework\ObjectManager;
use Magento\Framework\Registry;

class CategoryViewTest extends \PHPUnit_Framework_TestCase
{

    public $objectManager;

    /** @var  CategoryView */
    public $block;

    public $model;

    public function setUp()
    {
        $this->objectManager = $objectManager = ObjectManager::getInstance();
        $this->block = $objectManager->create(CategoryView::class);
        $this->model = $this->objectManager->create(Category::class);
    }

    public function testIsInstanceOfTemplate()
    {
        $this->assertInstanceOf(Template::class, $this->block);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/category_with_position.php
     */
    public function testToHtmlReturnsDepartment()
    {

        /** @var Category $category */
        $category = $this->model->load(444);
        $category->setPath('1/2/3');
        $category->setLevel(2);
        $category->setName('Luxury Home Furniture');
        $this->register($category);
        $this->assertEquals($this->getSampleOutPut()['department'], $this->block->toHtml());

    }

    /**
     * @param $category
     *
     * @return void
     */
    private function register($category)
    {
        /** @var Registry $registry */
        $registry = $this->objectManager->get(Registry::class);
        if ($registry->registry('current_category')) {
            $registry->unregister('current_category');
        }
        $registry->register('current_category', $category);
    }

    /**
     * @return array
     */
    private function getSampleOutPut()
    {
        return array(
            'department'  => "dataLayer.push({\"promo_id\":\"2\",\"department\":\"Luxury Home Furniture\"});\n",
            'category'    => "dataLayer.push({\"promo_id\":\"3\",\"category\":\"Luxury Home Furniture\",\"department\":\"Category 1\"});\n",
            'subcategory' => "dataLayer.push({\"promo_id\":\"4\",\"sub_category\":\"Luxury Home Furniture\",\"category\":\"Category 1.1\",\"department\":\"Category 1\"});\n"
        );
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/category_tree.php
     */
    public function testToHtmlReturnsCategory()
    {

        /** @var Category $category */
        $category = $this->model->load(402);
        $category->setLevel(3);
        $category->setName('Luxury Home Furniture');

        /** @var Registry $registry */
        $this->register($category);
        $this->assertEquals($this->getSampleOutPut()['category'], $this->block->toHtml());

    }

    /**
     * @magentoDataFixture   Magento/Catalog/_files/category_tree.php
     */
    public function testToHtmlReturnsSubCategory()
    {
        /** @var Category $category */
        $category = $this->model->load(402);
        $category->setLevel(4);
        $category->setName('Luxury Home Furniture');
        $this->register($category);

        $this->assertEquals($this->getSampleOutPut()['subcategory'], $this->block->toHtml());

    }
}
