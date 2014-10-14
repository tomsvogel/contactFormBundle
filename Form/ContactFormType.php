<?php
namespace Arkulpa\Bundle\DeskBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactFormType extends AbstractType
{

    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder->add(
            'name',
            null,
            array(
                'constraints' => array(
                    new NotBlank(array('message' => 'name-empty-error')),
                ),
            )
        );
        $builder->add(
            'email',
            null,
            array(
                'constraints' => array(
                    new NotBlank(array('message' => 'email-empty-error')),
                    new NotBlank(array('message' => 'email-empty-error')),

                ),
            )
        );
    }

    public function getName()
    {
        return 'arkulpa_contact_form_type';
    }
}
