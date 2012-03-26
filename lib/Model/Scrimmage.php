<?php

class Model_Scrimmage extends Model_Table {
    public $entity_code='scrimmage';
    public $table_alias='s';

    function init(){
        parent::init();

        $this->addField('date')->type('date')->mandatory(true);
        $this->addField('start_time')->type('datetime')->mandatory(true);
        $this->addField('location')->mandatory(true);

        $this->addField('current_start')->type('timestamp');
        $this->addField('current_end')->type('timestamp');

        $this->addField('created_dts')->type('timestamp')->system(true);
        $this->addField('updated_dts')->type('timestamp')->system(true);
    }
    function beforeModify(&$data) {
        parent::beforeModify($data);

        $data['current_start'] = $mbr_types[$this->getField('type')].' Membership';
    }
    function signPlayerUp($player_id, $position) {        
        $p=$this->add('Model_Player')->loadData($player_id);
        $s=$this->add('Model_Signup')->addCondition('player_id', $player_id);

        $s->loadBy('scrimmage_id',    $this->get('id'));

        $s->set('scrimmage_id',       $this->get('id'))
          ->set('player_id',          $p->get('id'))
          ->set('position',           $position)
          ->set('priority',           $p->get('priority'))
          ->set('signup_dts',         date('Y-m-d H:i:s'))
          ->update()
          ;
    }
    function cancelSignup($player_id) {
        $s=$this->add('Model_Signup')
          ->addCondition('player_id', $player_id)
          ->loadBy('scrimmage_id',    $this->get('id'))
          ->delete();
    }
}
