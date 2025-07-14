<?php

namespace SilverShop\HasOneField;

use SilverStripe\Control\Controller;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_ActionProvider;
use SilverStripe\Forms\GridField\GridField_FormAction;
use SilverStripe\Forms\GridField\GridField_HTMLProvider;
use SilverStripe\ORM\DataObject;
use SilverStripe\Core\Validation\ValidationException;

/**
 * Class GridFieldHasOneEditButton
 */
class GridFieldHasOneUnlinkButton implements GridField_HTMLProvider, GridField_ActionProvider
{
    /**
     * If this is set to true, this {@link GridField_ActionProvider} will
     * remove the object from the list, instead of deleting.
     *
     * @var boolean
     */
    protected $removeRelation = true;

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
        return ['unlinkrelation', 'deleterecord'];
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
        if (!in_array($actionName, ['unlinkrelation', 'deleterecord'])) {
            return;
        }

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

        $gridField->setRecord(null);

        if ($actionName === 'deleterecord') {
            if (!$item->canDelete()) {
                throw new ValidationException(
                    _t(__CLASS__ . '.DeletePermissionsFailure', "No delete permissions")
                );
            }

            $item->delete();

            $message = _t(__CLASS__ . '.Deleted', 'Deleted');
        } else {
            $gridField->getList()->remove($item);
            $message = _t(__CLASS__ . '.Unlinked', 'Unlinked');
        }

        Controller::curr()->getResponse()->setStatusCode(200, $message);
    }

    /**
     * @param GridField|HasOneButtonField $gridField
     * @return array
     */
    public function getHTMLFragments($gridField)
    {
        $record = $gridField->getRecord();
        if (!$record || !$record->exists()) return [];

        if (!$this->getRemoveRelation()) {
            $field = new GridField_FormAction(
                $gridField,
                'gridfield_unlinkrelation',
                _t(__CLASS__ . '.Delete', 'Delete'),
                'deleterecord',
                'deleterecord'
            );

            $field->setAttribute('data-icon', 'chain--plus')
                ->addExtraClass('align-items-center d-flex btn btn-outline-secondary font-icon-trash action_gridfield_unlinkrelation');
        } else {
            $field = new GridField_FormAction(
                $gridField,
                'gridfield_unlinkrelation',
                _t(__CLASS__ . '.Unlink', "Unlink"),
                'unlinkrelation',
                'unlinkrelation'
            );

            $field->setAttribute('data-icon', 'chain--plus')
                ->addExtraClass('align-items-center d-flex btn btn-outline-secondary font-icon-link-broken action_gridfield_unlinkrelation');
        }

        return [
            $this->targetFragment => $field->Field(),
        ];
    }

    /**
     * Get whether to remove or delete the relation
     *
     * @return bool
     */
    public function getRemoveRelation()
    {
        return $this->removeRelation;
    }

    /**
     * Set whether to remove or delete the relation
     *
     * @param bool $removeRelation
     * @return $this
     */
    public function setRemoveRelation($removeRelation)
    {
        $this->removeRelation = (bool) $removeRelation;

        return $this;
    }
}
