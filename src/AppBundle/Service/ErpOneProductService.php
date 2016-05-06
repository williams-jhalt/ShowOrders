<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

class ErpOneProductService {

    /**
     * @var ErpOneConnectorService
     */
    private $_erp;

    /**
     * @var EntityManager
     */
    private $_em;

    public function __construct(ErpOneConnectorService $erp, EntityManager $em) {
        $this->_erp = $erp;
        $this->_em = $em;
    }

    /**
     * 
     * 
     * @param string $sku
     * @return array
     */
    public function getProduct($sku) {

        $fields = "item.item,item.manufacturer,item.product_line,item.descr,item.date_added,wa_item.qty_oh,wa_item.list_price,item.upc1,item.sy_lookup,item.vendor";
        $query = "FOR EACH item NO-LOCK WHERE "
                . "item.company_it = '{$this->_erp->getCompany()}' AND item.item = '{$sku}', "
                . "EACH wa_item NO-LOCK WHERE "
                . "wa_item.company_it = item.company_it AND wa_item.item = item.item";

        $response = $this->_erp->read($query, $fields, 0, 1);
        
        if (empty($response)) {
            return null;
        }
        
        return $response[0];
        
    }

}
