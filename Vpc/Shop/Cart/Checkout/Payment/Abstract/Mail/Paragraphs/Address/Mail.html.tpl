<table width="600" cellspacing="0" cellpadding="0">
    <tr>
        <td><?=$this->order->title.' '.$this->order->firstname.' '.$this->order->lastname;?></td>
    </tr>
    <tr>
        <td><?=$this->order->street;?></td>
    </tr>
    <tr>
        <td><?=$this->order->zip;?> <?=$this->order->city;?></td>
    </tr>
    <tr>
        <td><?=$this->order->country;?><br/></td>
    </tr>
</table>
