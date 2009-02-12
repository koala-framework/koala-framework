<?php
/**
 * @group Component_CacheVars
 */
class Vps_Component_CacheVars_Paragraphs_Test extends PHPUnit_Framework_TestCase
{
    private $_root;

    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_CacheVars_Paragraphs_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testParagraphs()
    {
        $view = $this->_root->getChildComponent('-paragraphs');
        $cacheVars = $view->getComponent()->getCacheVars();
        $this->assertEquals(2, count($cacheVars));
        $this->assertEquals('Vps_Component_CacheVars_Paragraphs_Model', $cacheVars[0]['model']);
        $this->assertEquals('1', $cacheVars[0]['id']);
        $this->assertEquals('Vps_Component_CacheVars_Paragraphs_Model', $cacheVars[1]['model']);
        $this->assertEquals('2', $cacheVars[1]['id']);
    }

    public function testParagraphComponent()
    {
        $view = $this->_root->getChildComponent('-paragraphs')
            ->getChildComponent('-2');
        $cacheVars = $view->getComponent()->getCacheVars();
        $this->assertEquals(2, count($cacheVars));
        $this->assertEquals('Vps_Model_FnF', $cacheVars[0]['model']);
        $this->assertEquals('root-paragraphs-2', $cacheVars[0]['id']);
        $this->assertEquals('Vps_Component_CacheVars_Paragraphs_Model', $cacheVars[1]['model']);
        $this->assertEquals('2', $cacheVars[1]['id']);
    }
}
