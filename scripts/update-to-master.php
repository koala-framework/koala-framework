<?php
$c = array(
    'require' => array(
        "koala-framework/koala-framework" => "dev-master"
    )
);
if (is_file('vkwf_branch')) {
    unlink('vkwf_branch');
    echo "removed kwf_branch file, composer.json is used instead\n";
    $c['require']['vivid-planet']['vkwf'] = 'dev-master';
} else if (is_file('kwf_branch')) {
    unlink('kwf_branch');
    echo "removed kwf_branch file, composer.json is used instead\n";
}
if (!file_exists('composer.json')) {
    file_put_contents('composer.json', json_encode($c, define('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0));
    echo "created efault composer.json\n";
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

deleteCacheFolder('cache/events');
deleteCacheFolder('cache/table');
deleteCacheFolder('cache/trl');

$c = file(".gitignore", FILE_IGNORE_NEW_LINES);
$c[] = 'build';
$c[] = 'vendor';
$c = array_filter($c); //remove empty lines
$c = array_unique($c);
file_put_contents('.gitignore', implode("\n", $c)."\n");
echo ".gitignore updated to ignore build and vendor\n";


$c = file_get_contents('bootstrap.php');
$c = preg_replace("#require(_once)? 'kwf-lib/([^']*)';#", "require 'vendor/koala-framework/koala-framework/\\2';", $c);
$c = preg_replace("#require(_once)? 'vkwf-lib/([^']*)';#", "require 'vendor/koala-framework/vkwf/\\2';", $c);
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
        $c = preg_replace('#(\.|\'|"|\' |" )(ext|x)-#', '\1\22-', $c);

        if ($c != $origC) {
            echo "Ext -> Ext2: $f\n";
            file_put_contents($f, $c);
        }
    }
}


