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
            ->addComponent(new GridFieldHasOneUnlinkButton($parent));

        $list = new HasOneButtonRelationList($this->record, $relationName, $parent);

        parent::__construct($fieldName ?: $relationName, $title, $list, $config);

        if ($title !== null) {
            $buttonName = $this->getRecord()->exists()
                ? _t(GridField::class . '.Edit', 'Edit {name}', ['name' => $title])
                : _t(GridField::class . '.Add', 'Add {name}', ['name' => $title]);

            /** @var \SilverShop\HasOneField\GridFieldHasOneEditButton $editButton */
            $editButton = $this->getConfig()->getComponentByType(GridFieldHasOneEditButton::class);
            $editButton->setButtonName($buttonName);
        }
    }

    /**
     * @return \SilverStripe\ORM\DataObject
     */
    public function getRecord()
    {
        return $this->record;
    }
}
