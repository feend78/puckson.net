<?php

class Model_Signup extends Model_Table {
  public $entity_code='signup';
  public $table_alias='su';

  function init(){
    parent::init();

    $this->addField('player_id')->refModel('Model_Player')->required(true);
    $this->addField('scrimmage_id')->refModel('Model_Scrimmage')->required(true);
    $this->addField('priority')->type('int')->display(array('grid'=>'priority'))->required(true);
    $this->addField('position')->required(true);

    $this->addField('number')->calculated(true);

    $this->addField('signup_dts')->type('datetime')->caption('Signup Date/Time');

    $this->join_entities['r']=array(
      'entity_name'   =>'(SELECT @curRow := 0)',
      'on'            =>'1=1',
      'join'          =>'inner',
    );
  }

  function calculate_number() {

    return '@curRow := @curRow + 1';
  }
}


