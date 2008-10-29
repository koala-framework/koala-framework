<?php
class Vps_Controller_Action_Cli_TrlParseController extends Vps_Controller_Action_Cli_Abstract
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
        $parser = new Vps_Trl_Parser($modelVps, $modelWeb, $this->_getParam('type'));
        $parser->setDebug($this->_getParam('debug'));
        set_time_limit(200);
        $results = $parser->parse();
        echo "\n\n------------------------\n";
        echo $results['files']." files parsed\n";
        echo $results['phpfiles']." PHP files\n";
        echo $results['jsfiles']." JavaScript files\n";
        echo $results['tplfiles']." TPL files\n";
        echo "------------------------\n";
        echo count($results['errors'])." errors\n";
        foreach ($results['errors'] as $key => $error) {
            echo (($key+1).". \t".$error['path'].' at line '.$error['linenr']."\n");
            echo ("\t".$error['message']."\n\n");
        }
        echo "------------------------\n";
        echo "Parsing end\n";
        exit();
    }

}

