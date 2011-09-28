<?php
/**
 * @group Db_TablesModel
 */
class Vps_Db_TablesModel_TableFieldsTest extends Vps_Test_TestCase
{
    public function setUp()
    {
        $this->_db = $this->getMock('Vps_Model_Db_TestAdapter',
            array('fetchCol', 'fetchAssoc', 'query'));
        $this->_db->expects($this->any())
            ->method('fetchCol')
            ->with($this->equalTo('SHOW TABLES'))
            ->will($this->returnValue(array('foo', 'bar', 'bam')));

        $this->_model = new Vps_Db_TablesModel(array(
            'db' => $this->_db
        ));
        parent::setUp();
    }

    public function testGetFields()
    {
        $this->_db->expects($this->exactly(1))
            ->method('fetchAssoc')
            ->with($this->equalTo('SHOW FIELDS FROM foo'))
            ->will($this->returnValue(array(
                array('Field'=>'id', 'Type'=>'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Default'=>null, 'Extra'=>'auto_increment'),
                array('Field'=>'foo', 'Type'=>'varchar(255)', 'Null'=>'NO', 'Key'=>'MUL', 'Default'=>'', 'Extra'=>''),
                array('Field'=>'bar', 'Type'=>'text', 'Null'=>'NO', 'Key'=>'', 'Default'=>'', 'Extra'=>''),
                array('Field'=>'baz', 'Type'=>'date', 'Null'=>'YES', 'Key'=>'', 'Default'=>'', 'Extra'=>'')
            )));
        $rows = $this->_model->getRow('foo')->getChildRows('Fields');
        $this->assertEquals(4, count($rows));

        $rows = $this->_model->getRow('foo')->getChildRows('Fields', $this->_model->select()->whereId('foo'));
        $this->assertEquals(1, count($rows));
        $row = $rows->current();
        $this->assertEquals('foo', $row->field);
        $this->assertEquals('varchar(255)', $row->type);
        $this->assertEquals(0, $row->null);
        $this->assertEquals('MUL', $row->key);
        $this->assertEquals('', $row->default);
        $this->assertEquals('', $row->extra);

        $rows = $this->_model->getRow('foo')->getChildRows('Fields', $this->_model->select()->whereId('baz'));
        $this->assertEquals(1, count($rows));
        $row = $rows->current();
        $this->assertEquals('baz', $row->field);
        $this->assertEquals('date', $row->type);
        $this->assertEquals(1, $row->null);
        $this->assertEquals('', $row->key);
        $this->assertEquals('', $row->default);
        $this->assertEquals('', $row->extra);
    }

    public function testInsertField()
    {
        $this->_db->expects($this->exactly(1))
            ->method('query')
            ->with($this->equalTo('ALTER TABLE foo ADD new_field TEXT NOT NULL'));
        $row = $this->_model->getRow('foo');
        $fieldRow = $row->createChildRow('Fields');
        $fieldRow->field = 'new_field';
        $fieldRow->type = 'TEXT';
        $fieldRow->null = 0;
        $fieldRow->default = '';
        $fieldRow->save();
    }

    public function testUpdateField()
    {
        $this->_db->expects($this->once())
            ->method('fetchAssoc')
            ->with($this->equalTo('SHOW FIELDS FROM foo'))
            ->will($this->returnValue(array(
                array('Field'=>'id', 'Type'=>'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Default'=>null, 'Extra'=>'auto_increment'),
                array('Field'=>'foo', 'Type'=>'varchar(255)', 'Null'=>'NO', 'Key'=>'MUL', 'Default'=>'', 'Extra'=>''),
            )));
        $this->_db->expects($this->exactly(1))
            ->method('query')
            ->with($this->equalTo('ALTER TABLE foo CHANGE foo foo VARCHAR(10) NULL'));
        $row = $this->_model->getRow('foo');
        $fieldRow = $this->_model->getRow('foo')
            ->getChildRows('Fields', $this->_model->select()->whereId('foo'))
            ->current();
        $this->assertNotNull($fieldRow);
        $fieldRow->type = 'VARCHAR(10)';
        $fieldRow->null = 1;
        $fieldRow->save();
    }

    public function testUpdateFieldDefaultNull()
    {
        $this->_db->expects($this->once())
            ->method('fetchAssoc')
            ->with($this->equalTo('SHOW FIELDS FROM foo'))
            ->will($this->returnValue(array(
                array('Field'=>'id', 'Type'=>'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Default'=>null, 'Extra'=>'auto_increment'),
                array('Field'=>'foo', 'Type'=>'varchar(255)', 'Null'=>'NO', 'Key'=>'MUL', 'Default'=>'', 'Extra'=>''),
            )));
        $this->_db->expects($this->exactly(1))
            ->method('query')
            ->with($this->equalTo('ALTER TABLE foo CHANGE foo foo VARCHAR(10) NULL DEFAULT NULL'));
        $row = $this->_model->getRow('foo');
        $fieldRow = $this->_model->getRow('foo')
            ->getChildRows('Fields', $this->_model->select()->whereId('foo'))
            ->current();
        $this->assertNotNull($fieldRow);
        $fieldRow->type = 'VARCHAR(10)';
        $fieldRow->null = 1;
        $fieldRow->default = null;
        $fieldRow->save();
    }

    public function testDropField()
    {
        $this->_db->expects($this->once())
            ->method('fetchAssoc')
            ->with($this->equalTo('SHOW FIELDS FROM foo'))
            ->will($this->returnValue(array(
                array('Field'=>'id', 'Type'=>'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Default'=>null, 'Extra'=>'auto_increment'),
                array('Field'=>'foo', 'Type'=>'varchar(255)', 'Null'=>'NO', 'Key'=>'MUL', 'Default'=>'', 'Extra'=>''),
            )));
        $this->_db->expects($this->exactly(1))
            ->method('query')
            ->with($this->equalTo('ALTER TABLE foo DROP foo'));
        $row = $this->_model->getRow('foo');
        $fieldRow = $this->_model->getRow('foo')
            ->getChildRows('Fields', $this->_model->select()->whereId('foo'))
            ->current();
        $this->assertNotNull($fieldRow);
        $fieldRow->delete();
    }
}
