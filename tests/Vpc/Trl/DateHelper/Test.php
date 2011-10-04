<?php
/**
 * @group Trl_DateHelper
 */
class Vpc_Trl_DateHelper_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Trl_DateHelper_Root');
    }

    public function testDe()
    {
        $c = $this->_root->getComponentById('root-master_date');
        $this->assertEquals('de', $c->getLanguage());
        $this->assertEquals('d.m.Y', $c->trlVps('Y-m-d'));
        $this->assertEquals('09.06.1983', $c->render());

        $c = $this->_root->getComponentById('root-master_dateTime');
        $this->assertEquals('09.06.1983 15:30', $c->render());
    }

    public function testEn()
    {
        $c = $this->_root->getComponentById('root-en_date');
        $this->assertEquals('en', $c->getLanguage());
        $this->assertEquals('Y-m-d', $c->trlVps('Y-m-d'));
        $this->assertEquals('1983-06-09', $c->render());

        $c = $this->_root->getComponentById('root-en_dateTime');
        $this->assertEquals('1983-06-09 15:30', $c->render());
    }
}
