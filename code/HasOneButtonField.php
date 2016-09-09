<?php

use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridField_HTMLProvider;
use SilverStripe\Control\Controller;
use SilverStripe\View\ArrayData;
use SilverStripe\ORM\DataList;

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

class GridFieldHasOneEditButton extends GridFieldAddNewButton implements GridField_HTMLProvider
{

    public function getHTMLFragments($gridField)
    {
        $record = $gridField->getRecord();
        if (!$record->exists() || !$record->isInDB()) {
            return parent::getHTMLFragments($gridField); //use parent add button
        }
        $singleton = singleton($gridField->getModelClass());
        if (!$singleton->canCreate()) {
            return array();
        }
        if (!$this->buttonName) {
            // provide a default button name, can be changed by calling {@link setButtonName()} on this component
            $objectName = $singleton->i18n_singular_name();
            $this->buttonName = _t('GridField.Edit', 'Edit {name}', array('name' => $objectName));
        }
        $data = new ArrayData(array(
            'NewLink' => Controller::join_links($gridField->Link('item'), $record->ID, 'edit'),
            'ButtonName' => $this->buttonName,
        ));

        return array(
            $this->targetFragment => $data->renderWith('GridFieldAddNewbutton')
        );
    }
}

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
