<?php

/**
 * This class fetches a list from nationalities from github
 * and stores them into the option group
 * 
 */

class CRM_Nationalities_Fetch {
  
  protected static $_singleton;
  
  protected $option_group_id;
  
  protected $file;
  
  protected function __construct($option_group_id, $file) {
    $this->option_group_id = $option_group_id;
    $this->file = $file;
    
    if (!file_exists($this->file)) {
      throw new Exception("File: '".$this->file."' does not exist");
    }
  }
  
  public static function singleton($option_group_id, $file) {
    if (!self::$_singleton) {
      self::$_singleton = new CRM_Nationalities_Fetch($option_group_id, $file);
    }
    return self::$_singleton;
  }
  
  public function updateNationalities() {
    $handle = fopen($this->file, "r");
    
    if (!$handle) {
      throw new Exception("Error opening file: '".$this->file."'");
    }
    
    while($line = fgets($handle)) {
      $n = str_replace(array("\r", "\n"), array("", ""), $line);
      $params = array();
      $params[1] = array($this->option_group_id, 'Integer');
      $params[2] = array($n, 'String');
      $dao = CRM_Core_DAO::executeQuery("SELECT * FROM `civicrm_option_value` WHERE `option_group_id` = %1 AND `value` = %2", $params);
      if ($dao->fetch()) {
        //record akready exist
        continue;
      }
      
      //record does not exist insert it
      $params[3] = array(n, 'String');
      $sql = "INSERT INTO `civicrm_option_value` (`option_group_id`, `value`, `label`, `is_reserved`, `is_active`, `weight`) VALUES(%1, %2, %3, '1', '1', '0')";
      CRM_Core_DAO::executeQuery($sql, $params);
    }
    
    fclose($handle);
  }
  
}

