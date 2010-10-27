<?
$ids = array(); foreach ($this->menu as $m) {
    $this->componentLink($m['data']); // zum Cache schreiben
    $ids[] = $m['text'];
}
echo $this->data->componentId . ' ' . implode(',', $ids);
?>