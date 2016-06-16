<? if ($this->user && $this->user->row && $this->user->row->signature) { ?>
    <p class="signature"><tt>--<br /><?=nl2br(htmlspecialchars($this->user->row->signature))?></tt></p>
<? } ?>
