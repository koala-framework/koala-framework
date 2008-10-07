<?php
if ($this->imagePosition == 'left') {
    echo $this->component($this->images);
    echo $this->component($this->text);
} else {
    echo $this->component($this->text);
    echo $this->component($this->images);
}