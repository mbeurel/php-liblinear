<?php


namespace PhpLiblinear\Model;

class LiblinearModel
{
  /**
   * @var array
   */
  protected $solverTypeKeys = array(
    "L2R_LR"                => 0,
    "L2R_L2LOSS_SVC_DUAL"   => 1,
    "L2R_L2LOSS_SVC"        => 2,
    "L2R_L1LOSS_SVC_DUAL"   => 3,
    "MCSVM_CS"              => 4,
    "L1R_L2LOSS_SVC"        => 5,
    "L1R_LR"                => 6,
    "L2R_LR_DUAL"           => 7,
    "L2R_L2LOSS_SVR"        => 11,
    "L2R_L2LOSS_SVR_DUAL"   => 12,
    "L2R_L1LOSS_SVR_DUAL"   => 13
  );

  /**
   * @var string
   */
  public $values = "";

  /**
   * @var string
   */
  public $solverType;

  /**
   * @var int
   */
  public $nrClass;

  /**
   * @var string
   */
  public $labels;

  /**
   * @var int
   */
  public $nrFeature;

  /**
   * @var float
   */
  public $bias;

  /**
   * @var array
   */
  public $w;


  /**
   * LiblinearModel constructor.
   *
   * @param string|null $values
   */
  public function __construct(string $values = null)
  {
    $this->values = $values;
    if($values)
    {
      $this->solverType = $this->retreiveFunctionByKey("/solver_type (.*)\\n/m", $values);
      $this->nrClass = $this->retreiveFunctionByKey("/nr_class (.*)\\n/m", $values);
      $this->nrFeature = $this->retreiveFunctionByKey("/nr_feature (.*)\\n/m", $values);
      $this->bias = $this->retreiveFunctionByKey("/bias (.*)\\n/m", $values);
      $this->labels = $this->retreiveFunctionByKey("/label (.*)\\n/m", $values);
      $this->constructW($values);
    }
  }

  /**
   * @param $regex
   * @param $values
   *
   * @return mixed
   */
  protected function retreiveFunctionByKey($regex, &$values)
  {
    preg_match($regex, $values, $match);
    $values = str_replace($match[0], "", $values);
    return $match[1];
  }

  /**
   * @param $solverTypeKey
   *
   * @return $this
   */
  public function setSolverTypeByKey($solverTypeKey)
  {
    $this->solverType = array_search($this->solverTypeKeys, $solverTypeKey);
    return $this;
  }

  /**
   * @return mixed
   */
  public function getSolverTypeKey()
  {
    return $this->solverTypeKeys[$this->solverType];
  }

  /**
   * @return bool
   */
  public function hasEstimate()
  {
    return ($this->getSolverTypeKey() == 0 || $this->getSolverTypeKey() == 1) ? true : false;
  }

  /**
   * @return array
   */
  public function getLabelsArray()
  {
    return explode(" ", $this->labels);
  }

  public function constructW($values)
  {
    foreach (explode("\n", $values) as $lignes)
    {
      $elements = explode(' ', trim($lignes));
      if(count($elements)>= $this->nrClass)
      {
        foreach($elements as $element)
        {
          $this->w[] = $element;
        }
      }
    }
  }



}