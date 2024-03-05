<?php
class Kwc_Box_StructuredData_Component extends Kwc_Abstract_Composite_Component
{
    //JSON validate regex
    const REGEX = '
      /
      (?(DEFINE)
         (?<number>   -? (?= [1-9]|0(?!\d) ) \d+ (\.\d+)? ([eE] [+-]? \d+)? )    
         (?<boolean>   true | false | null )
         (?<string>    " ([^"\\\\]* | \\\\ ["\\\\bfnrt\/] | \\\\ u [0-9a-f]{4} )* " )
         (?<array>     \[  (?:  (?&json)  (?: , (?&json)  )*  )?  \s* \] )
         (?<pair>      \s* (?&string) \s* : (?&json)  )
         (?<object>    \{  (?:  (?&pair)  (?: , (?&pair)  )*  )?  \s* \} )
         (?<json>   \s* (?: (?&number) | (?&boolean) | (?&string) | (?&array) | (?&object) ) \s* )
      )
      \A (?&json) \Z
      /six   
    ';

    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Structured Data');
        $ret['flags']['hasHeaderIncludeCode'] = true;
        $ret['componentIcon'] = 'tag';
        $ret['ownModel'] = 'Kwc_Box_StructuredData_Model';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['content'] = $this->getRow()->content;
        return $ret;
    }

    public function getIncludeCode()
    {
        return $this->getData();
    }
}
