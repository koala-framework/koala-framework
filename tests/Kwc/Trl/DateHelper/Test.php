<?php
/**
 * @group Trl_DateHelper
 */
class Kwc_Trl_DateHelper_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_DateHelper_Root');
        $trlElements = array();
        $trlElements['kwf']['de']['Y-m-d-'] = 'd.m.Y';
        $trlElements['kwf']['de']['Y-m-d H:i-'] = 'd.m.Y H:i';
        Kwf_Trl::getInstance()->setTrlElements($trlElements);
    }

    public function testDe()
    {
        $c = $this->_root->getComponentById('root-master_date');
        $this->assertEquals('de', $c->getLanguage());
        $this->assertEquals('d.m.Y', $c->trlKwf('Y-m-d'));
        $this->assertEquals('09.06.1983', $c->render());

        $c = $this->_root->getComponentById('root-master_dateTime');
        $this->assertEquals('09.06.1983 15:30', $c->render());
    }

    public function testEn()
    {
        $c = $this->_root->getComponentById('root-en_date');
        $this->assertEquals('en', $c->getLanguage());
        $this->assertEquals('Y-m-d', $c->trlKwf('Y-m-d'));
        $this->assertEquals('1983-06-09', $c->render());

        $c = $this->_root->getComponentById('root-en_dateTime');
        $this->assertEquals('1983-06-09 15:30', $c->render());
    }
}
