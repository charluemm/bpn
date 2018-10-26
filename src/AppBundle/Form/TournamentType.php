<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class TournamentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	//$data = $builder->getData();
//     	$players = $options['players'];
        $builder
            ->add('date', DateType::class, array(
            		'label' => 'Datum',
            		'required' => false,
            ))
            ->add('event', EntityType::class, array(
            		'label' => 'übergeordnete Veranstaltung',
            		'class' => 'AppBundle:Event',
            		'choice_label' => 'name',
            		'multiple' => false,
            		'expanded' => false,
            		'required' => false
            ))
            ->add('mainTournament', CheckboxType::class, array(
            		'label' => 'Hauptturnier zum Event',
            		'required' => false,
            	))
            ->add('name', TextType::class, array(
            		'label' => 'Veranstaltungsname',
            		'required' => false,
            ))
            ->add('description', TextareaType::class, array(
            		'label' => 'Beschreibung',
            		'required' => false,
            ))
            ->add('location', EntityType::class, array(
            		'label' => 'Veranstaltungsort',
            		'class' => 'AppBundle:Location',
            		'choice_label' => 'name',
            		'placeholder' => 'Veranstaltungsort auswählen',
            		'empty_data' => null,
            		'multiple' => false,
            		'expanded' => false,
            		'required' => false
            ))
//             ->add('ranking', 'collection', array(
//             		'type' => new RankingFieldType(),
//             		'label' => 'Plazierung',
//             		'options'  => array(
//             				'label' => false,
//             				'required'  => false,
//            		)
//             ))
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($builder)
            {
            	$form = $event->getForm();
            	$data = $event->getData();
            	$form->add('players', EntityType::class, array(
            			'label' => 'Teilnemer',
            			'class' => 'AppBundle:Player',
            			'data' => $data->getPlayers(),
            	        'query_builder' => function (EntityRepository $er) {
                	        return $er->createQueryBuilder('p')
                    	        ->addOrderBy('p.givenname', 'ASC')
                    	        ->addOrderBy('p.surname', 'ASC');
            	        },
            			'multiple' => true,
            			'expanded' => true,
            			'required' => false,
            			'mapped' => false
            	));
            });
        ;

//         if($data->getRanking()->count() == 0)
//         {
// 	        foreach ($players as $player)
// 	        {
// 	        	$builder
// 	        		->add('ranking_'.$player->getId(), new RankingFieldType(new TournamentRanking($data, $player)), array('mapped' => false));
// 	        }
//         }
    }
    
    /**
     * @param OptionsResolverInterface $resolver
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
        return 'reu_pokernight_AppBundle_tournament';
    }
}
