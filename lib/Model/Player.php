<?php

class Model_Player extends Model_Table {
    public $entity_code='player';
    public $table_alias='p';
    function init(){
        parent::init();

        $this->addField('name')->mandatory(true);
        $this->addField('email')->mandatory(false);
        $this->addField('membership_id')->refModel('Model_Membership')->mandatory(false);
        $this->addField('is_goalie')->type('boolean')->mandatory(true);

        $this->addField('priority')->type('int')->calculated(true);

        $this->addField('created_dts')->type('timestamp')->system(true);
        $this->addField('updated_dts')->type('timestamp')->system(true);
    }

    function calculate_priority() {
        return $this->add('Model_Membership')
            ->dsql()
            ->field('CASE type WHEN 1 THEN 0 WHEN 2 THEN 1 ELSE 2 END AS type')
            ->where('p.membership_id=m.id')
            ->select()
            ;
    }
    function inviteGuest($name, $email) {
        $p = $this->add('Model_Player')
            ->set('name',   $name)
            ->set('email',  $email)
            ->update();
    }
}
