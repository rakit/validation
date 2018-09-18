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
$validation = $validator->make($_POST + $_FILES, [
    'name'                  => 'required',
    'email'                 => 'required|email',
    'password'              => 'required|min:6',
    'confirm_password'      => 'required|same:password',
    'avatar'                => 'required|uploaded_file:0,500K,png,jpeg',
    'skills'                => 'array',
    'skills.*.id'           => 'required|numeric',
    'skills.*.percentage'   => 'required|numeric'
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

$validation = $validator->validate($_POST + $_FILES, [
    'name'                  => 'required',
    'email'                 => 'required|email',
    'password'              => 'required|min:6',
    'confirm_password'      => 'required|same:password',
    'avatar'                => 'required|uploaded_file:0,500K,png,jpeg',
    'skills'                => 'array',
    'skills.*.id'           => 'required|numeric',
    'skills.*.percentage'   => 'required|numeric'
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

In this case, 2 examples above will output the same results. 

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

And also there are several message variables depends on their rules.

Here are some ways to register/set your custom message(s):

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
To do this you can use `:` as message separator or using chaining methods.

Examples:

```php
$validator = new Validator;

$validation_a = $validator->make($dataset_a, [
	'age' => 'required|min:18'
]);

$validation_a->setMessages([
	'age:min' => '18+ only',
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
* [required_unless](#rule-required_unless)
* [required_with](#rule-required_with)
* [required_without](#rule-required_without)
* [required_with_all](#rule-required_with_all)
* [required_without_all](#rule-required_without_all)
* [uploaded_file](#rule-uploaded_file)
* [default/defaults](#rule-default)
* [email](#rule-email)
* [uppercase](#rule-uppercase)
* [lowercase](#rule-lowercase)
* [json](#rule-json)
* [alpha](#rule-alpha)
* [numeric](#rule-numeric)
* [alpha_num](#rule-alpha_num)
* [alpha_dash](#rule-alpha_dash)
* [in](#rule-in)
* [not_in](#rule-not_in)
* [min](#rule-min)
* [max](#rule-max)
* [between](#rule-between)
* [digits](#rule-digits)
* [digits_between](#rule-digits_between)
* [url](#rule-url)
* [integer](#rule-integer)
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
* [callback](#callback)

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

<a id="rule-required_unless"></a>
#### required_unless:another_field,value_1,value_2,...

The field under validation must be present and not empty unless the anotherfield field is equal to any value.

<a id="rule-required_with"></a>
#### required_with:field_1,field_2,...

The field under validation must be present and not empty only if any of the other specified fields are present.

<a id="rule-required_without"></a>
#### required_without:field_1,field_2,...

The field under validation must be present and not empty only when any of the other specified fields are not present.

<a id="rule-required_with_all"></a>
#### required_with_all:field_1,field_2,...

The field under validation must be present and not empty only if all of the other specified fields are present.

<a id="rule-required_without_all"></a>
#### required_without_all:field_1,field_2,...

The field under validation must be present and not empty only when all of the other specified fields are not present.

<a id="rule-uploaded_file"></a>
#### uploaded_file:min_size,max_size,file_type_a,file_type_b,...

This rule will validate `$_FILES` data, but not for multiple uploaded files. 
Field under this rule must be following rules below to be valid:

* `$_FILES['key']['error']` must be `UPLOAD_ERR_OK` or `UPLOAD_ERR_NO_FILE`. For `UPLOAD_ERR_NO_FILE` you can validate it with `required` rule. 
* If min size is given, uploaded file size **MUST NOT** be lower than min size.
* If max size is given, uploaded file size **MUST NOT** be higher than max size.
* If file types is given, mime type must be one of those given types.

Here are some example definitions and explanations:

* `uploaded_file`: uploaded file is optional. When it is not empty, it must be `ERR_UPLOAD_OK`. 
* `required|uploaded_file`: uploaded file is required, and it must be `ERR_UPLOAD_OK`. 
* `uploaded_file:0,1M`: uploaded file size must be between 0 - 1 MB, but uploaded file is optional.
* `required|uploaded_file:0,1M,png,jpeg`: uploaded file size must be between 0 - 1MB and mime types must be `image/jpeg` or `image/png`.

<a id="rule-default"></a>
#### default/defaults

This is special rule that doesn't validate anything. 
It just set default value to your attribute if that attribute is empty or not present.

For example if you have validation like this

```php
$validation = $validator->validate([
    'enabled' => null
], [
    'enabled' => 'default:1|required|in:0,1'
    'published' => 'default:0|required|in:0,1'
]);

$validation->passes(); // true
```

Validation passes because we sets default value for `enabled` and `published` to `1` and `0` which is valid.

<a id="rule-email"></a>
#### email

The field under this validation must be valid email address.

<a id="rule-uppercase"></a>
#### uppercase

The field under this validation must be valid uppercase.

<a id="rule-lowercase"></a>
#### lowercase

The field under this validation must be valid lowercase.

<a id="rule-json"></a>
#### json

The field under this validation must be valid JSON string.

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
#### in:value_1,value_2,...

The field under this rule must be included in the given list of values.

This rule is using `in_array` to check the value. 
By default `in_array` disable strict checking. 
So it doesn't check data type.
If you want enable strict checking, you can invoke validator like this:

```php
$validation = $validator->validate($data, [
    'enabled' => [
        'required', 
        $validator('in', [true, 1])->strict()
    ]
]);
```

Then 'enabled' value should be boolean `true`, or int `1`.

<a id="rule-not_in"></a>
#### not_in:value_1,value_2,...

The field under this rule must not be included in the given list of values.

This rule also using `in_array`. You can enable strict checking by invoking validator and call `strict()` like example in rule `in` above.

<a id="rule-min"></a>
#### min:number

The field under this rule must have a size greater or equal than the given number. 

For string data, value corresponds to the number of characters. For numeric data, value corresponds to a given integer value. For an array, size corresponds to the count of the array.

<a id="rule-max"></a>
#### max:number

The field under this rule must have a size lower or equal than the given number. 
Value size calculated in same way like `min` rule.

<a id="rule-between"></a>
#### between:min,max

The field under this rule must have a size between min and max params. 
Value size calculated in same way like `min` and `max` rule.

<a id="rule-digits"></a>
#### digits:value

The field under validation must be numeric and must have an exact length of `value`.

<a id="rule-digits_between"></a>
#### digits_between:min,max

The field under validation must have a length between the given `min` and `max`.

<a id="rule-url"></a>
#### url

The field under this rule must be valid url format.
By default it check common URL scheme format like `any_scheme://...`.
But you can specify URL schemes if you want.

For example:

```php
$validation = $validator->validate($inputs, [
    'random_url' => 'url',          // value can be `any_scheme://...`
    'https_url' => 'url:http',      // value must be started with `https://`
    'http_url' => 'url:http,https', // value must be started with `http://` or `https://`
    'ftp_url' => 'url:ftp',         // value must be started with `ftp://`
    'custom_url' => 'url:custom',   // value must be started with `custom://`
    'mailto_url' => 'url:mailto',   // value must conatin valid mailto URL scheme like `mailto:a@mail.com,b@mail.com`
    'jdbc_url' => 'url:jdbc',       // value must contain valid jdbc URL scheme like `jdbc:mysql://localhost/dbname`
]);
```

> For common URL scheme and mailto, we combine `FILTER_VALIDATE_URL` to validate URL format and `preg_match` to validate it's scheme. 
  Except for JDBC URL, currently it just check a valid JDBC scheme.

<a id="rule-integer"></a>
#### integer
The field under this rule must be integer.

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

<a id="callback"></a>
#### callback

You can use this rule to define your own validation rule.
This rule can't be registered using string pipe.
To use this rule, you should put Closure inside array of rules.

For example:

```php
$validation = $validator->validate($_POST, [
    'even_number' => [
        'required',
        function ($value) {
            // false = invalid
            return (is_numeric($value) AND $value % 2 === 0);
        }
    ]
]);
```

You can set invalid message by returning a string. 
For example, example above would be:

```php
$validation = $validator->validate($_POST, [
    'even_number' => [
        'required',
        function ($value) {
            if (!is_numeric($value)) {
                return ":attribute must be numeric.";
            }
            if ($value % 2 !== 0) {
                return ":attribute is not even number.";
            }
            // you can return true or don't return anything if value is valid
        }
    ]
]);
```

> Note: `Rakit\Validation\Rules\Callback` instance is binded into your Closure. 
  So you can access rule properties and methods using `$this`.

## Register/Modify Rule

Another way to use custom validation rule is to create a class extending `Rakit\Validation\Rule`. 
Then register it using `setValidator` or `addValidator`.

For example, you want to create `unique` validator that check field availability from database. 

First, lets create `UniqueRule` class:

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
        $stmt = $this->pdo->prepare("select count(*) as count from `{$table}` where `{$column}` = :value");
        $stmt->bindParam(':value', $value);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
	
        // true for valid, false for invalid
        return intval($data['count']) === 0;
    }
}

```

Then you need to register `UniqueRule` instance into validator like this:

```php
use Rakit\Validation\Validator;

$validator = new Validator;

$validator->addValidator('unique', new UniqueRule($pdo));
```

Now you can use it like this:

```php
$validation = $validator->validate($_POST, [
    'email' => 'email|unique:users,email,exception@mail.com'
]);
```

In `UniqueRule` above, property `$message` is used for default invalid message. And property `$fillable_params` is used for `fillParameters` method (defined in `Rakit\Validation\Rule` class). By default `fillParameters` will fill parameters listed in `$fillable_params`. For example `unique:users,email,exception@mail.com` in example above, will set:

```php
$params['table'] = 'users';
$params['column'] = 'email';
$params['except'] = 'exception@mail.com';
```

> If you want your custom rule accept parameter list like `in`,`not_in`, or `uploaded_file` rules, 
  you just need to override `fillParameters(array $params)` method in your custom rule class.

Note that `unique` rule that we created above also can be used like this:

```php
$validation = $validator->validate($_POST, [
    'email' => [
    	'required', 'email',
    	$validator('unique', 'users', 'email')->message('Custom message')
    ]
]);
```

So you can improve `UniqueRule` class above by adding some methods that returning its own instance like this:

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
        $this->params['column'] = $column;
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

Then you can use it in more funky way like this:

```php
$validation = $validator->validate($_POST, [
    'email' => [
    	'required', 'email',
    	$validator('unique')->table('users')->column('email')->except('exception@mail.com')->message('Custom message')
    ]
]);
```

## Getting Validated, Valid, and Invalid Data

For example you have validation like this:

```php
$validation = $validator->validate([
    'title' => 'Lorem Ipsum',
    'body' => 'Lorem ipsum dolor sit amet ...',
    'published' => null,
    'something' => '-invalid-'
], [
    'title' => 'required',
    'body' => 'required',
    'published' => 'default:1|required|in:0,1',
    'something' => 'required|numeric'
]);
```

You can get validated data, valid data, or invalid data using methods in example below:

```php
$validatedData = $validation->getValidatedData();
// [
//     'title' => 'Lorem Ipsum',
//     'body' => 'Lorem ipsum dolor sit amet ...',
//     'published' => '1' // notice this
//     'something' => '-invalid-'
// ]

$validData = $validation->getValidData();
// [
//     'title' => 'Lorem Ipsum',
//     'body' => 'Lorem ipsum dolor sit amet ...',
//     'published' => '1'
// ]

$invalidData = $validation->getInvalidData();
// [
//     'something' => '-invalid-'
// ]
```

