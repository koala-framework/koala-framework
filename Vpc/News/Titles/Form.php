<?php
class Vpc_News_Titles_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        // model besser wenn nicht hardcoded
        $newsModel = new Vpc_News_Model(array('componentClass' => 'Vpc_News_Component'));
        $sqlTableName = $newsModel->info();
        $sqlTableName = $sqlTableName['name'];

        $select = $newsModel->getAdapter()->select();
        $select->from($sqlTableName, 'component_id');
        $select->group('component_id');
        $select->order('component_id ASC');

        $rowset = $newsModel->getAdapter()->fetchAll($select);

        $selectValues = array();
        foreach ($rowset as $row) {
            $row = (object)$row;
            $row = $newsModel->fetchRow(
                array('component_id = ?' => $row->component_id),
                'id DESC'
            );

            $selectValues[$row->component_id] = $row->component_id
                .' (z.B.: '.$row->title.')';
        }

        $this->fields->add(new Vps_Form_Field_Select('news_component_id', 'Component Id'))
            ->setValues($selectValues)
            ->setWidth(400);
    }
}
