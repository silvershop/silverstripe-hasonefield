<?php

namespace SilverShop\HasOneField;

use SilverStripe\Control\Controller;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Forms\GridField\AbstractGridFieldComponent;
use SilverStripe\Forms\GridField\GridField_HTMLProvider;
use SilverStripe\View\ArrayData;
use SilverStripe\View\SSViewer;

/**
 * This component provides a button for opening the the view only form provided by
 * {@link GridFieldDetailForm}.
 *
 * Only returns a button if canView() for this record returns true.
 */
class GridFieldHasOneViewButton extends AbstractGridFieldComponent implements GridField_HTMLProvider
{
    protected $targetFragment;

    protected $buttonName;

    /**
     * Set to true if this view button will redirect the user
     * to an object that needs sudo mode enabled to view it
     *
     * @var bool
     */
    protected $requireSudoMode;

    public function setButtonName($name)
    {
        $this->buttonName = $name;

        return $this;
    }

    public function __construct($targetFragment = 'before', $requireSudoMode = false)
    {
        $this->targetFragment = $targetFragment;
        $this->requireSudoMode = $requireSudoMode;
    }

    public function getHTMLFragments($gridField)
    {
        $record = $gridField->getRecord();
        $singleton = singleton($gridField->getModelClass());
        if (!$record->exists() || !$record->isInDB() || !$singleton->canView()) {
            return [];
        }

        if (!$this->buttonName) {
            // provide a default button name, can be changed by calling {@link setButtonName()} on this component
            $objectName = $singleton->hasMethod('i18n_singular_name') ? $singleton->i18n_singular_name() : ClassInfo::shortName($singleton);
            $this->buttonName = _t('SilverStripe\\Forms\\GridField\\GridField.View', 'View {name}', ['name' => $objectName]);
        }

        $viewLink = Controller::join_links($gridField->Link('item'), $record->ID, 'view');
        // The edit url is used for DataObjects that require sudo mode so that
        // object fields will become editable after verification
        if ($this->requireSudoMode && $singleton->canEdit()) {
            $viewLink = Controller::join_links($gridField->Link('item'), $record->ID, 'edit');
        }

        $data = new ArrayData([
            'ViewLink' => $viewLink,
            'ButtonName' => $this->buttonName,
        ]);

        $templates = SSViewer::get_templates_by_class($this, '', __CLASS__);
        return [
            $this->targetFragment => $data->renderWith($templates),
        ];
    }
}
