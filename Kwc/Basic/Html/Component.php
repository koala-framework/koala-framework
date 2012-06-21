<?php
/**
 * @package Kwc
 * @subpackage Basic
 */
class Kwc_Basic_Html_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlKwfStatic('Html'),
            'componentIcon' => new Kwf_Asset('tag'),
            'ownModel'     => 'Kwc_Basic_Html_Model'
        ));
        $ret['flags']['searchContent'] = true;
        $ret['flags']['hasFulltext'] = true;
        $ret['throwHasContentChangedOnRowColumnsUpdate'] = 'content';
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
            $helper = new Kwf_Component_View_Helper_Component;
            foreach ($m[1] as $i) {
                if (isset($childComponents[$i]) && $childComponents[$i] instanceof Kwf_Component_Data) {
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
        $ret = parent::getTemplateVars();
        $ret['content'] = $this->_getContent();
        return $ret;
    }

    public function hasContent()
    {
        return trim($this->getRow()->content) != '';
    }

    public function getSearchContent()
    {
        return strip_tags($this->_getRow()->content);
    }


    public function modifyFulltextDocument(Zend_Search_Lucene_Document $doc)
    {
        $text = strip_tags($this->_getRow()->content);

        $doc->getField('content')->value .= ' '.$text;
        $doc->getField('normalContent')->value .= ' '.$text;

        return $doc;
    }
}
