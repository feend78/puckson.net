<?php

class page_mgr extends Page {
    function init(){
        parent::init();

        $this->api->template->trySet('page_title',$this->api->getConfig('site/full_name','Hockey Pickup Group Admin'));

        $tabs=$this->add('Tabs');
        $crud=$tabs->addTab('Players')->add('CRUD');
        $crud->setModel('Player');

        if($crud->grid){
            $crud->grid->addColumn('prompt','set_password');
            if($_GET['set_password']){
                $auth = $this->add('SQLAuth')->usePasswordEncryption('sha256/salt');

                $model = $this->add('Model_Member');
                $model->addField('password')->system(true);

                $model->loadData($_GET['set_password']);

                if($model->isInstanceLoaded()) {
                    $enc_p = $auth->encryptPassword($_GET['value'],$model->get('email'));
                    $model->set('password',$enc_p)->update();
                    $this->js()->univ()->successMessage('Changed password for '.$model->get('email'))
                        ->execute();
                    $model->destroy();
                } else {
                    $this->js()->univ()->successMessage('Suck!')->execute();
                }
            }
        }
        $tabs->addTab('Memberships')->add('CRUD')->setModel('Membership');
        $tabs->addTab('Scrimmages')->add('CRUD')->setModel('Scrimmage');
        $tabs->addTab('Signups')->add('CRUD')->setModel('Signup');
    }
}
