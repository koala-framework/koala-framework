<?php
class Vpc_Advanced_SocialBookmarks_Trl_Component extends Vpc_Abstract
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        unset($ret['generators']['child']);
        $ret['generators']['socialBookmarks'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => $masterComponentClass,
        );
        $ret['editComponents'][] = 'socialBookmarks';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['socialBookmarks'] = $this->getData()->getChildComponent('-socialBookmarks');
        return $ret;
    }
}
