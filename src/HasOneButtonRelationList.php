<?php

namespace SilverShop\HasOneField;

use SilverStripe\ORM\DataList;

/**
 * Class HasOneButtonRelationList
 */
class HasOneButtonRelationList extends DataList
{
    protected $record;
    protected $name;
    protected $parent;

    public function __construct($record, $name, $parent)
    {
        $this->record = $record;
        $this->name = $name;
        $this->parent = $parent;
        parent::__construct($record->ClassName);
    }

    public function add($item)
    {
        $this->parent->{$this->name."ID"} = $item->ID;
        $this->parent->write();
    }
}
