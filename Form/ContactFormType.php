<?php
namespace Arkulpa\Bundle\ContactFormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
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
                    new Email(array('message' => 'email-not-valid')),

                ),
            )
        );
        $builder->add(
            'subject',
            null,
            array(
                'constraints' => array(
                    new NotBlank(array('message' => 'subject-empty-error')),
                ),
            )
        );
        $builder->add(
            'message',
            null,
            array(
                'constraints' => array(
                    new NotBlank(array('message' => 'message-empty-error')),
                ),
            )
        );
    }

    public function getName()
    {
        return 'arkulpa_contact_form_type';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'csrf_protection' => false,
            )
        );
    }
}
