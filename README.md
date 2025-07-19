<div align="center">
    <a href="https://www.php.net">
        <img
            alt="PHP"
            src="https://www.php.net/images/logos/new-php-logo.svg"
            width="150">
    </a>
</div>


<p align="center">
  <a href="https://php.net" target="_blank"><img src="https://img.shields.io/static/v1?label=PHP&message=%3E= 8.0&color=blue&style=flat-square" alt="PHP Version : >= 8.0"></a>
  <a href="https://phpstan.org/" target="_blank"><img src="https://img.shields.io/static/v1?label=PHPstan&message=Level 8&color=blue&style=flat-square" alt="PHP Version : >= 8.0"></a>
    <img src="https://img.shields.io/static/v1?label=License&message=MIT&color=brightgreen&style=flat-square" alt="License">
</p>

## Installation

#### With [Composer](https://getcomposer.org/):
`
${target-root} - destination root
`

```bash
  graffiti:/Aurora$ git --clone https://github.com/graffiti-concept/dot-array.git
  graffiti:/Aurora$ mkdir -p ${target-root}/Aurora/Generic/Dot
  graffiti:/Aurora$ cp ./dot-array/Aurora/Generic/Dot/* ${target-root}/Aurora/Generic/Dot
```

Edit composer.json and add namespace to autoload section
`
  "autoload": {
    "psr-4": {
      "Aurora\\": "path/to/Aurora"
    }
  }
`

#### Manual installation
1. Download the latest release
2. Extract the files into your project
```php
require_once '/path/to/Aurora/src/Dot/DotArrayService.php';
require_once '/path/to/Aurora/src/Dot/DotArray.php';
```

## Test
```bash
  graffiti:/Aurora$ vendor/bin/phpstan analyse
```
`
Note: Using configuration file /home/php-projects/Aurora/dot-array/phpstan.neon.
4/4 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%
`
[OK] No errors


## Examples

Creating an object DotArray

```php
$dotArray = new \Aurora\Aurora\Generic\Dot\DotArray($source);
```

### Example of input data
Here and further in the description an example of working with the following associative array will be considered.
```php
$source = [
    0 => 'ok',
    '3' => 5555,
    5 => false,
    '6.0'=>true,
    'vString' => 'string line',
    'vInt' => 2025,
    'vFloat' => M_PI,
    'vBool' => true,
    'vOn' => 'on',
    'vOff' => 'off',
    'vNull' => null,
    'vObject' => new ArrayObject([2, 4, 6, 8, 0, 10]),
    'branch' => [
        0, 1, 2, 3, 4, 5, 6, 7, 8, 9
    ],
    'branch2' => [
        'leaf' => 'value1',
        'leaf2' => 'value2',
        'leaf3' => 'value3',
        5 => 55,
        10 => null,
        'vOn' => 'on',
        'vOff' => 'off',
    ],
    'branch3' => [
        'leaf' => M_PI,
        'leaf2' => (int)M_PI,
        'leaf3' => (string)M_PI,
        'leaf4' => new ArrayObject([2, 4, 6, 8, 0]),
        'leaf5' => [
            'el' => 'v1',
            'el2' => 'v2',
            'el3' => [11, '22', 33, 44, 55],
        ],
        'leaf6' => [],
    ],
];
```

## Methods

<a name="has"></a>
### has()
Checks if a given key exists:
```php
public function has(int|string $key): bool{...}
```
<a name="isEmpty"></a>
### isEmpty()
Checks if the value of a given key in an array is empty
```php
public function isEmpty(null|int|string $key): bool{...}
```
<a name="count"></a>
### count()
If the search value is an array, returns the number of elements. Otherwise, -1.
```php
public function count(null|int|string $key): int{...}
```
<a name="delete"></a>
### delete()
Deletes the given key
```php
public function delete(null|int|string $key): bool{...}
```
<a name="get"></a>
### get()
Returns the value of a given key
```php
public function get(null|int|string $key, bool &$finded = false): mixed{...}
```

<a name="get"></a>
### getMultiple()
Returns the values of a given keys
```php
public function getMultiple(array $arrayOfValues): array{...}
```

#### example 1
```php
print_r($dotArray->getMultiple([
    'branch3.leaf',
    'branch3.leaf2',
    'branch3.leaf3',
    'branch3.leaf5',
]));
````
#### result 1
```
[
    [branch3.leaf] => 3.14159265359
    [branch3.leaf2] => 3
    [branch3.leaf3] => 3.14159265359
    [branch3.leaf5] => [...]
]
```
#### example 2
    ['branch3.leaf', 'leaf']

    - 'branch3.leaf' - current source path

    - 'leaf' - result path

```php
print_r($dotArray->getMultiple([
    ['branch3.leaf', 'leaf'],
    ['branch3.leaf2', 'leaf2'],
    ['branch3.leaf3', 'leaf3'],
    ['branch3.leaf5', 'leaf5'],
]));
```
#### result 2
```
[
    [leaf] => 3.14159265359
    [leaf2] => 3
    [leaf3] => 3.14159265359
    [leaf5] => [
            [el] => v1
            [el2] => v2
            [el3] => [...]
        ]
]
```
#### example 3
    the third parameter in array specifies the type of return values

    - 0 by default
    - [1|'int'] equal $dotArray->getInt(...),
    - [2|'float'] equal $dotArray->getFloat(...),
    - [3|'string'] equal $dotArray->getString(...),
    - [4|'array'] equal $dotArray->getArray(...),
    - [5|'object'] equal $dotArray->getObject(...),
    - [6|'bool'|'boolean'] equal $dotArray->getBool(...),

```php
print_r($dotArray->getMultiple([
    ['branch3.leaf', 'leaf', 1],
    ['branch3.leaf2', 'leaf2', 1],
    ['branch3.leaf3', 'leaf3', 1],
    ['branch3.leaf5', 'leaf5', 1],
]));
```
#### result 3
```
[
    [leaf] => 3
    [leaf2] => 3
    [leaf3] => 3
]
```


<a name="getString"></a>
### getString()
Returns the value of the specified key if the type of the searched value is a `string` or can be cast to one. Otherwise, null.

if the found value has an object type, then an attempt is made to convert the object to a string by calling magic methods:`jsonSerialize`,`__toString`,`__serialize`

```php
public function getString(null|int|string $key, bool &$finded = false): ?string{...}
```

#### example

```php
$dotArray = new \Aurora\Aurora\Generic\Dot\DotArray($source);
var_dump($dotArray->getString('branch3.leaf2')); // contains a value of type (int)3.14159265359
var_dump($dotArray->getString('branch3.leaf')); // contains a value of type (float)3.14159265359
var_dump($dotArray->getString('branch3.leaf3')); // contains a value of type (string)3.14159265359
var_dump($dotArray->getString('branch3.leaf4')); // contains a value of type (object)
var_dump($dotArray->getString('branch3.leaf5')); // contains a value of type (array)
var_dump($dotArray->getString('branch-unexists.leaf')); // undefined
```
#### result
```
NULL
NULL
string(13) "3.14159265359"
string(23) "[0,[2,4,6,8,0],[],null]"
NULL
NULL
```

<a name="getInt"></a>
### getInt()
Returns the value of the specified key if the type of the searched value is a `int` or can be cast to one. Otherwise, null.
If a `$minValue` and/or `$maxValue` return value is specified, `null` is returned if the range is exceeded.
```php
public function getInt(int|string $key, ?int $minValue = null, ?int $maxValue = null): ?int{...}
```

#### example

```php
$dotArray = new \Aurora\Aurora\Generic\Dot\DotArray($source);
var_dump($dotArray->getInt('branch3.leaf2')); // contains a value of type (int)3.14159265359
var_dump($dotArray->getInt('branch3.leaf')); // contains a value of type (float)3.14159265359
var_dump($dotArray->getInt('branch3.leaf3')); // contains a value of type (string)3.14159265359
var_dump($dotArray->getInt('branch3.leaf4')); // contains a value of type (object)
var_dump($dotArray->getInt('branch3.leaf5')); // contains a value of type (array)
var_dump($dotArray->getInt('branch-unexists.leaf')); // undefined
```
#### result
```
int(3)
int(3)
int(3)
NULL
NULL
NULL
```

<a name="getFloat"></a>
### getFloat()
Returns the value of the specified key if the type of the searched value is a `float` or can be cast to one. Otherwise, null.
If a `$minValue` and/or `$maxValue` return value is specified, `null` is returned if the range is exceeded.

```php
public function getFloat(int|string $key, ?float $minValue = null, ?float $maxValue = null): ?float{...}
```
#### example

```php
$dotArray = new \Aurora\Aurora\Generic\Dot\DotArray($source);
var_dump($dotArray->getFloat('branch3.leaf2')); // contains a value of type (int)3.14159265359
var_dump($dotArray->getFloat('branch3.leaf')); // contains a value of type (float)3.14159265359
var_dump($dotArray->getFloat('branch3.leaf3')); // contains a value of type (string)3.14159265359
var_dump($dotArray->getFloat('branch3.leaf4')); // contains a value of type (object)
var_dump($dotArray->getFloat('branch3.leaf5')); // contains a value of type (array)
var_dump($dotArray->getFloat('branch-unexists.leaf')); // undefined
```
#### result
```
float(3)
float(3.141592653589793)
float(3.14159265359)
NULL
NULL
NULL
```

<a name="getObject"></a>
### getObject()
Returns the value of the specified key if the type of the searched value is a `object`. Otherwise, null.
```php
public function getObject(int|string $key): ?object{...}
```
#### example

```php
$dotArray = new \Aurora\Aurora\Generic\Dot\DotArray($source);
var_dump($dotArray->getObject('branch3.leaf2')); // contains a value of type (int)3.14159265359
var_dump($dotArray->getObject('branch3.leaf')); // contains a value of type (float)3.14159265359
var_dump($dotArray->getObject('branch3.leaf3')); // contains a value of type (string)3.14159265359
var_dump($dotArray->getObject('branch3.leaf4')); // contains a value of type (object)
var_dump($dotArray->getObject('branch3.leaf5')); // contains a value of type (array)
var_dump($dotArray->getObject('branch-unexists.leaf')); // undefined
```
#### result
```
NULL
NULL
NULL
object(ArrayObject)#7 (1) {
  ["storage":"ArrayObject":private]=>[]
}
NULL
NULL
```


<a name="getArray"></a>
### getArray()
Returns the value of the specified key if the type of the searched value is a `array`. Otherwise, null.
```php
public function getArray(int|string $key): ?array{...}
```
#### example

```php
$dotArray = new \Aurora\Aurora\Generic\Dot\DotArray($source);
var_dump($dotArray->getArray('branch3.leaf2')); // contains a value of type (int)3.14159265359
var_dump($dotArray->getArray('branch3.leaf')); // contains a value of type (float)3.14159265359
var_dump($dotArray->getArray('branch3.leaf3')); // contains a value of type (string)3.14159265359
var_dump($dotArray->getArray('branch3.leaf4')); // contains a value of type (object)
var_dump($dotArray->getArray('branch3.leaf5')); // contains a value of type (array)
var_dump($dotArray->getArray('branch-unexists.leaf')); // undefined
```
#### result
```
NULL
NULL
NULL
NULL
array(3) {
  ["el"]=>string(2) "v1"
  ["el2"]=>string(2) "v2"
  ["el3"]=> array(5) {...}
}
NULL
```

<a name="getBool"></a>
### getBool()
Returns the value of the specified key if the type of the searched value is a `bool`. Otherwise, null.
```php
public function getBool(int|string $key): ?array{...}
```
#### example

```php
$dotArray = new \Aurora\Aurora\Generic\Dot\DotArray($source);
var_dump($dotArray->getArray('branch3.leaf2')); // value = (int)3
var_dump($dotArray->getArray('branch3.leaf')); //  value = (float)3.14159265359
var_dump($dotArray->getBool('vBool'));  // value = (bool)true
var_dump($dotArray->getBool('5'));      // value = (bool)false
var_dump($dotArray->getBool('6.0'));    // value = (bool)true
var_dump($dotArray->getBool('vOn'));    // value = (string)on
var_dump($dotArray->getBool('vOff'));   // value = (string)off
```
#### result
```
NULL
NULL
bool(true)
bool(false)
bool(true)
bool(true)
bool(false)
```


<a name="set"></a>
### set()
Sets the given key/value pair, if the key does not exist, then creates it
```php
public function set(int|string $key, mixed $value): bool{...}
```
##### example

```php
$dotArray = new \Aurora\Aurora\Generic\Dot\DotArray([]);
$dotArray->set('branchFloat.node.leaf',M_PI);
```

##### result
```php
[
    [branchFloat] => [
            [node] => [
                [leaf] => 3.14159265359
            ]
    ]
]
```

<a name="setMultiple"></a>
### setMultiple()
#### Sets the value via an array of given key/value pairs, if the key does not exist, then creates it

```php
public function setMultiple(array $arrayOfValues): int{...}
```
##### example

```php
$dotArray = new \Aurora\Aurora\Generic\Dot\DotArray([]);
$dotArray->setMultiple([
    'branchString.node.leaf' => 'string',
    'branchInt.node.leaf' => 10,
    'branchFloat.node.leaf' => M_PI,
]);
```

##### result
```php
[
    [branchString] => [
            [node] => [
                    [leaf] => string
            ]
    ]
    [branchInt] => [
            [node] => [
                [leaf] => 10
            ]
    ]
    [branchFloat] => [
            [node] => [
                [leaf] => 3.14159265359
            ]
    ]
]
```



<a name="push"></a>
### push()
Pushes a given value to the end of the array in a given key
```php
public function push(int|string $key, mixed $value): self{...}
```

### clone()
A new object is created with the data obtained by the key.
```php
public function clone(int|string $key, bool $deleteOriginalTwig = false): self{...}
```

` if set $deleteOriginalTwig = true, the actions are similar `

```php
$dotArray = new \Aurora\Aurora\Generic\Dot\DotArray($source);
$newDotArray = $dotArray->clone('branch3.leaf5');
$dotArray->delete('branch3.leaf5');
```



<a name="dotify"></a>

### Dotify()
#### Converts the source array to a dotnotation
```php
public function dotify(): array{...}
```
##### example

```php
(new \Aurora\Aurora\Generic\Dot\DotArray($source))->dotify();
```
##### result
```
    [0] => ok
    [3] => 5555
    [5] => 
    [vString] => string line
    [vInt] => 2025
    [vFloat] => 3.14159265359
    [vNull] => 
    [vObject.0] => 2
    ...
    [branch.9] => 9
    [branch2.leaf] => value1
    [branch2.leaf2] => value2
    [branch2.leaf3] => value3
    [branch2.5] => 55
    [branch2.10] => 
    [branch3.leaf] => 3.14159265359
    [branch3.leaf2] => 3
    [branch3.leaf3] => 3.14159265359
    [branch3.leaf4.0] => 2
    ...
    [branch3.leaf4.4] => 0
    [branch3.leaf5.el] => v1
    [branch3.leaf5.el2] => v2
    [branch3.leaf5.el3.0] => 11
    ...
    [branch3.leaf5.el3.4] => 55
```





## License

This package is an open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).