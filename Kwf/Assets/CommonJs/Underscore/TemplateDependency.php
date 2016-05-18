<?php
class Kwf_Assets_CommonJs_Underscore_TemplateDependency extends Kwf_Assets_Dependency_File
{
    public function getContentsPacked()
    {
        $contents = file_get_contents($this->getAbsoluteFileName());
        $contents = str_replace("\n", '\n', $contents);
        $contents = str_replace("'", "\\'", $contents);
        $contents = "var _ = require('underscore');\n".
                    "module.exports = _.template('".$contents."');\n";

        $replacements = array();
        if (strpos($contents, 'kwfUp-') !== false) {
            if (Kwf_Config::getValue('application.uniquePrefix')) {
                $replacements['kwfUp-'] = Kwf_Config::getValue('application.uniquePrefix').'-';
            } else {
                $replacements['kwfUp-'] = '';
            }
        }
        foreach ($replacements as $search=>$replace) {
            $contents = str_replace($search, $replace, $contents);
        }
        return Kwf_SourceMaps_SourceMap::createEmptyMap($contents);
    }
}
