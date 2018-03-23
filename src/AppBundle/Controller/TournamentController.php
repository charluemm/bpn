<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Tournament;
use AppBundle\Entity\TournamentManager;
use AppBundle\Entity\TournamentRanking;
use AppBundle\Form\AddRankingType;
use AppBundle\Form\TournamentType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\TournamentRanking\TournamentRankingManager;


/**
 * Tournament controller.
 *
 * @Route("/tournament")
 */
class TournamentController extends Controller
{
    
    /**
     * Creates a new Tournament entity.
     *
     * @Route("/", name="tournament_create")
     * @Method("POST")
     * @Template("AppBundle:Tournament:new.html.twig")
     */
    public function createAction(Request $request)
    {
        /** @var TournamentManager $tournamentManager **/
        $tournamentManager = $this->get('bpn.tournament.manager');
        
        $entity = $tournamentManager->create();
        
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) 
        {
            $em = $this->getDoctrine()->getManager();
            $rankingRepo = $em->getRepository('AppBundle:TournamentRanking');
            $players = $form->get('players')->getData();

            $tournamentManager->update($entity, false);
            
            foreach ($players as $player)
            {
            	$ranking = $rankingRepo->findBy(array('tournament' => $entity, 'player' => $player));
            	if(empty($ranking))
            	{
            		$ranking = new TournamentRanking($entity, $player);
            		$em->persist($ranking);
            		$entity->addRanking($ranking);
            	}
            }
            $em->flush();

            return $this->redirect($this->generateUrl('tournament_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Tournament entity.
     *
     * @param Tournament $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Tournament $entity)
    {
        $form = $this->createForm(TournamentType::class, $entity, array(
            'action' => $this->generateUrl('tournament_create'),
            'method' => 'POST',
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Tournament entity.
     *
     * @Route("/new", name="tournament_new")
     * @Template()
     */
    public function newAction()
    {
        /** @var TournamentManager $tournamentManager **/
        $tournamentManager = $this->get('bpn.tournament.manager');
        
        $entity = $tournamentManager->create();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Tournament entity.
     *
     * @Route("/{id}", name="tournament_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        /** @var TournamentManager $tournamentManager **/
        $tournamentManager = $this->get('bpn.tournament.manager');
        
        $entity = $tournamentManager->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tournament entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Tournament entity.
     *
     * @Route("/{id}/edit", name="tournament_edit")
     * @Template()
     */
    public function editAction(Request $request, $id)
    {
        /** @var TournamentManager $tournamentManager **/
        $tournamentManager = $this->get('bpn.tournament.manager');
        
        $entity = $tournamentManager->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tournament entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        $editForm->handleRequest($request);
        if($editForm->isSubmitted() && $editForm->isValid())
        {
            $tournamentManager->update($entity);
            $this->addFlash('success', "Turnier wurde erfolgreich bearbeitet.");
            return $this->redirectToRoute('tournament_show', array('id' => $id));
        }
        
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Tournament entity.
    *
    * @param Tournament $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Tournament $entity)
    {
        $form = $this->createForm(TournamentType::class, $entity, array(
            'action' => $this->generateUrl('tournament_edit', array('id' => $entity->getId())),
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Update'));

        return $form;
    }

    /**
     * Deletes a Tournament entity.
     *
     * @Route("/{id}", name="tournament_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var TournamentManager $tournamentManager **/
            $tournamentManager = $this->get('bpn.tournament.manager');
            
            $entity = $tournamentManager->find($id);
            
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Tournament entity.');
            }
            
            $tournamentManager->remove($entity);
        }

        return $this->redirect($this->generateUrl('tournament'));
    }

    /**
     * Creates a form to delete a Tournament entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tournament_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * @Route("/{tournamentId}/ranking", name="tournament_ranking_update")
     * @Template("AppBundle:Tournament:update_ranking.html.twig")
     */
    public function updateTournamentRankingAction(Request $request, $tournamentId)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Tournament')->find($tournamentId);
        /** @var $tournamentRankingManager TournamentRankingManager **/
        $tournamentRankingManager = $this->get('bpn.tournament_ranking.manager');
        
        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find Tournament entity.');
        }
     
        $editForm = $this->createForm(AddRankingType::class, $entity, array(
                'label' => false,
        ))
            ->add('submit', SubmitType::class, array('label' => 'Update'));
        
        $editForm->handleRequest($request);
        if ($editForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $tournamentRankingManager->refreshRanking($entity);
            
            return $this->redirect($this->generateUrl('administration_live', array('tournamentId' => $tournamentId)));
        }
        
        return array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
        );
        
    }
}
