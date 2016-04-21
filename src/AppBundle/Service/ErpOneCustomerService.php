<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

class ErpOneCustomerService {

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
    public function getCustomer($customerNumber) {

        $fields = "customer.customer";
        $query = "FOR EACH customer NO-LOCK WHERE "
                . "customer.company_cu = '{$this->_erp->getCompany()}' AND customer.customer = '{$customerNumber}'";

        $response = $this->_erp->read($query, $fields, 0, 1);
        
        if (empty($response)) {
            return null;
        }
        
        return $response[0];
        
    }

}
