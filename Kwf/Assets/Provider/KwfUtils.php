<?php
class Kwf_Assets_Provider_KwfUtils extends Kwf_Assets_Provider_Abstract
{
    public function getDependenciesForDependency(Kwf_Assets_Dependency_Abstract $dependency)
    {
        if ($dependency instanceof Kwf_Assets_Dependency_File_Js && $dependency->getFileNameWithType() != 'kwf/Kwf_js/Utils/Element.js') {
            $deps = array();
            $c = file_get_contents($dependency->getAbsoluteFileName());
            if (preg_match('#^\s*Kwf\.Utils\.ResponsiveEl\(#m', $c)) {
                $deps[] = 'KwfResponsiveEl';
            }
            if (preg_match('#^\s*Kwf\.on(Element)(Ready|Show|Hide|WidthChange)\(#m', $c, $m)) {
                $deps[] = 'KwfOnReady';
            }
            if (preg_match('#^\s*Kwf\.on(Content)(Ready)\(#m', $c)) {
                $deps[] = 'KwfOnReadyCore';
            }
            if (preg_match('#^\s*Kwf\.onJElement(Ready|Show|Hide|WidthChange)\(#m', $c)) {
                $deps[] = 'KwfOnReadyJQuery';
            }
            if (preg_match('#^\s*Kwf\.onComponentEvent\(#m', $c)) {
                $deps[] = 'Kwf';
            }
            $ret = array();
            foreach ($deps as $i) {
                $d = $this->_providerList->findDependency($i);
                if (!$d) throw new Kwf_Exception("Can't find dependency '$i'");
                $ret[] = $d;
            }
            return array(
                Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES => $ret
            );
        }
        return array();
    }

    public function getDependency($dependencyName)
    {
        return null;
    }
}
