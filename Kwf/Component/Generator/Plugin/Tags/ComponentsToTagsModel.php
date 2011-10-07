<?php
class Vps_Component_Generator_Plugin_Tags_ComponentsToTagsModel extends Vps_Model_Db
{
    protected $_table = 'vpc_components_to_tags';
    protected $_referenceMap = array(
        'Tag' => array(
            'refModelClass' => 'Vps_Component_Generator_Plugin_Tags_TagsModel',
            'column' => 'tag_id'
        ),
        'Component' => array(
            'refModelClass' => 'Vps_Component_Model', //ungetestet
            'column' => 'component_id'
        )
    );

    protected function _init()
    {
        parent::_init();
        $this->_exprs['tag_text'] = new Vps_Model_Select_Expr_Parent('Tag', 'text');
    }
}
