<?php

namespace AppBundle\Form;

use AppBundle\Model\Tournament\TournamentRankingInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RankingFieldType extends AbstractType 
{

	/**
	 *
	 * @param FormBuilderInterface $builder        	
	 * @param array $options        	
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) 
	{
	    $builder
	       ->add('kickedByPlayer', EntityType::class, array(
			        'label' => 'ausgeschieden gegen',
			        'class' => 'AppBundle:Player',
	               'placeholder' => 'Spieler wählen',
	               'required' => true
			))
			->add('kickedAt', DateTimeType::class, array(
			        'label' => 'Zeitpunkt',
			        'widget' => 'single_text',
			        'format' => 'dd.MM.yyyy HH:mm',
			        'placeholder' => 'dd.MM.yyy HH:mm',
			        'required' => true,
			));
	}
	/**
	 *
	 * @return string
	 */
	public function getBlockPrefix() 
	{
		return 'ranking_field';
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => TournamentRankingInterface::class
		));
		$resolver->setRequired(array('max'));
	}
}
