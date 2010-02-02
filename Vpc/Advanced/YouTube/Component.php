<?php
class Vpc_Advanced_YouTube_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlVps('YouTube'),
            'componentIcon' => new Vps_Asset('film'),
            'ownModel'     => 'Vpc_Advanced_YouTube_Model'
        ));
        $ret['assets']['dep'][] = 'SwfObject';
        return $ret;
    }

    public function hasContent()
    {
        if ($this->getRow()->url) return true;
        return false;
    }
}
