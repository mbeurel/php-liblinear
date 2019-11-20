<?php
/*
 * This file is part of the php-liblinear.
 *
 * (c) Matthieu Beurel <m.beurel@nexboard.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpLiblinear\Classification\Base;
use PhpLiblinear\Converter\DataConverter;
use PhpLiblinear\Tools\FilesystemTrait;

/**
 * Class Classification
 * @package PhpLiblinear\Classification\Base
 * @author Matthieu Beurel <m.beurel@nexboard.fr>
 */
abstract class Classification
{
  use FilesystemTrait;

  /**
   * @var bool
   */
  protected $debug = false;

  /**
   * @var string
   */
  protected $libname;

  /**
   * @var int
   */
  protected $type;

  /**
   * @var float
   */
  protected $cost;

  /**
   * @var float
   */
  protected $epsilon;

  /**
   * @var string
   */
  protected $binPath = "/usr/bin/";

  /**
   * @var string
   */
  protected $varPathPool;

  /**
   * @var string
   */
  protected $model;

  /**
   * @var string
   */
  protected $nameInstance;

  /**
   * @var string
   */
  protected $modelFilePath;

  /**
   * @var DataConverter
   */
  protected $dataConverter;

  /**
   * Classification constructor.
   *
   * @param string $nameInstance
   * @param $varPath
   * @param array $config
   *
   * @throws \Exception
   */
  public function __construct(string $nameInstance, $varPath, array $config)
  {
    $this->nameInstance = $nameInstance;
    $this->varPath = $varPath;
    $this->varPathPool = $this->join($this->getVarPath(), "pool");
    $this->mkdir(array($this->varPath, $this->varPathPool));

    $this->modelFilePath = $this->getVarPath()."/model.bin";
    $this->verifyBinPath($this->binPath);
    $this->debug = (isset($config) && $config["debug"]) ? true : false;
    $this->dataConverter = new DataConverter($nameInstance, $varPath);
  }

  /**
   * @param string $binPath
   */
  public function setBinPath(string $binPath): void
  {
    $this->ensureDirectorySeparator($binPath);
    $this->verifyBinPath($binPath);
    $this->binPath = $binPath;
  }

  /**
   * @throws \Exception
   */
  public function save(): void
  {
    if (!is_writable(dirname($this->modelFilePath))) {
      throw new \Exception(sprintf('File "%s" can\'t be saved.', basename($this->modelFilePath)));
    }
    $serialized = $this->model;
    $result = file_put_contents($this->modelFilePath, $serialized, LOCK_EX);
    if ($result === false) {
      throw new \Exception(sprintf('File "%s" can\'t be saved.', basename($this->modelFilePath)));
    }
  }

  /**
   * @return $this
   * @throws \Exception
   */
  public function load()
  {
    if (!file_exists($this->modelFilePath) || !is_readable($this->modelFilePath)) {
      throw new \Exception(sprintf('File "%s" can\'t be open.', basename($this->modelFilePath)));
    }
    $this->model = file_get_contents($this->modelFilePath);
    return $this;
  }

  /**
   * @param array $data
   *
   * @throws \Exception
   */
  public function train(array $data): void
  {
    $this->dataConverter->createSvm($data)->save();
    $command = $this->buildTrainCommand();
    $output = [];
    exec(escapeshellcmd($command).' 2>&1', $output, $return);
    if($this->debug)
    {
      foreach($output as $ligne)
      {
        echo $ligne."\n";
      }
    }
    if ($return !== 0) {
      throw new \Exception(
        sprintf('Failed running %s command: "%s" with reason: "%s"', $this->libname, $command, array_pop($output))
      );
    }
    $this->model = (string) file_get_contents($this->modelFilePath);
    $this->remove($this->modelFilePath);
  }

  /**
   * @return string
   */
  public function getModel(): string
  {
    return $this->model;
  }

  /**
   * @param array|string $samples
   *
   * @return array|mixed
   * @throws \Exception
   */
  public function predict($samples)
  {
    $samples = $this->toArray($samples);
    $this->dataConverter->load();
    foreach($samples as $key => $sample)
    {
      $samples[$key] = $this->dataConverter->convertToSvm($sample);
    }
    $samples = $this->dataConverter->transformSamplesForPredict($samples);
    $predictResult = $this->runSvmPredict($samples, true);
    $predictions = $this->dataConverter->transformPredictions($predictResult);
    return $predictions;
  }

  /**
   * @param array $samples
   * @param bool $probabilityEstimates
   *
   * @return string
   * @throws \Exception
   */
  protected function runSvmPredict(array $samples, bool $estimates): string
  {
    file_put_contents($predictFileName = $this->join($this->varPathPool, uniqid('php-liblinear_', true)), implode("\n", $samples));
    $outputFileName = $predictFileName.'-output';
    $command = $this->buildPredictCommand(
      $predictFileName,
      $outputFileName,
      $estimates
    );
    $output = [];
    exec(escapeshellcmd($command).' 2>&1', $output, $return);
    unlink($predictFileName);
    $predictions = (string) file_get_contents($outputFileName);
    unlink($outputFileName);

    if ($return !== 0) {
      throw new  \Exception(
        sprintf('Failed running libsvm command: "%s" with reason: "%s"', $command, array_pop($output))
      );
    }

    return $predictions;
  }

  /**
   * @return string
   */
  protected function getOSExtension(): string
  {
    $os = strtoupper(substr(PHP_OS, 0, 3));
    if ($os === 'WIN') {
      return '.exe';
    } elseif ($os === 'DAR') {
      return '-osx';
    }
    return '';
  }

  /**
   * @param string $path
   */
  protected function ensureDirectorySeparator(string &$path): void
  {
    if (substr($path, -1) !== DIRECTORY_SEPARATOR) {
      $path .= DIRECTORY_SEPARATOR;
    }
  }

  /**
   * @param $data
   *
   * @return array
   */
  private function toArray($data): array
  {
    return \is_array($data) ? $data : [$data];
  }

  /**
   * @return string
   */
  protected abstract function buildTrainCommand();

  /**
   * @param string $predictFileName
   * @param string $outputFileName
   * @param bool $probabilityEstimates
   *
   * @return string
   */
  protected abstract function buildPredictCommand(string $predictFileName, string $outputFileName, bool $estimates);

  /**
   * @param string $path
   */
  protected abstract function verifyBinPath(string $path);


}
