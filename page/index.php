<?php

class page_index extends Page {
    function init(){
        parent::init();

        $this->add('P')->setText($this->api->getVersion());

        $form = $this->add('Form',null,'LoginForm');
        $form->setFormClass('inline');
        $form->addField('line','email');
        $form->addField('password','password');
        $form->addSubmit('Login');
        $form->addButton('Register')->js('click')
            ->univ()->location($this->api->getDestinationURL('register'));

        if($form->isSubmitted()) {

            // Short-cuts
            $auth=$this->api->auth;
            $l=$form->get('email');
            $p=$form->get('password');

            // Manually encrypt password
            $enc_p = $auth->encryptPassword($p,$l);

            // Manually verify login
            if($auth->verifyCredintials($l,$enc_p)){

                // We won't log-in because the form will show
                $auth->login($l);
                $form->js()->univ()->redirect('signup')->execute();
            }
            $form->getElement('password')->displayFieldError('Incorrect login');
        }
    }
    function defaultTemplate(){
        return array('page/index');
    }
}
