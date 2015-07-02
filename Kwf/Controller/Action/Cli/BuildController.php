<?php
class Kwf_Controller_Action_Cli_BuildController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "build";
    }

    public static function getHelpOptions()
    {
        $types = array();
        foreach (Kwf_Util_Build::getInstance()->getTypes() as $t) {
            $types[] = $t->getTypeName();
        }
        return array(
            array(
                'param'=> 'type',
                'value'=> implode(',', $types),
                'valueOptional' => true,
                'help' => 'what to build'
            )
        );
    }

    public function indexAction()
    {
        $options = array(
            'types' => $this->_getParam('type'),
            'output' => true,
            'refresh' => true,
        );
        if (is_string($this->_getParam('exclude-type'))) {
            $options['excludeTypes'] = $this->_getParam('exclude-type');
        }
        Kwf_Util_Build::getInstance()->build($options);
        exit;
    }

    public function showExtDepAction()
    {
        $d = Kwf_Assets_Package_Default::getDefaultProviderList()->findDependency('Frontend');
        $this->_showExtDep($d, array());
    }

    private function _showExtDep($d, $stack)
    {
        //if ($d->getDeferLoad()) return;
        $stack[] = $d;
        if ($d instanceof Kwf_Assets_Dependency_File && $d->getType() == 'ext2' || $d->__toString() == 'KwfOnReady') {
            $i = 0;
            foreach ($stack as $s) {
                $i++;
                echo str_repeat(' ', $i*2).' '.$s."\n";
            }
            return;
        }
        foreach ($d->getDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_ALL) as $i) {
            if (!in_array($i, $stack, true)) {
                $this->_showExtDep($i, $stack);
            }
        }
    }

    public function showFrontendAssetSizesAction()
    {
        $packages = array(
            Kwf_Assets_Package_Default::getInstance('Frontend'),
        );
        $mimeTypes = array(
            'text/javascript',
            'text/javascript; defer',
            'text/css',
            'text/css; defer',
        );

        foreach ($packages as $p) {
            foreach ($mimeTypes as $mimeType) {
                $sizes = array();
                echo "\n".$p->getDependencyName()." $mimeType\n";
                foreach ($p->getFilteredUniqueDependencies($mimeType) as $i) {
                    $sizes[(string)$i] = strlen(gzencode($i->getContentsPacked('en')->getFileContents(), 9, FORCE_GZIP));
                }
                arsort($sizes);
                $sumSize = array_sum($sizes);
                $topSizes = array_slice($sizes, 0, 10);
                foreach ($topSizes as $name=>$size) {
                    echo "".str_pad(number_format(round(($size/$sumSize)*100, 1), 1).'%', 5).' '.str_pad(Kwf_View_Helper_FileSize::fileSize($size), 10)." $name\n";
                }
            }
        }
    }
}
