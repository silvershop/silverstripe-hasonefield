<?php

namespace SilverShop\HasOneField;

use SilverStripe\Control\Controller;
use SilverStripe\Forms\GridField\GridField_HTMLProvider;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Model\ArrayData;
use SilverStripe\View\SSViewer;

/**
 * Class GridFieldHasOneEditButton
 */
class GridFieldHasOneEditButton extends GridFieldAddNewButton implements GridField_HTMLProvider
{

    /**
     * @param \SilverShop\HasOneField\HasOneButtonField $gridField
     * @return array
     */
    public function getHTMLFragments($gridField)
    {
        $record = $gridField->getRecord();

        if (!$record->exists() || !$record->isInDB()) {
            return parent::getHTMLFragments($gridField); //use parent add button
        }

        $singleton = singleton($gridField->getModelClass());
        if (!$singleton->canCreate()) return [];

        if (!$this->buttonName) {
            // provide a default button name, can be changed by calling {@link setButtonName()} on this component
            $objectName = $singleton->i18n_singular_name();

            if ($record->exists()) {
                $buttonName = _t('SilverStripe\Forms\GridField\GridField.Edit', 'Edit {name}', ['name' => $objectName]);
            } else {
                $buttonName = _t('SilverStripe\Forms\GridField\GridField.Add', 'Add {name}', ['name' => $objectName]);
            }

            $this->setButtonName($buttonName);
        }

        $data = ArrayData::create(
            [
                'NewLink'    => Controller::join_links($gridField->Link('item'), $record->ID, 'edit'),
                'ButtonName' => $this->buttonName,
            ]
        );

        return [
            $this->targetFragment => $data->renderWith(SSViewer::get_templates_by_class(static::class)),
        ];
    }
}
