<?php
class Kwc_Menu_BreadCrumbs_Cc_Component extends Kwc_Chained_Cc_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $links = array();
        foreach ($ret['links'] as $m) {
            $links[] = self::getChainedByMaster($m, $this->getData(), array('ignoreVisible' => true));
        }
        $ret['links'] = $links;

        $ret['items'] = array();
        $i = 0;
        foreach ($ret['links'] as $l) {
            $class = '';
            if ($i == 0) $class .= 'first ';
            if ($i == count($ret['links'])-1) {
                $class .= 'last ';
            }
            $ret['items'][] = array(
                'data' => $l,
                'class' => trim($class),
                'last' => $i == count($ret['links'])-1
            );
            $i++;
        }
        return $ret;
    }
}
