# SilverStripe has_one field

Allows you to create a CMS button for creating and editing a single related object. It is actually a grid field, but just looks like a button.

![demo](https://raw.github.com/wiki/burnbright/silverstripe-hasonefield/images/hasonefield.gif)

## How to use

You must pass through the parent context ($this), so that the has_one relationship can be set
by the `GridFieldDetailForm`.

In Warehouse.php
```php
	public function getCMSFields() {
		$fields = parent::getCMSFields();
		if($this->Address()->exists()){
			$fields->addFieldsToTab("Root.Main", array(
				ReadonlyField::create("add", "Address", $this->Address()->toString())
			));
		}
		$fields->removeByName("AddressID");
		$fields->addFieldToTab("Root.Main",
			HasOneButtonField::create("Address", "Address", $this->Address(), $this)
		);

		return $fields;
	}
```

## Caveats

The field name must match the has_one relationship name.