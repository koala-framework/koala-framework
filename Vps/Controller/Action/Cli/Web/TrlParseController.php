<?php
class Vps_Controller_Action_Cli_Web_TrlParseController extends Vps_Controller_Action_Cli_Abstract
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
                'value'=> array('all', 'web', 'vps'),
                'valueOptional' => true,
                'help' => 'what to parse'
            ),
            array(
                'param'=> 'cleanUp',
                'value'=> array('none', 'all', 'web', 'vps'),
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
        $modelVps = new Vps_Trl_Model_Vps();
        $modelWeb = new Vps_Trl_Model_Web();
        //festsetzen der sprachen
        $parser = new Vps_Trl_Parser($modelVps, $modelWeb, $this->_getParam('type'), $this->_getParam('cleanUp'));
        $parser->setDebug($this->_getParam('debug'));
        set_time_limit(200);
        $results = $parser->parse();
        echo "\n\n------------------------\n";
        echo $results['files']." files parsed\n";
        echo $results['phpfiles']." PHP files\n";
        echo $results['jsfiles']." JavaScript files\n";
        echo $results['tplfiles']." TPL files\n";
        echo "------------------------\n";
        echo count($results['added'][get_class($modelVps)])." Added Vps\n";
        foreach ($results['added'][get_class($modelVps)] as $key => $added) {
            echo (($key+1).". \t".$added['before']."\n");
        }
        echo count($results['added'][get_class($modelWeb)])." Added Web\n";
        foreach ($results['added'][get_class($modelWeb)] as $key => $added) {
            echo (($key+1).". \t".$added['before']."\n");
        }
        echo "------------------------\n";
        if ($results['deleted']) {
            echo count($results['deleted'][get_class($modelVps)])." Deleted Vps\n";
            foreach ($results['deleted'][get_class($modelVps)] as $key => $deleted) {
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

}

