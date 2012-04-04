<?php

class Model_Signup extends Model_Table {
  public $entity_code='signup';
  public $table_alias='su';

  function init(){
    parent::init();

    $this->addField('player_id')
      ->refModel('Model_Player')
      ->required(true);
      
    $this->addField('scrimmage_id')
      ->refModel('Model_Scrimmage')
      ->required(true);
      
    $this->addField('priority')
      ->type('list')
      ->display(array('grid'=>'priority'))
      ->required(true)
      ->listData(array('Guest','Associate','Member'));
      
    $this->addField('position')
      ->type('list')
      ->required(true)
      ->display('dropdown')
      ->listData(array('skater'=>'skater','goalie'=>'goalie'));

    $this->addField('signup_dts')
      ->type('datetime')
      ->caption('Signup Date/Time')
      ->system(true);

    $this->addField('is_before_deadline')->system(true);

  }

  function beforeInsert(&$data) {
    $data['signup_dts']=date('Y-m-d H:i:s');
  }

}


