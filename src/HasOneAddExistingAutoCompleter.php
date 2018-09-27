<?php

namespace SilverShop\HasOneField;

use SilverStripe\Control\Controller;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_HTMLProvider;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\View\ArrayData;
use SilverStripe\View\SSViewer;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\ORM\DataObject;

/**
 * Class GridFieldHasOneEditButton
 */
class HasOneAddExistingAutoCompleter extends GridFieldAddExistingAutocompleter
{
    /**
     * Check if a record has been set, if so, don't load the fields
     * 
     * @param GridField $gridField
     * 
     * @return array
     */
    public function getHTMLFragments($gridField)
    {
        if (!$gridField->getRecord()->exists()) {
            return parent::getHTMLFragments($gridField);
        }

        return [
            $this->targetFragment => ""
        ];
    }

    /**
     * Overwrite default add to and inlude redirect
     *
     * @param GridField $gridField
     * @param string $actionName Action identifier, see {@link getActions()}.
     * @param array $arguments Arguments relevant for this
     * @param array $data All form data
     */
    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if ($actionName == 'addto' && isset($data['relationID']) && $data['relationID']) {
            $parent = $gridField->getParent();
            $relation = $gridField->getRelation() . "ID";
            $item = DataObject::get($gridField->getModelClass())
                ->byID($data['relationID']);

            if (empty($parent)) {
                throw new ValidationException(
                    _t(__CLASS__ . '.ParentNotFound', "Parent record not found")
                );
            }

            if (empty($item)) {
                throw new ValidationException(
                    _t(__CLASS__ . '.ItemNotFound', "Related record not found")
                );
            }

            // Save this relation to the DB
            $parent->{$relation} = $data['relationID'];
            $parent->write();

            $gridField->State->GridFieldAddRelation = $data['relationID'];

            return Controller::curr()->getResponse()->setStatusCode(
                200,
                _t(__CLASS__ . '.Linked', "Linked")
            );
        }
    }
    
}
