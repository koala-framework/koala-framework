<?php
/**
 * @author Dipl.-Ing. (FH) Martin Mayrhofer
 * @copyright 2007, Vivid Planet Software GmbH
 * @since 21.03.2007
 */
 
 class E3_Component_Events extends E3_Component_Abstract
 {
    private $_paragraphs;
 	protected function generateTreeHierarchy(E3_PageCollection_Tree $pageCollection, $filename)
    {
        $this->_paragraphs = array();
		for($i = 2000; $i <= 2007; $i++)
		{
            if ($filename != '' && $filename != $i) continue;

    		$this->_paragraphs[] = $this->createPageInTree($pageCollection, 'E3_Component_TextPic', $i, $this->getComponentId(), $i-1999);
		}
    }
 	
 	public function getTemplateVars()
 	{
 		$ret['years']= array(2000, 2001, 2002, 2003, 2004, 2005, 2006, 2007);
 		$ret['template'] = 'Events.html';
        return $ret;
 	}
    public function getComponentInfo()
    {
    	$info = parent::getComponentInfo();
    	foreach ($this->_paragraphs as $p) {
    		$info += $p->getComponentInfo();
    	}
    	return $info;
    }
}
 
