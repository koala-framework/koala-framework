<?php
class Kwf_Assets_TinyMce_Provider extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if ($dependencyName == 'KwfTinyMce') {
            return new Kwf_Assets_TinyMce_BuildDependency();
        }
    }
}
