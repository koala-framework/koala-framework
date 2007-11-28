<?p
class Vps_View_Mail_Smarty extends Vps_View_Smar

    protected $_renderFile = 'mails/Master.txt

    public function __construct($config = array(
   
        parent::__construct($config
        $this->setScriptPath('application/views/'
   
    protected function _run
   
        $path = $this->getScriptPaths(
        $this->template = substr(func_get_arg(0), strlen($path[0])
        parent::_run(
   
