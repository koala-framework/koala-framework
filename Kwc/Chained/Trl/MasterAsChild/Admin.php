<?php
class Kwc_Chained_Trl_MasterAsChild_Admin extends Kwc_Abstract_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        $admin = Kwc_Admin::getInstance($data->chained->componentClass);
        return $admin->componentToString($data->getChildComponent('-child'));
    }

    public function exportContent(Kwf_Component_Data $cmp)
    {
        $admin = Kwc_Admin::getInstance($cmp->chained->componentClass);
        return $admin->exportContent($cmp->getChildComponent('-child'));
    }

    public function importContent(Kwf_Component_Data $cmp, $data)
    {
        $admin = Kwc_Admin::getInstance($cmp->chained->componentClass);
        return $admin->importContent($cmp->getChildComponent('-child'), $data);
    }
}
