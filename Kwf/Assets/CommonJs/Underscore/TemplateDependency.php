<?php
class Kwf_Assets_CommonJs_Underscore_TemplateDependency extends Kwf_Assets_Dependency_File
{
    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function getContentsPacked()
    {
        $contents = file_get_contents($this->getAbsoluteFileName());

        $useTrl = strpos($contents, 'trl') !== false && preg_match('#trl(c|p|cp)?(Kwf)?(Static)?\(#', $contents);
        $trlData = array();
        if ($useTrl) {
            foreach (Kwf_TrlJsParser_UnderscoreTemplateParser::parseContent($contents) as $trlElement) {
                $d = Kwf_Assets_Util_Trl::getJsReplacement($trlElement);
                $contents = str_replace($d['before'], $d['replace'], $contents);
                $trlData[] = $d['trlElement'];
            }
        }


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


        $map = Kwf_SourceMaps_SourceMap::createEmptyMap($contents);

        $data = $map->getMapContentsData();
        $data->{'_x_org_koala-framework_masterFiles'} = array(
            $this->getAbsoluteFileName()
        );
        if ($trlData) {
            $data->{'_x_org_koala-framework_trlData'} = $trlData;
        }

        return $map;
    }
}
