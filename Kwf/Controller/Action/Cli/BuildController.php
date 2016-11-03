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
        if (file_exists(VENDOR_PATH.'/koala-framework/koala-framework/node_modules')) {
            throw new Kwf_Exception('Please delete node_modules folder from koala-framework/koala-framework. All node packages has moved into ./node_modules');
        }

        $options = array(
            'types' => $this->_getParam('type'),
            'output' => true,
            'refresh' => true,
        );
        if (is_string($this->_getParam('exclude-type'))) {
            $options['excludeTypes'] = $this->_getParam('exclude-type');
        }
        if (!Kwf_Util_Build::getInstance()->build($options)) {
            exit(1);
        } else {
            exit;
        }
    }

    public function clearAction()
    {
        $paths = array(
            'cache/uglifyjs',
            'cache/commonjs',
            'cache/assetdeps',
            sys_get_temp_dir().'/kwf-uglifyjs/',
            'build'
        );
        foreach ($paths as $path) {
            echo "clearing $path...\n";
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $fileinfo) {
                if ($fileinfo->getFilename() != '.gitignore') {
                    $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                    $todo((string)$fileinfo);
                }
            }
        }
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
        if ($d instanceof Kwf_Assets_Dependency_File && $d->getType() == 'ext2' || $d->__toString() == 'kwf/commonjs/on-ready-ext2.js') {
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
            'text/css; ie8',
        );

        foreach ($packages as $p) {
            foreach ($mimeTypes as $mimeType) {
                $sizes = array();
                echo "\n".$p->getDependencyName()." $mimeType\n";
                foreach ($p->getFilteredUniqueDependencies($mimeType) as $i) {
                    $sizes[(string)$i] = strlen(gzencode($i->getContentsPacked()->getFileContents(), 9, FORCE_GZIP));
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

    public function showAssetPackageSizesAction()
    {
        $a = new Kwf_Util_Build_Types_Assets();
        $packages = $a->getAllPackages();
        $langs = $a->getAllLanguages();

        $exts = array('js', 'defer.js', 'css');
        foreach ($packages as $p) {
            $depName = $p->getDependencyName();
            $language = $langs[0];
            foreach ($exts as $extension) {
                $cacheId = Kwf_Assets_Dispatcher::getInstance()->getCacheIdByPackage($p, $extension, $language);
                $cacheContents = Kwf_Assets_BuildCache::getInstance()->load($cacheId);
                echo "$depName ";
                $h = new Kwf_View_Helper_FileSize();
                echo "$extension size: ".$h->fileSize(strlen(gzencode($cacheContents['contents'], 9, FORCE_GZIP)));
                echo "\n";
            }
        }
        $d = Kwf_Assets_Package_Default::getDefaultProviderList()->findDependency('Frontend');
        foreach ($d->getFilteredUniqueDependencies('text/javascript') as $i) {
            if ($i instanceof Kwf_Assets_Dependency_File && $i->getType() == 'ext2') {
                echo "\n[WARNING] Frontend text/javascript contains ext2\n";
                echo "To improve frontend performance all ext2 dependencies should be moved to defer\n\n";
                break;
            }
        }
        exit;
    }

    public function countCssSelectorsAction()
    {
        $a = new Kwf_Util_Build_Types_Assets();
        $langs = $a->getAllLanguages();
        $packages = $a->getAllPackages();

        foreach ($packages as $p) {
            $c = $p->getBuildContents('text/css', $langs[0]);
            $count = Kwf_Assets_Util_CssSelectorCount::count($c);
            echo $p->getDependency().': '.$count." rules\n";
        }
        exit;


    }
}
