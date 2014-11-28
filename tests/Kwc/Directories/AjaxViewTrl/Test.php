<?php
class Kwc_Directories_AjaxViewTrl_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Directories_AjaxViewTrl_Root_Component');
    }

    public function testSerializeDe()
    {
        $c = $this->_root->getComponentById('root-master_directory_1');
        $s = $c->kwfSerialize();
        unset($c);
        Kwf_Component_Data_Root::reset();
        $c = Kwf_Component_Data::kwfUnserialize($s);
        $this->assertEquals($c->componentId, 'root-master_directory_1');
    }

    public function testSerializeEn()
    {
        $c = $this->_root->getComponentById('root-en_directory_1');
        $s = $c->kwfSerialize();
        unset($c);
        Kwf_Component_Data_Root::reset();
        $c = Kwf_Component_Data::kwfUnserialize($s);
        $this->assertEquals($c->componentId, 'root-en_directory_1');
    }
}
