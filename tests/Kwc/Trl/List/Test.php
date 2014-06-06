<?php
/**
 * @group Kwc_Trl
 *
ansicht frontend:
http://doleschal.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_List_Root/de/test
http://doleschal.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_List_Root/en/test

DE bearbeiten:
http://doleschal.kwf.niko.vivid/kwf/componentedittest/Kwc_Trl_List_Root/Kwc_Trl_List_List_Component?componentId=root-master_test
EN bearbeiten
http://doleschal.kwf.niko.vivid/kwf/componentedittest/Kwc_Trl_List_Root/Kwc_Trl_List_List_Trl_Component.Kwc_Trl_List_List_Component/?componentId=root-en_test
 */
class Kwc_Trl_List_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_List_Root');
    }
    public function testIt()
    {
        $c = $this->_root->getComponentById('root-master_test');
        $this->assertTrue(substr_count($c->render(), 'foo')==2);

        $c = $this->_root->getComponentById('root-en_test');
        $this->assertTrue(substr_count($c->render(), 'foo')==1);
    }

    public function testMasterLimitWithVisible()
    {
        $c = $this->_root->getComponentById('root-master_test');
        $cmp = $c->getChildComponents(array(
            'limit' => 1
        ));
        $cmp = array_values($cmp);
        $this->assertEquals(1, count($cmp));
        $this->assertEquals('root-master_test-2', $cmp[0]->componentId);
    }

    public function testTrlLimitWithVisible()
    {
        $c = $this->_root->getComponentById('root-en_test');
        $cmp = $c->getChildComponents(array(
            'limit' => 1
        ));
        $cmp = array_values($cmp);
        $this->assertEquals(1, count($cmp));
        $this->assertEquals('root-en_test-2', $cmp[0]->componentId);
    }
}
