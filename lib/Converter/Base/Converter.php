<?php
/*
 * This file is part of the php-liblinear.
 *
 * (c) Matthieu Beurel <m.beurel@nexboard.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpLiblinear\Converter\Base;
use PhpLiblinear\Tools\FilesystemTrait;

/**
 * Class Converter
 * @package PhpLiblinear\Converter\Base
 * @author Matthieu Beurel <m.beurel@nexboard.fr>
 */
abstract class Converter
{
  use FilesystemTrait;

  /**
   * @var array
   */
  protected $configFiles = array();

  /**
   * @var string
   */
  protected $configName;

  /**
   * Converter constructor.
   *
   * @param string $configName
   * @param string $nameInstance
   * @param string $varPath
   *
   * @throws \Exception
   */
  public function __construct(string $configName, string $nameInstance, string $varPath)
  {
    $this->nameInstance = $nameInstance;
    $this->varPath = $varPath;
    $varPathData = $this->join($this->getVarPath(), "data");
    $this->mkdir($varPathData);
    $this->configName = $configName;
    $this->configFiles = array(
      'textPrep'   => $varPathData.'/text_prep.json',
      'featPrep'   => $varPathData.'/feat_prep.json',
      'classMap'   => $varPathData.'/class_map.json',
      'values'     => $varPathData.'/values.svm',
    );
    if(!array_key_exists($this->configName, $this->configFiles))
    {
      throw new \Exception("Config name is not defined !!!");
    }
  }

  /**
   * @return $this
   */
  public function save()
  {
    if(!file_exists($this->configFiles[$this->configName]))
    {
      $this->remove($this->configFiles[$this->configName]);
    }
    file_put_contents($this->configFiles[$this->configName], json_encode($this->getData()));
    return $this;
  }

  /**
   * @return $this
   * @throws \Exception
   */
  public function load()
  {
    if(!file_exists($this->configFiles[$this->configName]))
    {
      throw new \Exception("The config file is not found !!!");
    }
    $this->setData(json_decode(file_get_contents($this->configFiles[$this->configName]), true));
    return $this;
  }

  /**
   * @param array $data
   *
   * @return $this
   */
  abstract public function setData(array $data) : self ;

  /**
   * @return array
   */
  abstract public function getData() : array;

  /**
   * @return int
   */
  public function countData()
  {
    return count($this->getData());
  }


}