<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddRankingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($builder, $options)
	        {
	        	$form = $event->getForm();
	        	$data = $event->getData();
	        	$form->add('ranking', CollectionType::class, array(
			            		'entry_type' => RankingFieldType::class,
			            		'label' => $options['label'] === null ? 'Plazierung' : $options['label'],
			            		'entry_options'  => array(
			            				'label' => false,
			            				'required'  => false,
			            				'max' => $data->getRanking()->count(),
			            		)
			            ));
// 		        	->add('players', 'entity', array(
// 	        			'label' => 'Teilnemer',
// 	        			'class' => 'PokernightAppBundle:Player',
// 	        			'data' => $data->getPlayers(),
// 	        			'multiple' => true,
// 	        			'expanded' => true,
// 	        			'required' => false,
// 	        			'mapped' => false
// 	        	));
        });
    }
    
	/* (non-PHPdoc)
	 * @see \Symfony\Component\Form\AbstractType::configureOptions()
	 */
	public function configureOptions(OptionsResolver $resolver) 
	{
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Tournament'
        ));
        
//         $resolver->setRequired(array('players'));
	}

    
    /**
     * @return string
     */
    public function getName()
    {
        return 'reu_pokernight_appbundle_tournament_add_ranking';
    }
}
