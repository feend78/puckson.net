<?php

class page_signup extends Page {
    function init(){
        parent::init();

        $this->api->template->trySet('page_title',$this->api->getConfig('site/full_name','Hockey Pickup Group').' Signups');

        $form = $this->add('Form',null,'SignupForm');
        $form->setFormClass('inline');
        $form->addField('line','email');
        $form->addField('password','password');

        $form->addSubmit('Login');

        $scrimmage_id = $this->add('Model_Scrimmage')
            ->addCondition('date_time', date('Y-m-d H:i:s').'..'.date('Y-m-d H:i:s',strtotime('+7 days')), true)
            ->getRows()[0]['id']
            ;

        $this->add('MVCGrid',null,'PlayingList')
            ->setModel('Signup_Playing', array('player','created_dts','is_playing'))
            ->addCondition('scrimmage_id',$scrimmage_id)
            ;

        $this->add('MVCGrid',null,'WaitingList')
            ->setModel('Signup_Waiting', array('player','created_dts','is_playing'))
            ->addCondition('scrimmage_id',$scrimmage_id)
            ;

        if($form->isSubmitted()) {

            // Short-cuts
            $auth=$this->api->auth;
            $l=$form->get('email');
            $p=$form->get('password');

            // Manually encrypt password
            $enc_p = $auth->encryptPassword($p,$l);

            // Manually verify login
            if($auth->verifyCredintials($l,$enc_p)){
                $form->js()->univ()->alert('debug: do signup')->execute();
            }
            $form->getElement('password')->displayFieldError('Incorrect login');
        }
    }
    function defaultTemplate(){
        return array('page/signup');
    }
}
