<?php
/*
 * This file is part of the php-liblinear.
 *
 * (c) Matthieu Beurel <m.beurel@nexboard.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpLiblinear\Classification;
use PhpLiblinear\Classification\Base\Classification;
use PhpLiblinear\Model\LiblinearModel;

/**
 * Class LibLinear
 * @package PhpLiblinear\Classification
 * @author Matthieu Beurel <m.beurel@nexboard.fr>
 */
class LibLinear extends Classification
{

  /**
   * @var string
   */
  protected $libname = "liblinear";

  /**
   * @var array
   */
  protected $trainParameters = array();

  /**
   * LibLinear constructor.
   *
   * @param string $nameInstance
   * @param string $varPath
   * @param array $config
   *
   * @throws \Exception
   */
  public function __construct(string $nameInstance, string $varPath, array $config = array())
  {
    $this->constructTrainParameters((isset($config["trainParameters"]) && $config["trainParameters"]) ? $config["trainParameters"] : array());
    $this->model = new LiblinearModel();
    parent::__construct($nameInstance, $varPath, $config);
  }

  protected function constructTrainParameters(array $parameters)
  {
    $type = (isset($parameters["type"]) && $parameters["type"]) ? $parameters["type"] : 0;
    $this->trainParameters[] = "-s ".$type;
    $this->trainParameters[] = "-c ".((isset($parameters["cost"]) && $parameters["cost"]) ? $parameters["cost"] : 1);
    $this->trainParameters[] = "-e ".((isset($parameters["epsilon"]) && $parameters["epsilon"]) ? $parameters["epsilon"] : 0.1);
    $this->trainParameters[] = "-p ".((isset($parameters["epsilonSVR"]) && $parameters["epsilonSVR"]) ? $parameters["epsilonSVR"] : 0.1);

    if(isset($parameters["bias"]) && $parameters["bias"])
    {
      $this->trainParameters[] = "-B ".$parameters["bias"];
    }
    if(isset($parameters["weight"]) && $parameters["weight"])
    {
      $this->trainParameters[] = "-wi ".$parameters["weight"];
    }
    if(isset($parameters["validationMode"]) && $parameters["validationMode"])
    {
      $this->trainParameters[] = "-v ".$parameters["validationMode"];
    }
    if(isset($parameters["parameterC"]) && $parameters["parameterC"] && ($type == 0 || $type == 2))
    {
      $this->trainParameters[] = "-C ".$parameters["parameterC"];
    }
  }

  /**
   * @param string $trainingSetFileName
   * @param string $modelFileName
   *
   * @return string
   */
  protected function buildTrainCommand(): string
  {
    return sprintf(
      '%sliblinear-train %s %s %s',
      $this->binPath,
      implode(" ", $this->trainParameters),
      escapeshellarg($this->dataConverter->getSvmFilePath()),
      escapeshellarg($this->modelFilePath)
    );
  }

  /**
   * @param string $testSetFileName
   * @param string $modelFileName
   * @param string $outputFileName
   * @param bool $probabilityEstimates
   *
   * @return string
   */
  protected function buildPredictCommand(string $predictFileName, string $outputFileName): string {
    return sprintf(
      '%sliblinear-predict%s -b %d %s %s %s',
      $this->binPath,
      $this->getOSExtension(),
      $this->model->hasEstimate() ? 1 : 0,
      escapeshellarg($predictFileName),
      escapeshellarg($this->modelFilePath),
      escapeshellarg($outputFileName)
    );
  }

  /**
   * @param string $path
   *
   * @throws \Exception
   */
  protected function verifyBinPath(string $path): void
  {
    if (!is_dir($path)) {
      throw new \Exception(sprintf('The specified path "%s" does not exist', $path));
    }
    $osExtension = $this->getOSExtension();
    foreach (['liblinear-predict', 'liblinear-train'] as $filename) {
      $filePath = $path.$filename.$osExtension;
      if (!file_exists($filePath)) {
        throw new \Exception(sprintf('The File "%s" is not found', $filePath));
      }

      if (!is_executable($filePath)) {
        throw new \Exception(sprintf('The File "%s" is not executable', $filePath));
      }
    }
  }
}
