<?php
class Kwc_Trl_Table_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_Table_Root_Root');
    }

    public function testMasterReplaceModel()
    {
        $model = new Kwc_Trl_Table_Table_Trl_TestAdminModel(array(
            'proxyModel' => Kwf_Model_Abstract::getInstance('Kwc_Trl_Table_Table_MasterModel'),
            'trlModel' => Kwf_Model_Abstract::getInstance('Kwc_Trl_Table_Table_Trl_TrlModel'))
        );
        $select = new Kwf_Model_Select();
        $select->whereEquals('component_id', 'root-en_table');
        $count = $model->countRows();
        $this->assertEquals($count, 2);
        $rows = $model->getRows($select);
        $this->assertEquals(count($rows), 2);
        $count = 0;
        foreach ($rows as $row) {
            if ($count == 0) {
                $this->assertEquals($row->column1, 'Abc');
            } else if ($count == 1) {
                $this->assertEquals($row->column1, 'Abc');
            }
            $count++;
        }
    }

    public function testTrlTableRender()
    {
        $components = Kwf_Component_Data_Root::getInstance()->getChildComponents();
        $html = $components['root-master']->getChildComponent('_table')->render();
        $html2 = $components['root-en']->getChildComponent('_table')->render();
        $this->assertNotEquals($html, $html2);

        $this->markTestIncomplete('Regexp not correct defined');
        $this->assertRegExp("#.*<tr class=\"even \">.*<td class=\"col2\">4321</td>.*#", $html2);
    }
}
