<?php
class Vpc_Forum_Thread_Moderate_Close_Component extends Vpc_Abstract
{
    private $_isClosed;

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
        $this->_isClosed = $row->closed == '1';
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
        $ret['closed'] = $this->isClosed();
        return $ret;
    }

    public function isClosed()
    {
        return $this->_isClosed;
    }

    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        $row = $this->getData()->parent->parent->row;
        $ret[] = array(
            'model' => get_class($row->getModel()->getTable()),
            'id' => $row->id
        );
        return $ret;
    }
}
