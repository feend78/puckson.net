<?php

class Model_Scrimmage extends Model_Table {
  public $entity_code='scrimmage';
  public $table_alias='s';

  function init(){
    parent::init();

    $this->addField('date')->type('date')->required(true);
    $this->addField('start_time')->type('time')->required(true);
    $this->addField('location')->required(true);
    //$this->addField('name')->calculated(true);

    $this->addField('current_start')->type('timestamp');
    $this->addField('current_end')->type('timestamp');

    $this->addField('created_dts')->type('timestamp')->system(true);
    $this->addField('updated_dts')->type('timestamp')->system(true);
  }
  function signPlayerUp($player_id, $position) {        
    $p=$this->add('Model_Player')->loadData($player_id);
    $s=$this->add('Model_Signup')
      ->addCondition('player_id', $player_id)
      ->addCondition('position',  $position)
      ->loadBy('scrimmage_id',    $this->get('id'))
      ;

    if (!$s->isInstanceLoaded()) {   
      $s->set('scrimmage_id',       $this->get('id'))
        ->set('player_id',          $p->get('id'))
        ->set('position',           $position)
        ->set('priority',           $p->get('priority'))
        ->set('signup_dts',         date('Y-m-d H:i:s'))
        ->update()
        ;
    }
  }
  function cancelSignup($player_id, $position) {
    $s=$this->add('Model_Signup')
      ->addCondition('player_id', $player_id)
      ->addCondition('position',  $position)
      ->loadBy('scrimmage_id',    $this->get('id'))
      ;
    if ($s->isInstanceLoaded()) {
      $s->delete();
    }
  }
  function calculate_name() {
    return 'CONCAT_WS(\' \', DATE_FORMAT(s.date, \'%m/%d/%Y\'), TIME_FORMAT(s.start_time, \'%h:%i %p\'))';
  }
}
