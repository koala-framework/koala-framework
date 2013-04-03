<?php
class Kwc_Trl_Table_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_Table_Root');
    }

    public function testMasterReplaceModel()
    {
        $model = new Kwc_Trl_Table_TestAdminModel(array(
            'proxyModel' => Kwf_Model_Abstract::getInstance('Kwc_Trl_Table_MasterModel'),
            'trlModel' => Kwf_Model_Abstract::getInstance('Kwc_Trl_Table_TrlModel'))
        );
        $select = new Kwf_Model_Select();
        $select->whereEquals('component_id', 'root-en');
        $count = $model->countRows();
        $this->assertEquals($count, 2);
        $rows = $model->getRows($select);
        $this->assertEquals(count($rows), 2);
        $count = 0;
        foreach ($rows as $row) {
            if ($count == 0) {
                $this->assertEquals($row->data, 'Daten aus Master');
            } else if ($count == 1) {
                $this->assertEquals($row->data, 'Daten aus Trl');
            }
            $count++;
        }
    }

    public function testTrlTableRender()
    {
        $components = Kwf_Component_Data_Root::getInstance()->getChildComponents();
        d($components['root-master']->getChildComponent('_table')->render());
        d($components['root-en']->getChildComponent('_table')->render());
    }
}
