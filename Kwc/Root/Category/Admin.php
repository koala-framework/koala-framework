<?php
class Kwc_Root_Category_Admin extends Kwc_Abstract_Admin
{
    public function getDuplicateProgressSteps($source)
    {
        $ret = parent::getDuplicateProgressSteps($source);
        //pages are not duplicated because they are not returned by 'inherit'=>false
        //so duplicate them here
        $s = array(
            'generatorFlags' => array('pageGenerator'=>true),
            'ignoreVisible'=>true
        );
        foreach ($source->getChildComponents($s) as $c) {
            $ret += $c->generator->getDuplicateProgressSteps($c);
        }
        return $ret;
    }

    public function duplicate($source, $target, Zend_ProgressBar $progressBar = null)
    {
        parent::duplicate($source, $target, $progressBar);

        //pages are not duplicated because they are not returned by 'inherit'=>false
        //so duplicate them here
        $s = array(
            'generatorFlags' => array('pageGenerator'=>true),
            'ignoreVisible'=>true
        );
        foreach ($source->getChildComponents($s) as $c) {
            $c->generator->duplicateChild($c, $target, $progressBar);
        }
    }
}
