<div class="<?=$this->cssClass?>">
   <?php
    if ($this->icon) {
        $domain = Vps_Registry::get('config')->server->domain;
        echo "<img src=\"http://{$domain}{$this->icon}\" />";
    }
    echo ' ';
    echo $this->component($this->downloadTag);
    echo $this->mailEncodeText($this->infotext);
    echo $this->ifHasContent($this->downloadTag);
    echo '</a>';
    if ($this->filesize) {
        echo ' <span>(' . $this->fileSize($this->filesize) . ')</span>';
    }
    echo $this->ifHasContent();
    ?>
</div>