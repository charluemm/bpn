<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TournamentRankType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add('players', EntityType::class, array(
	        		'label' => 'Spieler',
	        		'class' => 'AppBundle:Player',
	        		'multiple' => true,
	        		'expanded' => true,
	        		'required' => false
	        ))
	        ->add('tournament', EntityType::class, array(
	        		'label' => 'Turnier',
	        		'class' => 'AppBundle:Tournament',
	        		'multiple' => true,
	        		'expanded' => true,
	        		'required' => false
	        ))
            ->add('rank')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\TournamentRank'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reu_pokernight_appbundle_location';
    }
}
