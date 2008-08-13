<div class="<?=$this->cssClass?>">
    <?php
    if ($this->row->vps_upload_id == 2) {
        echo $this->image($this->row, null, 'default', '', $this->imgCssClass);
    } else {
        echo $this->image('/assets/vps/images/avatar_ghost.jpg');
    }
    ?>
</div>