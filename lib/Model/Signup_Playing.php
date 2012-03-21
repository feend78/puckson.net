<?php

class Model_Signup_Playing extends Model_Signup {
    function init(){
        parent::init();

        $this->addCondition('is_playing', true);
    }
}
