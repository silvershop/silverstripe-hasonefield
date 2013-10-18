# Has one field

Allows you to create a CMS button for creating and editing a single related object.
It is a grid field, but just looks like a button.

Very much a proof-of-concept at this stage. Feel free to take this and make it nice :smirk:

## How to use

You must pass through the parent context ($this), so that the has_one relationship can be set
by the `GridFieldDetailForm`.

```php
	function getCMSFields(){
		$fields = parent::getCMSFields();
		$fields->merge(new FieldList(
			HasOneButtonField::create("Likebox", "Likebox", $this->Likebox(), $this)
		));
		return $fields;
	}
```
