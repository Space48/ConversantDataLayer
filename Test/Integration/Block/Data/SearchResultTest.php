<?php
/**
 * SearchResultTest.php
 *
 * @Date        07/2017
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @author      @diazwatson
 */

declare(strict_types=1);

namespace Space48\ConversantDataLayer\Block\Data;

use Magento\Framework\View\Element\Template;
use Magento\TestFramework\ObjectManager;

class SearchResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SearchResult
     */
    public $block;

    public function setUp()
    {
        $objectManager = ObjectManager::getInstance();
        $this->block = $objectManager->create(SearchResult::class);
    }

    public function testItExtendTemplateBlock()
    {
        $this->assertInstanceOf(Template::class, $this->block);
    }

    public function testToHtmlReturnsTheRightOutPut()
    {
        $this->assertEquals($this->getSampleOutPut(), $this->block->toHtml());
    }

    /**
     * @return string
     */
    private function getSampleOutPut()
    {
        return "dataLayer.push({\"promo_id\":\"7\"});\n";
    }
}
