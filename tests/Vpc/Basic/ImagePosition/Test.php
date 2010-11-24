<?php
/**
 * @group Basic_ImagePosition
 *
 * Testet vorallem das Vps_Component_FieldModel Model
 */
class Vpc_Basic_ImagePosition_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Basic_ImagePosition_Root');
    }

    public function testTemplateVars()
    {
        $c = $this->_root->getComponentById('1900');
        $vars = $c->getComponent()->getTemplateVars();
        $this->assertEquals('right', $vars['row']->image_position);
        $this->assertEquals('1900-image', $vars['image']->componentId);
    }

    public function testHtml()
    {
        $output = new Vps_Component_Output_NoCache();
        $html = $output->render($this->_root->getComponentById('1900'));
        $this->assertEquals("<div class=\"vpcBasicImagePosition vpcBasicImagePositionTestComponent\">\n".
            '    <div class="right"><div class="vpcBasicImagePositionImageTestComponent"><img src="/media/Vpc_Basic_ImagePosition_Image_TestComponent/1900-image/default/9fef39ee396927f548e7e79529f4e345/foo.png" width="16" height="16" alt="" class="" /></div></div>'.
            "\n</div>", $html);
    }
}
