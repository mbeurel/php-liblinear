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
use PhpLiblinear\Tools\FilesystemTrait;

/**
 * Class DataConverter
 * @package PhpLiblinear\Converter
 * @author Matthieu Beurel <m.beurel@nexboard.fr>
 */
class DataConverter
{

  use FilesystemTrait;

  /**
   * @var ClassMapping
   */
  protected $classMapping;

  /**
   * @var FeatureGenerator
   */
  protected $featureGenerator;

  /**
   * @var TextPreProcessor
   */
  protected $textPreProcessor;

  /**
   * @var string
   */
  protected $svmFilePath;

  /**
   * DataConverter constructor.
   *
   * @param $nameInstance
   * @param $varPath
   *
   * @throws \Exception
   */
  public function __construct($nameInstance, $varPath)
  {
    $this->nameInstance = $nameInstance;
    $this->varPath = $varPath;
    $varPathData = $this->join($this->getVarPath(), "data");
    $this->mkdir($varPathData);
    $this->svmFilePath  = $this->join($varPathData, "values.svm");

    $this->classMapping = new ClassMapping($nameInstance, $varPath);
    $this->featureGenerator = new FeatureGenerator($nameInstance, $varPath);
    $this->textPreProcessor = new TextPreProcessor($nameInstance, $varPath);
  }

  /**
   * @param $data
   *
   * @return $this
   */
  public function createSvm($data)
  {
    if(!file_exists($this->svmFilePath))
    {
      $this->remove($this->svmFilePath);
    }
    $handle = fopen($this->svmFilePath, 'w');
    foreach ($data as $key => $value)
    {
      list($label, $text) = $value;
      list($feats, $label) = $this->convertToSvm($text, $label);
      fwrite($handle, sprintf("%s  %s\n",$label, $feats));
    }
    fclose($handle);
    $this->save();
    return $this;
  }

  /**
   * @return string
   */
  public function getSvmFilePath()
  {
    return $this->svmFilePath;
  }

  /**
   * @param $text
   * @param null $label
   *
   * @return array|string
   */
  public function convertToSvm($text, $label = null)
  {
    if($label)
    {
      $this->classMapping->addLabel($label);
    }
    $result = $this->textPreProcessor->textPreprocessor($text);
    $feats = $this->featureGenerator->bigram($result);
    $featsForString = array();
    foreach ($feats as $i => $featValue)
    {
      $featsForString[(int) $i] = sprintf("%s:%s", $i, $featValue);
    }
    ksort($featsForString);
    $featValue = implode(" ", $featsForString);
    if(!$label)
    {
      return $featValue;
    }
    return array(
      $featValue,
      $this->classMapping->getClassMapId($label)
    );
  }

  /**
   * @param array $sample
   *
   * @return array
   */
  public function transformSamplesForPredict(array $samples)
  {
    $resultSamples = array();
    foreach ($samples as $sample)
    {
      $resultSamples[] = sprintf("%s  %s", 0, $sample);
    }
    return $resultSamples;
  }

  /**
   * @param $predictResult
   *
   * @return array
   */
  public function transformResultsPredictions($predictResult)
  {
    $predictResultArray = explode("\n", $predictResult);
    $headFile = explode(" ", $predictResultArray[0]);
    unset($predictResultArray[0]);
    $predictions = array();
    foreach($predictResultArray as $keyLigne => $ligne)
    {
      $values = explode(" ", $ligne);
      if(count($values) > $this->classMapping->countData())
      {
        $prediction = array(
          "value"       =>  null,
          "percentage"  =>  0,
          "percentages" =>  array()
        );
        foreach($values as $keyValue => $value)
        {
          $labelId = $headFile[$keyValue];
          if($labelId == "labels")
          {
            $prediction['value'] = $this->classMapping->getClassMapValue($value);
          }
          else
          {
            $prediction["percentages"][$this->classMapping->getClassMapValue($labelId)] = (float) $value;
          }
        }
        if($prediction['value'])
        {
          $prediction["percentage"] = (float) $prediction["percentages"][$prediction['value']];
        }
        $predictions[] = $prediction;
      }
    }
    return $predictions;
  }

  public function transformResultPredictionsWithDecValues($value, array $decValues)
  {
    $pourcentages = array();
    foreach($decValues as $key => $pourcentage)
    {
      $pourcentages[$this->classMapping->getClassMapValue($key)] = $pourcentage;
    }
    return array(
      "value"       =>  $this->classMapping->getClassMapValue($value),
      "percentage"  =>  $decValues[$value],
      "percentages" =>  $pourcentages
    );
  }

  /**
   * @return $this
   */
  public function save()
  {
    $this->classMapping->save();
    $this->featureGenerator->save();
    $this->textPreProcessor->save();
    return $this;
  }

  /**
   * @return $this
   * @throws \Exception
   */
  public function load()
  {
    $this->classMapping->load();
    $this->featureGenerator->load();
    $this->textPreProcessor->load();
    return $this;
  }

}