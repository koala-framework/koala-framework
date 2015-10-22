<?php
class Kwc_Columns_Trl_Component extends Kwc_Abstract_List_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['child']['class'] = 'Kwc_Chained_Abstract_Generator';
        unset($ret['childModel']);
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $columnTypes = Kwc_Abstract::getSetting($this->getData()->chained->componentClass, 'columns');
        $type = $ret['row']->type;
        if (!$type) {
            //default is first
            $type = array_shift(array_keys($columnTypes));
        }
        $columns = $columnTypes[$type];

        $i = 1;
        foreach($ret['listItems'] as $key => $value) {
            $cls = " span{$columns['colSpans'][$i-1]}";
            if ($i == 1) $cls .= " lineFirst";
            if ($i == count($columns['colSpans'])) $cls .= " lineLast";
            $ret['listItems'][$key]['class'] .= $cls;
            ($i == count($columns['colSpans'])) ? $i = 1 : $i++;
        }
        return $ret;
    }
}
