<?php
class Kwf_Component_FindHome_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_FindHome_Root_Component');
    }

    public function testFindHome()
    {
        $subroot = Kwf_Component_Data_Root::getInstance()->getChildComponent('-at');
        $home = Kwf_Component_Data_Root::getInstance()
                    ->getChildPage(array('home' => true, 'subroot'=>$subroot), array());
        $this->assertEquals($home->componentClass, 'Kwc_Basic_None_Component');
    }
}
