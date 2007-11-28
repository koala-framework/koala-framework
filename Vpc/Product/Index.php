<?p
/
 * @package V
 * @subpackage Componen
 
class Vpc_Product_Component extends Vpc_Abstra

    private $_name
    const NAME = 'Produktkatalog
  
    protected function getChildPages($filename = '
   
        $dao = $this->getDao(
        $where = $dao->getDb()->quoteInto('visible = ?', '1'
        $rows = $dao->getTable('Vps_Dao_ProductCategories')->fetchAll($where

        $pages = array(
        foreach($rows as $row)
            if ($filename != '' && $filename != $row->filename) continu

            $page = $this->createPage('Vpc_Product_List', 0, $row->id
            $page->setCategoryId($row->id
            $pages[$row->filename] = $pag
            $this->_names[$row->filename] = $row->nam
       
        return $page
   
  
    public function getTemplateVars
   
        $ret = parent::getTemplateVars(
        $pages = $this->generateHierarchy(
        foreach($pages as $filename => $page)
            $data['name'] = $this->_names[$filename
            $data['filename'] = $page->getUrl(
            $ret['categories'][] = $dat
       

        $ret['template'] = 'Product/Categories.html
        return $re
   

    public function getChildComponents
   
        return $this->getChildPages(
   

