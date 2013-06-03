<?php
class Kwc_ColumnsResponsive_Basic_GeneratorTest extends Kwc_TestAbstract
{
    //also manual test: /kwf/kwctest/Kwc_ColumnsResponsive_Basic_Root/foo
    public function setUp()
    {
        parent::setUp('Kwc_ColumnsResponsive_Basic_Root');
    }

    public function testGetChildsFromParagraphs()
    {
        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentsBySameClass(
                'Kwc_ColumnsResponsive_Basic_Paragraphs_Component',
                array(
                    'id' => 1
                )
            );
        $this->assertEquals(0, count($components));
    }
}
