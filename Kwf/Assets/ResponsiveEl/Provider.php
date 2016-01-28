<?php
class Kwf_Assets_ResponsiveEl_Provider extends Kwf_Assets_Provider_Abstract
{
    public function getDependenciesForDependency(Kwf_Assets_Dependency_Abstract $dependency)
    {
        if ($dependency->getMimeType() == 'text/css') {
            if (strpos($dependency->getContentsSourceString(), 'el-breakpoint') === false) {
                return array();
            }
            $contents = $dependency->getContentsSourceString();
            if (preg_match_all('#([^}{]*){([^}]*kwf-responsive-el-(gt|lt):[^}]*)}#', $contents, $m)) {
                $selectors = array();
                foreach (array_keys($m[1]) as $k) {
                    $selector = trim($m[1][$k]);
                    if (!isset($selectors[$selector])) $selectors[$selector] = array();
                    $ruleContent = $m[2][$k];
                    if (preg_match_all('#kwf-responsive-el-(gt|lt):\s*([0-9]+)#', $ruleContent, $m2)) {
                        foreach (array_keys($m2[1]) as $k2) {
                            $mode = $m2[1][$k];
                            $size = $m2[2][$k];
                            if ($mode == 'gt') {
                                $selectors[$selector][] = $size;
                            } else if ($mode == 'lt') {
                                $selectors[$selector][] = array(
                                    'maxWidth' => $size,
                                    'cls' => 'lt'.$size
                                );
                            }
                        }
                    }
                }
                $selectorNum = 0;
                foreach ($selectors as $selector=>$breakpoints) {
                    $selectorNum++;
                    $d = new Kwf_Assets_ResponsiveEl_JsDependency(
                        $this->_providerList,
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
