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
     * The name of the relation
     *
     * @var string 
     */
    protected $name;

    /**
     * Get the value of name
     *
     * @return string
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param string $name the relation name
     *
     * @return self
     */ 
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

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
     * @param string $name           The name of the relation
     * @param string $summaryField   The field on the record to use for the summary 
     * @param string $targetFragment The location of this fragment
     */
    public function __construct($name, $summaryField = "Title", $targetFragment = 'before')
    {
        $this->name = $name;
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
        $name = $this->name;

        $field = ReadonlyField::create(
            'gridfield_hasone_summary',
            $name
        );
        
        $field->setValue($record->{$summary});

        return [
            $this->targetFragment => $field->FieldHolder()
        ];
    }
}
