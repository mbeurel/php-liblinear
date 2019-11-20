<?php
/*
 * This file is part of the php-liblinear.
 *
 * (c) Matthieu Beurel <m.beurel@nexboard.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpLiblinear\Tools;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Trait FilesystemTrait
 * @package PhpLiblinear\Tools
 * @author Matthieu Beurel <m.beurel@nexboard.fr>
 */
trait FilesystemTrait
{

  /**
   * @var Filesystem
   */
  protected $filesystem;

  /**
   * @var string
   */
  protected $varPath;

  /**
   * @var string
   */
  protected $nameInstance;

  /**
   * @return mixed|string
   * @throws \Exception
   */
  public function getVarPath()
  {
    return $this->join($this->varPath, "instance", $this->nameInstance);
  }

  /**
   * @return string
   * @throws \Exception
   */
  public function join()
  {
    $parts = func_get_args();
    $dirtyPath = implode('/', $parts);
    if(strpos($dirtyPath, '//') !== false)
    {
      $dirtyPath = preg_replace('|(/{2,})|', '/', $dirtyPath);
    }
    $cleanPath = trim($dirtyPath, '/');
    if ('/' === DIRECTORY_SEPARATOR)
    {
      $cleanPath = '/'.$cleanPath;
    }
    else
    {
      throw new \Exception('IS NOT UNIX SYSTEM');
    }
    return $cleanPath;
  }

  /**
   * @return Filesystem
   */
  public function getFilesystem()
  {
    if(!$this->filesystem)
    {
      $this->filesystem = new Filesystem();
    }
    return $this->filesystem;
  }

  /**
   * @param $dirs
   * @param int $mode
   *
   * @return $this
   */
  public function mkdir($dirs, $mode = 0777)
  {
    $this->getFilesystem()->mkdir($dirs, $mode);
    return $this;
  }

  /**
   * @param $originFile
   * @param $targetFile
   * @param bool $overwriteNewerFiles
   *
   * @return $this
   */
  public function copy($originFile, $targetFile, $overwriteNewerFiles = false)
  {
    $this->getFilesystem()->copy($originFile, $targetFile, $overwriteNewerFiles);
    return $this;
  }

  /**
   * @param $files
   *
   * @return $this
   */
  public function remove($files)
  {
    $this->getFilesystem()->remove($files);
    return $this;
  }

}