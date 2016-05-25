<?php
class Kwf_Assets_Dependency_File_Css extends Kwf_Assets_Dependency_File
{
    public function getMimeType()
    {
        return 'text/css';
    }

    protected function _processContents($ret)
    {
        $pathType = $this->getType();

        if ($pathType == 'ext2') {
            //hack um bei ext-css-dateien korrekte pfade für die bilder zu haben
            $ret = str_replace('../images/', '/assets/ext2/resources/images/', $ret);
        }

        //convert relative paths (as in bower dependencies; example: jquery.socialshareprivacy, mediaelement
        $fn = $this->getFileNameWithType();
        $fnDir = substr($fn, 0, strrpos($fn, '/'));
        $ret = preg_replace('#url\(\s*([^)]+?)\s*\)#', 'url(\1)', $ret); //remove spaces inside url()
        $ret = preg_replace('#url\((\'|")(?![a-z]+:)([^/\'"])#', 'url(\1/assets/'.$fnDir.'/\2', $ret);
        $ret = preg_replace('#url\((?![a-z]+:)([^/\'"])#', 'url(/assets/'.$fnDir.'/\1', $ret);

        //hack around postcss imcompatibility with *prefixed css rules
        //those rules where used for IE 6-7 specifics (=css star hack)
        //as we don't support them anymore drop them
        $ret = preg_replace('#[^/]\\*[a-z-]+:[^;}]+#', '', $ret);

        return $ret;
    }

    public function getContentsPacked()
    {
        $contents = $this->getContentsSourceString();
        $contents = str_replace("\r", "\n", $contents);

        $contents = $this->_processContents($contents);


        // remove comments
        //$contents = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $contents);

        // multiple whitespaces
        $contents = str_replace("\t", " ", $contents);
        $contents = preg_replace('/(\n)\n+/', '$1', $contents);
        $contents = preg_replace('/(\n)\ +/', '$1', $contents);
        $contents = preg_replace('/(\ )\ +/', '$1', $contents);

        $ret = Kwf_SourceMaps_SourceMap::createEmptyMap($contents);
        $data = $ret->getMapContentsData();
        $data->{'_x_org_koala-framework_masterFiles'} = array(
            $this->getAbsoluteFileName()
        );
        $data->sources = array(
            '/assets/'.$this->getFileNameWithType()
        );
        $ret->setMimeType('text/css');
        return $ret;
    }
}
