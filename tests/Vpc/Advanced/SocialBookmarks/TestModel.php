<?php
class Vpc_Advanced_SocialBookmarks_TestModel extends Vps_Model_FnF
{
    protected $_primaryKey = 'component_id';
    protected function _init()
    {
        $this->_data = array(
            array(
                'component_id'=>'root-socialBookmarks',
                'data'=>serialize(array(
                    'autoId' => 3,
                    'data' => array(
                        array('id'=>1, 'network_id'=>'facebook'),
                        array('id'=>2, 'network_id'=>'twitter'),
                    )
                ))
            )
        );

        $this->_dependentModels = array(
            'Networks' => new Vps_Model_FieldRows(array(
                'fieldName' => 'data'
            ))
        );
        parent::_init();
    }
}
