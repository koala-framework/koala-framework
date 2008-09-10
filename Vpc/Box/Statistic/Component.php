<?php
class Vpc_Box_Statistic_Component extends Vpc_Abstract
{
    protected function _getStatisticVars()
    {
        return array();
    }
    
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $dbname = Zend_Registry::get('config')->service->users->webcode;
        if (!$dbname) {
            $dbname = Zend_Registry::get('config')->application->name;
        }
        $statistic = array();
        foreach ($this->_getStatisticVars() as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $k => $v) {
                    unset($val[$k]);
                    $val['D_' . $k] = $v;
                }
                $statistic[$dbname . '$' . $key] = $val;
            } else {
                $statistic[$dbname . '$temp']['D_' . $key] = $val;
            }
        }
        foreach ($statistic as $temptable => $vars) {
            $jsonvars = array();
            foreach ($vars as $k => $v) {
                $jsonvars[] = "'$k': '$v'";
            }
            $statistic[$temptable] = $jsonvars;
        }
        $ret['statistic'] = $statistic;
        $ret['domain'] = Zend_Registry::get('config')->statistic->domain;
        
        return $ret;
    }
}
