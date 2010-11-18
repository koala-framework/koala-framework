<?php
class Vps_Controller_Action_Cli_Svn_CreateSvnIgnoreController_DirectoryFilter
    extends RecursiveFilterIterator
{
    private $it;

    public function __construct(DirectoryIterator $it)
    {
        parent::__construct($it);
        $this->it = $it;
    }

    public function accept()
    {
        if (!$this->it->isDir()) return false;
        if ($this->it->getFilename() == '.svn') return false;
        return true;
    }

}

class Vps_Controller_Action_Cli_Svn_CreateSvnIgnoreController_IgnoredFilter
    extends Vps_Controller_Action_Cli_Svn_CreateSvnIgnoreController_DirectoryFilter
{
    private $it;

    public function __construct(DirectoryIterator $it)
    {
        parent::__construct($it);
        $this->it = $it;
    }

    public function accept()
    {
        if (!parent::accept()) return false;
        $p = $this->it->getPathname();
        $st = simplexml_load_string(`svn st --non-recursive --xml $p`);
        $st = (string)$st->target->entry->{'wc-status'}['item'];
        if ($st == 'unversioned') {
            return false;
        }
        if ($st == 'ignored') {
            return false;
        }
        return true;
    }

}

class Vps_Controller_Action_Cli_Svn_CreateSvnIgnoreController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return 'erstellt svn-ignore eintraege fuer nicht vps-projekte';
    }

    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'dir',
                'help' => 'what to import'
            )
        );
    }

    public function indexAction()
    {
        $dir = $this->_getParam('dir');
        if (!$dir) {
            throw new Vps_ClientException("Parameter dir wird benoetigt");
        }
        foreach (new RecursiveIteratorIterator(
                    new Vps_Controller_Action_Cli_Svn_CreateSvnIgnoreController_DirectoryFilter(
                        new RecursiveDirectoryIterator($dir)),
                    RecursiveIteratorIterator::SELF_FIRST) as $d
        ) {
            $d = (string)$d;
            echo (string)$d."\n";

            $x = '';
            foreach (explode('/', $d) as $i) {
                if (!$x) {
                    $x = $i;
                    continue;
                }
                $x .= '/'.$i;
                $s = simplexml_load_string(`svn st --verbose --non-recursive --xml $x/..`);
                $st = false;
                foreach ($s->target->entry as $e) {
                    if ($e['path'] == $x) {
                       $st = $e->{'wc-status'}['item'];
                    }
                }
                if (!$st) {
                    $s = simplexml_load_string(`svn st --verbose --non-recursive --xml $x`);
                    if ($s->target->entry['path']==$x) {
                        $st = $s->target->entry->{'wc-status'}['item'];
                    }
                }
                if (!$st) {
                    throw new Vps_Exception("can't find out status");
                }
                if ($st == 'unversioned') {
                    echo "svn add --non-recursive $x\n";
                    $this->_systemCheckRet("svn add --non-recursive $x");
                }
                if ($st == 'ignored') {
                    continue 2;
                }
            }

            $numeric = 0;
            $nonNumeric = array();
            $extensions = array();
            foreach (new DirectoryIterator($d) as $i) {
                if ($i->isDir()) continue;
                $i = (string)$i;
                if (strrpos($i, '.') == strlen($i)-1) {
                    echo "KEINE ENDUNG: $i\n";
                    continue;
                }
                $x = substr($i, -(strlen($i)-strrpos($i, '.')-1));
                $i = substr($i, 0, strrpos($i, '.'));
                //echo "'$i' . '$x'\n";
                if (is_numeric(str_replace('_', '', $i))) {
                    $numeric++;
                    if (!in_array($x, $extensions)) {
                        $extensions[] = $x;
                    }
                } else {
                    $nonNumeric[] = $i.'.'.$x;
                }
            }
//             p($numeric);
            if ($extensions) {
                $ignore = $this->_getSvnIgnore($d);
                $added = false;
                foreach ($extensions as $x) {
                    if (!in_array('*.'.$x, $ignore)) {
                        echo "      ---------> *.$x\n";
                        $ignore[] = '*.'.$x;

                        $added = true;
                    }
                    $this->_removeFromSvn($d, "*.$x");
                }
                if ($added) {
                    $this->_setSvnIgnore($d, $ignore);
                }
            }
            if ($nonNumeric) {
                if (preg_match('#/[^/]*(rte|download)[^/]*$#', $d)
                    || preg_match('#/[^/]*(rte|download)[^/]*/datei$#', $d))
                {
                    $ignore = $this->_getSvnIgnore($d);
                    if (!in_array('*', $ignore)) {
                        $ignore[] = '*';
                        $this->_setSvnIgnore($d, $ignore);
                    }
                    $this->_removeFromSvn($d, "*");
                } else {
                    foreach ($nonNumeric as $i) {
                        if ($i == 'Thumbs.db') {
                            $ignore = $this->_getSvnIgnore($d);
                            if (!in_array($i, $ignore)) {
                                $ignore[] = $i;
                                $this->_setSvnIgnore($d, $ignore);
                            }
                        } else {
                            echo "UNBEKANNTE DATEI: $i\n";
                        }
                    }
                }
            }
        }
        exit;
    }

    private function _removeFromSvn($dir, $pattern)
    {
        $php = "foreach(glob('$dir/$pattern') as \$i) echo \$i.\"\\0\";";
        $cmd = "php -r ".escapeshellarg($php)." | xargs -0 svn st --xml";
        echo $cmd."\n";
        exec($cmd, $out, $ret);
        if ($ret != 0) throw new Vps_Exception("Status failed");
        $out = implode("\n", $out);
        $out = substr($out, strlen('<?xml version="1.0"?>'));
        $out = explode('<?xml version="1.0"?>', $out);
        foreach ($out as $o) {
            $o = '<?xml version="1.0"?>'.$o;
            $st = simplexml_load_string($o);
            foreach ($st->target as $t) {
                if ($t->entry->{'wc-status'}['item'] == 'ignored') continue;
                if ($t->entry->{'wc-status'}['item'] == 'unversioned') continue;
                if (substr((string)$t['path'], -1) == '*') continue;
                $cmd = "svn rm --force ".escapeshellarg((string)$t['path']);
                echo $cmd."\n";
                $this->_systemCheckRet($cmd);
            }
        }
    }

    /*
    public function getIgnoresAction()
    {
        $dir = $this->_getParam('dir');
        if (!$dir) {
            throw new Vps_ClientException("Parameter dir wird benoetigt");
        }
        $ig = simplexml_load_string(`svn propget --recursive --xml svn:ignore $dir`);
        $ignores = array();
        foreach ($ig->target as $t) {
            $p = explode("\n", trim((string)$t->property));
            foreach ($p as $i) {
                $ignores[] = (string)$t['path'] . '/' . trim($i);
            }
        }

        $includes = array();
        foreach ($ignores as $i) {
            $p = '';
            foreach (explode('/', $i) as $j) {
                $p .= $j.'/';
                $e = trim($p, '/');
                if (substr($e, -1) == '*') $e .= '*';
                if (!in_array($e, $includes)) {
                    $includes[] = $e;
                }
            }
        }
        $cmd = 'rsync --progress --verbose --recursive --exclude=\'.svn\' ';
        foreach ($includes as $i) {
            $cmd .= "--include='$i' ";
        }
        $cmd .= "--exclude='*' ";
        $cmd .= '. /www/public/niko/vwtest';
        echo $cmd;
        passthru($cmd);
        exit;
    }
    */

    private function _getSvnIgnore($dir)
    {
        exec("svn propget svn:ignore $dir", $ret);
        foreach ($ret as &$i) {
            $i = trim($i);
        }
        return $ret;
    }
    
    private function _setSvnIgnore($dir, $ignore)
    {
        if (in_array('*', $ignore)) $ignore = array('*');
        $ignore = implode("\n", array_unique($ignore));
        $ignore = escapeshellarg($ignore);
        $cmd = "svn propset svn:ignore $ignore $dir";
        echo $cmd."\n";
        $this->_systemCheckRet($cmd." >/dev/null");
    }
}
