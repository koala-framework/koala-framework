<?php
class Kwc_Tabs_Admin extends Kwc_Abstract_Admin
{
    public function exportContent(Kwf_Component_Data $cmp)
    {
        $ret = array();
        $vars = $cmp->getComponent()->getTemplateVars();
        foreach($vars['listItems'] as $k => $v) {
            $row = $v['data']->row;
            $ret['title_' . $row->id] = $row->title;
        }
        return $ret;
    }

    public function importContent(Kwf_Component_Data $cmp, $data)
    {
        $vars = $cmp->getComponent()->getTemplateVars();
        foreach($vars['listItems'] as $k => $v) {
            $row = $v['data']->row;
            if (isset($data['title_' . $row->id])) {
                foreach ($data['title_' . $row->id] as $k => $v) {
                    $row->$k = $v;
                }
                $row->save();
            }
        }
    }

}
