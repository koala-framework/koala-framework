<?php
/**
 * @group Kwc_Columns
 */
class Kwc_Columns_ColumnsInColumns_WidthTest extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Columns_ColumnsInColumns_Root');
    }

    public function testFixedWidth()
    {
        $c = $this->_root->getComponentById('1');
        $html = $c->render(false, false);
        $xml = simplexml_load_string($html);

        $cols = (array)$xml->xpath('/div/div/div/div[contains(@class, "column")]');
        $this->assertEquals('width: 90px', (string)$cols[0]['style']);
        $this->assertEquals('width: 10px', (string)$cols[1]['style']);

        $cols = (array)$xml->xpath('/div/div/div/div[contains(@class, "column") and position()=1]//div[contains(@class, "column")]');
        $this->assertEquals(2, count($cols));
        $this->assertEquals('width: 70px', (string)$cols[0]['style']);
        $this->assertEquals('width: 20px', (string)$cols[1]['style']);

        $cols = (array)$xml->xpath('/div/div/div/div[contains(@class, "column") and position()=2]//div[contains(@class, "column")]');
        $this->assertEquals(0, count($cols));
    }

    public function testPercentageWidth()
    {
        $c = $this->_root->getComponentById('2');

        $html = $c->render(false, false);
        $xml = simplexml_load_string($html);

        $cols = (array)$xml->xpath('/div/div/div/div[contains(@class, "column")]');
        $this->assertEquals('width: 522px', (string)$cols[0]['style']); //90%
        $this->assertEquals('width: 58px', (string)$cols[1]['style']);  //10%

        $cols = (array)$xml->xpath('/div/div/div/div[contains(@class, "column") and position()=1]//div[contains(@class, "column")]');
        $this->assertEquals(3, count($cols));
        $this->assertEquals('width: 337px', (string)$cols[0]['style']); //70%
        $this->assertEquals('width: 96px', (string)$cols[1]['style']); //20%
        $this->assertEquals('width: 49px', (string)$cols[2]['style']); //remainder (=10%)

        $cols = (array)$xml->xpath('/div/div/div/div[contains(@class, "column") and position()=2]//div[contains(@class, "column")]');
        $this->assertEquals(0, count($cols));
    }

    public function testChangeWidth1()
    {
        $this->_root->getComponentById('1')->render(true, false);

        $row = Kwf_Model_Abstract::getInstance('Kwc_Columns_ColumnsInColumns_Columns_ColumnsTestModel')
            ->getRow(1);
        $row->width = 190;
        $row->save();

        $this->_process();

        $html = $this->_root->getComponentById('1')->render(true, false);
        $xml = simplexml_load_string($html);
        $cols = (array)$xml->xpath('/div/div/div/div[contains(@class, "column")]');
        $this->assertEquals('width: 190px', (string)$cols[0]['style']);
        $this->assertEquals('width: 10px', (string)$cols[1]['style']);
    }

    public function testChangeWidth2()
    {
        $this->_root->getComponentById('2')->render(true, false);

        $row = Kwf_Model_Abstract::getInstance('Kwc_Columns_ColumnsInColumns_Columns_ColumnsTestModel')
            ->getRow(10);
        $row->width = 200;
        $row->save();

        $this->_process();

        $html = $this->_root->getComponentById('2')->render(true, false);
        $xml = simplexml_load_string($html);
        $cols = (array)$xml->xpath('/div/div/div/div[contains(@class, "column")]');
        $this->assertEquals('width: 200px', (string)$cols[0]['style']);

        $cols = (array)$xml->xpath('/div/div/div/div[contains(@class, "column") and position()=1]//div[contains(@class, "column")]');
        $this->assertEquals(3, count($cols));
        $this->assertEquals('width: 112px', (string)$cols[0]['style']); //70%
    }

    public function testBoxChangesContent()
    {
        $html = $this->_root->getComponentById('3')->render(true, false);
        $xml = simplexml_load_string($html);
        $cols = (array)$xml->xpath('/div/div/div/div[contains(@class, "column")]');
        $this->assertEquals('width: 400px', (string)$cols[0]['style']); //600-100 80%

        //box changes HasContent; 100px more available
        $row = Kwf_Model_Abstract::getInstance('Kwc_Columns_ColumnsInColumns_Box_TestModel')
            ->getRow('3-box');
        $row->content = '';
        $row->save();

        $this->_process();

        $html = $this->_root->getComponentById('3')->render(true, false);
        $xml = simplexml_load_string($html);
        $cols = (array)$xml->xpath('/div/div/div/div[contains(@class, "column")]');
        $this->assertEquals('width: 480px', (string)$cols[0]['style']);
    }

    public function testUniqueBoxChangesContent()
    {
        $html = $this->_root->getComponentById('3')->render(true, false);
        $xml = simplexml_load_string($html);
        $cols = (array)$xml->xpath('/div/div/div/div[contains(@class, "column")]');
        $this->assertEquals('width: 400px', (string)$cols[0]['style']); //600-100 80%

        //box changes HasContent; 50px less available
        $row = Kwf_Model_Abstract::getInstance('Kwc_Columns_ColumnsInColumns_Box_TestModel')
            ->createRow();
        $row->component_id = 'root-uniqueBox';
        $row->content = 'foo';
        $row->save();

        $this->_process();

        $html = $this->_root->getComponentById('3')->render(true, false);
        $xml = simplexml_load_string($html);
        $cols = (array)$xml->xpath('/div/div/div/div[contains(@class, "column")]');
        $this->assertEquals('width: 360px', (string)$cols[0]['style']); //-80% of 50px
        
    }
}
