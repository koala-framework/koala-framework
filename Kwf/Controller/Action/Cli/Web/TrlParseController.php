<?php
class Kwf_Controller_Action_Cli_Web_TrlParseController extends Kwf_Controller_Action_Cli_Abstract
{

    public static function getHelp()
    {
        return "parse for translation calls";
    }
    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'type',
                'value'=> array('all', 'web', 'kwf'),
                'valueOptional' => true,
                'help' => 'what to parse'
            ),
            array(
                'param'=> 'cleanUp',
                'value'=> array('none', 'all', 'web', 'kwf'),
                'valueOptional' => true,
                'help' => 'what to cleanup'
            ),
            array(
                'param'=> 'debug',
                'help' => 'enable debug output'
            )
        );
    }

    private $_defaultLanguage;
    private $_languages = array();
    public function indexAction()
    {
        $modelKwf = new Kwf_Trl_Model_Kwf();
        $modelWeb = new Kwf_Trl_Model_Web();
        //festsetzen der sprachen
        $parser = new Kwf_Trl_Parser($modelKwf, $modelWeb, $this->_getParam('type'), $this->_getParam('cleanUp'));
        $parser->setDebug($this->_getParam('debug'));
        set_time_limit(2000);
        $results = $parser->parse();
        echo "\n\n------------------------\n";
        echo $results['files']." files parsed\n";
        echo $results['phpfiles']." PHP files\n";
        echo $results['jsfiles']." JavaScript files\n";
        echo $results['tplfiles']." TPL files\n";
        echo "------------------------\n";
        echo count($results['added'][get_class($modelKwf)])." Added Kwf\n";
        foreach ($results['added'][get_class($modelKwf)] as $key => $added) {
            echo (($key+1).". \t".$added['before']."\n");
        }
        echo count($results['added'][get_class($modelWeb)])." Added Web\n";
        foreach ($results['added'][get_class($modelWeb)] as $key => $added) {
            echo (($key+1).". \t".$added['before']."\n");
        }
        echo "------------------------\n";
        if ($results['deleted']) {
            echo count($results['deleted'][get_class($modelKwf)])." Deleted Kwf\n";
            foreach ($results['deleted'][get_class($modelKwf)] as $key => $deleted) {
                echo (($key+1).". \tExpression '".$deleted."' deleted\n");
            }
            echo count($results['deleted'][get_class($modelWeb)])." Deleted Web\n";
            foreach ($results['deleted'][get_class($modelWeb)] as $key => $deleted) {
                echo (($key+1).". \tExpression '".$deleted."' deleted\n");
            }
        } else {
            echo "Deleting disabled\n";
        }
        echo "------------------------\n";
        echo count($results['warnings'])." warnings\n";
        foreach ($results['warnings'] as $key => $warning) {
            echo (($key+1).". \t".$warning['dir']." -> '".$warning['before']."' used in ".
                $warning['path'].' at line '.$warning['linenr']."\n");
        }

        echo "------------------------\n";
        echo count($results['errors'])." errors\n";
        foreach ($results['errors'] as $key => $error) {
            echo (($key+1).". \t".$error['path'].' at line '.$error['linenr']."\n");
            echo ("\t".$error['message']."\n\n");
        }
        echo "------------------------\n";
        echo "Parsing end\n";
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function checkIdsAction()
    {
        $models = array(
            Kwf_Model_Abstract::getInstance('Kwf_Trl_Model_Kwf'),
            Kwf_Model_Abstract::getInstance('Kwf_Trl_Model_Web')
        );
        foreach ($models as $m) {
            while ($m instanceof Kwf_Model_Proxy) $m = $m->getProxyModel();
            if (!file_exists($m->getFilePath())) continue;
            $ids = array();
            $duplicate = 0;
            $xml = simplexml_load_file($m->getFilePath());
            $maxId = 0;
            foreach ($xml->text as $row) {
                $maxId = max($maxId, (int)$row->id);
            }
            foreach ($xml->text as $row) {
                $id = (int)$row->id;
                if (in_array($id, $ids)) {
                    echo $m->getFilePath().": $id doppelt\n";
                    $duplicate++;
                    $row->id = ++$maxId;
                    continue;
                }
                $ids[] = $id;
            }
            if (!$duplicate) {
                echo $m->getFilePath().": alles ok\n";
            } else {
                file_put_contents($m->getFilePath(), Kwf_Model_Xml::asPrettyXML($xml->asXML()));
            }
        }
        exit;
    }

    public function checkDoubleEntriesAction()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Trl_Model_Kwf');
        $entries = array();
        foreach ($m->getRows() as $row) {
            $key = $row->context.$row->en;
            if (!isset($entries[$key])) {
            } else {
                echo "double entry: $row->en ($row->de)\n";
            }
            $entries[$key][] = $row;
        }
        foreach ($entries as $rows) {
            if (count($rows) == 1) continue;
            $deleted = false;
            foreach ($rows as $row) {
                if (!$row->de) {
                    $deleted = true;
                    $row->delete();
                    break;
                }
            }
            if (!$deleted) {
                $rows[count($rows)-1]->delete();
            }
        }
        exit;
    }

    public function copyAction()
    {
        Kwf_Component_ModelObserver::getInstance()->disable();
        $source = $this->_getParam('source');
        $target = $this->_getParam('target');
        if (!$source || !$target) throw new Kwf_Exception_Client("source/target parameter required");

        $models = array(
            Kwf_Model_Abstract::getInstance('Kwf_Trl_Model_Kwf'),
            Kwf_Model_Abstract::getInstance('Kwf_Trl_Model_Web'),
        );
        foreach ($models as $m) {
            foreach ($m->getRows() as $row) {
                if (!$row->$target && $row->$source) {
                    echo $row->$source."\n";
                    $row->$target = $row->$source;
                    $row->save();
                }
            }
        }
        exit;
    }

    public function changeWebCodeLanguageAction()
    {
        $target = $this->_getParam('target');
        if (!$target) throw new Kwf_Exception_Client("target parameter required");

        $source = Kwf_Config::getValue('webCodeLanguage');
        $c = file_get_contents('config.ini');
        $c = str_replace("webCodeLanguage = ".$source."\n", "webCodeLanguage = $target\n", $c);
        file_put_contents('config.ini', $c);

        $texts = array(
            'trl' => array(),
            'trlc' => array(),
            'trlp' => array(),
            'trlcp' => array(),
        );
        foreach (Kwf_Model_Abstract::getInstance('Kwf_Trl_Model_Web')->getRows() as $row) {
            $type = 'trl';
            if ($row->context) {
                $type .= 'c';
            }
            if ($row->{$source.'_plural'}) $type .= 'p';
            if (!$row->{$target}) continue;
            if (substr(trim($row->{$target}), 0, 1) == '?') continue;
            $texts[$type][] = array(
                'source' => $row->{$source},
                'source_plural' => $row->{$source.'_plural'},
                'target' => $row->{$target},
                'target_plural' => $row->{$target.'_plural'},
                'context' => $row->context
            );
        }

        $iterator = new RecursiveDirectoryIterator('.');
        foreach(new RecursiveIteratorIterator($iterator) as $file) {

            if ($file->isDir()) continue;
            if (strpos($file->getPathname(), ".svn")) continue;
            if (strpos($file->getPathname(), ".git")) continue;
            if (stripos($file->getPathname(), KWF_PATH) !== false) continue;
            if (stripos($file->getPathname(), '/cache/') !== false) continue;
            $extension = end(explode('.', $file->getFileName()));
            if($extension!='php' && $extension !='js' && $extension !='tpl') continue;
            $file = $file->getPathname();

            echo $file;
            $c = file_get_contents($file);
            $changedC = $c;
            foreach ($texts as $type=>$t) {
                foreach ($t as $text) {
                    if ($type == 'trl') {
                        $changedC = preg_replace('#(trl(Static)?\\((\'|"))'.preg_quote($text['source']).'(\3)#', '\1'.$text['target'].'\4', $changedC);
                    } else if ($type == 'trlc') {
                        $changedC = preg_replace('#(trlc(Static)?\\((\'|")'.preg_quote($text['context']).'\3,\s*(\'|"))'.preg_quote($text['source']).'(\4)#', '\1'.$text['target'].'\5', $changedC);
                    }
                }
            }
            if ($changedC != $c) {
                echo " [changed]";
                file_put_contents($file, $changedC);
            }
            echo "\n";
        }
        exit;
    }
}

