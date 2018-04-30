<?php

namespace SilverShop\HasOneField;

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

    public function __construct($name, $title, $parent)
    {
        $this->record = $parent->{$name}();
        $this->parent = $parent;
        $config = GridFieldConfig::create()
            ->addComponent(new GridFieldSummaryField($name))
            ->addComponent(new GridFieldDetailForm())
            ->addComponent(new GridFieldHasOneEditButton())
            ->addComponent(new GridFieldHasOneUnlinkButton($parent));

        if (!$this->record->exists()) {
            $config->addComponent(new GridFieldAddExistingAutocompleter());
        }

        $list = new HasOneButtonRelationList($this->record, $name, $parent);

        // Limit the existing list so that autocomplete will find results
        $list = $list->filter("ID", $this->record->ID);

        parent::__construct($name, $title, $list, $config);
    }

    public function getRecord()
    {
        return $this->record;
    }
}
