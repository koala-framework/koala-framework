<?php
class Vpc_Forum_User_Detail_Component extends Vpc_User_Detail_Component  
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['generators']['child']['component']['avatar'] = 'Vpc_User_Detail_Avatar_Component';
        $ret['generators']['child']['component']['community'] = 'Vpc_User_Detail_Community_Component';
        $ret['generators']['child']['component']['rating'] = 'Vpc_Prohaustier_User_Detail_Rating_Component';
        return $ret;
    }
}