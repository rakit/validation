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

There are two ways to validating data with this library. Using `make` to make validation object, 
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
$validator = new Validator;

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
To do this you can set your custom messages as 3rd argument of `$validator->make` or `$validator->validate` like this:

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

Below is list of all available validation rules

* [required](#rule-required)
* [required_if](#rule-required_if)
* [uploaded_file](#rule-uploaded_file)
* [email](#rule-email)
* [alpha](#rule-alpha)
* [numeric](#rule-numeric)
* [alpha_num](#rule-alpha_num)
* [alpha_dash](#rule-alpha_dash)
* [in](#rule-in)
* [not_in](#rule-not_in)
* [min](#rule-min)
* [max](#rule-max)
* [between](#rule-between)
* [url](#rule-url)
* [ip](#rule-ip)
* [ipv4](#rule-ipv4)
* [ipv6](#rule-ipv6)
* [array](#rule-array)
* [same](#rule-same)
* [regex](#rule-regex)
* [date](#rule-date)
* [accepted](#rule-accepted)
* [present](#rule-present)
* [different](#rule-different)
* [after](#after)
* [before](#before)

<a id="rule-required"></a>
#### required

The field under this validation must be present and not 'empty'.

Here are some examples:

| Value         | Valid |
|---------------|-------|
| `'something'` | true  |
| `'0'`         | true  |
| `0`           | true  |
| `[0]`         | true  |
| `[null]`      | true  |
| null          | false |
| []            | false |
| ''            | false |

For uploaded file, `$_FILES['key']['error']` must not `UPLOAD_ERR_NO_FILE`.

<a id="rule-required_if"></a>
#### required_if:another_field,value_1,value_2,...

The field under this rule must be present and not empty if the anotherfield field is equal to any value.

For example `required_if:something,1,yes,on` will be required if `something` value is one of `1`, `'1'`, `'yes'`, or `'on'`. 

<a id="rule-uploaded_file"></a>
#### uploaded_file:min_size,max_size,file_type_a,file_type_b,...

This rule will validate `$_FILES` data, but not for multiple uploaded files. 
Field under this rule must be following rules below to be valid:

* `$_FILES['key']['error']` must be `UPLOAD_ERR_OK` or `UPLOAD_ERR_NO_FILE`. For `UPLOAD_ERR_NO_FILE` you can validate it with `required` rule. 
* If min size is given, uploaded file size **MUST NOT** be lower than min size.
* If max size is given, uploaded file size **MUST NOT** be higher than max size.
* If file types is given, mime type must be one of those given types.

Here are some example definitions and explanations:

| Definition                             | Explanation                                                                                   |
|----------------------------------------|-----------------------------------------------------------------------------------------------|
| `uploaded_file`                        | Uploaded file is optional. When it is not empty, it must be `ERR_UPLOAD_OK`.                  |
| `required|uploaded_file`               | Uploaded file is required, and it must be `ERR_UPLOAD_OK`.                                    |
| `uploaded_file:0,1M`                   | uploaded file size must be between 0 - 1 MB, but uploaded file are optional                   |
| `required|uploaded_file:0,1M,png,jpeg` | uploaded file size must be between 0 - 1MB and mime types must be `image/jpeg` or `image/png` |

<a id="rule-email"></a>
#### email

The field under this validation must be valid email address.

<a id="rule-alpha"></a>
#### alpha

The field under this rule must be entirely alphabetic characters.

<a id="rule-numeric"></a>
#### numeric

The field under this rule must be numeric.

<a id="rule-alpha_num"></a>
#### alpha_num

The field under this rule must be entirely alpha-numeric characters.

<a id="rule-alpha_dash"></a>
#### alpha_dash

The field under this rule may have alpha-numeric characters, as well as dashes and underscores.

<a id="rule-in"></a>
#### in

The field under this rule must be included in the given list of values.

<a id="rule-not_in"></a>
#### not_in

The field under this rule must not be included in the given list of values.

<a id="rule-min"></a>
#### min

soon ...

<a id="rule-max"></a>
#### max

soon ...

<a id="rule-between"></a>
#### between

soon ...

<a id="rule-url"></a>
#### url

The field under this rule must be valid url format.

<a id="rule-ip"></a>
#### ip

The field under this rule must be valid ipv4 or ipv6.

<a id="rule-ipv4"></a>
#### ipv4

The field under this rule must be valid ipv4.

<a id="rule-ipv6"></a>
#### ipv6

The field under this rule must be valid ipv6.

<a id="rule-array"></a>
#### array

The field under this rule must be array.

<a id="rule-same"></a>
#### same:another_field

The field value under this rule must be same with `another_field` value.

<a id="rule-regex"></a>
#### regex:/your-regex/

The field under this rule must be match with given regex.

<a id="rule-date"></a>
#### date:format

The field under this rule must be valid date format. Parameter `format` is optional, default format is `Y-m-d`.

<a id="rule-accepted"></a>
#### accepted

The field under this rule must be one of `'on'`, `'yes'`, `'1'`, `'true'`, or `true`.

<a id="rule-present"></a>
#### present

The field under this rule must be exists, whatever the value is.

<a id="rule-different"></a>
#### different:another_field

Opposite of `same`. The field value under this rule must be different with `another_field` value.

<a id="after"></a>
#### after:tomorrow

Anything that can be parsed by `strtotime` can be passed as a parameter to this rule. Valid examples include :
- after:next week
- after:2016-12-31
- after:2016
- after:2016-12-31 09:56:02

<a id="before"></a>
#### before:yesterday

This also works the same way as the [after rule](#after). Pass anything that can be parsed by `strtotime`

## Register/Modify Rule

To create your own validation rule, you need to create a class extending `Rakit\Validation\Rule` 
then register it using `setValidator` or `addValidator`.

For example, you want to create `unique` validator that check field availability from database. 
First create your own class.

```php
<?php

use Rakit\Validation\Rule;

class UniqueRule extends Rule
{
    protected $message = ":attribute :value has been used";
    
    protected $fillable_params = ['table', 'column', 'except'];
    
    protected $pdo;
    
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    public function check($value)
    {
        // make sure required parameters exists
        $this->requireParameters(['table', 'column']);
    
        // getting parameters
        $column = $this->parameter('column');
        $table = $this->parameter('table');
        $except = $this->parameter('except');
	
        if ($except AND $except == $value) {
            return true;
        }
	
        // do query
        $stmt = $pdo->prepare("select count(*) as count from `{$table}` where `{$column}` = :value");
        $stmt->bindParam(':value', $value);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
	
        // true for valid, false for invalid
        return intval($data['count']) === 0;
    }
}

```

Then you can register `UniqueRule` into validator like this:

```php

use Rakit\Validation\Validator;

$validator = new Validator;

// register it
$validator->addValidator('unique, new UniqueRule($pdo));

// then you can use it like this:
$validation = $validator->validate($_POST, [
    'email' => 'required|email|unique:users,email,exception@mail.com'
]);

```

In `UniqueRule` above, property `$message` is used for default invalid message. And property `$fillable_params` is used for `setParameters` method. By default `setParameters` will fill parameters listed in `$fillable_params` property. For example `unique:users,email,exception@mail.com` in example above, will set:

```php
$params['table'] = 'users';
$params['column'] = 'email';
$params['except'] = 'exception@mail.com';
```

So if you want your own rule have dynamic rule params, you just need to override `setParameters(array $params)` method in your own Rule class.

Note that `unique` rule that we created above also can be used like this:

```php
$validation = $validator->validate($_POST, [
    'email' => [
    	'required', 'email',
    	$validator('unique', 'users', 'email')->message('Custom message')
    ]
]);
```

So you can improve example above by adding some methods that returning its own instance like this:

```php
<?php

use Rakit\Validation\Rule;

class UniqueRule extends Rule
{
    ...
    
    public function table($table)
    {
        $this->params['table'] = $table;
        return $this;
    }
    
    public function column($column)
    {
        $this->params['table'] = $column;
        return $this;
    }
    
    public function except($value)
    {
        $this->params['except'] = $value;
        return $this;
    }
    
    ...
}

```

And then you can use it in more funky way like this:

```php
$validation = $validator->validate($_POST, [
    'email' => [
    	'required', 'email',
    	$validator('unique')->table('users')->column('email')->except('exception@mail.com')->message('Custom message')
    ]
]);
```
