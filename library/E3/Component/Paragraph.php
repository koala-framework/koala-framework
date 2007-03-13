<?php
class E3_Component_Paragraph extends E3_Component_Abstract
{
    protected $_paragraphs;
    function __construct($componentId, $dao)
    {
        parent::__construct($componentId, $dao);
        $this->_paragraphs = array();

        $rows = $dao->getModel('E3_Model_Paragraphs')
                ->fetchParagraphsByParentComponentId($this->getComponentId());

        $componentModel = $dao->getModel('E3_Model_Components');
        foreach($rows as $row) {
            $componentClass = $componentModel->getComponentClass($row->componentId);
            $this->_paragraphs[] = new $componentClass($row->componentId, $dao);
        }
    }

    public function getTemplateVars()
    {
        $ret = array();
        foreach($this->_paragraphs as $paragraph) {
            $ret[] = $paragraph->getTemplateVars();
        }
        return array("paragraphs" => $ret);
    }
}
