<?php
/**
 * @group Advanced_GoogleMap
 */
class Vpc_Advanced_GoogleMapView_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Advanced_GoogleMapView_Root');
    }

    public function testNoCoordinatesOptionException()
    {
        $this->setExpectedException("Vps_Exception");
        $this->_root->getComponentById(2000)->getComponent()->getTemplateVars();
    }

    public function testEmptyCoordinates()
    {
        $c = $this->_root->getComponentById(2001)->getComponent();
        $this->assertFalse($c->hasContent());
        $vars = $c->getTemplateVars();
        $this->assertEquals('', $vars['options']['coordinates']);
    }

    public function testDefaultValues()
    {
        $c = $this->_root->getComponentById(2002)->getComponent();
        $this->assertTrue($c->hasContent());
        $vars = $c->getTemplateVars();
        $this->assertEquals('12,13', $vars['options']['coordinates']);
        $this->assertEquals('12', $vars['options']['latitude']);
        $this->assertEquals('13', $vars['options']['longitude']);
        $this->assertEquals('0', $vars['options']['zoom_properties']);
        $this->assertEquals('10', $vars['options']['zoom']);
        $this->assertEquals('1', $vars['options']['routing']);
    }

    public function testOwnOptions()
    {
        $c = $this->_root->getComponentById(2003)->getComponent();
        $vars = $c->getTemplateVars();
        $this->assertEquals('1', $vars['options']['zoom_properties']);
        $this->assertEquals('11', $vars['options']['zoom']);
        $this->assertEquals('0', $vars['options']['routing']);
    }

    public function testHtml()
    {
        $output = new Vps_Component_Output_NoCache();
        $html = $output->render($this->_root->getComponentById(2002));
        $this->assertContains('<div class="webStandard vpcAdvancedGoogleMapView vpcAdvancedGoogleMapViewTestComponent">', $html);

        $this->assertEquals(1, preg_match('#value="([^"]+)"#', $html, $m));
        $options = Zend_Json::decode((str_replace("'", '"', $m[1])));
        $this->assertNotNull($options);
        $this->assertEquals(13, $options['longitude']);
        $this->assertEquals(1, $options['routing']);
    }
}
