<?php
class Vpc_Forum_Thread_Moderate_Close_Component extends Vpc_Abstract
{
    private $_isClosed;
    
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
        if (is_null($this->_isClosed)) {
            $row = $this->getData()->parent->parent->parent->row;
            if (!is_null($this->_getParam('close'))) {
                $row->closed = $this->_getParam('close');
                $row->save();            
            }
            $this->_isClosed = $row->closed == '1';
        }
        return $this->_isClosed;
    }
}
