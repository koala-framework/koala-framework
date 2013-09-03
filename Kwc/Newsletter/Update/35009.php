<?php
class Kwc_Newsletter_Update_35009 extends Kwf_Update
{
    public function update()
    {
        $done = array();
        foreach (Kwf_Model_Abstract::getInstance('Kwc_Newsletter_Model')->getRows() as $row) {
            if (in_array($row->component_id, $done)) continue;
            $done[] = $row->component_id;
            if ($this->_progressBar) $this->_progressBar->next(1, "35009: updating ".$row->component_id);
            $a = new Kwf_Update_Action_Component_ConvertComponentIds(array(
                'pattern' => $row->component_id.'_%-mail%',
                'search' => '-mail',
                'replace' => '_mail',
            ));
            $a->update();
        }
    }

    public function getProgressSteps()
    {
        return Kwf_Registry::get('db')->query("SELECT COUNT(*) FROM kwc_newsletter GROUP BY component_id")->fetchColumn();
    }
}
