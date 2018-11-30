<?php

namespace SilverShop\HasOneField\Tests;

use SilverStripe\Dev\SapphireTest;

class HasOneButtonFieldTest extends SapphireTest
{
    private static $fixture_file = 'fixtures.yml';

    public function testHasOneButtonField()
    {
        $field = HasOneButtonField::create($record);
    }
}
