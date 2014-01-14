<?php
class Kwc_Columns_Basic_GeneratorTest extends Kwc_TestAbstract
{
    //also manual test: /kwf/kwctest/Kwc_Columns_Basic_Root/foo
    public function setUp()
    {
        parent::setUp('Kwc_Columns_Basic_Root');
    }

    public function testGetChildsFromParagraphs()
    {
        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentsBySameClass(
                'Kwc_Columns_Basic_Paragraphs_Component',
                array(
                    'id' => 1
                )
            );
        $this->assertEquals(0, count($components));
    }
}
