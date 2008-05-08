<?php
class Vpc_Master_Statistic_Component extends Vpc_Master_Abstract
{
    public static function getSettings()
    {
        return parent::getSettings();
    }
    
    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $statistic = array();
        $dbname = Zend_Registry::get('config')->service->users->webcode;
        if (!$dbname) {
            $dbname = Zend_Registry::get('config')->application->name;
        }
        $domain = Zend_Registry::get('config')->statistic->domain;
        foreach ($this->_component->getStatisticVars() as $key => $val) {
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
        $src = '';
        if (!empty($statistic)) {
            $src  = '<script type="text/javascript"><!--' . "\n";
            $src .= 'if (typeof count != \'undefined\') {' . "\n";
            foreach ($statistic as $temptable => $vars) {
                $jsonvars = array();
                foreach ($vars as $k => $v) {
                    $jsonvars[] = "'$k': '$v'";
                }
                $src .= "count('$temptable', {" . implode(', ', $jsonvars) . "}, '$domain');\n";
            }
            $src .= "}\n//--></script>\n";
        }
        $return['statistic'] = $src;
        
        return $return;
    }
    
}
