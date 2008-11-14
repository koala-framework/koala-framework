<?php
class Vps_Controller_Action_Cli_TextComponentsController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "Regenerate vpc_basic_text_components table";
    }

    public function indexAction()
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
            $existingEntries[] = $row->component_id.'-'.substr($row->component, 0, 1).$row->nr;
        }
        $validEntries = array();
        foreach ($t->fetchAll() as $row) {
            foreach ($row->getContentParts() as $part) {
                if (is_array($part) && in_array($part['type'], $validTypes)) {
                    $id = $row->component_id.'-'.substr($part['type'], 0, 1).$part['nr'];
                    $validEntries[] = $id;
                    if (in_array($id, $existingEntries)) {
                        $existingCount++;
                    } else {
                        $addedCount++;
                        $r = $ccm->createRow();
                        $r->component_id = $row->component_id;
                        $r->component = $part['type'];
                        $r->nr = $part['nr'];
                        $r->saved = 1;
                        $r->save();
                    }
                }
            }
        }
        foreach ($ccm->fetchAll() as $row) {
            $id = $row->component_id.'-'.substr($row->component, 0, 1).$row->nr;
            if (!in_array($id, $validEntries)) {
                $deletedCount++;
                $row->delete();
            }
        }
        echo "existing: $existingCount\n";
        echo "added: $addedCount\n";
        echo "deleted: $deletedCount\n";
        echo 'done in '.(microtime(true)-$start).'sec';
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
