<?php
abstract class Kwf_Assets_Filter_Abstract
{
    const EXECUTE_FOR_DEPENDENCY = 'dep';
    const EXECUTE_FOR_PACKAGE = 'pack';

    abstract public function getExecuteFor();

    abstract public function getMimeType();

    abstract public function filter(Kwf_SourceMaps_SourceMap $sourcemap, Kwf_Assets_Dependency_Abstract $dependency = null);
}
