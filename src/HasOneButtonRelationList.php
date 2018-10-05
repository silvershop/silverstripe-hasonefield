<?php

namespace SilverShop\HasOneField;

use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;

/**
 * Class HasOneButtonRelationList
 */
class HasOneButtonRelationList extends DataList
{
    /**
     * @var DataObject
     */
    protected $record;

    /**
     * @var string
     */
    protected $relationName;

    /**
     * @var DataObject
     */
    protected $parent;

    /**
     * HasOneButtonRelationList constructor.
     * @param DataObject $parent
     * @param DataObject $record
     * @param string $relationName
     */
    public function __construct(DataObject $parent, DataObject $record, $relationName)
    {
        $this->record = $record;
        $this->relationName = $relationName;
        $this->parent = $parent;

        parent::__construct($record->ClassName);
    }

    public function add($item)
    {
        $this->parent->setField("{$this->relationName}ID", $item->ID);
        $this->parent->write();
    }

    public function remove($item)
    {
        $this->parent->setField("{$this->relationName}ID", 0);
        $this->parent->write();
    }
}
