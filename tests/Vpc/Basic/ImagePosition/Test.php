<?php
/**
 * @group Basic_ImagePosition
 * @group Image
 *
 * Testet vorallem das Vps_Component_FieldModel Model
 */
class Vpc_Basic_ImagePosition_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Basic_ImagePosition_Root');
        $this->_root->setFilename(null);
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
        $output = new Vps_Component_View();
        $html = $output->render($this->_root->getComponentById('1900'));
        $this->assertRegExp('#^\s*<div class="vpcBasicImagePosition vpcBasicImagePositionTestComponent">'.
            '\s*<div class="posright">'
            .'\s*<div class="vpcBasicImagePositionImageTestComponent">'
            .'\s*<img src="/media/Vpc_Basic_ImagePosition_Image_TestComponent/1900-image/default/9fef39ee396927f548e7e79529f4e345/[0-9]+/foo.png" width="16" height="16" alt="" class="" />'
            .'\s*</div>\s*</div>'.
            '\s*</div>\s*$#ms', $html);
    }
}
