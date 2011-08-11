<?php
class Vpc_Basic_Table_Admin extends Vpc_Admin
{
    public function duplicate($source, $target)
    {
        parent::duplicate($source, $target);
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
