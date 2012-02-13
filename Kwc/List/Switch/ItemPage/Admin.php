<?php
class Kwc_List_Switch_ItemPage_Admin extends Kwc_Abstract_Admin
{
    public function getDuplicateProgressSteps($source)
    {
        return 0;
    }

    public function duplicate($source, $target, Zend_ProgressBar $progressBar = null)
    {
    }
}
