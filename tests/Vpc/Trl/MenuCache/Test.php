<?php
/**
 * @group Vpc_Trl
 
ansicht frontend:
http://vps.vps.niko.vivid/vps/vpctest/Vpc_Trl_MenuCache_Root/de
http://vps.vps.niko.vivid/vps/vpctest/Vpc_Trl_MenuCache_Root/de/home_de/test
http://vps.vps.niko.vivid/vps/vpctest/Vpc_Trl_MenuCache_Root/en
http://vps.vps.niko.vivid/vps/vpctest/Vpc_Trl_MenuCache_Root/en/home_en/test
 */
class Vpc_Trl_MenuCache_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Trl_MenuCache_Root');
    }
    public function testMenuDe()
    {
        $c = $this->_root->getComponentById('1-mainMenu');
        $html = $c->render();
        $this->assertContains('Home de</a>', $html);
        $this->assertContains('Test</a>', $html);

        $row = Vps_Model_Abstract::getInstance('Vpc_Trl_MenuCache_Category_PagesTestModel')
            ->getRow(2);
        $row->name = 'Testx';
        $row->save();

        $this->_process();

        $html = $c->render();
        $this->assertContains('Home de</a>', $html);
        $this->assertContains('Testx</a>', $html);
    }

    public function testMenuEn()
    {
        $c = $this->_root->getComponentById('root-en-main_1-mainMenu');
        /*
        $html = $c->render();
        $this->assertContains('Home en</a>', $html);
        $this->assertContains('Test</a>', $html);
        $this->assertContains('Test2 en</a>', $html);
        */

        $row = Vps_Model_Abstract::getInstance('Vpc_Trl_MenuCache_Category_Trl_PagesTrlTestModel')
            ->getRow('root-en-main_2');
        $row->name = 'Testxen';
        $row->save();

        $this->_process();

        $html = $c->render();
        $this->assertContains('Home en</a>', $html);
        $this->assertContains('Testxen</a>', $html);
    }
}
