<?php
include "../vendor/autoload.php";
use PhpLiblinear\Classification\LibLinear;
$create = 0;
$searchWord = "";
$instance = "default";
foreach($argv as $key => $value)
{
  if(strpos($value, "--create") !== false)
  {
    $create = 1;
  }
  elseif(strpos($value, "--search") !== false)
  {
    $searchWord = str_replace("--search=", "", $value);
  }
  elseif(strpos($value, "--instance") !== false)
  {
    $instance = str_replace("--instance=", "", $value);
  }
  elseif($key > 0)
  {
    throw new \Exception("Error : The parameters $value is not defined");
  }
}
$data = [
  ["French", "Ceci est un texte dans la langue française"],
  ["French", "Bonjour, comment allez vous ?"],
  ["French", "Bonjour, je m'appelle Jean !!!"],
  ["English", "This is a english language text"],
  ["English", "Hello, How are you ?"],
  ["English", "Hello, my name is Jean !!!"],
];
try {
  $libLinear = new LibLinear($instance, __DIR__."/var", array(
      "type"      =>  0,
      "cost"      =>  1.0,
      "epsilon"   =>  0.1,
      "debug"     =>  false
    )
  );
  if($create)
  {
    echo "Classification -> Start \n";
    $libLinear->train($data);
    $libLinear->save();
    echo "Classification -> Finish\n";
  }
  if($searchWord)
  {
    $libLinear->load();
    var_dump($libLinear->predict($searchWord));
  }
} catch(\Exception $e) {
  echo $e;
}

