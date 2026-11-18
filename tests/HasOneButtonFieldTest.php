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
use SilverStripe\Core\Validation\ValidationException;

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

    public function testAddExistingRequiresPermissionToEditParent(): void
    {
        $this->logOut();

        $group = $this->objFromFixture(Group::class, 'testGroup');
        $other = $this->objFromFixture(Group::class, 'testGroup2');
        $field = HasOneButtonField::create($group, 'Parent');
        $autocompleter = $field->getConfig()->getComponentByType(HasOneAddExistingAutoCompleter::class);

        try {
            $autocompleter->handleAction($field, 'addto', [], ['relationID' => $other->ID]);
            $this->fail('Linking a record without edit permission on the parent should fail');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('permission', $e->getMessage());
        }

        $this->assertNotEquals($other->ID, (int) Group::get()->byID($group->ID)->ParentID);
    }
}
