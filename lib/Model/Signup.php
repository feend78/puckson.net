<?php

class Model_Signup extends Model_Table {
    public $entity_code='signup';
    public $table_alias='su';

    function init(){
        parent::init();

        $this->addField('player_id')->refModel('Model_Player');
        $this->addField('scrimmage_id')->refModel('Model_Scrimmage');

        $this->addField('is_playing')->type('boolean')->calculated(true);

        $this->addField('created_dts')->type('timestamp')->system(true);
        $this->addField('updated_dts')->type('timestamp')->system(true);
    }

    function calculate_is_playing() {
        $dsql = $this->api->dsql()
            ->table('signup')
            ->field('id')

            ->limit($this->api->getConfig('signup/max_players', 22))
            ;

        return $this->dsql()->debug()
            ->field('id')
            ->where('su.id in ('.$dsql->select().')')
            ->select()
            ;
    }
}


