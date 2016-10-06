<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('number')
            ->add('date')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    	$resolver->setDefaults(array());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reu_pokernight_appbundle_event';
    }
}
