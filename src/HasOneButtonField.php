<?php

namespace SilverShop\HasOneField;

use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDetailForm;

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
                    ->addComponent(new GridFieldDetailForm())
                    ->addComponent(new GridFieldHasOneEditButton());
        $list = new HasOneButtonRelationList($this->record, $name, $parent);
        parent::__construct($name, $title, $list, $config);
    }

    public function getRecord()
    {
        return $this->record;
    }
}
