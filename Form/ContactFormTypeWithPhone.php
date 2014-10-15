<?php
namespace Arkulpa\Bundle\ContactFormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactFormTypeWithPhone extends AbstractType
{

    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $baseForm = new ContactFormType();
        $baseForm->buildForm($builder, $options);

        $builder->add(
            'phone',
            null,
            array(
                'constraints' => array(
                    new NotBlank(array('message' => 'name-empty-error')),
                ),
            )
        );

    }

    public function getName()
    {
        return 'arkulpa_contact_form_type_with_phone';
    }
}
