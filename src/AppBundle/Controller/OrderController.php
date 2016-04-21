<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ShowOrder;
use AppBundle\Entity\ShowOrderItem;
use AppBundle\Form\ShowOrderItemType;
use AppBundle\Form\ShowOrderType;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends Controller {

    /**
     * @Route("/orders", name="order_index")
     */
    public function indexAction() {

        $orders = $this->getDoctrine()->getRepository('AppBundle:ShowOrder')->findAll();

        return $this->render('order/index.html.twig', array(
                    'orders' => $orders
        ));
    }

    /**
     * @Route("/orders/new", name="order_new")
     */
    public function newAction(Request $request) {

        $order = new ShowOrder();
        $order->setCreatedOn(new DateTime("now"));
        $order->setUpdatedOn(new DateTime("now"));

        $form = $this->createForm(ShowOrderType::class, $order);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $customer = $this->get('erp_one_customer_service')->getCustomer($order->getCustomerNumber());

            if ($customer == null) {

                $this->addFlash("notice", "Could Not Find Customer");
            } else {

                $order->setCustomerNumber($customer->customer_customer);

                $em = $this->getDoctrine()->getManager();
                $em->persist($order);
                $em->flush();

                return $this->redirectToRoute('order_edit', array('id' => $order->getId()));
            }
        }

        return $this->render('order/new.html.twig', array(
                    'form' => $form->createView()
        ));
    }

    /**
     * @Route("/orders/edit/{id}", name="order_edit")
     */
    public function editAction($id, Request $request) {

        $order = $this->getDoctrine()->getRepository('AppBundle:ShowOrder')->find($id);

        $orderItem = new ShowOrderItem();

        $orderItem->setQuantity(1);
        $orderItem->setShowOrder($order);

        $form = $this->createForm(ShowOrderItemType::class, $orderItem);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $product = $this->get('erp_one_product_service')->getProduct($orderItem->getSku());

            if ($product == null) {

                $this->addFlash("notice", "Could Not Find Item");
            } else {

                $em = $this->getDoctrine()->getManager();

                $existingProduct = $this->getDoctrine()->getRepository('AppBundle:ShowOrderItem')->findOneBy(array('showOrder' => $order, 'sku' => $product->item_item));

                if ($existingProduct !== null) {
                    $existingProduct->setQuantity($orderItem->getQuantity() + $existingProduct->getQuantity());
                    $em->persist($existingProduct);
                } else {
                    $orderItem->setSku($product->item_item);
                    $orderItem->setName($product->item_descr[0]);
                    $orderItem->setVendor($product->item_vendor);
                    $em->persist($orderItem);
                }

                $order->setUpdatedOn(new DateTime("now"));

                $em->persist($order);

                $em->flush();

                return $this->redirectToRoute('order_edit', array('id' => $order->getId()));
            }
        }

        return $this->render('order/edit.html.twig', array(
                    'form' => $form->createView(),
                    'order' => $order
        ));
    }

    /**
     * @Route("/orders/remove_item/{id}", name="order_remove_item")
     */
    public function removeItemAction($id) {

        $orderItem = $this->getDoctrine()->getRepository('AppBundle:ShowOrderItem')->find($id);

        $orderId = $orderItem->getShowOrder()->getId();

        $em = $this->getDoctrine()->getManager();
        $em->remove($orderItem);
        $em->flush();

        return $this->redirectToRoute('order_edit', array('id' => $orderId));
    }

    /**
     * @Route("/orders/remove/{id}", name="order_remove")
     */
    public function removeAction($id) {
        $order = $this->getDoctrine()->getRepository('AppBundle:ShowOrder')->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($order);
        $em->flush();

        return $this->redirectToRoute('order_index');
    }
    
    /**
     * @Route("/orders/update_quantities/{id}", name="order_update_quantities")
     */
    public function updateQuantitiesAction($id, Request $request) {
        
        $quantities = $request->get('quantity');
        
        $repo = $this->getDoctrine()->getRepository('AppBundle:ShowOrderItem');
        $em = $this->getDoctrine()->getManager();
        
        foreach ($quantities as $itemId => $quantity) {
            $orderItem = $repo->find($itemId);
            $orderItem->setQuantity($quantity);
            $em->persist($orderItem);
        }
        
        $em->flush();
        
        return $this->redirectToRoute('order_edit', array('id' => $id));
        
    }

}
