<?php
/**
 * @group Cc
 */
class Kwc_Cc_PageTree_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Cc_PageTree_Root');
    }

    public function testChangePageTypeClearCache()
    {
//         d($this->_root->getComponentById('root-master-main')->getChildComponents());
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('2');
        $html = $c->render(true, false);
        $this->assertEquals('', $html);

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root-slave-main_2');
        $html = $c->render(true, false);
        $this->assertEquals('', $html);

        $r = Kwf_Model_Abstract::getInstance('Kwc_Cc_PageTree_Master_Category_PagesModel')->getRow(2);
        $r->component = 'test';
        $r->save();
        $this->_process();

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('2');
        $html = $c->render(true, false);
        $this->assertEquals('Test', $html);

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root-slave-main_2');
        $html = $c->render(true, false);
        $this->assertEquals('Test', $html);
    }
}
