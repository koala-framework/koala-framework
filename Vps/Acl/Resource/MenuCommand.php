<?p
class Vps_Acl_Resource_MenuCommand extends Vps_Acl_Resource_Abstra

    protected $_menuCommandClas
    protected $_menuCommandConfi

    public function __construct($resourceId, $menuConfig = null, $class = null, $menuCommandConfig = nul
   
        $this->_menuCommandClass = $clas
        $this->_menuCommandConfig = $menuCommandConfi
        parent::__construct($resourceId, $menuConfig
   

    public function setMenuCommandClass($menuClas
   
        $this->_menuCommandClass = $menuClas
   

    public function getMenuCommandClass
   
        return $this->_menuCommandClas
   

    public function setMenuCommandConfig($menuCommandConfi
   
        $this->_menuCommandConfig = $menuCommandConfi
   

    public function getMenuCommandConfig
   
        return $this->_menuCommandConfi
   

