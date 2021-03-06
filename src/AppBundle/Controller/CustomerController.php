<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class CustomerController extends Controller {

    /**
     * @Route("/customers", name="customer_index")
     */
    public function indexAction() {
                
        $session = new Session();

        $showId = $session->get('showId');

        $show = $this->getDoctrine()->getRepository('AppBundle:Show')->find($showId);

        $query = $this->getDoctrine()->getManager()->createQuery(
                "SELECT o.customerNumber as customer, COUNT(o) as quantity "
                . "FROM AppBundle:ShowOrder o "
                . "WHERE o.show = :show "
                . "GROUP BY o.customerNumber")->setParameter('show', $show);
        $orders = $query->getResult();

        return $this->render('customer/index.html.twig', array(
                    'orders' => $orders
        ));
    }

    /**
     * @Route("/customers/view/{customer}", name="customer_view")
     */
    public function viewAction($customer) {
                
        $session = new Session();

        $showId = $session->get('showId');

        $show = $this->getDoctrine()->getRepository('AppBundle:Show')->find($showId);
        
        $query = $this->getDoctrine()
                ->getManager()
                ->createQuery(
                        "SELECT i.vendor, count(o) as quantity "
                        . "FROM AppBundle:ShowOrderItem i "
                        . "JOIN i.showOrder o "
                        . "WHERE o.show = :show "
                        . "AND o.customerNumber = :customerNumber "
                        . "GROUP BY i.vendor");
        
        $query->setParameter('customerNumber', $customer);
        $query->setParameter('show', $show);
        
        $orders = $query->getResult();

        return $this->render('customer/view.html.twig', array(
                    'orders' => $orders,
                    'customer' => $customer
        ));
    }
    
    /**
     * @Route("/customers/detail/{customer}/{vendor}", name="customer_detail")
     */
    public function detailAction($customer, $vendor) {
                
        $session = new Session();

        $showId = $session->get('showId');

        $show = $this->getDoctrine()->getRepository('AppBundle:Show')->find($showId);
        
        $query = $this->getDoctrine()
                ->getManager()
                ->createQuery(
                        "SELECT i FROM AppBundle:ShowOrderItem i "
                        . "JOIN i.showOrder o "
                        . "WHERE o.show = :show "
                        . "AND o.customerNumber = :customerNumber "
                        . "AND i.vendor = :vendor");
        
        $query->setParameters(array(
            'customerNumber' => $customer,
            'vendor' => $vendor,
            'show' => $show
        ));
        
        $items = $query->getResult();
        
        return $this->render('customer/detail.html.twig', array(
            'customer' => $customer,
            'vendor' => $vendor,
            'items' => $items
        ));
        
    }

    /**
     * @Route("/customers/export/{customer}/{vendor}", name="customer_export")
     */
    public function exportAction($customer, $vendor) {
                
        $session = new Session();

        $showId = $session->get('showId');

        $show = $this->getDoctrine()->getRepository('AppBundle:Show')->find($showId);
        
        $query = $this->getDoctrine()
                ->getManager()
                ->createQuery(
                        "SELECT i FROM AppBundle:ShowOrderItem i "
                        . "JOIN i.showOrder o "
                        . "WHERE o.show = :show "
                        . "AND o.customerNumber = :customerNumber "
                        . "AND i.vendor = :vendor");
        
        $query->setParameters(array(
            'customerNumber' => $customer,
            'vendor' => $vendor,
            'show' => $show
        ));
        
        $items = $query->getResult();

        $tempfile = tempnam(sys_get_temp_dir(), 'export');
        $out = fopen($tempfile, 'w');
        fputcsv($out, array('vendor', 'sku', 'name', 'quantity'));

        foreach ($items as $item) {
            fputcsv($out, array(
                $vendor,
                $item->getSku(),
                $item->getName(),
                $item->getQuantity()
            ));
        }
        fclose($out);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $customer . '_' . $vendor . '_export.csv"');
        $response->setContent(file_get_contents($tempfile));

        unlink($tempfile);

        return $response;
    }


}
