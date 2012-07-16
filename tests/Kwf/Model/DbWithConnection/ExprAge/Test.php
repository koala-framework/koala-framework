<?php
class Kwf_Model_DbWithConnection_ExprAge_Test extends Kwf_Test_TestCase
{
    public function testExprLazyLoad()
    {
        $expectNYE = 0;
        //if it is new Year's Eve the expected age is 2
        if (date('m-d') == '12-31') {
            $expectNYE = 1;
        }
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprAge_Model');
        $m->setUp();
        $this->assertEquals(18, $m->getRow(1)->age);
        $this->assertEquals(1, $m->getRow(2)->age);
        $this->assertEquals(0, $m->getRow(3)->age);
        $this->assertEquals(1, $m->getRow(4)->age);
        $this->assertEquals($expectNYE, $m->getRow(5)->age);
        $this->assertEquals(1, $m->getRow(6)->age);
        $this->assertEquals(null, $m->getRow(7)->age);
        $m->dropTable();
    }

    public function testExprRefLazyLoad()
    {
        $today = date('Y-m-d');
        $realTomorrow = date("Y-m-d", strtotime("$today +1 day"));
        $expectNYE = 0;
        $expectNY = 1;
        //if it is new Year's Eve the expected age for new Year = 2;
        if ($realTomorrow == date('Y').'-12-31') {
            $expectNYE = 1;
        }
        //if it is new Year's Eve the expected age for new Year = 2;
        if (date('m-d') == '12-31') {
            $expectNY = 2;
        }
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprAge_Model');
        $m->setUp();
        $this->assertEquals(18, $m->getRow(1)->age_ref);
        $this->assertEquals(1, $m->getRow(2)->age_ref);
        $this->assertEquals(1, $m->getRow(3)->age_ref);
        $this->assertEquals(1, $m->getRow(4)->age_ref);
        $this->assertEquals($expectNYE, $m->getRow(5)->age_ref);
        $this->assertEquals($expectNY, $m->getRow(6)->age_ref);
        $this->assertEquals(null, $m->getRow(7)->age_ref);
        $m->dropTable();
    }

    public function testExprPreload()
    {
        $expectNYE = 0;
        //if it is new Year's Eve the expected age is 2
        if (date('m-d') == '12-31') {
            $expectNYE = 1;
        }
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprAge_Model');
        $m->setUp();

        $s = $m->select();
        $s->expr('age');

        $s->whereId(1);
        $this->assertEquals(18, $m->getRow($s)->age);

        $s->whereId(2);
        $this->assertEquals(1, $m->getRow($s)->age);

        $s->whereId(3);
        $this->assertEquals(0, $m->getRow($s)->age);

        $s->whereId(4);
        $this->assertEquals(1, $m->getRow($s)->age);

        $s->whereId(5);
        $this->assertEquals($expectNYE, $m->getRow($s)->age);

        $s->whereId(6);
        $this->assertEquals(1, $m->getRow($s)->age);

        $s->whereId(7);
        $this->assertEquals(null, $m->getRow($s)->age);

        $m->dropTable();
    }
    public function testExprRefPreload()
    {
        $today = date('Y-m-d');
        $realTomorrow = date("Y-m-d", strtotime("$today +1 day"));
        $expectNYE = 0;
        $expectNY = 1;
        //if it is new Year's Eve the expected age for new Year = 2;
        if ($realTomorrow == date('Y').'-12-31') {
            $expectNYE = 1;
        }
        //if it is new Year's Eve the expected age for new Year = 2;
        if (date('m-d') == '12-31') {
            $expectNY = 2;
        }

        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprAge_Model');
        $m->setUp();

        $s = $m->select();
        $s->expr('age_ref');


        $s->whereId(1);
        $this->assertEquals(18, $m->getRow($s)->age_ref);

        $s->whereId(2);
        $this->assertEquals(1, $m->getRow($s)->age_ref);

        $s->whereId(3);
        $this->assertEquals(1, $m->getRow($s)->age_ref);

        $s->whereId(4);
        $this->assertEquals(1, $m->getRow($s)->age_ref);

        $s->whereId(5);
        $this->assertEquals($expectNYE, $m->getRow($s)->age_ref);

        $s->whereId(6);
        $this->assertEquals($expectNY, $m->getRow($s)->age_ref);

        $s->whereId(7);
        $this->assertEquals(null, $m->getRow($s)->age_ref);

        $m->dropTable();
    }
}
