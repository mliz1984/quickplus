<?php 
  class BaseAdapter
  {
  	 protected  $testMode = false;
  	 public function setTestMode($testMode)
  	 {
  	 	$this->testMode = $testMode;
  	 }
  	 public function getTestMode($testMode)
  	 {
  	 	return $this->testMode;
  	 }
  }
?>