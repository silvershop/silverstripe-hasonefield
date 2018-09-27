<?php

namespace SilverShop\HasOneField;

use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;

/**
 * Class HasOneButtonField
 */
class HasOneButtonField extends GridField
{
    protected $record;
    protected $parent;

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
        $config = GridFieldConfig::create()
            ->addComponent(new GridFieldSummaryField($relationName))
            ->addComponent(new GridFieldDetailForm())
            ->addComponent(new GridFieldHasOneEditButton())
            ->addComponent(new GridFieldHasOneUnlinkButton($parent))
            ->addComponent(new HasOneAddExistingAutoCompleter());

        $list = new HasOneButtonRelationList($this->record, $relationName, $parent);

        // Limit the existing list so that autocomplete will find results
        $list = $list->filter("ID", $this->record->ID);

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
    public function setRecord($record): void
    {
        $this->record = $record ?: singleton(get_class($this->record));
    }
}
