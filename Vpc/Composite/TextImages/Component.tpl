<?php
if ($this->imagePosition == 'left') {
    $this->component($this->images);
    $this->component($this->text);
} else {
    $this->component($this->text);
    $this->component($this->images);
}