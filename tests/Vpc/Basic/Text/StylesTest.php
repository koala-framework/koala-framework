<?php
/**
 * @group Vpc_Basic_Text
 * @group StylesModel
 */
class Vpc_Basic_Text_StylesTest extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Basic_Text_Root');
    }

    public function tearDown()
    {
        Vpc_Basic_Text_StylesModel::removeCache();
    }

    public function testStyles()
    {
        $model = Vps_Model_Abstract::getInstance('Vpc_Basic_Text_TestStylesModel');
        $styles = $model->getStyles();
        $this->assertEquals(array('p'=>trlVps('Default'), 'p.style2'=>'Test2', 'h1.style1'=>'Test1'), $styles['block']);
        $this->assertEquals(array('span'=>'Normal', 'span.style3'=>'Test3'), $styles['inline']);
    }

    public function testStylesContent()
    {
        $model = new Vpc_Basic_Text_TestStylesModel();
        Vpc_Basic_Text_TestStylesModel::removeCache();
        $content = $model->getStylesContents2();
        $this->assertEquals(".vpcText h1.style1 { font-weight: bold; font-size: 10px; text-align: center; } /* Test1 */
.vpcText p.style2 { font-size: 10px; color: #ff0000; } /* Test2 */
.vpcText span.style3 { font-size: 8px; color: #00ff00; } /* Test3 */\n", $content);
        Vpc_Basic_Text_TestStylesModel::removeCache();
    }
}
