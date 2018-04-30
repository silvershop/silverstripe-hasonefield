<?php

namespace SilverShop\HasOneField;

use SilverStripe\View\ArrayData;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_FormAction;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridField_HTMLProvider;
use SilverStripe\Forms\GridField\GridField_ActionProvider;
use SilverStripe\Forms\GridField\GridField_URLHandler;

/**
 * Class GridFieldHasOneEditButton
 */
class GridFieldHasOneUnlinkButton implements GridField_HTMLProvider, GridField_ActionProvider
{
    /**
     * Fragment to write the button to
     */
    protected $targetFragment;

    /**
     * Get fragment to write the button to
     */ 
    public function getTargetFragment()
    {
        return $this->targetFragment;
    }

    /**
     * Set fragment to write the button to
     *
     * @return self
     */ 
    public function setTargetFragment($targetFragment)
    {
        $this->targetFragment = $targetFragment;
        return $this;
    }

    /**
     * The parent record to unlink the current record from
     * 
     * @var DataObject
     */
    protected $parent;

    /**
     * Get the parent record to unlink the current record from
     *
     * @return DataObject
     */ 
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set the parent record to unlink the current record from
     *
     * @param DataObject $parent The parent record to unlink the current record from
     *
     * @return self
     */ 
    public function setParent(DataObject $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    public function __construct($parent, $targetFragment = "before")
    {
        $this->parent = $parent;
        $this->targetFragment = $targetFragment;
    }

    /**
     *
     * @param GridField $gridField
     * @return array
     */
    public function getActions($gridField)
    {
        return ['unlinkrelation'];
    }

    /**
     * Manipulate the state to add a new relation
     *
     * @param GridField $gridField
     * @param string $actionName Action identifier, see {@link getActions()}.
     * @param array $arguments Arguments relevant for this
     * @param array $data All form data
     */
    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if ($actionName == 'unlinkrelation') {
            $parent = $this->parent;
            $record = $gridField->getRecord();

            if (!$record || $record && !$record->exists()) {
                return;
            }
            
            $item = $gridField->getList()->byID($record->ID);
    
            if (!$item) {
                return;
            }
    
            if (!$item->canEdit()) {
                throw new ValidationException(
                    _t(__CLASS__ . '.EditPermissionsFailure', "No permission to unlink record")
                );
            }
    
            $gridField->getList()->remove($item);

            $response = Controller::curr()->getResponse();
            $response->setStatusCode(
                200,
                _t(__CLASS__ . '.Unlinked', "Unlinked")
            );
        }
    }

    public function getHTMLFragments($gridField)
    {
        $record = $gridField->getRecord();

        if ($record->exists()) {
            $field = new GridField_FormAction(
                $gridField,
                'gridfield_unlinkrelation',
                _t(__CLASS__ . '.Unlink', "Unlink"),
                'unlinkrelation',
                'unlinkrelation'
            );
            $field->setAttribute('data-icon', 'chain--plus');
            $field->addExtraClass('btn btn-outline-secondary font-icon-link-broken action_gridfield_unlinkrelation');

            return array(
                $this->targetFragment => $field->Field()
            );
        }
    }
}
