<?php
/**
 * @group Vpc_Trl
 *
ansicht frontend:
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Trl_NewsCategories_Root/de/test
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Trl_NewsCategories_Root/en/test

http://doleschal.vps.niko.vivid/vps/componentedittest/Vpc_Trl_NewsCategories_Root/Vpc_Trl_NewsCategories_News_Component/Index?componentId=root-master_test
http://doleschal.vps.niko.vivid/vps/componentedittest/Vpc_Trl_NewsCategories_Root/Vpc_Trl_NewsCategories_News_Trl_Component.Vpc_Trl_NewsCategories_News_Component/Index?componentId=root-en_test
 */
class Vpc_Trl_NewsCategories_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Trl_NewsCategories_Root');
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
