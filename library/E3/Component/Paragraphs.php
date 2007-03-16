<?php
class E3_Component_Paragraphs extends E3_Component_Abstract
{
    protected $_paragraphs;

    public function getTemplateVars()
    {
        $this->_paragraphs = array();

        $rows = $this->_dao->getTable('E3_Dao_Paragraphs')
                ->fetchParagraphsByParentComponentId($this->getComponentId());

        $componentModel = $this->_dao->getTable('E3_Dao_Components');
        foreach($rows as $row) {
            $componentClass = $componentModel->getComponentClass($row->componentId);
            $this->_paragraphs[] = new $componentClass($row->componentId, $this->_dao);
        }

        $ret = array('paragraphs'=>array());
        foreach($this->_paragraphs as $paragraph) {
            $ret['paragraphs'][] = $paragraph->getTemplateVars();
        }
        $ret['template'] = 'Paragraphs.html';
        return $ret;
    }
}
