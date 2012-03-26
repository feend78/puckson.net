<?php

class Model_Player extends Model_Table {
    public $entity_code='player';
    public $table_alias='p';
    function init(){
        parent::init();

        $this->addField('name');
        $this->addField('email');
        $this->addField('membership_id')->refModel('Model_Membership');

        $this->addField('priority')->type('int')->calculated(true);

        $this->addField('created_dts')->type('timestamp')->system(true);
        $this->addField('updated_dts')->type('timestamp')->system(true);
    }

    function calculate_priority() {
        return $this->add('Model_Membership')
            ->dsql()
            ->field('CASE type WHEN null THEN 0 WHEN 1 THEN 1 ELSE 2 END AS type')
            ->where('p.membership_id=m.id')
            ->select()
            ;
    }
}
