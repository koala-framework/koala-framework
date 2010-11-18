<?php
class Vpc_Basic_LinkTag_ParentPage_Data extends Vpc_Basic_LinkTag_Intern_Data
{
    protected function _getData()
    {
        $pageParent = $this->getPage()->parent;
        if ($pageParent) {
            return $pageParent;
        }
        return false;
    }
}
