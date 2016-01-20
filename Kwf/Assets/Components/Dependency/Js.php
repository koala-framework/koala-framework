<?php
class Kwf_Assets_Components_Dependency_Js extends Kwf_Assets_Components_Dependency_Abstract
{
    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function isCommonJsEntry()
    {
        return true;
    }

    public function getContentsPacked($language)
    {
        $ret = Kwf_SourceMaps_SourceMap::createEmptyMap('');
        $trlData = array();
        foreach ($this->_componentDependencies as $dep) {
            $c = $dep->getContentsPacked($language);
            $data = $c->getMapContentsData(false);
            if (isset($data->{'_x_org_koala-framework_trlData'})) {
                $trlData = array_merge($trlData, $data->{'_x_org_koala-framework_trlData'});
            }
            if (Kwf_Config::getValue('application.uniquePrefix')) {
                $c->stringReplace('kwcBem--', $this->_getKwcClass().'--');
                $c->stringReplace('kwcBem__', $this->_getKwcClass().'__');
            } else {
                $c->stringReplace('kwcBem--', '');
                $c->stringReplace('kwcBem__', '');
            }
            $c->stringReplace('.kwcClass', '.'.$this->_getKwcClass());
            $ret->concat($c);
        }
        $ret->{'_x_org_koala-framework_trlData'} = $trlData;

        $ret->setMimeType('text/javascript');
        return $ret;
    }
}
