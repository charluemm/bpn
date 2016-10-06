<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class RankingFieldType extends AbstractType 
{

	/**
	 *
	 * @param FormBuilderInterface $builder        	
	 * @param array $options        	
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
// 		->add('player', 'text', array (
// 				'label' => 'Spieler',
// 				'read_only' => true
// 		))
		// 		->add( 'tournament', 'text', array (
// 				'label' => 'Turnier',
// 				'read_only' => true
// 		))
// 		->add ( 'player' )
// 		->add ( 'tournament' )
// 		->add ( 'rank' );
		
// 		$builder->get('player')->addModelTransformer(new CallbackTransformer(
// 				function(Player $player){ return $player->__toString(); }, function($submitted){ return null; }));

			->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($builder, $options)
			{
				$form = $event->getForm();
				$data = $event->getData();
				$form->add('rank', IntegerType::class, array(
						'label' => $data->getPlayer()->__toString(),
						'attr' => array('min' => 1, 'max' => $options['max']),
						'constraints' => array(new Range(array('min' => 1, 'max' => $options['max'])))
				));
			});
	}
	/**
	 *
	 * @return string
	 */
	public function getName() {
		return 'ranking_field';
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'AppBundle\Entity\TournamentRanking' 
		));
		$resolver->setRequired(array('max'));
	}
}
