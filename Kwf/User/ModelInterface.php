<?php
interface Kwf_User_ModelInterface
{
    public function getAuthedUserRole();
    public function getAuthedUser();
    public function clearAuthedUser();
    public function getAuthedChangedUserRole();
    public function changeUser($user);
    public function login($identity, $credential);
    public function getKwfModel();
}
