<?php
class Kwc_Menu_OtherCategory_Admin extends Kwc_Abstract_Admin
{
    public function getDuplicateProgressSteps($source)
    {
        return 0;
    }

    public function duplicate($source, $target, Zend_ProgressBar $progressBar = null)
    {
    }
}
