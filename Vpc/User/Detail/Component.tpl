<div class="<?=$this->cssClass?>">
    <h1><?=trlVps('Userprofile')?>:</h1>
    <div class="text">
        <h3>
            <?=$this->data->row->title?>
            <?=$this->data->row->firstname?>
            <?=substr($this->data->row->lastname, 0, 1).'.'?>
        </h3>

        <p>
            <strong><?=trlVps('Member since')?>:</strong>
            <?=$this->date($this->data->row->created)?>
        </p>

        <p>
            <strong><?=trlVps('Latest online')?>:</strong>
            <?=$this->dateTime($this->data->row->last_login)?>
        </p>
    </div>

</div>