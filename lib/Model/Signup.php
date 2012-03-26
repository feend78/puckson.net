<?php

class Model_Signup extends Model_Table {
  public $entity_code='signup';
  public $table_alias='su';

  function init(){
    parent::init();

    $this->addField('player_id')->refModel('Model_Player')->mandatory(true);
    $this->addField('scrimmage_id')->refModel('Model_Scrimmage')->mandatory(true);
    $this->addField('priority')->type('int')->mandatory(true);
    $this->addField('position')->mandatory(true);

    $this->addField('is_playing')->type('boolean')->calculated(true);

    $this->addField('signup_dts')->type('timestamp')->caption('Signup Time');

  }

  function calculate_is_playing() {
    
    $subq = $this->api->db->dsql()
      ->table('signup')
      ->field('id')
      ->where('position in', array('skater','either'))
      ->order('priority', 'desc')
      ->order('signup_dts')
      ->limit($this->api->getConfig('signup/max_players',22))
      ;

    return $this->api->db->dsql()
      ->table('signup su2')
      ->field('IF(su2.id IS NULL, \'N\', \'Y\')')
      ->join('('.$subq->select() .') AS su3', 'su2.id=su3.id')
      ->where('su.id=su2.id')
      ->select()
      ;
  }
}


