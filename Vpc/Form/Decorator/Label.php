<?php
class Vpc_Form_Decorator_Label extends Vpc_Form_Decorator_Abstract
{
    public function processItem($item)
    {
        if (isset($item['items'])) {
            foreach ($item['items'] as $k=>$i) {
                $item['items'][$k] = $this->processItem($i);
            }
        } else if (isset($item['html'])) {
            if (!isset($item['preHtml'])) $item['preHtml'] = '';
            if (!isset($item['postHtml'])) $item['postHtml'] = '';
            $errors = false;
            if ($item['item']) {
//todo: nicht nochmal fragen, vorallem nicht mit $_REQUEST (problem bei File+MultiFields)
//                 $errors = $item['item']->validate($_REQUEST);
            }
            $class = 'vpsField';
            if ($item['item'] && $item['item']->getLabelAlign()) {
                $class .= ' vpsFieldLabelAlign'.ucfirst($item['item']->getLabelAlign());
            }
            if ($errors) {
                $class .= ' vpsFieldError';
            }
            if ($item['item'] && $item['item']->getAllowBlank()===false) {
                $class .= ' vpsFieldRequired';
            }
            if ($item['item']) {
                $c = get_class($item['item']);
                if (substr($c, -10) == '_Component') $c = substr($c, 0, -10);
                $c = str_replace('_', '', $c);
                $class .= ' '.strtolower(substr($c, 0, 1)).substr($c, 1);
				if (!isset($item['id'])) {
                    $class .= ' '.$item['item']->getFieldName();
                }
            }
            if (isset($item['id'])) {
                $class .= ' '.$item['id'];
            }
            $item['preHtml'] .= '<div class="'.$class.'">';
            if ($item['item'] && !$item['item']->getHideLabels() && $item['item']->getFieldLabel()) {
                $item['preHtml'] .= '<label for="'
                    .(isset($item['id']) ? $item['id'] : $item['item']->getFieldName())
                .'">';
                $item['preHtml'] .= $item['item']->getFieldLabel();
                if ($item['item']->getAllowBlank()===false) {
                    $item['preHtml'] .= '*';
                }
                $item['preHtml'] .= $item['item']->getLabelSeparator();
                $item['preHtml'] .= '</label>';
            }
            $item['postHtml'] .= '</div>';
        }
        return $item;
    }
}
