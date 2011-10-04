<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_Html_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Html'),
            'componentIcon' => new Vps_Asset('tag'),
            'ownModel'     => 'Vpc_Basic_Html_Model'
        ));
        $ret['flags']['searchContent'] = true;
        $ret['flags']['hasFulltext'] = true;
        return $ret;
    }

    private function _getContent()
    {
        $childComponents = array();
        foreach ($this->getData()->getChildComponents(array('generator' => 'child')) as $c) {
            $childComponents[$c->id] = $c;
        }

        $c = $this->_getRow()->content;
        preg_match_all('#{([a-z0-9]+)}#', $c, $m);
        if ($m[0]) {
            $helper = new Vps_Component_View_Helper_Component;
            foreach ($m[1] as $i) {
                if (isset($childComponents[$i]) && $childComponents[$i] instanceof Vps_Component_Data) {
                    $c = str_replace('{'.$i.'}', $helper->component($childComponents[$i]), $c);
                }
            }
        }
        return $c;
    }

    public function getExportData()
    {
        $ret = parent::getExportData();
        $ret['content'] = $this->_getContent();
        return $ret;
    }

    public function getTemplateVars()
    {
        if (Vpc_Abstract::hasSetting(get_class($this), 'default')) {
            throw new Vps_Exception("Setting 'default' doesn't exist anymore for ".get_class($this).", you need to overwrite the Model.");
        }
        $ret = parent::getTemplateVars();
        $ret['content'] = $this->_getContent();
        return $ret;
    }

    public function hasContent()
    {
        if (trim($this->_getRow()->content) != "") {
            return true;
        }
        return false;
    }

    public function getSearchContent()
    {
        return strip_tags($this->_getRow()->content);
    }


    public function modifyFulltextDocument(Zend_Search_Lucene_Document $doc)
    {
        $fieldName = $this->getData()->componentId;

        $text = strip_tags($this->_getRow()->content);

        $doc->getField('content')->value .= ' '.$text;

        $field = Zend_Search_Lucene_Field::UnStored($fieldName, $text, 'utf-8');
        $doc->addField($field);
    }
}
