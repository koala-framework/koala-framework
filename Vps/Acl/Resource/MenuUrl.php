<?p
class Vps_Acl_Resource_MenuUrl extends Vps_Acl_Resource_Abstra

    protected $_menuUr

    public function __construct($resourceId, $menuConfig = null, $menuUrl = nul
   
        $this->_menuUrl = $menuUr
        parent::__construct($resourceId, $menuConfig
   

    public function setMenuUrl($menuUr
   
        $this->_menuUrl = $menuUr
   

    public function getMenuUrl
   
        return $this->_menuUr
   

