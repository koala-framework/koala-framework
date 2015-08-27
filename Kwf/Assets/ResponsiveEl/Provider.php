<?php
class Kwf_Assets_ResponsiveEl_Provider extends Kwf_Assets_Provider_Abstract
{
    public function getDependenciesForDependency(Kwf_Assets_Dependency_Abstract $dependency)
    {
        if ($dependency->getMimeType() == 'text/css') {
            if (strpos($dependency->getContentsSourceString(), 'el-breakpoint') === false) {
                return array();
            }
            $contents = $dependency->getContents('en');
            if (preg_match_all('#([^}{]*){([^}]*kwf-responsive-el-gt:[^}]*)}#', $contents, $m)) {
                $selectors = array();
                foreach (array_keys($m[1]) as $k) {
                    $selector = trim($m[1][$k]);
                    if (!isset($selectors[$selector])) $selectors[$selector] = array();
                    $ruleContent = $m[2][$k];
                    if (preg_match_all('#kwf-responsive-el-gt:\s*([0-9]+)#', $ruleContent, $m2)) {
                        foreach ($m2[1] as $size) {
                            $selectors[$selector][] = $size;
                        }
                    }
                }
                $selectorNum = 0;
                foreach ($selectors as $selector=>$breakpoints) {
                    $selectorNum++;
                    $d = new Kwf_Assets_ResponsiveEl_JsDependency(
                        trim($selector),
                        $breakpoints,
                        'ResponsiveEl-'.$dependency->__toString().'-'.$selectorNum
                    );
                    $d->addDependency(
                        Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_COMMONJS,
                        $this->_providerList->findDependency('kwf/responsive-el'),
                        'kwf/responsive-el'
                    );
                    $ret[] = $d;
                }
                return array(
                    Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES => $ret
                );
            }
        }
        return array();
    }

    public function getDependency($dependencyName)
    {
        return null;
    }
}
