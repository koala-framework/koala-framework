<?php
/**
 * @group Kwc_Basic_Text
 * @group StylesModel
 */
class Kwc_Basic_Text_StylesTest extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_Text_Root');
    }

    public function tearDown()
    {
        Kwf_Model_Abstract::getInstance('Kwc_Basic_Text_TestStylesModel')->removeCache();
        parent::tearDown();
    }

    public function testStyles()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwc_Basic_Text_TestStylesModel');
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
        $model = new Kwc_Basic_Text_TestStylesModel();
        $model->removeCache();
        $content = $model->getStylesContents2();
        $this->assertEquals(".kwcText h1.style1 { font-weight: bold; font-size: 10px; text-align: center; } /* Test1 */
.kwcText p.style2 { font-size: 10px; color: #ff0000; } /* Test2 */
.kwcText span.style3 { font-size: 8px; color: #00ff00; } /* Test3 */\n", $content);
        $model->removeCache();
    }
}
