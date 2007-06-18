<?php
class Vpc_Events_Index extends Vpc_Abstract
{
    private $_paragraphs;

    protected function getChildPages($filename = '')
    {
        $components = array();
        for ($i = 2000; $i <= 2007; $i++) {
            if ($filename != '' && $filename != $i) continue;

            $component = $this->createComponent('Vpc_TextPic', 0, $i-1999);
            $components[$i] = $component;
        }
        $this->_paragraphs = $components;
        return $components;
    }
   
    public function getTemplateVars($mode)
    {
        $ret = parent::getTemplateVars($mode);
        $ret['years']= array(2000, 2001, 2002, 2003, 2004, 2005, 2006, 2007);
        $ret['template'] = 'Events.html';
        return $ret;
    }
    
}
 
