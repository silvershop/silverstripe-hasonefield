<?php

namespace SilverShop\HasOneField;

use SilverStripe\ORM\DataObject;
use SilverStripe\View\Requirements;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverShop\HasOneField\GridFieldHasOneButtonRow;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;

/**
 * Class HasOneButtonField
 */
class HasOneButtonField extends GridField
{

    /**
     * The related object to the parent
     * 
     * @var DataObject
     */
    protected $record;

    /**
     * The current parent of the relationship (the base object we are editing)
     * 
     * @var DataObject
     */
    protected $parent;

    /**
     * The name of the relation this field is managing
     * 
     * @var string
     */
    protected $relation;

    /**
     * HasOneButtonField constructor.
     * @param \SilverStripe\ORM\DataObject $parent
     * @param string $relationName
     * @param string|null $fieldName
     * @param string|null $title
     */
    public function __construct(DataObject $parent, $relationName, $fieldName = null, $title = null)
    {
        $this->record = $parent->{$relationName}();
        $this->parent = $parent;
        $this->relation = $relationName;

        Requirements::css("silvershop/silverstripe-hasonefield:client/css/hasonefield.css");
        Requirements::javascript("silvershop/silverstripe-hasonefield:client/js/hasonefield.js");

        $config = GridFieldConfig::create()
            ->addComponent(new GridFieldHasOneButtonRow())
            ->addComponent(new GridFieldSummaryField($relationName))
            ->addComponent(new GridFieldDetailForm())
            ->addComponent(new GridFieldHasOneUnlinkButton($parent, 'buttons-before-right'))
            ->addComponent(new GridFieldHasOneEditButton('buttons-before-right'))
            ->addComponent(new HasOneAddExistingAutoCompleter('buttons-before-right'));

        $list = HasOneButtonRelationList::create($parent, $this->record, $relationName);

        // Limit the existing list so that autocomplete will find results
        $list = $list->filter("ID", $this->record->ID);

        // Get columns to display inline
        $this->addExtraClass("d-flex align-items-start");

        parent::__construct($fieldName ?: $relationName, $title, $list, $config);
    }

    /**
     * @return \SilverStripe\ORM\DataObject
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * @param DataObject|null $record
     */
    public function setRecord($record)
    {
        $this->record = $record ?: singleton(get_class($this->record));
    }

    /**
     * Get the current parent
     *
     * @return DataObject
     */ 
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set the current parent
     *
     * @param DataObject $parent parent of the relationship
     *
     * @return self
     */ 
    public function setParent(DataObject $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Get the name of the relation this field is managing
     *
     * @return string
     */ 
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * Set the name of the relation this field is managing
     *
     * @param string $relation The relation name
     *
     * @return self
     */ 
    public function setRelation(string $relation)
    {
        $this->relation = $relation;
        return $this;
    }
}
