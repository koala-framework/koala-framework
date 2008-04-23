<?php
class Vps_Filter_Ascii implements Zend_Filter_Interface
{
    public function filter($value)
    {
        
        if (function_exists('transliterate')
            //online deaktiviert wg. server problem
            && $_SERVER['SERVER_ADDR']=='192.168.0.10') {

            $filter[] = 'cyrillic_transliterate_bulgarian';
            $value = transliterate($value, $filter, 'utf-8', 'utf-8');
        }
        $value = strtolower(htmlentities($value, ENT_COMPAT, 'utf-8'));
        $value = preg_replace('/&szlig;/', 'ss', $value);
        $value = preg_replace('/&(.)(uml);/', '$1e', $value);
        $value = preg_replace('/&(.)(acute|breve|caron|cedil|circ|dblac|die|dot|grave|macr|ogon|ring|tilde|uml);/', '$1', $value);
        $value = preg_replace('/([^a-z0-9]+)/', '_', html_entity_decode($value));
        $value = trim($value, '_');
        
        return $value;
    }
}
