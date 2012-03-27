<?php

class Model_Signup_Goalie extends Model_Signup {
  function init(){
    parent::init();

    $this->addCondition('position',  'goalie');
  }
  
}
