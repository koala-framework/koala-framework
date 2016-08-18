<?php
class Kwc_Posts_Detail_Signature_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $userDir = Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass(
                'Kwc_User_Directory_Component',
                array('subroot' => $this->getData())
            );
        if ($userDir) {
            $ret['user'] = $userDir->getChildComponent('_'.$this->getData()->parent->row->user_id);
        } else {
            $ret['user'] = false;
        }
        return $ret;
    }
}
