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
   * LibLinear constructor.
   *
   * @param string $nameInstance
   * @param string $varPath
   * @param array $config
   *
   * @throws \Exception
   */
  public function __construct(string $nameInstance, string $varPath, array $config)
  {
    $this->type = (isset($config) && $config["type"]) ? $config["type"] : 0;
    $this->cost = (isset($config) && $config["cost"]) ? $config["cost"] : 1.0;
    $this->epsilon = (isset($config) && $config["epsilon"]) ? $config["epsilon"] : 0.1;
    parent::__construct($nameInstance, $varPath, $config);
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
      '%sliblinear-train -s %s -c %s -p %s %s %s',
      $this->binPath,
      $this->type,
      $this->cost,
      $this->epsilon,
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
  protected function buildPredictCommand(string $predictFileName, string $outputFileName, bool $estimates): string {
    return sprintf(
      '%sliblinear-predict%s -b %d %s %s %s',
      $this->binPath,
      $this->getOSExtension(),
      $estimates ? 1 : 0,
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
