<?php
/**
 * @group Kwf_Trl
 * @group Kwf_Trl_ChainedByMaster
 */
class Kwf_Trl_ChainedByMaster_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Trl_ChainedByMaster_Root');
    }

    public function testMenuForPage1()
    {
        $this->assertEquals('root-en_1', $this->_getChainedId('1'));
        $this->assertEquals('root-en_2', $this->_getChainedId('2'));
        $this->assertEquals('root-en', $this->_getChainedId('root-master'));
        $this->assertEquals(null, $this->_getChainedId('root'));
        $this->assertEquals('root-en_1-switchLanguage', $this->_getChainedId('1-switchLanguage'));
        $this->assertEquals('root-en_2-switchLanguage', $this->_getChainedId('2-switchLanguage'));
        $this->assertEquals('root-en-99', $this->_getChainedId('root-master-99'));
    }

    private function _getChainedId($masterId, $select = array())
    {
        $root = Kwf_Component_Data_Root::getInstance();
        $masterData = $root->getComponentById($masterId);
        $chainedData = $root->getComponentById('root-en');
        $component = Kwc_Chained_Trl_Component::getChainedByMaster($masterData, $chainedData, $select);
        if ($component) return $component->componentId;
        return null;
    }
}
