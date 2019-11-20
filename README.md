# php-liblinear

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.2-8892BF.svg)](https://php.net/)
[![Latest Stable Version](https://img.shields.io/packagist/v/mbeurel/php-liblinear.svg)](https://packagist.org/packages/mbeurel/php-liblinear)
[![Total Downloads](https://poser.pugx.org/mbeurel/php-liblinear/downloads.svg)](https://packagist.org/packages/mbeurel/php-liblinear)
[![License](https://poser.pugx.org/mbeurel/php-liblinear/license.svg)](https://packagist.org/packages/mbeurel/php-liblinear)

A simple, light and efficient short-text classification tool based on [Liblinear](https://www.csie.ntu.edu.tw/~cjlin/liblinear/) for PHP.

Inspired by Python Library [TextGrocery](https://github.com/2shou/TextGrocery).

For Lemmarizer words, useddistribution [TreeTagger](https://www.cis.uni-muenchen.de/~schmid/tools/TreeTagger)

## Installation Liblinear library

Debian or Ubuntu :

```bash
apt-get install liblinear-dev liblinear-tools liblinear3
```

Other distribution view [repository github](https://github.com/cjlin1/liblinear)

## Install php-liblinear

You can install it with Composer:

```
composer require mbeurel/php-liblinear
```

## Examples

Example scripts are available ina separate repository [php-liblinear/examples](https://github.com/mbeurel/php-liblinear/tree/master/exemple).

## Sample Code
```php
include "vendor/autoload.php";
use PhpLiblinear\Classification\LibLinear;
$data = [
  ["French", "Ceci est un texte dans la langue française."],
  ["French", "Bonjour, comment allez vous ?"],
  ["French", "Bonjour, je m'appelle Jean !!!"],
  ["English", "This is a english language text."],
  ["English", "Hello, How are you ?"],
  ["English", "Hello, my name is Jean !!!"],
];
try {
  
  // Init library
  $libLinear = new LibLinear("instanceName", 0);

  // Liblinear train
  $libLinear->train($data);
  
  // Save model
  $libLinear->save();

  // Load model
  $libLinear->load();
  
  // Liblinear predict : String or Array parameters, to array => ["Bonjour, je m'appelle Louis", "Comment allez vous ?"]
  $result = $libLinear->predict("Bonjour, je m'appelle Louis");
  
  // View result
  var_dump($result);

  //  $result = array(
  //    0  =>  array(
  //      "value"        =>  "French",
  //      "percentage"   =>  0.763259,
  //      "percentages"  =>  array(
  //        "French"        => 0.763259
  //        "English"       => 0.236741
  //      )
  //    )
  //  )

} catch(\Exception $e) {
  echo $e;
}
```

## Credits

Created by [Matthieu Beurel](https://www.mbeurel.com). Sponsored by [Nexboard](https://www.nexboard.fr).