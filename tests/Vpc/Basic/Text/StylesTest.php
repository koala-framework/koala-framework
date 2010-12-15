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
        Vps_Model_Abstract::getInstance('Vpc_Basic_Text_TestStylesModel')->removeCache();
        parent::tearDown();
    }

    public function testStyles()
    {
        $model = Vps_Model_Abstract::getInstance('Vpc_Basic_Text_TestStylesModel');
        $styles = $model->getStyles();
        $this->assertEquals(array(
            array(
                'id' => 'blockdefault',
                'name' => 'Default',
                'tagName' => 'p',
                'className' => false,
                'type' => 'block'
            ),
            array(
                'id' => 'inlinedefault',
                'name' => 'Normal',
                'tagName' => 'span',
                'className' => false,
                'type' => 'inline'
            ),
            array(
                'id' => 'style3',
                'name' => 'Test3',
                'tagName' => 'span',
                'className' => 'style3',
                'type' => 'inline'
            ),
            array(
                'id' => 'style2',
                'name' => 'Test2',
                'tagName' => 'p',
                'className' => 'style2',
                'type' => 'block'
            ),
            array(
                'id' => 'style1',
                'name' => 'Test1',
                'tagName' => 'h1',
                'className' => 'style1',
                'type' => 'block'
            ),
        ), $styles);
    }

    public function testStylesContent()
    {
        $model = new Vpc_Basic_Text_TestStylesModel();
        $model->removeCache();
        $content = $model->getStylesContents();
        $this->assertEquals(".vpcText h1.style1 { font-weight: bold; font-size: 10px; text-align: center; } /* Test1 */
.vpcText p.style2 { font-size: 10px; color: #ff0000; } /* Test2 */
.vpcText span.style3 { font-size: 8px; color: #00ff00; } /* Test3 */\n", $content);
        $model->removeCache();
    }
}
