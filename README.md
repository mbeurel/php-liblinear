# php-liblinear

A simple, light and efficient short-text classification tool based on LibLinear for PHP.

Inspired by Python Librairy [TextGrocery](https://github.com/2shou/TextGrocery).

For Lemmarizer words, used [TreeTagger](https://www.cis.uni-muenchen.de/~schmid/tools/TreeTagger)

## Installation

You can install it with Composer:

```
composer require mbeurel/php-liblinear
```

## Examples

Example scripts are available in a separate repository [php-liblinear/examples](https://github.com).

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