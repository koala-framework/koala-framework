<?php
class Kwf_Assets_Dependency_File_Js extends Kwf_Assets_Dependency_File
{
    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function getContentsPacked()
    {
        $fileName = $this->getFileNameWithType();
        $fileName = $this->getAbsolutePath($fileName);
        $rawContents = file_get_contents($this->getAbsoluteFileName());


        $usesUniquePrefix = strpos($rawContents, 'kwfUp-') !== false;

        $pathType = $this->getType();
        if ($pathType == 'ext2' && strpos($rawContents, 'ext2-gen') !== false) {
            $usesUniquePrefix = true;
        }

        $useTrl = $pathType != 'ext2';
        if (substr($this->getAbsoluteFileName(), 0, 24) == 'vendor/bower_components/') {
            //dependencies loaded via bower never use kwf translation system
            $useTrl = false;
        }
        if ($useTrl) {
            $useTrl = strpos($rawContents, 'trl') !== false && preg_match('#trl(c|p|cp)?(Kwf)?(Static)?\(#', $rawContents);
        }
        $useBabel = strpos($rawContents, '"use es6";') !== false;

        if ($usesUniquePrefix || $useTrl || $useBabel) {
            //when contents contain .cssClass we must cache per app
            $buildFile = 'cache/uglifyjs'.$fileName.'.v6'.md5(file_get_contents($this->getAbsoluteFileName()).Kwf_Config::getValue('application.uniquePrefix'));
        } else {
            $buildFile = sys_get_temp_dir().'/kwf-uglifyjs'.$fileName.'.v6'.md5(file_get_contents($this->getAbsoluteFileName()));
        }

        if (!file_exists("$buildFile.min.js")) {

            $dir = dirname($buildFile);
            if (!file_exists($dir)) mkdir($dir, 0777, true);
            file_put_contents($buildFile, $rawContents);

            if ($useBabel) {
                $filter = new Kwf_Assets_Dependency_Filter_BabelJs($buildFile);
                $map = $filter->build();
                file_put_contents($buildFile, $map->getFileContents()); //TODO: map support
            }

            $map = Kwf_Assets_Dependency_Filter_UglifyJs::build($buildFile, '/assets/'.$this->getFileNameWithType());

            $data = $map->getMapContentsData();
            $data->{'_x_org_koala-framework_masterFiles'} = array(
                $this->getAbsoluteFileName()
            );

            $contents = file_get_contents("$buildFile.min.js");
            $replacements = array();
            if ($pathType == 'ext2') {
                $replacements['../images/'] = '/assets/ext2/resources/images/';
            } else if ($pathType == 'mediaelement') {
                $replacements['url('] = 'url(/assets/mediaelement/build/';
            }
            if ($usesUniquePrefix) {
                if ($pathType == 'ext2') {
                    //hack for ext2 to avoid duplicated ids getting generated
                    $uniquePrefix = Kwf_Config::getValue('application.uniquePrefix');
                    if ($uniquePrefix) {
                        $map->stringReplace('ext2-gen', $uniquePrefix.'-ext2-gen');
                    }
                }
                if (strpos($rawContents, 'kwfUp-') !== false) {
                    if (Kwf_Config::getValue('application.uniquePrefix')) {
                        $replacements['kwfUp-'] = Kwf_Config::getValue('application.uniquePrefix').'-';
                    } else {
                        $replacements['kwfUp-'] = '';
                    }
                }
            }
            foreach ($replacements as $search=>$replace) {
                $map->stringReplace($search, $replace);
            }

            if ($useTrl) {
                $trlData = array();
                foreach (Kwf_TrlJsParser_JsParser::parseContent($contents) as $trlElement) {
                    $d = Kwf_Assets_Util_Trl::getJsReplacement($trlElement);
                    $map->stringReplace($d['before'], $d['replace']);
                    $trlData[] = $d['trlElement'];
                }
                $data->{'_x_org_koala-framework_trlData'} = $trlData;
            }

            $map->save("$buildFile.min.js.map.json", "$buildFile.min.js"); //adds last extension

        } else {
            $map = new Kwf_SourceMaps_SourceMap(file_get_contents("$buildFile.min.js.map.json"), file_get_contents("$buildFile.min.js"));
        }

        return $map;
    }
}
