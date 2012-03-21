<?php

class Model_Scrimmage extends Model_Table {
    public $entity_code='scrimmage';
    public $table_alias='s';

    function init(){
        parent::init();

        $this->addField('date_time')->type('datetime');
        $this->addField('location');

        $this->addField('created_dts')->type('timestamp')->system(true);
        $this->addField('updated_dts')->type('timestamp')->system(true);
    }
}