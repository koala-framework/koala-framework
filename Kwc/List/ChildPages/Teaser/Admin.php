<?php
class Kwc_List_ChildPages_Teaser_Admin extends Kwc_Abstract_Admin
{
    private $_duplicated = array();
    public function duplicate($source, $target, Zend_ProgressBar $progressBar = null)
    {
        parent::duplicate($source, $target, $progressBar);
        $this->_duplicated[] = array(
            'source' => $source->componentId,
            'target' => $target->componentId,
        );
    }

    public function afterDuplicate($rootSource, $rootTarget)
    {
        //duplicate children of this generator *here* because we know the new child ids now
        //as they depend on the new pages
        foreach ($this->_duplicated as $d) {
            $source = Kwf_Component_Data_Root::getInstance()->getComponentById($d['source'], array('ignoreVisible'=>true));
            $target = Kwf_Component_Data_Root::getInstance()->getComponentById($d['target'], array('ignoreVisible'=>true));
            $sourcePageIdToChild = array();
            foreach ($source->getChildComponents(array('generator'=>'child', 'ignoreVisible'=>true)) as $c) {
                $sourcePageIdToChild[$c->row->target_page_id] = $c;
            }
            foreach ($target->getChildComponents(array('generator'=>'child', 'ignoreVisible'=>true)) as $targetChild) {

                $sql = "SELECT source_component_id FROM kwc_log_duplicate WHERE target_component_id = ? ORDER BY id DESC LIMIT 1";
                $q = Kwf_Registry::get('db')->query($sql, $targetChild->row->target_page_id);
                $q = $q->fetchAll();
                if (!$q) continue;
                $sourcePargeId =  $q[0]['source_component_id'];

                $sourceChild = $sourcePageIdToChild[$sourcePargeId];
                Kwc_Admin::getInstance($source->componentClass)->duplicate($sourceChild, $targetChild);
            }
        }
    }
}
