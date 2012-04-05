<?php

class Model_Scrimmage extends Model_Table {
  public $entity_code='scrimmage';
  public $table_alias='s';

  function init(){
    parent::init();

    $this->addField('date')
      ->type('date')
      ->required(true);
    $this->addField('start_time')
      ->type('time')
      ->display(array('grid'=>'time'))
      ->required(true);
    $this->addField('location')->required(true);

    $this->addField('current_start')->type('datetime');
    $this->addField('current_end')->type('datetime');

  }
  function signPlayerUp($player_id, $position) {        
    $p=$this->add('Model_Player')->loadData($player_id);
    $s=$this->add('Model_Signup')
      ->addCondition('player_id', $player_id)
      ->addCondition('position',  $position)
      ->loadBy('scrimmage_id',    $this->get('id'))
      ;

    if (!$s->isInstanceLoaded()) {   
      $deadline=(time() < strtotime($this->api->getConfig('signup/pri_deadline','-1 day 17:00'), strtotime($this->get('date'))))?'Y':'N';

      $s->set('scrimmage_id',       $this->get('id'))
        ->set('player_id',          $p->get('id'))
        ->set('position',           $position)
        ->set('priority',           $p->get('priority'))
        ->set('signup_dts',         date('Y-m-d H:i:s'))
        ->set('is_before_deadline', $deadline)        
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
