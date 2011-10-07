<?php
/**
 * Fasst $amount partials zusammen und öffnet wieder einen neuen tag.
 * Ähnlich wie NewColumn, nur dass dieses immer x partials zusammenfasst und
 * NewColumn egal wieviele partials in y columns aufteilt
 *
 * Das hier macht zwischendrin zB </div><div class="vpsDynamicCombine">
 * Das aller erste und letzte div muss selbst außerhalb der partials im .tpl geschrieben werden
 */
class Vps_Component_Dynamic_Combine
    extends Vps_Component_Dynamic_Abstract
{
    protected $_amount;
    protected $_tag;
    protected $_cssClass;

    public function setArguments($amount, $tag = 'div', $cssClass = 'vpsDynamicCombine')
    {
        $this->_amount = $amount;
        $this->_tag = $tag;
        $this->_cssClass = $cssClass;
    }

    public function getContent()
    {
        $info = $this->_info;
        $currentNumber = $info['number'] + 1; // $this->_info[number] fängt bei 0 zu zählen an, currentNumber nicht

        // bei number == 0 nichts machen - das erste wird händisch hingeschrieben
// p('------');
        if ($info['number'] == 0) return '';
// p('a');
        if ($currentNumber == $info['total']) return '';
// p('b');
        if ($currentNumber % $this->_amount == 0) {
// p('c');
            return "</$this->_tag><$this->_tag class=\"{$this->_cssClass}\">";
        }
// p('d');
        return '';
    }
}
