<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ShowOrder;
use AppBundle\Entity\ShowOrderItem;
use AppBundle\Form\ShowOrderItemType;
use AppBundle\Form\ShowOrderType;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SplFileObject;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class OrderController extends Controller {

    /**
     * @Route("/orders", name="order_index")
     */
    public function indexAction(Request $request) {
        
        $session = new Session();

        $showId = $request->get('showId', $session->get('showId'));

        if ($showId !== null) {
            $session->set('showId', $showId);
        }

        $show = $this->getDoctrine()->getRepository('AppBundle:Show')->find($showId);

        $orders = $this->getDoctrine()->getRepository('AppBundle:ShowOrder')->findByShow($show);

        return $this->render('order/index.html.twig', array(
                    'orders' => $orders
        ));
    }

    /**
     * @Route("/orders/new", name="order_new")
     */
    public function newAction(Request $request) {

        $session = new Session();
        $showId = $session->get('showId');

        $show = $this->getDoctrine()->getRepository('AppBundle:Show')->find($showId);

        $order = new ShowOrder();
        $order->setShow($show);
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
                    $orderItem->setName(implode(" ", $product->item_descr));
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

    /**
     * @Route("/orders/import/{id}", name="order_import")
     */
    public function importAction($id, Request $request) {

        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository('AppBundle:ShowOrder')->find($id);

        if ($request->isMethod(Request::METHOD_POST)) {

            $file = $request->files->get('file');

            $fh = $file->openFile("r");
            $fh->setFlags(SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);

            $goodfile = false;

            if ($file->getClientOriginalExtension() == 'csv' || $file->getClientOriginalExtension() == 'txt') {

                while (!$fh->eof()) {
                    $row = $fh->fgetcsv();
                    if (empty($row[0])) {
                        continue;
                    }
                    $product = $this->get('erp_one_product_service')->getProduct(trim($row[0]));
                    if ($product !== null) {
                        $quantity = $row[1];
                        $item = $this->getDoctrine()->getRepository('AppBundle:ShowOrderItem')->findOneBy(array('showOrder' => $order, 'sku' => $product->item_item));
                        if ($item === null) {
                            $item = new ShowOrderItem();
                            $item->setSku($product->item_item);
                            $item->setName(implode(" ", $product->item_descr));
                            $item->setVendor($product->item_vendor);
                            $item->setShowOrder($order);
                        }
                        $item->setQuantity($quantity);
                        $em->persist($item);
                        $goodfile = true;
                    } else {
                        $this->addFlash("notice", "Could not find SKU {$row[0]}");
                    }
                }

                $em->flush();
            }

            if ($goodfile == false) {
                $this->addFlash("notice", "File was not in expected format");
            } else {
                return $this->redirectToRoute('order_edit', array('id' => $id));
            }
        }

        return $this->render('order/import.html.twig', array(
                    'order' => $order
        ));
    }

}
