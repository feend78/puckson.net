<?php

class Model_Signup_Waiting extends Model_Signup {
    function init(){
        parent::init();

        $this->addCondition('is_playing', false);
    }
}
