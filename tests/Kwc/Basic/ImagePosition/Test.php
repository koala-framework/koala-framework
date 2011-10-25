<?php
/**
 * @group Basic_ImagePosition
 * @group Image
 *
 * Testet vorallem das Kwf_Component_FieldModel Model
 */
class Kwc_Basic_ImagePosition_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_ImagePosition_Root');
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
        $html = $this->_root->getComponentById('1900')->render();
        $this->assertRegExp('#^\s*<div class="kwcBasicImagePosition kwcBasicImagePositionTestComponent">'.
            '\s*<div class="posright">'
            .'\s*<div class="kwcBasicImagePositionImageTestComponent">'
            .'\s*<img src="/media/Kwc_Basic_ImagePosition_Image_TestComponent/1900-image/default/[^/]+/[0-9]+/foo.png" width="16" height="16" alt="" />'
            .'\s*</div>\s*</div>'.
            '\s*</div>\s*$#ms', $html);
    }
}
