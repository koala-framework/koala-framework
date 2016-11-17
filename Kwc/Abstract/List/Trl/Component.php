<?php
class Kwc_Abstract_List_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['componentIcon'] = 'page';
        $ret['generators']['child']['class'] = 'Kwc_Abstract_List_Trl_Generator';
        $ret['childModel'] = 'Kwc_Abstract_List_Trl_Model';

        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Abstract/List/Trl/FullSizeEditPanel.js';
        $ret['assetsAdmin']['dep'][] = 'KwfAutoGrid';
        $ret['assetsAdmin']['dep'][] = 'KwfComponent';

        $ret['extConfig'] = 'Kwc_Abstract_List_Trl_ExtConfigList';

        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $children = $this->getData()->getChildComponents($this->getData()->chained->getComponent()->getSelect());

        // children ist die alte methode, bleibt drin wegen kompatibilität
        $ret['children'] = $children;

        // das hier ist die neue variante und ist besser, weil man leichter mehr daten
        // zurückgeben kann, bzw. in der übersetzung überschreiben kann
        // zB: Breite bei übersetzung von Columns
        $ret['listItems'] = array();
        $i = 0;
        foreach ($children as $child) {
            $class = $this->_getBemClass('listItem', 'listItem').' ';
            if ($i == 0) $class .= ' '.$this->_getBemClass('listItem--first', 'kwcFirst');
            if ($i == count($children)-1) $class .= ' '.$this->_getBemClass('listItem--last', 'kwcLast');
            if ($i % 2 == 0) {
                $class .= ' '.$this->_getBemClass('listItem--even', 'kwcEven');
            } else {
                $class .= ' '.$this->_getBemClass('listItem--odd', 'kwcOdd');
            }
            $class = trim($class);
            $i++;

            $preHtml = '';
            $postHtml = '';
            foreach (Kwf_Component_Data_Root::getInstance()->getPlugins('Kwf_Component_PluginRoot_Interface_MaskComponent') as $plugin) {
                $mask = $plugin->getMaskCode($child->chained);
                $preHtml = $mask['begin'] . $preHtml;
                $postHtml = $postHtml . $mask['end'];
            }

            $ret['listItems'][] = array(
                'data' => $this->getData()->chained->getComponent()->getItemComponent($child),
                'class' => $class,
                'style' => '',
                'preHtml' => $preHtml,
                'postHtml' => $postHtml
            );
        }
        return $ret;
    }

    public function hasContent()
    {
        $childComponents = $this->getData()->getChildComponents(array('generator' => 'child'));
        foreach ($childComponents as $c) {
            if ($c->hasContent()) return true;
        }
        return false;
    }

    public function getExportData()
    {
        $ret = array('list' => array());
        $children = $this->getData()->getChildComponents(array('generator' => 'child'));
        foreach ($children as $child) {
            $ret['list'][] = $child->getComponent()->getExportData();
        }
        return $ret;
    }
}
