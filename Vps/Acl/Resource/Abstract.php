<?p
abstract class Vps_Acl_Resource_Abstract extends Zend_Acl_Resour

    protected $_menuConfi

    public function __construct($resourceId, $menuConfig = nul
   
        $this->_menuConfig = $menuConfi
        parent::__construct($resourceId
   

    public function setMenuConfig($menuConfi
   
        $this->_menuConfig = $menuConfi
   

    public function getMenuConfig
   
        return $this->_menuConfi
   

