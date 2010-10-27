<?
$ids = array();
foreach ($this->menu as $m) {
    $ids[] = $m['text'];
}
echo implode(',', $ids);
if (isset($this->subMenu) && $this->subMenu->hasContent()) echo ' - ' . $this->component($this->subMenu);
?>