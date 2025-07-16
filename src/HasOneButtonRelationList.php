<?php

namespace SilverShop\HasOneField;

use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\DataObject;

/**
 * Class HasOneButtonRelationList
 */
class HasOneButtonRelationList extends ArrayList
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

        parent::__construct([$record]);
    }

    public function add(mixed $item): void
    {
        $parent = $this->parent;
        // Get the relationship type (has_one or belongs_to)
        $relationType = $parent->getRelationType($this->relationName);
        switch ($relationType) {
            // If belongs_to, retrieve and write to the has_one side of the relationship
            case 'belongs_to':
                $parent->{$this->relationName} = $item;
                $hasOneRecord = $parent->getComponent($this->relationName);
                $hasOneRecord->write();
                break;
            // Otherwise assume has_one, and write to this record
            default:
                $parent->{$this->relationName} = $item;
                $parent->write();
                break;
        }

        $this->items = [$item];
    }

    public function remove(mixed $item)
    {
        $parent = $this->parent;
        $relationName = $this->relationName;
        // Get the relationship type (has_one or belongs_to)
        $relationType = $parent->getRelationType($relationName);
        switch ($relationType) {
            // If belongs_to, retrieve and write to the has_one side of the relationship
            case 'belongs_to':
                $hasOneRecord = $parent->getComponent($this->relationName);
                $parentClass = $parent->getClassName();

                $schema = $parentClass::getSchema();
                $hasOneFieldName = $schema->getRemoteJoinField(
                    $parentClass,
                    $relationName,
                    $relationType,
                    $polymorphic
                );

                $hasOneRecord->{$hasOneFieldName} = null;
                $hasOneRecord->write();
                break;
            // Otherwise assume has_one, and write to this record
            default:
                $parent->{$relationName} = null;
                $parent->write();
                break;
        }

        $this->items = [];
    }
}
