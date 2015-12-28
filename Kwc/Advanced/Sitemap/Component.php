<?php
class Kwc_Advanced_Sitemap_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Sitemap');
        $ret['componentIcon'] = 'sitemap_color.png';
        $ret['rootElementClass'] = 'kwfUp-webStandard';
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['levels'] = $this->getRow()->levels;
        $ret['target'] = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->getRow()->target, array('limit'=>1));
        $ret['listHtml'] = '';
        if ($ret['target']) {
            $ret['listHtml'] = self::getListHtml($renderer, $ret['target'], 0, $ret['levels']);
        }
        return $ret;
    }

    //not in template for easier recursion
    // public because for trl
    public static function getListHtml(Kwf_Component_Renderer_Abstract $renderer, Kwf_Component_Data $c, $level, $levels)
    {
        $ret = '';
        $level++;
        $select = new Kwf_Component_Select();
        $select->whereShowInMenu(true);
        $ret .= "<ul>\n";
        foreach ($c->getChildPages($select) as $child) {
            $noChild = '';
            if (!$child->getChildPages($select)) {
                $noChild = 'noChild';
            }
            $ret .= '<li class="' . $noChild . '">';
            $helper = new Kwf_Component_View_Helper_ComponentLink();
            $helper->setRenderer($renderer);
            $ret .= $helper->componentLink($child);
            $ret .= "\n";
            if ($level < $levels) {
                $ret .= self::getListHtml($renderer, $child, $level, $levels);
            }
            $ret .= "</li>\n";
        }
        $ret .= "</ul>\n";
        return $ret;
    }
}
