<?php

class Model_Signup_Skater extends Model_Signup {
  function init(){
    parent::init();

    $this->addCondition('position',  'skater');
  }
}
