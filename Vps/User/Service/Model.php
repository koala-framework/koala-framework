<?php
class Vps_User_Service_Model extends Vps_User_Model
{
    public function __construct(array $config = array())
    {
        if (!isset($config['proxyModel'])) {
            $config['proxyModel'] = 'Vps_User_Mirror';
        }
        $this->_siblingModels['webuser'] = 'Vps_User_Web_Model';
        parent::__construct($config);
    }

    public function createUserRow($email, $webcode = null)
    {
        if (is_null($webcode)) {
            $webcode = self::getWebcode();
        }

        if (empty($webcode) && !is_null($webcode) && $email) {
            $this->lockCreateUser();
            $row = $this->getRow($this->select()
                ->whereEquals('email', $email)
                ->whereEquals('webcode', '')
            );
            if ($row) {
                if (!$row->deleted) {
                    $this->unlockCreateUser();
                    throw new Vps_Exception_Client(
                        trlVps('An account with this email address already exists')
                    );
                }
                // global user wurde gelöscht und wird wieder angelegt
                $row->locked = 0;
                $row->deleted = 0;
                $this->_resetPermissions($row);
                $this->unlockCreateUser();
                return $row;
            } else {
                // globaler benutzer existiert im web noch nicht. schauen, ob
                // es ihn bereits gibt, sonst komplett neu anlegen
                $allModel = Vps_Model_Abstract::getInstance('Vps_User_All_Model');
                $allRow = $allModel->getRow($allModel->select()
                    ->whereEquals('email', $email)
                    ->whereEquals('webcode', '')
                );
                if ($allRow) {
                    $relationModel = Vps_Model_Abstract::getInstance('Vps_User_Relation_Model');
                    $relRow = $relationModel->createRow();
                    $relRow->user_id = $allRow->id;
                    $relRow->locked = 0;
                    $relRow->deleted = 0;
                    $relRow->save();

                    $allRow->forceSave(); // damit last_modified geschrieben wird

                    $this->getProxyModel()->synchronize(Vps_Model_MirrorCache::SYNC_ALWAYS);

                    $this->unlockCreateUser();

                    $row = $this->getRow($this->select()
                        ->whereEquals('email', $email)
                        ->whereEquals('webcode', '')
                    );
                    $this->_resetPermissions($row);
                    $row->setNotifyGlobalUserAdded(true);
                    return $row;
                }
            }
            $this->unlockCreateUser();
        }

        $row = parent::createRow(array('email' => $email, 'webcode' => $webcode));
        $this->_resetPermissions($row);
        return $row;
    }
}
