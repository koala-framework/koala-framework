<?php
class Vpc_Posts_Write_Preview_Component extends Vpc_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        if (isset($_POST['content'])) {
            $ret['content'] = Vpc_Posts_Post_Component::replaceCodes($_POST['content']);
        } else {
            $ret['content'] = '';
        }
        return $ret;
    }
}
