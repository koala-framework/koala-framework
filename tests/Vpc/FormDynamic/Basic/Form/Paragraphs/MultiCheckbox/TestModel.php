<?php
class Vpc_FormDynamic_Basic_Form_Paragraphs_MultiCheckbox_TestModel extends Vpc_Form_Field_MultiCheckbox_Model
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'data' => array(
                array('component_id'=>'root_form3-paragraphs-9', 'field_label'=>'Label', 'values'=>serialize(array(
                    'data' => array(
                        array('id'=>1, 'value'=>'foobar', 'pos'=>1),
                        array('id'=>2, 'value'=>'foobar1', 'pos'=>2),
                        array('id'=>3, 'value'=>'foobar2', 'pos'=>3),
                    ),
                    'autoId' => 3
                ))),
                array('component_id'=>'root_form3-paragraphs-10', 'field_label'=>'Required', 'required'=>true, 'values'=>serialize(array(
                    'data' => array(
                        array('id'=>1, 'value'=>'foobarx', 'pos'=>1),
                        array('id'=>2, 'value'=>'foobarx1', 'pos'=>2),
                        array('id'=>3, 'value'=>'foobarx2', 'pos'=>3),
                    ),
                    'autoId' => 3
                ))),
            ),
            'columns' => array(),
            'primaryKey' => 'component_id',
        ));
        parent::__construct($config);
    }
}
