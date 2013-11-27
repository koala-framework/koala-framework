<?php
class Kwc_FulltextSearch_Search_Directory_Admin extends Kwc_Directories_Item_Directory_Admin
{
    //don't duplicate as this directory contains the search results
    public function getDuplicateProgressSteps($source)
    {
        return 0;
    }

    public function duplicate($source, $target, Zend_ProgressBar $progressBar = null)
    {
        return;
    }
}

