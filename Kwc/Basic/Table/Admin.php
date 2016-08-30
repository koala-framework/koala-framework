<?php
class Kwc_Basic_Table_Admin extends Kwc_Admin
{
    public function duplicate($source, $target, Zend_ProgressBar $progressBar = null)
    {
        parent::duplicate($source, $target, $progressBar);
        if ($model = $source->getComponent()->getChildModel()) {
            $rows = $model->getRows($model->select()
                ->whereEquals('component_id', $source->dbId)
                ->order('pos', 'ASC')
            );
            if ($rows) {
                foreach ($rows as $row) {
                    $newRow = $row->duplicate(array(
                        'component_id' => $target->dbId
                    ));
                }
            }
        }
    }
}
