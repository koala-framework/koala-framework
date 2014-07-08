<?php
class Kwc_FulltextSearch_Search_Directory_Admin extends Kwc_Directories_Item_Directory_Admin
{
    public function getDuplicateProgressSteps($source)
    {
        return 0;
    }

    public function duplicate($source, $target, Zend_ProgressBar $progressBar = null)
    {
    }
}
