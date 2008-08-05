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
            $errors = $item['item']->validate($_REQUEST);
            $class = 'vpsField';
            if ($errors) {
                $class .= ' vpsFieldError';
            }
            if ($item['item']->getAllowBlank()===false) {
                $class .= ' vpsFieldRequired';
            }
            $item['preHtml'] = '<div class="'.$class.'">';
            if (!$item['item']->getHideLabels()) {
                $item['preHtml'] .= '<label for="'.$item['item']->getFieldName().'">';
                $item['preHtml'] .= $item['item']->getFieldLabel();
                if ($item['item']->getAllowBlank()===false) {
                    $item['preHtml'] .= '*';
                }
                $item['preHtml'] .= $item['item']->getLabelSeparator();
                $item['preHtml'] .= '</label>';
            }
            $item['postHtml'] = '</div>';
        }
        return $item;
    }
}
