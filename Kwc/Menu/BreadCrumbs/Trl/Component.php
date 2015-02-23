<?php
class Kwc_Menu_BreadCrumbs_Trl_Component extends Kwc_Menu_Abstract_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $links = array();
        foreach ($ret['links'] as $m) {
            $links[] = self::getChainedByMaster($m, $this->getData());
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
