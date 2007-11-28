<?p
class Vps_Auto_Container_Column extends Vps_Auto_Container_Abstra

    public function getMetaData
   
        $ret = parent::getMetaData(
        if (!isset($ret['layout'])) $ret['layout'] = 'form
        if (!isset($ret['border'])) $ret['border'] = fals
        if (!isset($ret['baseCls'])) $ret['baseCls'] = 'x-plain
        return $re
   

