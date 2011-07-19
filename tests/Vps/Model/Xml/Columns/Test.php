<?php
/**
 * @group Model
 * @group xmlModel
 */
class Vps_Model_Xml_Columns_Test extends Vps_Test_TestCase
{
    private $_model;

    public function setUp()
    {
        parent::setUp();
        $this->_model = new Vps_Model_Xml(array(
            'xpath' => '/trl',
        	'xpathRead' => '//text',
            'topNode' => 'text',
            'xmlContent' => '<trl><text><id>1</id><en>Visible</en><de>Sichtbar</de></text></trl>',
            'columns' => array('id', 'en', 'de', 'fr')
        ));
    }

    public function testXmlColumns()
    {
        $this->assertEquals($this->_model->getRow(1)->en, 'Visible');
        $this->assertEquals($this->_model->getRow(1)->de, 'Sichtbar');
        $this->assertEquals($this->_model->getRow(1)->fr, null);
    }

    /**
     * @expectedException Vps_Exception
     * @group xmlModel
     */
    public function testXmlException()
    {
        $this->_model->getRow(1)->notexistent = 'x';
    }

    public function testXmlCreateRow()
    {
        $row = $this->_model->createRow(array('en'=>'hallo', 'de'=>'whatever'));
        $row->save();

    }

    /**
     * @expectedException Vps_Exception
     */
    public function testXmlCreateRowException()
    {
        $row = $this->_model->createRow(array('en'=>array('hallo' => "whatever"), 'de'=>'whatever'));
        $row->save();

    }

    public function testXmlNoColumnsSet()
    {
        $this->_model->setColumns(array());
        $this->_model->getRow(1)->notexistent = 'x';
    }
}
