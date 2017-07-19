<?php
/**
 * HomepageTest.php
 *
 * @Date        07/2017
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @author      @diazwatson
 */

declare(strict_types=1);

namespace Space48\ConversantDataLayer\Block\Data;

use Magento\Framework\View\Element\Template;
use Magento\TestFramework\ObjectManager;

class HomepageTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Homepage
     */
    public $block;

    public function setUp()
    {
        $objectManager = ObjectManager::getInstance();

        $this->block = $objectManager->create(Homepage::class);
    }

    public function testIsInstanceOfTemplate()
    {
        $this->assertInstanceOf(Template::class, $this->block);
    }

    public function testToHtmlReturnsCodeWithPromoIdWhenEnabled()
    {
        $this->assertEquals($this->getSampleOutPut(), $this->block->toHtml());
    }

    /**
     * @return string
     */
    private function getSampleOutPut()
    {
        return "dataLayer.push({\"promo_id\":\"1\"});\n";
    }
}
