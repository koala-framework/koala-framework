<?p
class Vps_Acl_Resource_MenuEvent extends Vps_Acl_Resource_MenuU

    protected $_menuEventConfi

    public function __construct($resourceId, $menuConfig = null, $menuEventConfig = nul
   
        $this->_menuEventConfig = $menuEventConfi
        parent::__construct($resourceId, $menuConfig
   

    public function setMenuEventConfig($menuEventConfi
   
        $this->_menuEventConfig = $menuEventConfi
   

    public function getMenuEventConfig
   
        return $this->_menuEventConfi
   

