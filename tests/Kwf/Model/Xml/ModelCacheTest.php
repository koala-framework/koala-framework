<?php
/**
 * @group Model
 * @group xmlModel
 */
class Kwf_Model_Xml_ModelCacheTest extends Kwf_Test_TestCase
{
    public function testXmlBasic()
    {
        $model = new Kwf_Model_Xml(array(
            'xpath' => '/trl',
            'topNode' => 'text',
            'xmlContent' => '<trl><text><id>1</id><en>Visible</en><de>Sichtbar</de></text></trl>'
        ));
    }

}