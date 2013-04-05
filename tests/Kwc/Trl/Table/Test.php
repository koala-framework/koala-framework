<?php
/**
 * @group Table
 */
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
                $this->assertEquals($row->getFrontend('column1'), 'Abc');
                $this->assertEquals($row->column1, '');
            } else if ($count == 1) {
                $this->assertEquals($row->column1, 'Abc');
                $this->assertEquals($row->getFrontend('column1'), 'Abc');
            }
            $count++;
        }
    }

    public function testSaveTrl()
    {
        $model = new Kwc_Trl_Table_Table_Trl_TestAdminModel(array(
            'proxyModel' => Kwf_Model_Abstract::getInstance('Kwc_Trl_Table_Table_MasterModel'),
            'trlModel' => Kwf_Model_Abstract::getInstance('Kwc_Trl_Table_Table_Trl_TrlModel'))
        );
        $id = 1;
        $select = new Kwf_Model_Select();
        $select->whereEquals('component_id', 'root-en_table');
        $select->whereEquals('id', $id);
        $row = $model->getRow($select);
        $row->column2 = 4312;
        $row->save();

        $masterModel = Kwf_Model_Abstract::getInstance('Kwc_Trl_Table_Table_MasterModel');
        $masterRow = $masterModel->getRow($id);
        //check if changed trl-column different than master-column
        $this->assertNotEquals($row->getFrontend('column2'), $masterRow->column2);
        //check if other trl-column equal than master-column
        $this->assertEquals($row->getFrontend('column3'), $masterRow->column3);

        $trlRow = $model->getRow($select);
        //check if changed trl-column is really saved
        $this->assertEquals($row->getFrontend('column2'), $trlRow->getFrontend('column2'));
        //check if other trl-column are really unchanged
        $this->assertEquals($row->getFrontend('column3'), $trlRow->getFrontend('column3'));

        $masterRow->column3 = 123;
        $masterRow->save();
        $trlRow2 = $model->getRow($select);
        //check if changed trl-column is still changed
        $this->assertEquals($trlRow2->getFrontend('column2'), $trlRow->getFrontend('column2'));
        $this->assertNotEquals($trlRow2->getFrontend('column2'), $masterRow->column2);
        //check if unchanged trl-column always same to master
        $this->assertEquals($masterRow->column3, $trlRow2->getFrontend('column3'));
    }

    public function testTrlTableRender()
    {
        $components = Kwf_Component_Data_Root::getInstance()->getChildComponents();
        $html = $components['root-master']->getChildComponent('_table')->render();
        $html2 = $components['root-en']->getChildComponent('_table')->render();
        $this->assertNotEquals($html, $html2);
        $this->assertRegExp("#.*<td class=\"col2\">4321</td>.*#", $html2);
    }

    public function testTrlTableCache()
    {
        $components = Kwf_Component_Data_Root::getInstance()->getChildComponents();
        $html = $components['root-master']->getChildComponent('_table')->render();
        $html2 = $components['root-de']->getChildComponent('_table')->render();
        preg_match_all("#.*<td class=\"col1\">.*</td>.*#", $html2, $matches);
        $this->assertEquals(count($matches[0]), 0);


        $model = new Kwc_Trl_Table_Table_Trl_TestAdminModel(array(
            'proxyModel' => Kwf_Model_Abstract::getInstance('Kwc_Trl_Table_Table_MasterModel'),
            'trlModel' => Kwf_Model_Abstract::getInstance('Kwc_Trl_Table_Table_Trl_TrlModel'))
        );
        $id = 1;
        $select = new Kwf_Model_Select();
        $select->whereEquals('component_id', 'root-de_table');
        $select->whereEquals('id', $id);
        $row = $model->getRow($select);
        $row->column2 = 4312;
        $row->visible = 1;
        $row->save();

        //check cache after create trl-data
        $this->_process();
        $html2 = $components['root-de']->getChildComponent('_table')->render();
        preg_match_all("#.*<td class=\"col1 first\">.*</td>.*#", $html2, $matches);
        $this->assertEquals(count($matches[0]), 1);
        //check for 4312-value and first master-data
        $this->assertRegExp("#.*<td class=\"col2\">4312</td>.*#", $html2);
        $this->assertRegExp("#.*<td class=\"col1 first\">Abc</td>.*#", $html2);

        $masterModel = Kwf_Model_Abstract::getInstance('Kwc_Trl_Table_Table_MasterModel');
        $masterRow = $masterModel->getRow($id);
        $masterRow->column3 = 123;
        $masterRow->save();
        //check cache after master-data change
        $this->_process();
        $html2 = $components['root-de']->getChildComponent('_table')->render();
        preg_match_all("#.*<td class=\"col1 first\">.*</td>.*#", $html2, $matches);
        $this->assertEquals(count($matches[0]), 1);
        //check for 4312-value and first master-data and changed master-data
        $this->assertRegExp("#.*<td class=\"col2\">4312</td>.*#", $html2);
        $this->assertRegExp("#.*<td class=\"col1 first\">Abc</td>.*#", $html2);
        $this->assertRegExp("#.*<td class=\"col3 last\">123</td>.*#", $html2);


        $row = $model->getRow($select);
        $row->column2 = 'abcdef';
        $row->save();
        //check cache after trl-data change
        $this->_process();
        $html2 = $components['root-de']->getChildComponent('_table')->render();
        preg_match_all("#.*<td class=\"col1 first\">.*</td>.*#", $html2, $matches);
        $this->assertEquals(count($matches[0]), 1);
        //check for abcdef-value and first master-data
        $this->assertRegExp("#.*<td class=\"col2\">abcdef</td>.*#", $html2);
        $this->assertRegExp("#.*<td class=\"col1 first\">Abc</td>.*#", $html2);
    }
}
