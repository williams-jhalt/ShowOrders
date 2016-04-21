<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VendorController extends Controller {

    /**
     * @Route("/vendors", name="vendor_index")
     */
    public function indexAction() {

        $query = $this->getDoctrine()->getManager()->createQuery("SELECT o.vendor as vendor, SUM(o.quantity) as quantity, COUNT(o) as lines FROM AppBundle:ShowOrderItem o GROUP BY o.vendor");
        $orders = $query->getResult();

        return $this->render('vendor/index.html.twig', array(
                    'orders' => $orders
        ));
    }

    /**
     * @Route("/vendors/view/{vendor}", name="vendor_view")
     */
    public function viewAction($vendor) {

        $query = $this->getDoctrine()
                ->getManager()
                ->createQuery(
                "SELECT i.sku as sku, i.name as name, SUM(i.quantity) as quantity, COUNT(i) as lines "
                . "FROM AppBundle:ShowOrderItem i "
                . "WHERE i.vendor = :vendor "
                . "GROUP BY i.sku, i.name");

        $query->setParameter('vendor', $vendor);

        $items = $query->getResult();

        return $this->render('vendor/view.html.twig', array(
                    'items' => $items,
                    'vendor' => $vendor
        ));
    }

    /**
     * @Route("/vendors/export/{vendor}", name="vendor_export")
     */
    public function exportAction($vendor) {

        $query = $this->getDoctrine()
                ->getManager()
                ->createQuery(
                "SELECT i.sku as sku, i.name as name, SUM(i.quantity) as quantity, COUNT(i) as lines "
                . "FROM AppBundle:ShowOrderItem i "
                . "WHERE i.vendor = :vendor "
                . "GROUP BY i.sku, i.name");

        $query->setParameter('vendor', $vendor);

        $items = $query->getResult();

        $tempfile = tempnam(sys_get_temp_dir(), 'export');
        $out = fopen($tempfile, 'w');
        fputcsv($out, array('vendor', 'sku', 'name', 'quantity'));

        foreach ($items as $item) {
            fputcsv($out, array(
                $vendor,
                $item['sku'],
                $item['name'],
                $item['quantity']
            ));
        }
        fclose($out);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $vendor . '_export.csv"');
        $response->setContent(file_get_contents($tempfile));

        unlink($tempfile);

        return $response;
    }

}
