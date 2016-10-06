<?php
class Kwc_Basic_LinkTag_ParentPage_Data extends Kwc_Basic_LinkTag_Intern_Data
{
    protected function _getData($select = array())
    {
        $pageParent = $this->getPage()->parent;
        if ($pageParent) {
            return $pageParent;
        }
        return false;
    }
}
