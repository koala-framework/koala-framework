<?php
class Vpc_Forum_LatestThreads_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName'         => 'Forum - Neueste Themen',
            'tablename'             => 'Vpc_Forum_Thread_Model'
        ));
        return $ret;
    }

    public function getTemplateVars()
    {
        if (!(Zend_Registry::get('userModel')->getAllCache())) {
            Zend_Registry::get('userModel')->createAllCache();
        }

        $vars = parent::getTemplateVars();
        $vars['forumLatestThreads'] = array();

        $threadTable = $this->getTable();
        $rowset = $threadTable->fetchAll(null, null, 5);

        $pc = $this->getPageCollection();
        foreach ($rowset as $row) {
            $parentComponent = $pc->getComponentById($row->component_id);
            if ($parentComponent) {
                $threadPage = $parentComponent->getPageFactory()->getChildPageByRow($row);
                if ($threadPage) {
                    $vars['forumLatestThreads'][] = array_merge(
                        $threadPage->getThreadVars(),
                        array(
                            'groupUrl' => $parentComponent->getUrl(),
                            'groupName' => $parentComponent->getName()
                        )
                    );
                }
            }
        }

        return $vars;
    }
}
