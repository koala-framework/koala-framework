<?p
/
 * Newskomponen

 * @package V
 * @subpackage Componen
 
class Vpc_News_Component extends Vpc_Abstra

    const NAME = 'News
    public function generateHierarchy($filename = '
   
        $pages = array(
        foreach($this->getDao()->getTable('Vpc_News_Model')->fetchAll() as $row)
            if ($filename != '' && $filename != $row->filename) continu
            $page = $this->createPage('Vpc_News_Details', 0, $row->id
            $page->setNewsId($row->id
            $page->title = $row->titl
            $this->getPagecollection()->addTreePage($page, $row->filename, $row->title, $this
            $pages[] = $pag
       
        return $page
   

    public function getTemplateVars
   
        $ret = parent::getTemplateVars(
        $ret['news'] = array(
        foreach($this->generateHierarchy() as $n)
            $data['title'] = $n->titl
            $data['filename'] = $n->getUrl(
            $ret['news'][] = $dat
       
        $ret['id'] = $this->getComponentId(
        $ret['template'] = 'News/Aktuelle.html
        return $re
   

