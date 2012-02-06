<?php
class Kwc_List_Switch_ItemPageGenerator extends Kwf_Component_Generator_Page_Table
{
    public function getDuplicateProgressSteps($source)
    {
        return 0;
    }

    public function duplicateChild($source, $parentTarget, Zend_ProgressBar $progressBar = null)
    {
        return null;
    }
}
