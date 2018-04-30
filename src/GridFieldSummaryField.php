<?php

namespace SilverShop\HasOneField;

use SilverStripe\View\ArrayData;
use SilverStripe\Forms\TextField;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridField_HTMLProvider;
use SilverStripe\Forms\ReadonlyField;

/**
 * Class GridFieldSummaryField
 */
class GridFieldSummaryField implements GridField_HTMLProvider
{

    /**
     * The location of this fragmemt
     * 
     * @var string
     */
    protected $targetFragment;

    /**
     * Get the value of targetFragment
     */ 
    public function getTargetFragment()
    {
        return $this->targetFragment;
    }

    /**
     * Set the value of targetFragment
     *
     * @param string $targetFragment The target fragment 
     * 
     * @return self
     */ 
    public function setTargetFragment($targetFragment)
    {
        $this->targetFragment = $targetFragment;
        return $this;
    }

    /**
     * @var string
     */
    protected $summaryField;

    /**
     * Get the value of summaryField
     *
     * @return string
     */ 
    public function getSummaryField()
    {
        return $this->summaryField;
    }

    /**
     * Set the value of summaryField
     *
     * @param string $summaryField The field name
     *
     * @return self
     */ 
    public function setSummaryField(string $summaryField)
    {
        $this->summaryField = $summaryField;
        return $this;
    }

    /**
     * Setup this component
     *
     * @param string $targetFragment The location of this fragment
     * @param string $summaryField   The field on the record to use for the summary 
     */
    public function __construct($targetFragment = 'before', $summaryField = "Title")
    {
        $this->targetFragment = $targetFragment;
        $this->summaryField = $summaryField;
    }

    /**
     * Generate HTML for the GridField
     * 
     * @param GridField $gridField The current GridField
     * 
     * @return array
     */
    public function getHTMLFragments($gridField)
    {
        $record = $gridField->getRecord();
        $summary = $this->summaryField;

        $field = ReadonlyField::create(
            'gridfield_hasone_summary',
            $record->i18n_singular_name()
        );
        
        $field->setValue($record->{$summary});

        return array(
            $this->targetFragment => $field->Field()
        );
    }
}
