<?php

namespace SilverShop\HasOneField;

use SilverStripe\Control\Controller;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_ActionProvider;
use SilverStripe\Forms\GridField\GridField_FormAction;
use SilverStripe\Forms\GridField\GridField_HTMLProvider;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ValidationException;

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
     * The parent record to unlink the current record from
     * @var DataObject
     */
    protected $parent;

    /**
     * GridFieldHasOneUnlinkButton constructor.
     * @param DataObject $parent
     * @param string $targetFragment
     */
    public function __construct(DataObject $parent, $targetFragment = "before")
    {
        $this->parent = $parent;
        $this->targetFragment = $targetFragment;
    }

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
     * @param string $targetFragment
     * @return static
     */
    public function setTargetFragment($targetFragment)
    {
        $this->targetFragment = $targetFragment;
        return $this;
    }

    /**
     * Get the parent record to unlink the current record from
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
     * @return static
     */
    public function setParent(DataObject $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
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
     * @param GridField|HasOneButtonField $gridField
     * @param string $actionName Action identifier, see {@link getActions()}.
     * @param array $arguments Arguments relevant for this
     * @param array $data All form data
     * @throws ValidationException
     */
    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if ($actionName !== 'unlinkrelation') return;

        $record = $gridField->getRecord();
        if (!$record || !$record->exists()) return;

        /** @var DataObject|null $item */
        $item = $gridField->getList()->byID($record->ID);
        if ($item === null) return;

        if (!$item->canEdit()) {
            throw new ValidationException(
                _t(__CLASS__ . '.EditPermissionsFailure', "No permission to unlink record")
            );
        }

        $gridField->getList()->remove($item);

        Controller::curr()->getResponse()->setStatusCode(
            200,
            _t(__CLASS__ . '.Unlinked', "Unlinked")
        );
    }

    /**
     * @param GridField|HasOneButtonField $gridField
     * @return array
     */
    public function getHTMLFragments($gridField)
    {
        $record = $gridField->getRecord();
        if (!$record || !$record->exists()) return [];

        $field = new GridField_FormAction(
            $gridField,
            'gridfield_unlinkrelation',
            _t(__CLASS__ . '.Unlink', "Unlink"),
            'unlinkrelation',
            'unlinkrelation'
        );

        $field->setAttribute('data-icon', 'chain--plus')
            ->addExtraClass('btn btn-outline-secondary font-icon-link-broken action_gridfield_unlinkrelation');

        return [
            $this->targetFragment => $field->Field(),
        ];
    }
}
