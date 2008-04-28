<?php
class Vps_Controller_Action_Component_PagesController extends Vps_Controller_Action_Auto_Synctree
{
    protected $_textField = 'name';
    protected $_rootVisible = false;
    protected $_icons = array (
        'default' => 'page',
        'invisible' => 'page_red',
        'reload' => 'control_repeat_blue',
        'add' => 'page_add',
        'delete' => 'page_delete',
        'folder' => 'folder'
    );
    protected $_buttons = array();

    public function indexAction()
    {
        $this->view->ext('Vps.Component.Pages');
    }

    public function init()
    {
        $this->_table = new Vps_Dao_Pages();
        $this->_table->showInvisible(true);
        parent::init();
    }

    protected function _formatNode($row)
    {
        $data = parent::_formatNode($row);
        if ($row->visible) {
            $data['bIcon'] = Vpc_Abstract::getSetting($row->component_class, 'componentIcon');
        }
        if ($row->is_home) {
            $data['bIcon'] = new Vps_Asset('application_home');
        }
        $data['uiProvider'] = 'Vps.Component.PagesNode';
        return $data;
    }

    public function jsonSavePageAction()
    {
        try {
            $id = $this->getRequest()->getParam('id');
            $this->_table->savePageName($id, $this->getRequest()->getParam('name'));
            $pageData = $this->_table->retrievePageData($id);
            $this->view->id = $id;
            $this->view->name = $pageData['name'];
        } catch (Vps_ClientException $e) {
            $this->view->error = $e->getMessage();
        }
    }

    public function jsonDataAction()
    {
        $id = $this->getRequest()->getParam('node');
        if ($id === '0') {

            $types = Zend_Registry::get('config')->vpc->pageTypes->toArray();
            if (sizeof($types) == 0) $types[''] = 'Seiten';
            foreach ($types as $type => $text) {
                $data = array();
                $data['id'] = $type;
                $data['text'] = $text;
                $data['leaf'] = false;
                $data['expanded'] = true;
                $data['allowDrag'] = false;
                $data['type'] = 'category';
                $data['bIcon'] = new Vps_Asset('folder_page');
                $data['bIcon'] = $data['bIcon']->__toString();
                $data['uiProvider'] = 'Vps.Component.PagesNode';
                $data['children'] = $this->_formatNodes($type);
                $return[] = $data;
            }
            $this->view->nodes = $return;

        } else {

            parent::jsonDataAction();

        }

    }

    protected function _getTreeWhere($parentId = null)
    {
        $where = array();
        $node = $this->getRequest()->getParam('node');
        if (is_string($parentId)) {
            $where['parent_id IS NULL'] = '';
            $where['type = ?'] = $parentId;
        } else {
            $where['parent_id = ?'] = $parentId;
        }
        return $where;
    }

    public function jsonVisibleAction()
    {
        $visible = $this->getRequest()->getParam('visible') == 'true';
        $id = $this->getRequest()->getParam('id');
        $row = $this->_table->find($id)->current();
        if ($row->is_home) {
            throw new Vps_ClientException('Cannot set Home Page invisible');
        } else {
            parent::jsonVisibleAction();
        }
    }

    public function jsonMakeHomeAction()
    {
        $id = $this->_getParam('id');
        $row = $this->_table->find($id)->current();
        if ($row) {
            $oldRows = $this->_table->fetchAll("is_home=1 AND id!='$id'");
            $oldId = $id;
            $oldVisible = false;
            foreach ($oldRows as $oldRow) {
                $oldId = $oldRow->id;
                $oldVisible = $oldRow->visible;
                $oldRow->is_home = 0;
                $oldRow->save();
            }

            $row->is_home = 1;
            $row->save();
            $this->view->home = $id;
            $this->view->oldhome = $oldId;
            $this->view->oldhomeVisible = $oldVisible;
        } else {
            $this->view->error = 'Node not found';
        }
    }

    public function jsonDeleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        if ($this->_table->deletePage($id)) {
            $this->view->id = $id;
        }
    }
    public function openPreviewAction()
    {
        $host = $_SERVER['HTTP_HOST'];
        $host = str_replace('www.', '', $host);
        $host = 'preview.' . $host;
        $pc = Vps_PageCollection_Abstract::getInstance();
        $p = $pc->getPageById($this->_getParam('page_id'));
        $href = 'http://' . $host . $pc->getUrl($p);
        header('Location: '.$href);
        exit;
    }

    public function regenerateTreeCacheAction()
    {

        $writer = new Zend_Log_Writer_Stream('php://output');
        $writer->setFormatter(new Vps_Log_Formatter_Html());
        $logger = new Zend_Log($writer);
        Zend_Registry::set('debugLogger', $logger);


        $db = Zend_Registry::get('db');
        $db->getProfiler()->setEnabled(true);

        $start = microtime(true);
        set_time_limit(15);
        $t = new Vps_Dao_TreeCache();
        $t->regenerate();
        echo 'done in '.(microtime(true)-$start).'sec';
/*
$profiler = $db->getProfiler();
$totalTime    = $profiler->getTotalElapsedSecs();
$queryCount   = $profiler->getTotalNumQueries();
$longestTime  = 0;
$longestQuery = null;
foreach ($profiler->getQueryProfiles() as $query) {
     p($query->getQuery());
    if ($query->getElapsedSecs() > $longestTime) {
        $longestTime  = $query->getElapsedSecs();
        $longestQuery = $query->getQuery();
    }
}

echo '<pre>Executed ' . $queryCount . ' queries in ' . $totalTime . ' seconds' . "\n";
echo 'Average query length: ' . $totalTime / $queryCount . ' seconds' . "\n";
echo 'Queries per second: ' . $queryCount / $totalTime . "\n";
echo 'Longest query length: ' . $longestTime . "\n";
echo "Longest query: \n" . $longestQuery . "\n";*/
        exit;
    }

    public function updateTextComponentsAction()
    {
        $start = microtime(true);
        $existingCount = $addedCount = $deletedCount = 0;
        $t = new Vpc_Basic_Text_Model(array(
            'componentClass'=>'Vpc_Basic_Text_Component'
        ));
        $validTypes = array('image', 'link', 'download');
        $ccm = new Vpc_Basic_Text_ChildComponentsModel();
        $existingEntries = array();
        foreach ($ccm->fetchAll() as $row) {
            $existingEntries[] = $row->component_id.'-'.$row->type.$row->nr;
        }
        $validEntries = array();
        foreach ($t->fetchAll() as $row) {
            foreach ($row->getContentParts() as $part) {
                if (is_array($part) && in_array($part['type'], $validTypes)) {
                    $id = $row->component_id.'-'.$part['type'].$part['nr'];
                    $validEntries[] = $id;
                    if (in_array($id, $existingEntries)) {
                        $existingCount++;
                    } else {
                        $addedCount++;
                        $r = $ccm->createRow();
                        $r->component_id = $row->component_id;
                        $r->type = $part['type'];
                        $r->nr = $part['nr'];
                        $r->saved = 1;
                        $r->save();
                    }
                }
            }
        }
        foreach ($ccm->fetchAll() as $row) {
            $id = $row->component_id.'-'.$row->type.$row->nr;
            if (!in_array($id, $validEntries)) {
                $deletedCount++;
                $row->delete();
            }
        }
        echo "existing: $existingCount<br />";
        echo "added: $addedCount<br />";
        echo "deleted: $deletedCount<br />";
        echo 'done in '.(microtime(true)-$start).'sec';
        exit;
    }
}
