<?ph
class Vpc_Basic_Link_Mail_Admin extends Vpc_Admi

    public function setup(
    
        $fields['mail']     = "varchar(255) NOT NULL"
        $fields['subject']  = "varchar(255) NOT NULL"
        $fields['text']     = "text NOT NULL"
        $this->createFormTable('vpc_basic_link_mail', $fields)
    
