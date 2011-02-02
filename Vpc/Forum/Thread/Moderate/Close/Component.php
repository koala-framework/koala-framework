<?php
class Vpc_Forum_Thread_Moderate_Close_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['processInput'] = true;
        return $ret;
    }

    public function processInput($postData)
    {
        $row = $this->getData()->parent->parent->row;
        if ($this->getData()->getParentPage()->getComponent()->mayModerate()) {
            if (isset($postData['close'])) {
                $row->closed = $postData['close'];
                $row->save();
            }
        }
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        if ($this->isClosed()) {
            $ret['linkText'] = trlVps('Reopen Thread');
            $ret['url'] = $this->getData()->url . '?close=0';
        } else {
            $ret['linkText'] = trlVps('Close Thread');
            $ret['url'] = $this->getData()->url . '?close=1';
        }
        return $ret;
    }

    // wird zB in Vpc_Forum_Thread_Component aufgerufen
    public function isClosed()
    {
        return $this->getData()->parent->parent->row->closed;
    }

    public function getCacheMeta()
    {
        $ret = parent::getCacheMeta();
        $model = $this->getData()->parent->parent->row->getModel();
        $ret[] = new Vps_Component_Cache_Meta_Static_Model($row, '{component_id}-moderate-close');
        return $ret;
    }
}
