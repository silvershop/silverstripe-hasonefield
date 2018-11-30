# SilverStripe has_one field

[![Build Status](https://travis-ci.org/silvershop/silverstripe-hasonefield.svg?branch=master)](https://travis-ci.org/silvershop/silverstripe-hasonefield)
[![Latest Stable Version](https://poser.pugx.org/silvershop/silverstripe-hasonefield/v/stable)](https://packagist.org/packages/silvershop/silverstripe-hasonefield)
[![Latest Unstable Version](https://poser.pugx.org/silvershop/silverstripe-hasonefield/v/unstable)](https://packagist.org/packages/silvershop/silverstripe-hasonefield)


Allows you to create a CMS button for creating and editing a single related
object. It is actually a grid field, but just looks like a button.

![demo](https://raw.github.com/wiki/silvershop/silverstripe-hasonefield/images/hasonefield.gif)

## Installation

```
composer require silvershop/silverstripe-hasonefield
```

## Usage

```php
    use SilverShop\HasOneField\HasOneButtonField;

    private static $has_one = [
        'Address' => 'Address'
    ];

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab("Root.Main",
            HasOneButtonField::create($this, "Address")
        );

        return $fields;
    }
```

You must pass through the parent context ($this), so that the `has_one`
relationship can be set by the `GridFieldDetailForm`.
