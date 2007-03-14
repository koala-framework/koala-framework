<?php
class E3_Component_Paragraphs extends E3_Component_Abstract
{
    protected $_paragraphs;
    function __construct($componentId, E3_Dao $dao)
    {
        parent::__construct($componentId, $dao);
        $this->_paragraphs = array();

        $rows = $dao->getTable('E3_Dao_Paragraphs')
                ->fetchParagraphsByParentComponentId($this->getComponentId());

        $componentModel = $dao->getTable('E3_Dao_Components');
        foreach($rows as $row) {
            $componentClass = $componentModel->getComponentClass($row->componentId);
            $this->_paragraphs[] = new $componentClass($row->componentId, $dao);
        }
    }

    public function getTemplateVars()
    {
        $ret = array('paragraphs'=>array());
        foreach($this->_paragraphs as $paragraph) {
            $ret['paragraphs'][] = $paragraph->getTemplateVars();
        }
        $ret['template'] = 'Paragraphs.html';
        return $ret;
    }
}
