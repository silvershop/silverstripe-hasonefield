<?php

namespace SilverShop\HasOneField\Tests;

use SilverShop\HasOneField\GridFieldHasOneButtonRow;
use SilverShop\HasOneField\GridFieldHasOneEditButton;
use SilverShop\HasOneField\GridFieldHasOneUnlinkButton;
use SilverShop\HasOneField\GridFieldSummaryField;
use SilverShop\HasOneField\HasOneAddExistingAutoCompleter;
use SilverStripe\Dev\SapphireTest;
use SilverShop\HasOneField\HasOneButtonField;
use SilverStripe\Security\Group;

class HasOneButtonFieldTest extends SapphireTest
{
    protected static $fixture_file = 'fixtures.yml';

    public function testHasOneButtonField(): void
    {
        $group = $this->objFromFixture(Group::class, 'testGroup');
        $field = HasOneButtonField::create($group, 'Parent');

        $this->assertInstanceOf(Group::class, $field->getRecord());
        $this->assertSame('Parent', $field->getRelation());

        $components = [
            GridFieldHasOneButtonRow::class,
            GridFieldSummaryField::class,
            GridFieldHasOneUnlinkButton::class,
            GridFieldHasOneEditButton::class,
            HasOneAddExistingAutoCompleter::class,
        ];
        foreach ($components as $component) {
            $this->assertInstanceOf($component, $field->getConfig()->getComponentByType($component));
        }

        $field = HasOneButtonField::create($group, 'Parent', null, null, null, false);
        $this->assertNull($field->getConfig()->getComponentByType(
            HasOneAddExistingAutoCompleter::class
        ));
    }
}
