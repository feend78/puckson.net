<?php

class Model_Signup extends Model_Table {
    public $entity_code='signup';
    public $table_alias='su';

    function init(){
        parent::init();

        $this->addField('player_id')->refModel('Model_Player')->mandatory(true);
        $this->addField('scrimmage_id')->refModel('Model_Scrimmage')->mandatory(true);
        $this->addField('priority')->type('int')->mandatory(true);

        $this->addField('is_playing')->type('boolean')->calculated(true);

        $this->addField('created_dts')->type('timestamp')->system(true);
        $this->addField('updated_dts')->type('timestamp')->system(true);
    }

    function calculate_is_playing() {

        return $this->api->db->dsql()
            ->table('signup su2')
            ->field('IF(su2.id IS NULL, \'N\', \'Y\')')
            ->join('(select id from signup order by priority desc, created_dts limit 0'.$this->api->getConfig('signup/max_players',22).') AS su3', 'su2.id=su3.id')
            ->where('su.id=su2.id')
            ->select()
            ;
    }
}


