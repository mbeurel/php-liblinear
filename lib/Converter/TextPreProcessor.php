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
 * Class TextPreProcessor
 * @package PhpLiblinear\Converter
 * @author Matthieu Beurel <m.beurel@nexboard.fr>
 */
class TextPreProcessor extends Converter
{
  /**
   * @var array
   */
  protected $textPrep = array(
    ">>remove<<" => 0
  );

  /**
   * TextPreProcessor constructor.
   *
   * @param string $nameInstance
   * @param string $varPath
   *
   * @throws \Exception
   */
  public function __construct(string $nameInstance, string $varPath)
  {
    parent::__construct("textPrep", $nameInstance, $varPath);
  }

  /**
   * @param array $data
   *
   * @return Converter
   */
  public function setData(array $data): Converter
  {
    $this->textPrep = $data;
    return $this;
  }

  /**
   * @return array
   */
  public function getData(): array
  {
    return $this->textPrep;
  }

  /**
   * @param $text
   *
   * @return array
   */
  public function textPreprocessor($text)
  {
    $returnArray = array();
    $words = $this->tokenize($text);

    foreach($words as $word)
    {
      if(!array_key_exists($word, $this->textPrep))
      {
        $this->textPrep[$word] = count($this->textPrep);
      }
      $returnArray[] = $this->textPrep[$word];
    }
    return $returnArray;
  }

  /**
   * @param string $text
   *
   * @return array
   */
  protected function tokenize(string $text): array
  {
    $tokens = [];
    preg_match_all('/\w\w+/u', $text, $tokens);
    return $tokens[0];
  }
}