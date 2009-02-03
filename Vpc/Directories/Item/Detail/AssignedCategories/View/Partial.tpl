<li><?=$this->componentLink($this->item, method_exists($this->item->row->getRow(), 'getTreePath') ? $this->item->row->getRow()->getTreePath() : $this->item->row->__toString());?></li>
