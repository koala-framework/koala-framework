<?p
/
 * Interface für Komponenten (Prefix Vp
 
 * Decorators implementieren dieses Interface und erweitern in Fol
 * Vpc_Decorator_Abstract, Komponenten erweitern Vpc_Abstra
 
 * @package V
 * @copyright Copyright (c) 2007, Vivid Planet Software Gm
 
interface Vpc_Interfa

    public function getId(
    public function getPageId(
    public function generateHierarchy($filename = ''
    public function setPageCollection(Vps_PageCollection_Abstract $pageCollection
    public function getTemplateVars(
    public function findComponent($id
    public function findComponentByClass($class
    // 
    public function getComponentInfo(
    public function saveFrontendEditing(Zend_Controller_Request_Http $request
