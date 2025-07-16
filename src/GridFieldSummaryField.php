<?php

namespace SilverShop\HasOneField;

use SilverStripe\Core\Convert;
use SilverStripe\Forms\GridField\GridField;
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
    protected $relationName;
    /**
     * The location of this fragmemt
     *
     * @var string
     */
    protected $targetFragment;
    /**
     * @var string
     */
    protected $summaryField;

    /**
     * Setup this component
     *
     * @param string $name The name of the relation
     * @param string $summaryField The field on the record to use for the summary
     * @param string $targetFragment The location of this fragment
     */
    public function __construct($name, $summaryField = "Title", $targetFragment = 'buttons-before-left')
    {
        $this->relationName = $name;
        $this->targetFragment = $targetFragment;
        $this->summaryField = $summaryField;
    }

    /**
     * Get the value of name
     *
     * @return string
     */
    public function getRelationName()
    {
        return $this->relationName;
    }

    /**
     * Set the value of name
     *
     * @param string $relationName the relation name
     * @return static
     */
    public function setRelationName($relationName)
    {
        $this->relationName = $relationName;
        return $this;
    }

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
     * @return static
     */
    public function setSummaryField($summaryField)
    {
        $this->summaryField = $summaryField;
        return $this;
    }

    /**
     * Generate HTML for the GridField
     *
     * @param GridField|HasOneButtonField $gridField The current GridField
     * @return array
     */
    public function getHTMLFragments($gridField)
    {
        $record = $gridField->getRecord();

        $field = ReadonlyField::create(
            $gridField->getName() . '_' . Convert::raw2htmlid(static::class),
            ReadonlyField::name_to_label($this->relationName)
        )
            ->setValue($record->{$this->summaryField})
            ->addExtraClass('gridfield-summary-field');

        return [
            $this->targetFragment => $field->Field(),
        ];
    }
}
