<?php
/*
 * This file is part of the php-liblinear.
 *
 * (c) Matthieu Beurel <m.beurel@nexboard.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpLiblinear\Converter;
use PhpLiblinear\Converter\Base\Converter;

/**
 * Class FeatureGenerator
 * @package PhpLiblinear\Converter
 * @author Matthieu Beurel <m.beurel@nexboard.fr>
 */
class FeatureGenerator extends Converter
{
  /**
   * @var array
   */
  protected $ngPrep = array(
    ">>remove<<" => 0
  );

  /**
   * FeatureGenerator constructor.
   *
   * @param string $nameInstance
   * @param string $varPath
   *
   * @throws \Exception
   */
  public function __construct(string $nameInstance, string $varPath)
  {
    parent::__construct("featPrep", $nameInstance, $varPath);
  }

  /**
   * @param array $data
   *
   * @return Converter
   */
  public function setData(array $data): Converter
  {
    $this->ngPrep = $data;
    return $this;
  }

  /**
   * @return array
   */
  public function getData(): array
  {
    return $this->ngPrep;
  }

  /**
   * @param $tokens
   *
   * @return array
   */
  public function bigram($tokens)
  {
    $feat = $this->unigram($tokens);
    foreach($this->arrayZip($tokens) as $values)
    {
      $key = implode(", ", $values);
      if(!array_key_exists($key, $this->ngPrep))
      {
        $this->ngPrep[$key] = count($this->ngPrep);
      }
      if(!array_key_exists($this->ngPrep[$key], $feat))
      {
        $feat[$this->ngPrep[$key]] = 0;
      }
      $feat[$this->ngPrep[$key]] += 1;
    }
    return $feat;
  }

  /**
   * @param $tokens
   *
   * @return array
   */
  public function unigram($tokens)
  {
    $feat = array();
    foreach($tokens as $v)
    {
      $key = sprintf("%s, ", $v);
      if(!array_key_exists($key, $this->ngPrep))
      {
        $this->ngPrep[$key] = count($this->ngPrep);
      }
      if(!array_key_exists($this->ngPrep[$key], $feat))
      {
        $feat[$this->ngPrep[$key]] = 0;
      }
      $feat[$this->ngPrep[$key]] += 1;
    }
    return $feat;
  }

  /**
   * @param $array
   *
   * @return mixed
   */
  protected function arrayZip($array)
  {
    $countArray = count($array);
    $out = array();
    for($i = 0; $i < $countArray; $i++)
    {
      $out[$i] = array();
      $out[$i][] = $array[$i];
      if($i+1 < $countArray)
      {
        $out[$i][] = $array[$i+1];
      }
      if(count($out[$i]) != 2)
      {
        unset($out[$i]);
      }
    }
    return $out;
  }
}