Rakit\Validation - PHP Data Validation
=========================================

[![Build Status](https://img.shields.io/travis/rakit/validation.svg?style=flat-square)](https://travis-ci.org/rakit/validation)
[![License](http://img.shields.io/:license-mit-blue.svg?style=flat-square)](http://doge.mit-license.org)


PHP Standalone library for validating data. Inspired by `Illuminate\Validation` Laravel.

## Example

```php

use Rakit\Validation\Validator;

$validator = new Validator;
$validation = $validator->validate($_POST, [
	'username' => 'required|alphadash',
	'email' => 'required|email',
	'password' => 'required|min:6', // using rule parameter
	'confirm_password' => 'required|same:password',
	// also can be used like this
	'avatar' => [
		'required',
		$validator('uploaded_file')->maxSize('1M')->fileTypes('png|jpeg')
	]
]);

// handling errors
if ($validation->fails()) {
	$errors = $validation->errors();
	
	// getting error messages
	$first_error_username = $errors->first('username'); // null or string
	$last_error_username = $errors->last('username'); // null or string
	$error_username_required = $errors->get('username', 'required'); // null or string
	$all_errors = $errors->all('<li>:message</li>'); // array of '<li>:message</li>'
	$imploded_errors = $errors->implode(', ', '<li>:message</li>'); // implode of all()
}

```

## Coming Soon

This is just beginning. Still need some feature and tests.