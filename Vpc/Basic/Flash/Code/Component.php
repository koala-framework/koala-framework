<?php
class Vpc_Basic_Flash_Code_Component extends Vpc_Abstract_Flash_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Flash.Code');
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        $ret['extConfig'] = 'Vps_Component_Abstract_ExtConfig_Form';
        return $ret;
    }

    private function _parseCode()
    {
        $row = $this->getRow();
        $vars = array();
        $params = array();
        $objectParams = array();
        $url = false;
        if (preg_match('#<object([^>]*)>(.+?)</object>#is', $row->code, $m)) {
            preg_match_all('#\s+([a-z0-9]+)=("[^"]*"|\'[^\']*\')#i', $m[1], $m2);
            foreach ($m2[1] as $k=>$name) {
                $objectParams[$name] = substr($m2[2][$k], 1, -1);
            }
            preg_match_all('#<param\s+name\s*=\s*("[^"]*"|\'[^\']*\')\s+value=("[^"]*"|\'[^\']*\')\s*>#is', $m[2], $m2);
            foreach ($m2[1] as $k=>$name) {
                $name = urldecode(substr($name, 1, -1));
                $value = urldecode(substr($m2[2][$k], 1, -1));
                if ($name == 'movie') {
                    $value =  parse_url($value);
                    if ($query = $value['query']) {
                        parse_str($query, $vars);
                    }
                    $url = $value['scheme'].'://'.$value['host'].$value['path'];
                } else {
                    $params[$name] = $value;
                }
            }
        }
        return array(
            'url' => $url,
            'vars' => $vars,
            'params' => $params,
            'objectParams' => $objectParams
        );
    }

    protected function _getFlashVars()
    {
        $c = $this->_parseCode();
        return $c['vars'];
    }

    protected function _getFlashData()
    {
        $ret = array();
        $c = $this->_parseCode();
        $ret['url'] = $c['url'];
        if (isset($c['objectParams']['width'])) $ret['width'] = $c['objectParams']['width'];
        if (isset($c['objectParams']['height'])) $ret['height'] = $c['objectParams']['height'];
        $ret['params'] = $c['params'];
        return $ret;
    }


    public function hasContent()
    {
        $c = $this->_parseCode();
        return !!$c['url'];
    }
}
