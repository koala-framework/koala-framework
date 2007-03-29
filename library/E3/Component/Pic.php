<?php
class E3_Component_Pic extends E3_Component_Abstract
{
    public function getTemplateVars($mode)
    {
        $ret = parent::getTemplateVars($mode);
        $ret['pic'] = 'files/pics/'.$this->getId().'.jpg';
        if(!file_exists($ret['pic'])) {
            $ret['pic'] = false;
        }
        if ($mode == 'edit') {
            $ret['template'] = dirname(__FILE__).'/Pic.html';
        } else {
       	    $ret['template'] = 'Pic.html';
       	}

        return $ret;
    }
    public function saveFrontendEditing()
    {
        move_uploaded_file($_FILES['content']['tmp_name'], 'files/pics/'.$this->getId().'.jpg');
    }
}
