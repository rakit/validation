Rakit Validation - PHP Standalone Validation Library
======================================================

[![Build Status](https://img.shields.io/travis/rakit/validation.svg?style=flat-square)](https://travis-ci.org/rakit/validation)
[![License](http://img.shields.io/:license-mit-blue.svg?style=flat-square)](http://doge.mit-license.org)


PHP Standalone library for validating data. Inspired by `Illuminate\Validation` Laravel.

## Requirements

* PHP 5.5 or higher
* Composer for installation

## Quick Start

#### Installation

```
composer require "rakit/validation"
```

#### Usage

There is two way to validate. Using `make` to make validation object, 
then validate it using `validate`. Or just use `validate`. 
Examples:

Using `make`:

```php
<?php

require('vendor/autoload.php');

use Rakit\Validation\Validator;

$validator = new Validator;

// make it
$validation = $validator->make($_POST, [
	'name' => 'required',	
	'email' => 'required|email',
	'password' => 'required|min:6',
	'confirm_password' => 'required|same:password',
]);

// then validate
$validation->validate();

if ($validation->fails()) {
	// handling errors
	$errors = $validation->errors();
	echo "<pre>";
	print_r($errors->firstOfAll());
	echo "</pre>";
	exit;
} else {
	// validation passes
	echo "Success!";
}

```

or just `validate` it:

```php
<?php

require('vendor/autoload.php');

use Rakit\Validation\Validator;

$validator = new Validator;

$validation = $validator->validate($_POST, [
	'name' => 'required',	
	'email' => 'required|email',
	'password' => 'required|min:6',
	'confirm_password' => 'required|same:password',
]);

if ($validation->fails()) {
	// handling errors
	$errors = $validation->errors();
	echo "<pre>";
	print_r($errors->firstOfAll());
	echo "</pre>";
	exit;
} else {
	// validation passes
	echo "Success!";
}

```

In this case, 2 example above will output the same results. 

But with `make` you can setup something like custom invalid message, custom attribute alias, etc before validation running.

### Attribute Alias

By default we will transform your attribute into more readable text. For example `confirm_password` will be displayed as `Confirm password`.
But you can set it anything you want with `setAlias` or `setAliases` method.

Example:

```php
$validator = new Validation;

// To set attribute alias, you should use `make` instead `validate`.
$validation->make([
	'province_id' => $_POST['province_id'],
	'district_id' => $_POST['district_id']
], [
	'province_id' => 'required|numeric',
	'district_id' => 'required|numeric'
]);

// now you can set aliases using this way:
$validation->setAlias('province_id', 'Province');
$validation->setAlias('district_id', 'District');

// or this way:
$validation->setAliases([
	'province_id' => 'Province',
	'district_id' => 'District'
]);

// then validate it
$validation->validate();

```

Now if `province_id` value is empty, error message would be 'Province is required'.

## Custom Validation Message

Before register/set custom messages, here are some variables you can use in your custom messages:

* `:attribute`: will replaced into attribute alias.
* `:value`: will replaced into stringify value of attribute. For array and object will replaced to json.
* `:params[n]`: will replaced into rule parameter, `n` is index array. For example `:params[0]` in `min:6` will replaced into `6`.

And here are some ways to register/set your custom message(s):

#### Custom Messages for Validator

With this way, anytime you make validation using `make` or `validate` it will set your custom messages for it.
It is useful for localization.

To do this, you can set custom messages as first argument constructor like this:

```php
$validator = new Validator([
	'required' => ':attribute harus diisi',
	'email' => ':email tidak valid',
	// etc
]);

// then validation belows will use those custom messages
$validation_a = $validator->validate($dataset_a, $rules_for_a);
$validation_b = $validator->validate($dataset_b, $rules_for_b);

```

Or using `setMessages` method like this:

```php
$validator = new Validator;
$validator->setMessages([
	'required' => ':attribute harus diisi',
	'email' => ':email tidak valid',
	// etc
]);

// now validation belows will use those custom messages
$validation_a = $validator->validate($dataset_a, $rules_for_dataset_a);
$validation_b = $validator->validate($dataset_b, $rules_for_dataset_b);

```

#### Custom Messages for Validation

Sometimes you may want to set custom messages for specific validation.
To do this you can set your custom messages as 3rd argument of `$validator->make` or `$validator->message` like this:

```php
$validator = new Validator;

$validation_a = $validator->validate($dataset_a, $rules_for_dataset_a, [
	'required' => ':attribute harus diisi',
	'email' => ':email tidak valid',
	// etc
]);

```

Or you can use `$validation->setMessages` like this:

```php
$validator = new Validator;

$validation_a = $validator->make($dataset_a, $rules_for_dataset_a);
$validation_a->setMessages([
	'required' => ':attribute harus diisi',
	'email' => ':email tidak valid',
	// etc
]);

...

$validation_a->validate();
```

#### Custom Message for Specific Attribute Rule

Sometimes you may want to set custom message for specific rule attribute. 
To do this you can use `.` as message separator or using chaining method.

Examples:

```php
$validator = new Validator;

$validation_a = $validator->make($dataset_a, [
	'age' => 'required|min:18'
]);

$validation_a->setMessages([
	'age.min' => '18+ only',
]);

$validation_a->validate();
```

Or using chaining methods:

```php
$validator = new Validator;

$validation_a = $validator->make($dataset_a, [
	'photo' => [
		'required',
		$validator('uploaded_file')->fileTypes('jpeg|png')->message('Photo must be jpeg/png image')
	]
]);

$validation_a->validate();
```

## Available Rules

Soon ...

## Register/Modify Rule

Soon ...
