<?php

namespace SilverShop\HasOneField;

use SilverStripe\Control\Controller;
use SilverStripe\Forms\GridField\GridField_HTMLProvider;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\View\ArrayData;

/**
 * Class GridFieldHasOneEditButton
 */
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
        $data = ArrayData::create(
            array(
                'NewLink' => Controller::join_links($gridField->Link('item'), $record->ID, 'edit'),
                'ButtonName' => $this->buttonName,
            )
        );

        return array(
            $this->targetFragment => $data->renderWith(GridFieldAddNewButton::class)
        );
    }
}
