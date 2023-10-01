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
 * Class ClassMapping
 * @package PhpLiblinear\Converter
 * @author Matthieu Beurel <m.beurel@nexboard.fr>
 */
class ClassMapping extends Converter
{
  /**
   * @var array
   */
  protected $classMap = array();

  /**
   * ClassMapping constructor.
   *
   * @param string $nameInstance
   * @param string $varPath
   *
   * @throws \Exception
   */
  public function __construct(string $nameInstance, string $varPath)
  {
    parent::__construct("classMap", $nameInstance, $varPath);
  }

  /**
   * @param array $data
   *
   * @return Converter
   */
  public function setData(array $data): Converter
  {
    $this->classMap = $data;
    return $this;
  }

  /**
   * @return array
   */
  public function getData(): array
  {
    return $this->classMap;
  }

  /**
   * @param string $label
   *
   * @return $this
   */
  public function addLabel(string $label)
  {
    if(!in_array($label, $this->classMap))
    {
      $this->classMap[] = $label;
    }
    return $this;
  }

  /**
   * @param $label
   *
   * @return false|int|string
   */
  public function getClassMapId($label)
  {
    return array_search($label, $this->classMap);
  }

  /**
   * @param $id
   *
   * @return mixed
   */
  public function getClassMapValue($id)
  {
    return array_key_exists($id, $this->classMap) ? $this->classMap[$id] : null;
  }
}