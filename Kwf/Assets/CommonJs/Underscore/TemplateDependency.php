<?php
class Kwf_Assets_CommonJs_Underscore_TemplateDependency extends Kwf_Assets_Dependency_File
{
    public function getContentsPacked($language)
    {
        $contents = file_get_contents($this->getAbsoluteFileName());
        $contents = str_replace("\n", '\n', $contents);
        $contents = str_replace("'", "\\'", $contents);
        $contents = "var _ = require('underscore');\n".
                    "module.exports = _.template('".$contents."');\n";
        return Kwf_SourceMaps_SourceMap::createEmptyMap($contents);
    }
}
