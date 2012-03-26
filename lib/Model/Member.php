<?php

class Model_Member extends Model_Player {
    function init() {
        parent::init();

        $this->addCondition('membership_id','>1',true);
    }
}
