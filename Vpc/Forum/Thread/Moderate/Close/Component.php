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

    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        $row = $this->getData()->parent->parent->row;
        $ret[] = array(
            'model' => get_class($row->getModel()),
            'id' => $row->id
        );
        return $ret;
    }
}
