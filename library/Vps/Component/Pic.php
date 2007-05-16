<?php
class Vps_Component_Pic extends Vps_Component_Abstract
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
            $ret['uniqid'] = rand() . '.' . time();
        } else {
             $ret['template'] = 'Pic.html';
         }
        return $ret;
    }
    public function saveFrontendEditing(Zend_Controller_Request_Http $request)
    {
        if(isset($_FILES['upload']) && file_exists($_FILES['upload']['tmp_name'])) {
            move_uploaded_file($_FILES['upload']['tmp_name'], 'files/pics/'.$this->getId().'.jpg');
        }
        return parent::saveFrontendEditing($request);
    }

    public function getStatus()
    {    
        $upId = $_POST['progress_upload'];
    
        $ret =  new stdClass();
        
        $tmp = uploadprogress_get_info($upId);
        if (!is_array($tmp)) {
            sleep(1);
            $tmp = uploadprogress_get_info($upId);
            if (!is_array($tmp)) {
                $ret->message = "Upload Complete";
                $ret->percent = "100";
                $ret->done = "1";
                return $ret;
            }
        }
    
        if ($tmp['bytes_total'] < 1) {
            $percent = 100;
        }
        else {
            $percent = round($tmp['bytes_uploaded'] / $tmp['bytes_total'] * 100, 2);
        }
    
        if ($percent == 100) {
            $ret->message = "Complete";
            $ret->done = "1";
        }
    
        $ret->message = "Uploading...";
        $ret->percent = $percent;
    
        return $ret;
    }
  
    public function getFrontendEditingData()
    {
        return array();
    }
}
