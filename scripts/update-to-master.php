<?
require_once dirname(__FILE__) . '/../Kwf/Setup.php';
Kwf_Setup::setUp();

$file = is_file('vkwf_branch') ? 'vkwf_branch' : 'kwf_branch';
file_put_contents($file, "master\n");
echo "Changed $file to master\n";

function glob_recursive($pattern, $flags = 0) {
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
    }
    return $files;
}

function checkGallery($files) {
    foreach ($files as $f) {
        $content = file_get_contents($f);
        if (strpos($content, 'Kwc_List_Gallery_Component')) {
            echo ("$f: Gallery changed the image enlarge tag from LinkTag to EnlargeTag. Please make sure that EnlargeTag is ok or change the component to Kwc_List_ImagesLinked_Component\n");
        }
    }
}

function replaceFiles($files, $from, $to) {
    foreach ($files as $f) {
        $content = file_get_contents($f);
        if (strpos($content, $from)) {
            file_put_contents($f, str_replace($from, $to, $content));
            echo "Change $f: $from -> $to\n";
        }
    }
}

function updateIncludeCode()
{
    $files = glob_recursive('Master.tpl');
    $removeBoxes = array('title', 'metatags', 'opengraph', 'piwik', 'analytics', 'assets', 'rssFeeds');
    foreach ($files as $f) {
        $c = file_get_contents($f);
        if (!strpos($c, 'this->includeCode')) {
            $c = str_replace("<head>\n", "<head>\n        <?=\$this->includeCode('header')?>\n", $c);
            $c = str_replace("</body>", "    <?=\$this->includeCode('footer')?>\n    </body>", $c);
            foreach ($removeBoxes as $b) {
                $c = preg_replace("#^\s*<\?=\\\$this->component\(\\\$this->boxes\['$b'\]\);\?> *\n#im", "", $c);
            }
            $c = preg_replace("#^\s*<\?=\\\$this->debugData\(\);\?> *\n#im", "", $c);
            $c = preg_replace("#^\s*<link rel=\"shortcut icon\" href=\"/assets/web/images/favicon\.ico\" /> *\n#im", "", $c);
            $c = preg_replace("#^\s*<\?=\\\$this->statisticCode\(\);\?> *\n#im", "", $c);
            $c = preg_replace("#^\s*<\?=\\\$this->assets\(\'Frontend\'\);\?> *\n#im", "", $c);
            file_put_contents($f, $c);
            echo "Updated $f to use new includeCode helper\n";
        }
    }
}

$files = glob_recursive('Component.php');
$files[] = 'config.ini';
replaceFiles($files, 'Kwc_Composite_Images_Component', 'Kwc_List_Images_Component');
replaceFiles($files, 'Kwc_Composite_LinksImages_Component', 'Kwc_List_ImagesLinked_Component');
replaceFiles($files, 'Kwc_Composite_Downloads_Component', 'Kwc_List_Downloads_Component');
replaceFiles($files, 'Kwc_Composite_ImagesEnlarge_Component', 'Kwc_List_Gallery_Component');
replaceFiles($files, 'Kwc_Composite_Links_Component', 'Kwc_List_Links_Component');
checkGallery($files);
updateIncludeCode();
