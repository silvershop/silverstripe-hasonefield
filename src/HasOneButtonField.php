<?php

namespace SilverShop\HasOneField;

use SilverStripe\ORM\DataObject;
use SilverStripe\View\Requirements;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDetailForm;

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
     * @param GridFieldConfig|null $customConfig
     * @param boolean|null $useAutocompleter
     */
    public function __construct(DataObject $parent, $relationName, $fieldName = null, $title = null, ?GridFieldConfig $customConfig = null, $useAutocompleter = true)
    {
        $record = $parent->{$relationName}();
        $this->setRecord($record);
        $this->parent = $parent;
        $this->relation = $relationName;

        $config = GridFieldConfig::create()
            ->addComponent(new GridFieldHasOneButtonRow())
            ->addComponent(new GridFieldSummaryField($relationName))
            ->addComponent(new GridFieldDetailForm())
            ->addComponent(new GridFieldHasOneUnlinkButton($parent, 'buttons-before-right'))
            ->addComponent(new GridFieldHasOneEditButton('buttons-before-right'));

        if ($useAutocompleter) {
            $config->addComponent(new HasOneAddExistingAutoCompleter('buttons-before-right'));
        }

        $list = HasOneButtonRelationList::create($parent, $this->record, $relationName);

        // Limit the existing list so that autocomplete will find results
        $list = $list->filter("ID", $this->record->ID);

        parent::__construct($fieldName ?: $relationName, $title, $list, ($customConfig) ?: $config);
        $this->setModelClass($record->ClassName);
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


    public function FieldHolder($properties = [])
    {
        Requirements::css("silvershop/silverstripe-hasonefield:client/dist/styles/bundle.css");
        Requirements::javascript("silvershop/silverstripe-hasonefield:client/dist/js/bundle.js");

        return parent::FieldHolder($properties);
    }
}
