<?php
/**
 * @group Model
 * @group xmlModel
 */
class Vps_Model_Xml_ModelCacheTest extends Vps_Test_TestCase
{
    public function testXmlBasic()
    {
        $model = new Vps_Model_Xml(array(
            'xpath' => '/trl',
            'topNode' => 'text',
            'xmlContent' => '<trl><text><id>1</id><en>Visible</en><de>Sichtbar</de></text></trl>'
        ));
    }

}