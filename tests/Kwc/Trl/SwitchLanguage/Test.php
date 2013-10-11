<?php
class Kwc_Trl_SwitchLanguage_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_SwitchLanguage_Root_Component');
        $this->_root->setFilename(null);
    }

    public function testDe()
    {
        $html = $this->_root->getComponentById('1-switchLanguage')->render();
        $this->assertContains('href="/de"', $html);
        $this->assertContains('href="/en"', $html);

        $html = $this->_root->getComponentById('2-switchLanguage')->render();
        $this->assertContains('href="/de/test"', $html);
        $this->assertContains('href="/en"', $html); //not visible in trl, link to home

        //3 is not visibile in de

        $html = $this->_root->getComponentById('4-switchLanguage')->render();
        $this->assertContains('href="/de/test3"', $html);
        $this->assertContains('href="/en/test3_en"', $html); //not visible in trl, link to home
    }

    public function testEn()
    {
        $html = $this->_root->getComponentById('root-en-main_1-switchLanguage')->render();
        $this->assertContains('href="/de"', $html);
        $this->assertContains('href="/en"', $html);

        //2 is not visibile in en

        $html = $this->_root->getComponentById('root-en-main_3-switchLanguage')->render();
        $this->assertContains('href="/de"', $html); //not visible in trl, link to home
        $this->assertContains('href="/en/test2_en"', $html);

        $html = $this->_root->getComponentById('4-switchLanguage')->render();
        $this->assertContains('href="/de/test3"', $html);
        $this->assertContains('href="/en/test3_en"', $html); //not visible in trl, link to home
    }

    public function testShowDe()
    {
        $this->_root->getComponentById('2-switchLanguage')->render();

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_SwitchLanguage_Category_Trl_PagesTrlTestModel')
            ->getRow('root-en-main_2');
        $row->visible = true;
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById('2-switchLanguage')->render();
        $this->assertContains('href="/de/test"', $html);
        $this->assertContains('href="/en/test_en"', $html);
    }

    public function testHideDe()
    {
        $this->_root->getComponentById('4-switchLanguage')->render();

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_SwitchLanguage_Category_Trl_PagesTrlTestModel')
            ->getRow('root-en-main_4');
        $row->visible = false;
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById('4-switchLanguage')->render();
        $this->assertContains('href="/de/test3"', $html);
        $this->assertContains('href="/en"', $html);
    }

    public function testShowEn()
    {
        $this->_root->getComponentById('root-en-main_3-switchLanguage')->render();

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_SwitchLanguage_Category_PagesTestModel')
            ->getRow('3');
        $row->visible = true;
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById('root-en-main_3-switchLanguage')->render();
        $this->assertContains('href="/en/test2_en"', $html);
        $this->assertContains('href="/de/test2"', $html);
    }

    public function testHideEn()
    {
        $this->_root->getComponentById('root-en-main_4-switchLanguage')->render();

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_SwitchLanguage_Category_PagesTestModel')
            ->getRow('4');
        $row->visible = false;
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById('root-en-main_4-switchLanguage')->render();
        $this->assertContains('href="/en/test3_en"', $html);
        $this->assertContains('href="/de"', $html);
    }

    public function testHideEnDomain()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Component_Model');
        $row = $model->getRow('root-en');

        $html = $this->_root->getComponentById('4-switchLanguage')->render();
        $this->assertContains('href="/de/test3"', $html);
        $this->assertContains('href="/en/test3_en"', $html);

        $row->visible = false;
        $row->save();
        $this->_process();
        $html = $this->_root->getComponentById('4-switchLanguage')->render();
        $this->assertNotContains('href="/en', $html);

        $row->visible = true;
        $row->save();
        $this->_process();
        $html = $this->_root->getComponentById('4-switchLanguage')->render();
        $this->assertContains('href="/en/test3_en"', $html);
    }
}
