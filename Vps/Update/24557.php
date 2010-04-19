<?php
class Vps_Update_24557 extends Vps_Update
{
    public function update()
    {
        ini_set('memory_limit', '512M');
        echo "\n\n *** Teile dem Service mit, welche User es im Web gibt ***\n";
        $appId = Vps_Registry::get('config')->application->id;

        $webModel = new Vps_User_Web_Model();
        $amount = $webModel->countRows();
        $relationModel = new Vps_User_Relation_Model();
        $rows = $webModel->getRows();
        $i=1;
        foreach ($rows as $row) {
            $exists = $relationModel->getRow(
                $relationModel->select()
                    ->whereEquals('user_id', $row->id)
                    ->whereEquals('web_id', $appId)
            );
            if (!$exists) {
                $nr = $relationModel->createRow();
                $nr->user_id = $row->id;
                $nr->web_id = $appId;
                $nr->save();
                echo "[".($i++)."/$amount] Hinzugefuegt (Id: ".$row->id.")\n";
            } else {
                echo "[".($i++)."/$amount] Bereits vorhanden (Id: ".$row->id.")\n";
            }
        }
        echo "\nSync fertig, service up to date.\n\n";
    }
}
