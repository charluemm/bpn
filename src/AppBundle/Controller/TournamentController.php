<?php

namespace Reu\Pokernight\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reu\Pokernight\AppBundle\Entity\Tournament;
use Reu\Pokernight\AppBundle\Form\TournamentType;
use Reu\Pokernight\AppBundle\Entity\TournamentRanking;
use Doctrine\Common\Util\Debug;
use Doctrine\Common\Collections\ArrayCollection;
use Reu\Pokernight\AppBundle\Form\AddRankingType;

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
     * @Template("PokernightAppBundle:Tournament:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Tournament();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) 
        {
            $em = $this->getDoctrine()->getManager();
            $rankingRepo = $em->getRepository('PokernightAppBundle:TournamentRanking');
            $players = $form->get('players')->getData();

            $em->persist($entity);
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
        $form = $this->createForm(new TournamentType(), $entity, array(
            'action' => $this->generateUrl('tournament_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Tournament entity.
     *
     * @Route("/new", name="tournament_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Tournament();
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
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PokernightAppBundle:Tournament')->find($id);

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
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PokernightAppBundle:Tournament')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tournament entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

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
        $form = $this->createForm(new TournamentType(), $entity, array(
            'action' => $this->generateUrl('tournament_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Displays a form to add Ranking to an existing Tournament entity.
     *
     * @Route("/{id}/ranking", name="tournament_add_ranking")
     * @Method("GET")
     * @Template("PokernightAppBundle:Tournament:edit.html.twig")
     */
    public function addRankingAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PokernightAppBundle:Tournament')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tournament entity.');
        }

        $form_edit = $this->createAddRankingForm($entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $form_edit->createView(),
        );
    }

    /**
    * Creates a form to add ranking to a Tournament entity.
    *
    * @param Tournament $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createAddRankingForm(Tournament $entity)
    {
        $form = $this->createForm(new AddRankingType(), $entity, array(
            'action' => $this->generateUrl('tournament_update_ranking', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Tournament entity.
     *
     * @Route("/{id}", name="tournament_update")
     * @Method("PUT")
     * @Template("PokernightAppBundle:Tournament:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PokernightAppBundle:Tournament')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tournament entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        
        if ($editForm->isValid()) 
        {
        	$em = $this->getDoctrine()->getManager();
        	$rankingRepo = $em->getRepository('PokernightAppBundle:TournamentRanking');
        	$players = $editForm->get('players')->getData();
        	
        	$allRankings = new ArrayCollection($rankingRepo->findBy(array('tournament' => $entity)));
        	$tournamentRanking = new ArrayCollection();

        	foreach($players as $player)
        	{
        		$ranking = $rankingRepo->findOneBy(array('tournament' => $entity, 'player' => $player));
        		if(empty($ranking))
        		{
        			$ranking = new TournamentRanking($entity, $player);
        			$em->persist($ranking);
        		}
        		$allRankings->removeElement($ranking);
        		$tournamentRanking->add($ranking);
        	}
        	
        	foreach ($allRankings as $ranking)
        		$em->remove($ranking);
        	
        	$entity->setRanking($tournamentRanking);
        	
            $em->flush();

            return $this->redirect($this->generateUrl('tournament_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Tournament Ranking .
     *
     * @Route("/{id}/ranking", name="tournament_update_ranking")
     * @Method("PUT")
     * @Template("PokernightAppBundle:Tournament:edit.html.twig")
     */
    public function updateRankingAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PokernightAppBundle:Tournament')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tournament entity.');
        }

        $editForm = $this->createAddRankingForm($entity);
        $editForm->handleRequest($request);
        
        if ($editForm->isValid()) 
        {
        	$em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirect($this->generateUrl('tournament_add_ranking', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
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
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PokernightAppBundle:Tournament')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Tournament entity.');
            }

            $em->remove($entity);
            $em->flush();
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
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
