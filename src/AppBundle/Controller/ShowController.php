<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Show;
use AppBundle\Form\ShowType;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ShowController extends Controller {

    /**
     * @Route("/shows", name="show_index")
     */
    public function indexAction() {

        $shows = $this->getDoctrine()->getRepository('AppBundle:Show')->findAll();

        return $this->render('show/index.html.twig', array(
                    'shows' => $shows
        ));
    }

    /**
     * @Route("/shows/new", name="show_new")
     */
    public function newAction(Request $request) {

        $order = new Show();
        $order->setCreatedOn(new DateTime("now"));
        $order->setUpdatedOn(new DateTime("now"));

        $form = $this->createForm(ShowType::class, $order);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();

            return $this->redirectToRoute('show_index');
        }

        return $this->render('show/new.html.twig', array(
                    'form' => $form->createView()
        ));
    }

    /**
     * @Route("/shows/edit/{id}", name="show_edit")
     */
    public function editAction($id, Request $request) {

        $show = $this->getDoctrine()->getRepository('AppBundle:Show')->find($id);

        $form = $this->createForm(ShowType::class, $show);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $show->setUpdatedOn(new DateTime("now"));

            $em = $this->getDoctrine()->getManager();

            $em->persist($show);

            $em->flush();

            return $this->redirectToRoute('show_edit', array('id' => $show->getId()));
        }

        return $this->render('show/edit.html.twig', array(
                    'form' => $form->createView(),
                    'show' => $show
        ));
    }

    /**
     * @Route("/shows/remove/{id}", name="show_remove")
     */
    public function removeAction($id) {
        $order = $this->getDoctrine()->getRepository('AppBundle:Show')->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($order);
        $em->flush();

        return $this->redirectToRoute('show_index');
    }

}
