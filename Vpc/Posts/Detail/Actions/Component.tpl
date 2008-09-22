<?
$echos = array();
if ($this->edit) {
    $echos[] = $this->componentLink($this->edit);
}
if ($this->delete) {
    $echos[] = $this->componentLink($this->delete);
}
$echos[] = $this->componentLink($this->report);
$echos[] = $this->componentLink($this->quote);
echo implode(' | ', $echos);