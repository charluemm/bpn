<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TournamentTableType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$data = $builder->getData();
     	$number = empty($data) ? $options['number'] : $data->getNumber();
     	
        $builder
        	->add('number', IntegerType::class, array('label' => 'Tisch-Nr.', 'data' => $number))
			->add('finalTable', CheckboxType::class, array('label' => 'Finaltable?'))
			->add('maxSeats', IntegerType::class, array('label' => 'max. Spielerplätze (2 - 10)'))
			->add('comment', TextareaType::class, array('label' => 'Kommentar', 'required' => false))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\TournamentTable'
        ));
        
        $resolver->setRequired(array('number'));
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'pokernight_tournament_table';
    }
}
