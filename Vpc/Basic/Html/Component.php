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
            'modelname'     => 'Vpc_Basic_Html_Model'
        ));
        $ret['flags']['searchContent'] = true;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $c = $this->_getRow()->content;
        preg_match_all('#{([a-z0-9]+)}#', $c, $m);
        if ($m[0]) {
            $helper = new Vps_View_Helper_Component;
            foreach ($m[1] as $i) {
                if (isset($ret[$i]) && $ret[$i] instanceof Vps_Component_Data) {
                    $c = str_replace('{'.$i.'}', $helper->component($ret[$i]), $c);
                }
            }
        }
        $ret['content'] = $c;
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
}
