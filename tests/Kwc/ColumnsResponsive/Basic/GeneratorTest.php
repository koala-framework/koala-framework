<?php
class Kwc_ColumnsResponsive_Basic_GeneratorTest extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_ColumnsResponsive_Basic_Root');
    }

    public function testGetChildsFromParagraphs()
    {
        $this->markTestIncomplete();
        $components = Kwf_Component_Data_Root::getInstance()->getComponentsBySameClass('Kwc_ColumnsResponsive_Basic_Paragraphs_Component');
        $this->assertEquals('Kwc_ColumnsResponsive_Basic_Paragraphs_Component', $components[0]);
    }
}
