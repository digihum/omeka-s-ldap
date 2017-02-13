<?php
namespace Omeka\Form;

use Omeka\Permissions\Acl;
use Zend\Form\Form;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\Event;

class UserForm extends Form
{
    use EventManagerAwareTrait;

    /**
     * @var array
     */
    protected $options = [
        'include_role' => false,
        'include_admin_roles' => false,
        'include_is_active' => false,
        'current_password' => false,
        'include_password' => false,
        'include_key' => false,
    ];

    /**
     * @var Acl
     */
    protected $acl;

    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, array_merge($this->options, $options));
    }

    public function init()
    {
        $this->add([
            'name' => 'user-information',
            'type' => 'fieldset'
        ]);
        $this->add([
            'name' => 'change-password',
            'type' => 'fieldset'
        ]);
        $this->add([
            'name' => 'edit-keys',
            'type' => 'fieldset'
        ]);
        $this->get('user-information')->add([
            'name' => 'o:email',
            'type' => 'Email',
            'options' => [
                'label' => 'Email', // @translate
            ],
            'attributes' => [
                'id' => 'email',
                'required' => true,
            ],
        ]);
        $this->get('user-information')->add([
            'name' => 'o:name',
            'type' => 'Text',
            'options' => [
                'label' => 'Display Name', // @translate
            ],
            'attributes' => [
                'id' => 'name',
                'required' => true,
            ],
        ]);

        $this->get('user-information')->add([
            'name' => 'o:name',
            'type' => 'Text',
            'options' => [
                'label' => 'Display Name', // @translate
            ],
            'attributes' => [
                'id' => 'name',
                'required' => true,
            ],
        ]);

        if ($this->getOption('include_role')) {
            $excludeAdminRoles = !$this->getOption('include_admin_roles');
            $roles = $this->getAcl()->getRoleLabels($excludeAdminRoles);
            $this->get('user-information')->add([
                'name' => 'o:role',
                'type' => 'select',
                'options' => [
                    'label' => 'Role', // @translate
                    'value_options' => $roles,
                ],
                'attributes' => [
                    'id' => 'role',
                    'required' => true,
                ],
            ]);
        }

        if ($this->getOption('include_is_active')) {
            $this->get('user-information')->add([
                'name' => 'o:is_active',
                'type' => 'checkbox',
                'options' => [
                    'label' => 'Is Active', // @translate
                ],
                'attributes' => [
                    'id' => 'is-active',
                ],
            ]);
        }

        if ($this->getOption('include_password')) {
            if ($this->getOption('current_password')){
                $this->get('change-password')->add([
                    'name' => 'current-password',
                    'type' => 'password',
                    'options' => [
                        'label' => 'Current Password', // @translate
                    ],
                ]);
            }
           $this->get('change-password')->add([
                'name' => 'password',
                'type' => 'Password',
                'options' => [
                    'label' => 'New Password', // @translate
                ],
                'attributes' => [
                    'id' => 'password',
                ],
            ]);
            $this->get('change-password')->add([
                'name' => 'password-confirm',
                'type' => 'Password',
                'options' => [
                    'label' => 'Confirm New Password', // @translate
                ],
                'attributes' => [
                    'id' => 'password-confirm',
                ],
            ]);
        }

        if ($this->getOption('include_key')) {
            $this->get('edit-keys')->add([
                'name' => 'new-key-label',
                'type' => 'Text',
                'options' => [
                    'label' => 'New Key Label', // @translate
                ],
                'attributes' => [
                    'id' => 'new-key-label',
                ],
            ]);

        }

        $addEvent = new Event('form.add_elements', $this);
        $this->getEventManager()->triggerEvent($addEvent);

        // separate input filter stuff so that the event work right
        $inputFilter = $this->getInputFilter();

        if ($this->getOption('include_password')) {
            $inputFilter->get('change-password')->add([
                'name' => 'password',
                'required' => false,
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 6,
                        ],
                    ],
                ],
            ]);
            $inputFilter->get('change-password')->add([
                'name' => 'password',
                'required' => false,
                'validators' => [
                    [
                        'name' => 'Identical',
                        'options' => [
                            'token' => 'password-confirm',
                            'messages' => [
                                'notSame' => 'Password confirmation must match new password', // @translate
                            ]
                        ],
                    ],
                ],
            ]);
        }

        if ($this->getOption('include_key')) {
            $inputFilter->get('edit-keys')->add([
                'name' => 'new-key-label',
                'required' => false,
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'max' => 255,
                        ],
                    ],
                ],
            ]);
        }

        $filterEvent = new Event('form.add_input_filters', $this, ['inputFilter' => $inputFilter]);
        $this->getEventManager()->triggerEvent($filterEvent);
    }

    /**
     * @param Acl $acl
     */
    public function setAcl(Acl $acl)
    {
        $this->acl = $acl;
    }

    /**
     * @return Acl
     */
    public function getAcl()
    {
        return $this->acl;
    }
}