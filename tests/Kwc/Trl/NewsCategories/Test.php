<?php
/**
 * @group Kwc_Trl
 *
ansicht frontend:
http://doleschal.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_NewsCategories_Root/de/test
http://doleschal.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_NewsCategories_Root/en/test

http://doleschal.kwf.niko.vivid/kwf/componentedittest/Kwc_Trl_NewsCategories_Root/Kwc_Trl_NewsCategories_News_Component/Index?componentId=root-master_test
http://doleschal.kwf.niko.vivid/kwf/componentedittest/Kwc_Trl_NewsCategories_Root/Kwc_Trl_NewsCategories_News_Trl_Component.Kwc_Trl_NewsCategories_News_Component/Index?componentId=root-en_test
 */
class Kwc_Trl_NewsCategories_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_NewsCategories_Root');
        $trlElements = array();
        $trlElements['kwf']['de'] = array();
        Kwf_Trl::getInstance()->setTrlElements($trlElements);
    }

    public function testDe()
    {
        $c = $this->_root->getComponentById('root-master_test-categories_1');
        $this->assertContains('/de/test/2_lipsum2', $c->render());
        $this->assertContains('/de/test/1_lipsum', $c->render());

        $c = $this->_root->getComponentById('root-master_test-categories_2');
        $this->assertContains('/de/test/1_lipsum', $c->render());
    }


    public function testEn()
    {
        $c = $this->_root->getComponentById('root-en_test-categories_1');
        $this->assertContains('/en/test/1_loremen', $c->render());

        $c = $this->_root->getComponentById('root-en_test-categories_2');
        $this->assertContains('/en/test/1_loremen', $c->render());
    }
}
