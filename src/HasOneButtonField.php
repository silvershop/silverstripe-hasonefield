<?php

namespace SilverShop\HasOneField;

use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\ORM\DataObject;

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
     * @param string $name
     * @param string|null $title
     */
    public function __construct(DataObject $parent, $name, $title = null)
    {
        $this->record = $parent->{$name}();
        $this->parent = $parent;
        $config = GridFieldConfig::create()
                    ->addComponent(new GridFieldDetailForm())
                    ->addComponent(new GridFieldHasOneEditButton());
        $list = new HasOneButtonRelationList($this->record, $name, $parent);
        parent::__construct($name, $title, $list, $config);
    }

    /**
     * @return \SilverStripe\ORM\DataObject
     */
    public function getRecord()
    {
        return $this->record;
    }
}
