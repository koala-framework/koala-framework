<?php
class Vpc_Forum_Decorator_LatestThreads_Component extends Vpc_Decorator_Abstract
{
    public function getTemplateVars()
    {
        $vars = parent::getTemplateVars();
        $vars['forumLatestThreads'] = array();

        $threadTable = new Vpc_Forum_Thread_Model();
        $rowset = $threadTable->fetchAll(null, null, 5);

        $pc = $this->getPageCollection();
        foreach ($rowset as $row) {
            $parentComponent = $pc->getComponentById($row->component_id);
            if ($parentComponent) {
                $threadPage = $parentComponent->getPageFactory()->getChildPageByRow($row);
                if ($threadPage) {
                    $vars['forumLatestThreads'][] = array(
                        'subject' => $row->subject,
                        'url'     => $threadPage->getUrl()
                    );
                }
            }
        }

        return $vars;
    }
}
