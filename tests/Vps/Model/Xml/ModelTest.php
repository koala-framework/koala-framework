<?php
/**
 * @group Model_Xml
 */

class Vps_Model_Xml_ModelTest extends PHPUnit_Framework_TestCase
{
    public function testXmlBasic()
    {
        $model = new Vps_Model_Xml(array(
            'xpath' => '/trl',
            'topNode' => 'text',
            'xmlContent' => '<trl><text><id>1</id><en>Visible</en><de>Sichtbar</de></text></trl>'
        ));
        $row = $model->getRow(1);
        $row->de = "anwesend";
        $row->save();
        $this->assertTrue((bool) strpos($model->getXmlContentString(), 'anwesend'));

        $newrow = $model->createRow(array('en' => "hello", 'de' => "hallo"));
        $newrow->save();
        $row = $model->getRow(2);
        $this->assertEquals("hello", $row->en);

        $newrow = $model->createRow(array('en' => "hi", 'de' => "servus"));
        $newrow->save();

        $select = $model->select();
        $select->whereEquals('en', 'hello');
        $select->order('en', 'ASC');
        $rows = $model->getRows($select);
        $this->assertEquals(1, $rows->count($select));
        $this->assertEquals(3, $model->getRows()->count());

        //model zum testen der inhalte
        $testmodel = new Vps_Model_Xml(array(
            'xpath' => '/trl',
            'topNode' => 'text',
            'xmlContent' => $model->getXmlContentString()
        ));

        $select = $model->select();
        $select->whereEquals('id', '1');
        $testrow = $testmodel->getRows($select)->current();
        $this->assertEquals("anwesend", $testrow->de);

        $select = $model->select();
        $select->order('id', 'DESC');
        $testrow = $testmodel->getRows($select)->current();
        $this->assertEquals(3, $testrow->id);
    }

    public function testXmlPaths()
    {
        $model = new Vps_Model_Xml(array(
            'xpath' => '/trl',
            'topNode' => 'text',
            'xmlContent' => '<trl><text><id>1</id><en>test</en><de>versuch</de></text></trl>'
        ));
       $row = $model->getRow(1);
       $this->assertEquals("versuch", $row->de);

       $model = new Vps_Model_Xml(array(
            'xpath' => '/trl',
            'topNode' => 'text',
            'xmlContent' => '<trl><text></text></trl>'
        ));
       $rows = $model->getRows();
       $this->assertEquals(1, $rows->count());
    }

    public function testXmlPathsInsertException()
    {
        $this->setExpectedException('Vps_Exception');
        $model = new Vps_Model_Xml(array(
            'xpath' => '/trlaaa', //hier ist ein fehler
            'topNode' => 'text',
            'xmlContent' => '<trl><text><id>1</id><en>Visible</en><de>Sichtbar</de></text></trl>'
        ));
       $model->createRow()->save();
    }

    public function testXmlInsert()
    {
        $model = new Vps_Model_Xml(array(
            'xpath' => '/trl', //hier ist ein fehler
            'topNode' => 'text',
            'xmlContent' => '<trl><text><id>1</id><en>Visible</en><de>Sichtbar</de></text></trl>'
        ));
        $model->createRow(array('en' => 'english', 'de' => 'englisch'))->save();
        $this->assertEquals(2, $model->getRows()->count());
        $this->assertEquals('englisch', $model->getRow(2)->de);

    }

    public function testXmlOutOfRange()
    {
        $model = new Vps_Model_Xml(array(
            'xpath' => '/trl', //hier ist ein fehler
            'topNode' => 'text',
            'xmlContent' => '<trl><text><id>1</id><en>Visible</en><de>Sichtbar</de></text></trl>'
        ));
        $this->assertEquals(null, $model->getRow(5));
    }

    public function testXmlSelect ()
    {
        $model = new Vps_Model_Xml(array(
            'xpath' => '/trl',
            'topNode' => 'text',
            'xmlContent' => '<trl><text><id>1</id><en>Visible</en><de>Sichtbar</de></text></trl>'
        ));
        $model->createRow(array('en' => 'english', 'de' => 'englisch'))->save();
        $model->createRow(array('en' => 'german', 'de' => 'deutsch'))->save();
        $select = $model->select();
        $select->whereEquals('en', 'german');
        $testrow = $model->getRows($select)->current();
        $this->assertEquals("deutsch", $testrow->de);

        $select = $model->select();
        $select->order('en', 'ASC');
        $testrow = $model->getRows($select)->current();
        $this->assertEquals("english", $testrow->en);

        $select = $model->select();
        $select->order('en', 'DESC');
        $testrow = $model->getRows($select)->current();
        $this->assertEquals("Visible", $testrow->en);

        $select = $model->select();
        $select->whereNotEquals('en', 'german');
        $select->order('en', 'DESC');
        $testrows = $model->getRows($select);
        $this->assertEquals(2, $testrows->count());
        $this->assertEquals("Sichtbar", $testrows->current()->de);
    }

    public function testXmlSelectException ()
    {
        $this->setExpectedException('Vps_Exception');
        $model = new Vps_Model_Xml(array(
            'xpath' => '/trl',
            'topNode' => 'text',
            'xmlContent' => '<trl><text><id>1</id><en>Visible</en><de>Sichtbar</de></text></trl>'
        ));
        $model->createRow(array('en' => 'english', 'de' => 'englisch'))->save();
        $model->createRow(array('en' => 'german', 'de' => 'deutsch'))->save();
        $select = $model->select();
        $select->whereEquals(null, null);
    }

    public function testXmlDelete()
    {
        $model = new Vps_Model_Xml(array(
            'xpath' => '/trl',
            'topNode' => 'text',
            'xmlContent' => '<trl>
            				 <text><id>1</id><en>Visible</en><de>Sichtbar</de></text>
            				 <text><id>2</id><en>Delete</en><de>löschen</de></text>
            				 </trl>'
        ));
        $this->assertEquals(2, $model->getRows()->count());
        $row = $model->getRow(2);
        $row->delete();
        $this->assertEquals(1, $model->getRows()->count());
        $this->assertEquals('Visible', $model->getRow(1)->en);
        $model->getRow(1)->delete();
        $this->assertEquals(0, $model->getRows()->count());

    }

    public function testXmlNoId()
    {
        $model = new Vps_Model_Xml(array(
            'xpath' => '/trl',
            'topNode' => 'text',
            'xmlContent' => '<trl>
            				 <text><en>Visible</en><de>Sichtbar</de></text>
            				 <text><en>Delete</en><de>löschen</de></text>
            				 </trl>'
        ));
        $select = $model->select();
        $select->whereEquals('en', 'Delete');
        $row = $model->getRows($select)->current();
        $this->assertEquals("löschen", $row->de);

        $select = $model->select();
        $select->whereEquals('en', 'Delete');
        $row = $model->getRow($select);
        $this->assertEquals("löschen", $row->de);
    }

    public function testXmlNoIdException()
    {
        $this->setExpectedException('Vps_Exception');
        $model = new Vps_Model_Xml(array(
            'xpath' => '/trl',
            'topNode' => 'text',
            'xmlContent' => '<trl>
            				 <text><en>Visible</en><de>Sichtbar</de></text>
            				 <text><en>Delete</en><de>löschen</de></text>
            				 </trl>',
        	'primaryKey' => ''
        ));
        $select = $model->select();
        $select->whereEquals('en', 'Delete');
        $row = $model->getRows($select)->current();
        $this->assertEquals("löschen", $row->de);

        $row->save();
    }

    public function testXmlDifferentIdString ()
    {
        $model = new Vps_Model_Xml(array(
            'xpath' => '/trl',
            'topNode' => 'text',
            'xmlContent' => '<trl>
            				 <text><en>Visible</en><de>Sichtbar</de></text>
            				 <text><en>Delete</en><de>löschen</de></text>
            				 </trl>',
            'primaryKey' => 'en'
        ));
        $select = $model->select();
        $select->whereEquals('en', 'Delete');
        $row = $model->getRows($select)->current();

        $this->assertEquals("löschen", $row->de);
        $row->save();

        $row = $model->createRow(array('en' => 'english', 'de' => 'englisch'));
        $this->assertEquals('english', $row->save());

        $row->de = "Englisch";
        $this->assertEquals('english', $row->save());
        $this->assertEquals('Englisch', $row->de);

        $row = $model->createRow(array('de' => 'nix'));
        $this->assertEquals(1, $row->save());
    }

    public function testXmlDifferentIdInt ()
    {
        $model = new Vps_Model_Xml(array(
            'xpath' => '/trl',
            'topNode' => 'text',
            'xmlContent' => '<trl>
            				 <text><ident>1</ident><en>Visible</en><de>Sichtbar</de></text>
            				 <text><ident>2</ident><en>Delete</en><de>löschen</de></text>
            				 </trl>',
            'primaryKey' => 'ident'
        ));

        $row = $model->createRow(array('en' => 'english', 'de' => 'englisch'));
        $this->assertEquals(3, $row->save());
    }

    public function testXmlDifferentIdIntExcpetion ()
    {
        $this->setExpectedException('Vps_Exception');
        $model = new Vps_Model_Xml(array(
            'xpath' => '/trl',
            'topNode' => 'text',
            'xmlContent' => '<trl>
            				 <text><ident>1</ident><en>Visible</en><de>Sichtbar</de></text>
            				 <text><ident>2</ident><en>Delete</en><de>löschen</de></text>
            				 </trl>',
            'primaryKey' => 'ident'
        ));

        $row = $model->createRow(array('ident' => 1, 'en' => 'english', 'de' => 'englisch'));
        $this->assertEquals(1, $row->save());
    }


    public function testDefaultValues()
    {
        $model = new Vps_Model_Xml(array(
            'default' => array('foo'=>'defaultFoo')
        ));
        $row = $model->createRow();
        $this->assertEquals('defaultFoo', $row->foo);
    }
}