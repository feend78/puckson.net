<?php

define("MBRTYPE_ASSOCIATE", 1);
define("MBRTYPE_FULL",      2);
define("MBRTYPE_STUDENT",   3);

class Model_Membership extends Model_Table {
    public $entity_code = 'membership';
    public $table_alias = 'm';
    function init() {
        parent::init();

        $this->addField('type')->type('list')->caption('Membership Type')
            ->listData(array(
                MBRTYPE_ASSOCIATE   => 'Associate',
                MBRTYPE_FULL        => 'Full',
                MBRTYPE_STUDENT     => 'Student'
            ));

        $this->addField('created_dts')->type('timestamp')->system(true);
        $this->addField('updated_dts')->type('timestamp')->system(true);
    }

}
