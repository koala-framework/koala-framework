<?php
class Kwf_Component_Generator_Plugin_Tags_ComponentsToTagsModel extends Kwf_Model_Db
{
    protected $_table = 'kwc_components_to_tags';
    protected $_referenceMap = array(
        'Tag' => array(
            'refModelClass' => 'Kwf_Component_Generator_Plugin_Tags_TagsModel',
            'column' => 'tag_id'
        ),
        'Component' => array(
            'refModelClass' => 'Kwf_Component_Model', //ungetestet
            'column' => 'component_id'
        )
    );

    protected function _init()
    {
        parent::_init();
        $this->_exprs['tag_text'] = new Kwf_Model_Select_Expr_Parent('Tag', 'text');
    }
}
