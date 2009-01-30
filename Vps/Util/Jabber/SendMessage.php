<?php
class Vps_Util_Jabber_SendMessage
{
    public static function send($sendMessageTo, $message)
    {
        require_once 'class_Jabber.php';
        $jab = new Jabber();

        new self($jab, $sendMessageTo, $message);

        if (!$jab->connect("vivid")) {
            throw new Vps_Exception("Could not connect to the Jabber server");
        }
        $jab->execute(1, 3);
        $jab->disconnect();
    }

    public function __construct(&$jab, $sendMessageTo, $message) {
        $this->sendMessageTo = $sendMessageTo;
        $this->message = $message;
        $this->jab = &$jab;
        $this->first_roster_update = true;
        $jab->set_handler("connected",$this,"handleConnected");
        $jab->set_handler("authenticated",$this,"handleAuthenticated");
        $jab->set_handler("authfailure",$this,"handleAuthFailure");
        $jab->set_handler("heartbeat",$this,"handleHeartbeat");
        $jab->set_handler("error",$this,"handleError");
        $jab->set_handler("rosterupdate",$this,"handleRosterUpdate");
        $jab->set_handler("subscribe",$this,"handleSubscribe");
    }

    function handleConnected()
    {
        $this->jab->login("zeiterfassung" , "zeiterfassung", 'Zeiterfassung');
    }

    function handleAuthenticated()
    {
        //$this->jab->browse();
        $this->jab->get_roster();
        //$this->jab->set_presence("","Kurz online...");
    }

    function handleAuthFailure($code, $error)
    {
        throw new Vps_Exception("Authentication failure: $error ($code)");
        $this->jab->terminated = true;
    }

    function handleHeartbeat()
    {
        if (!$this->first_roster_update) {
            if (!isset($this->jab->roster[$this->sendMessageTo])) {
                $this->jab->subscribe($this->sendMessageTo);
            }
            if (isset($this->jab->roster[$this->sendMessageTo]) && $this->message) {
                $msg = trim($this->message);
                $this->jab->message($this->sendMessageTo, "chat", null, $msg);
                $this->message = false;
            }
        }
    }

    function handleError($code,$error,$xmlns)
    {
        throw new Vps_Exception("Error: $error ($code)".($xmlns?" in $xmlns":""));
    }

    function handleRosterUpdate($jid)
    {
        if ($this->first_roster_update) {
            $this->first_roster_update = false;
        }
    }

    function handleSubscribe($packet)
    {
        if (isset($packet['presence']) && isset($packet['presence']['@']) && isset($packet['presence']['@']['from'])) {
            $this->jab->subscription_request_accept($packet['presence']['@']['from']);
        }
    }
}
