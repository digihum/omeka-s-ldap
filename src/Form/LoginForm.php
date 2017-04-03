<?php
namespace LDAP\Form;
use Zend\Form\Form;
class LoginForm extends Form
{
    public function init()
    {
        $this->setAttribute('class', 'disable-unsaved-warning');

        $this->add([
            'name' => 'username',
            'type' => 'Text',
            'options' => [
                'label' => 'Username', // @translate
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);

        $this->add([
            'name' => 'password',
            'type' => 'Password',
            'options' => [
                'label' => 'Password', // @translate
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => [
                'value' => 'Log in', // @translate
            ],
        ]);
    }
}