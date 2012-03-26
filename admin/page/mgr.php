<?php

class page_mgr extends Page {
    function init(){
        parent::init();

        $tabs=$this->add('Tabs');
        $crud=$tabs->addTab('Players')->add('CRUD');
        $crud->setModel('Player');

        if($crud->grid){
            $crud->grid->addColumn('prompt','set_password');
            if($_GET['set_password']){
                $auth = $this->add('SignupAuth');
                $model = $auth->getModel()->loadData($_GET['set_password']);
                $enc_p = $auth->encryptPassword($_GET['value'],$model->get('email'));
                $model->set('password',$enc_p)->update();
                $this->js()->univ()->successMessage('Changed password for '.$model->get('email'))
                    ->execute();
            }
        }
        $tabs->addTab('Memberships')->add('CRUD')->setModel('Membership');
        $tabs->addTab('Scrimmages')->add('CRUD')->setModel('Scrimmage');
        $tabs->addTab('Signups')->add('CRUD')->setModel('Signup');
    }
}
