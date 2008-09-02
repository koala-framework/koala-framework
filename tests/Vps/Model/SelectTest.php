<?php
class Vps_Model_SelectTest extends PHPUnit_Framework_TestCase
{
    public function testSelect()
    {
        $select = new Vps_Model_Select();

        $select->where('foo = ?', 1);
        $this->assertEquals($select->getPart(Vps_Model_Select::WHERE),
                    array(array('foo = ?', 1, null)));

        $select->where('bar = ?', 1);
        $this->assertEquals($select->getPart(Vps_Model_Select::WHERE),
                    array(array('foo = ?', 1, null), array('bar = ?', 1, null)));

        $select->whereEquals('foo', 1);
        $this->assertEquals($select->getPart(Vps_Model_Select::WHERE_EQUALS), array('foo' => 1));

        $select->whereEquals('foo', 'bar');
        $this->assertEquals($select->getPart(Vps_Model_Select::WHERE_EQUALS), array('foo' => 'bar'));

        $select->whereEquals('foo2', 'bar2');
        $this->assertEquals($select->getPart(Vps_Model_Select::WHERE_EQUALS),
                    array('foo' => 'bar',
                          'foo2' => 'bar2'));

        $select->order('foo');
        $this->assertEquals($select->getPart(Vps_Model_Select::ORDER),
                            array(array('field'=>'foo', 'dir'=>'ASC')));

        $select->order('foo2', 'DESC');
        $this->assertEquals($select->getPart(Vps_Model_Select::ORDER),
                            array(array('field'=>'foo', 'dir'=>'ASC'),
                                  array('field'=>'foo2', 'dir'=>'DESC')));

        $select->limit(10);
        $this->assertEquals($select->getPart(Vps_Model_Select::LIMIT_COUNT), 10);
        $this->assertEquals($select->getPart(Vps_Model_Select::LIMIT_OFFSET), null);

        $select->limit(20);
        $this->assertEquals($select->getPart(Vps_Model_Select::LIMIT_COUNT), 20);

        $select->limit(25, 10);
        $this->assertEquals($select->getPart(Vps_Model_Select::LIMIT_COUNT), 25);
        $this->assertEquals($select->getPart(Vps_Model_Select::LIMIT_OFFSET), 10);

        $this->assertEquals(count($select->getParts()), 5);

        $select = new Vps_Model_Select(array(
            'id' => 1,
        ));
        $this->assertEquals($select->getPart(Vps_Model_Select::WHERE_ID), 1);

        $select->whereId(10);
        $this->assertEquals($select->getPart(Vps_Model_Select::WHERE_ID), 10);
        $select->whereId(11);
        $this->assertEquals($select->getPart(Vps_Model_Select::WHERE_ID), 11);

    }


    public function testUnsetPart()
    {
        $select = new Vps_Model_Select();

        $select->where('foo = ?', 1);
        $this->assertEquals(count($select->getParts()), 1);

        $select->unsetPart(Vps_Model_Select::WHERE);
        $this->assertEquals(count($select->getParts()), 0);
    }
}
