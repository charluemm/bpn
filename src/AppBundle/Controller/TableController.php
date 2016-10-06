<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Tournament;
use AppBundle\Entity\TournamentTable;
use AppBundle\Form\TournamentTableType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Table controller.
 *
 * @Route("/table")
 */
class TableController extends Controller
{
	/**
	 * @Route("/live/add/{tournamentId}", name="table_add_live")
	 * @Template("AppBundle:Administration:live_add_table.html.twig")
	 */
	public function addTableAction(Request $request,$tournamentId)
	{
		$em = $this->getDoctrine()->getManager();
		$tournamentRepo = $em->getRepository('AppBundle:Tournament');
		$tableRepo = $em->getRepository('AppBundle:TournamentTable');

		$tournament = $tournamentRepo->find($tournamentId);
		$number = 1 + $tableRepo->findMaxNumberByTournament($tournament);

		$newTable = new TournamentTable($tournament);
		$formNewTable = $this->createFormBuilder($newTable)
			->add('number', 'integer', array('label' => 'Tisch-Nr.', 'data' => $number))
			->add('finalTable', 'checkbox', array('label' => 'Finaltable?', 'required' => false))
			->add('maxSeats', 'integer', array('label' => 'max. Spielerplätze (2 - 10)'))
			->add('comment', 'textarea', array('label' => 'Kommentar', 'required' => false))
			->add('submit', 'submit', array('label' => 'Hinzufügen'))
			->getForm();

		$formNewTable->handleRequest($request);
		if($formNewTable->isValid())
		{
			$em->persist($newTable);
			
			//seats anlegen FIXME (nur mit Spielerzuordnung möglich), Zuordnung über AJAX-REQUEST im LIVE Modus
//			for ($i = 1; $i <= $newTable->getMaxSeats(); $i++)
//			{
//				$newSeat = new Seat($newTable);
//				$em->persist($newSeat);
//				$newSeat->setNumber($i)->setName("Platz $i");
//				$newTable->addSeat($newSeat);
//			}
			
			$em->flush();
			$this->addFlash('success', "Tisch $number erfolgreich zum Turnier hinzugefügt.");
			return $this->redirect($this->generateUrl('administration_live', array('tournamentId' => $tournamentId)));
		}
		
		return array(
				'tournament' => $tournament,
				'frm_add_table' => $formNewTable->createView()
		);
	}

    /**
     * Displays a form to edit an existing Table entity.
     *
     * @Route("/{id}/edit", name="table_edit")
     * @Method("GET")
     * @Template("AppBundle:Administration:live_edit_table.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:TournamentTable')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Table entity.');
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
    * Creates a form to edit a Table entity.
    *
    * @param Table $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TournamentTable $entity)
    {
    	$number = 1 + $this->getRepository()->findMaxNumberByTournament($entity->getTournament());

        $form = $this->createForm(new TournamentTableType(), $entity, array(
            'action' => $this->generateUrl('table_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'number' => $number
        ));
        
        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing table entity.
     *
     * @Route("/{id}", name="table_update")
     * @Method("PUT")
     * @Template("AppBundle:Table:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getRepository()->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tournament entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        
        if ($editForm->isValid()) 
        {
        	$em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirect($this->generateUrl('table_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a table entity.
     *
     * @Route("/{id}", name="table_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $this->getRepository()->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Tournament entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('tournament_show', array('tournamentId' => $entity->getTournament()->getId())));
    }

    /**
     * Creates a form to delete a Table entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('table_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    private function getRepository()
    {
    	return $this->getDoctrine()->getRepository('AppBundle:TournamentTable');
    }
}
