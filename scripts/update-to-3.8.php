<?php
$c = array(
    'require' => array(
        "koala-framework/koala-framework" => "3.8.x-dev"
    ),
    "minimum-stability"=> "dev",
    "prefer-stable"=> true,
);
if (is_file('vkwf_branch')) {
    exec("git rm vkwf_branch");
    echo "removed kwf_branch file, composer.json is used instead\n";
    $c['require']['vivid-planet/vkwf'] = '3.8.x-dev';
    $c['repositories'] = array(
        array(
            'type' => 'composer',
            'url' => 'http://packages.vivid-planet.com/'
        )
    );
} else if (is_file('kwf_branch')) {
    unlink('kwf_branch');
    echo "removed kwf_branch file, composer.json is used instead\n";
}
if (!file_exists('composer.json')) {
    file_put_contents('composer.json', json_encode($c, (defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0) + (defined('JSON_UNESCAPED_SLASHES') ? JSON_UNESCAPED_SLASHES : 0) ));
    echo "created default composer.json\n";
    exec("git add composer.json");
    file_put_contents('composer.lock', '');
    exec("git add composer.lock");
}

function deleteCacheFolder($path)
{
    if (!file_exists($path)) return;
    system("git rm $path/.gitignore");
    clearstatcache();
    if (!file_exists($path)) return;

    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($it as $file) {
        if (in_array($file->getBasename(), array('.', '..'))) {
            continue;
        } elseif ($file->isDir()) {
            rmdir($file->getPathname());
        } elseif ($file->isFile() || $file->isLink()) {
            unlink($file->getPathname());
        }
    }
    rmdir($path);
}

function glob_recursive($pattern, $flags = 0) {
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        if (dirname($dir) == './kwf-lib' || $dir == './kwf-lib') continue;
        if (dirname($dir) == './vkwf-lib' || $dir == './vkwf-lib') continue;
        if (dirname($dir) == './library' || $dir == './library') continue;
        if (dirname($dir) == './vendor' || $dir == './vendor') continue;
        $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
    }
    return $files;
}

if (!file_exists('scss')) {
    mkdir('scss');
    mkdir('scss/config');
    file_put_contents('scss/config/.gitkeep', '');
    system('git add scss/config/.gitkeep');
}

if (!is_dir('cache/uglifyjs')) {
    mkdir('cache/uglifyjs');
    file_put_contents('cache/uglifyjs/.gitignore', "*\n!.gitignore\n");
    system("git add cache/uglifyjs/.gitignore");
    echo "folder \"cache/uglifyjs\" created\n";
}
if (!is_dir('cache/twig')) {
    mkdir('cache/twig');
    file_put_contents('cache/twig/.gitignore', "*\n!.gitignore\n");
    system("git add cache/twig/.gitignore");
    echo "folder \"cache/twig\" created\n";
}
if (!is_dir('cache/zend')) {
    mkdir('cache/zend');
    file_put_contents('cache/zend/.gitignore', "*\n!.gitignore\n");
    system("git add cache/zend/.gitignore");
    echo "folder \"cache/zend\" created\n";
}

deleteCacheFolder('cache/events');
deleteCacheFolder('cache/table');
deleteCacheFolder('cache/trl');

$c = file(".gitignore", FILE_IGNORE_NEW_LINES);
$c[] = '/build';
$c[] = '/vendor';
$c[] = '/bower.json';
$c[] = '/.bowerrc';
$c = array_filter($c); //remove empty lines
$c = array_unique($c);
file_put_contents('.gitignore', implode("\n", $c)."\n");
echo ".gitignore updated to ignore build and vendor\n";


$c = file_get_contents('bootstrap.php');
$c = preg_replace("#require(_once)? 'kwf-lib/([^']*)';#", "require 'vendor/koala-framework/koala-framework/\\2';", $c);
$c = preg_replace("#require(_once)? 'vkwf-lib/([^']*)';#", "require 'vendor/vivid-planet/vkwf/\\2';", $c);
file_put_contents('bootstrap.php', $c);
echo "bootstrap.php updated to require kwf from vendor\n";

$libDirs = array('kwf-lib', 'vkwf-lib', 'library');
foreach ($libDirs as $libDir) {
    if (file_exists($libDir)) {
        if (is_link($libDir)) {
            unlink($libDir);
            echo "$libDir symlink removed, vendor is now used\n";
        } else {
            echo "$libDir is not used anymore, we switched to composer. Delete it manually.\n";
        }
    }
}

// ********* Ext -> Ext2
class MyRecursiveFilterIterator extends RecursiveFilterIterator {

    public static $FILTERS = array(
        'build', 'vendor', 'cache', '.git', '.svn', 'log', 'temp', 'uploads'
    );

    public function accept() {
        return !in_array(
            $this->current()->getFilename(),
            self::$FILTERS,
            true
        );
    }
}

$it = new RecursiveDirectoryIterator('.');
$it->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);

$it = new MyRecursiveFilterIterator($it);

$it  = new RecursiveIteratorIterator($it);
$extensions = array(
    'js', 'php', 'css', 'scss', 'printcss'
);
foreach ($it as $f) {
    $extension = pathinfo($f->getFilename(), PATHINFO_EXTENSION);
    if (in_array($extension, $extensions)) {
        $c = file_get_contents($f);
        $origC = $c;

        //Ext. -> Ext2.
        $c = preg_replace('#^Ext\.#', 'Ext2.', $c);
        $c = preg_replace('#([^a-zA-Z\.])Ext\.#', '\1Ext2.', $c);

        //ext- -> ext2-
        //x- -> x2-
        $c = preg_replace('#(\.|\'|"|\' |" )(ext|x)-#', '${1}${2}2-', $c);

        if ($c != $origC) {
            echo "Ext -> Ext2: $f\n";
            file_put_contents($f, $c);
        }
    }
}
echo "\n";

// ********* Component.yml -> Component.php
$files = glob_recursive('Component.yml');
foreach ($files as $file) {
    $newFile = substr($file, 0, -3).'php';
    system("git mv $file $newFile");
    echo "\n$file -> $newFile (CHECK MANUALLY!!)";
    $parser = '../library/yaml/4.0.2/sfYamlParser.php';
    if (!file_exists($parser)) {
        echo "can't convert $parser not found ";
        continue;
    }
    require_once $parser;
    $yaml = new sfYamlParser();
    $settings = $yaml->parse(file_get_contents($newFile));
    $settings['base'];
    if (isset($settings['childSettings'])) {
        $settings['settings']['childSettings'] = $settings['childSettings'];
    }
    $cls = $file;
    $cls = str_replace(array('.yml', 'component/', './'), '', $cls);
    $cls = str_replace('/', '_', $cls);
    $c = "<?php\n";
    $c .= "class ".$cls." extends $settings[base]\n";
    $c .= "{\n";
    $c .= "    public static function getSettings()\n";
    $c .= "    {\n";
    $c .= "        \$ret = parent::getSettings()\n";
    foreach ($settings['settings'] as $k=>$i) {
        $c .= "        \$ret[$k] = ".var_export($i, true)."\n";
    }
    $c .= "        return \$ret;\n";
    $c .= "    }\n";
    $c .= "}\n";
    $c = file_get_contents($newFile);
    file_put_contents($newFile, $c);
}
deleteCacheFolder('cache/generated');

function replaceFiles($files, $from, $to) {
    foreach ($files as $f) {
        $content = file_get_contents($f);
        if (strpos($content, $from)) {
            file_put_contents($f, str_replace($from, $to, $content));
            echo "Change $f: $from -> $to\n";
        }
    }
}
$files = glob_recursive('Component.php');
replaceFiles($files, 'Kwc_List_Fade_Component', 'Kwc_Legacy_List_Fade_Component');
replaceFiles($files, 'Kwc_List_Carousel_Component', 'Kwc_Legacy_List_Carousel_Component');

$files = glob_recursive('Component.php');
$files = array_merge($files, glob_recursive('*.ini'));
replaceFiles($files, 'ModernizrMediaQueries', 'ModernizrMediaqueries');

$files = array_merge(glob_recursive('Component.php'), glob_recursive('Events.php'));
replaceFiles($files, 'Kwf_Component_Events_Log', 'Kwf_Events_Log');
replaceFiles($files, 'Kwf_Component_Event_Media_', 'Kwf_Events_Event_Media_');
replaceFiles($files, 'Kwf_Component_Event_Model_', 'Kwf_Events_Event_Model_');
replaceFiles($files, 'Kwf_Component_Event_Row_', 'Kwf_Events_Event_Row_');
replaceFiles($files, 'Kwf_Component_Event_Abstract', 'Kwf_Events_Event_Abstract');
replaceFiles($files, 'Kwf_Component_Events::fireEvent', 'Kwf_Events_Dispatcher::fireEvent');
replaceFiles($files, 'Kwf_Component_Events', 'Kwf_Events_Subscriber');

$files = glob_recursive('*.js');
replaceFiles($files, 'Kwc.List.Fade', 'Kwc.Legacy.List.Fade');
replaceFiles($files, 'Kwc.List.Carousel', 'Kwc.Legacy.List.Carousel');

$files = glob_recursive('*.scss');
replaceFiles($files, '@import "susy";', '@import "kwf/susyone";');
replaceFiles($files, '@import "kwf/form/', '@import "kwf/legacy/form/');

echo "\n";
echo "run now 'composer install' to install dependencies\n";
