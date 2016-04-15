<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Functional\Spryker\Zed\Sales\Business;

use Orm\Zed\Oms\Persistence\SpyOmsOrderItemState;
use Orm\Zed\Oms\Persistence\SpyOmsOrderProcess;
use Orm\Zed\Sales\Persistence\SpySalesExpense;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Orm\Zed\Sales\Persistence\SpySalesOrderAddress;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Orm\Zed\Shipment\Persistence\SpyShipmentMethodQuery;

class TestOrderCreator
{

    const DEFAULT_OMS_PROCESS_NAME = 'test';
    const DEFAULT_ITEM_STATE = 'test';

    /**
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrder
     */
    public function create()
    {
        $salesOrderAddressEntity = $this->createSalesOrderAddress();
        $omsStateEntity = $this->createOmsState();
        $omsProcessEntity = $this->createOmsProcess();
        $salesOrderEntity = $this->createSpySalesOrderEntity($salesOrderAddressEntity);
        $this->createSalesExpense($salesOrderEntity);

        $this->createOrderItem(
            $omsStateEntity,
            $salesOrderEntity,
            $omsProcessEntity,
            $quantity = 2,
            $unitGrosPrice = 500,
            $taxRate = 19
        );

        $this->createOrderItem(
            $omsStateEntity,
            $salesOrderEntity,
            $omsProcessEntity,
            $quantity = 1,
            $unitGrosPrice = 800,
            $taxRate = 19
        );

        return $salesOrderEntity;
    }

    /**
     * @param \Orm\Zed\Oms\Persistence\SpyOmsOrderItemState $omsStateEntity
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $salesOrderEntity
     * @param int $quantity
     * @param int $grossPrice
     * @param int $taxRate
     *
     * @throws \Propel\Runtime\Exception\PropelException
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrderItem
     */
    protected function createOrderItem(
        SpyOmsOrderItemState $omsStateEntity,
        SpySalesOrder $salesOrderEntity,
        SpyOmsOrderProcess $omsOrderProcessEntity,
        $quantity,
        $grossPrice,
        $taxRate
    ) {
        $salesOrderItem = new SpySalesOrderItem();
        $salesOrderItem->setGrossPrice($grossPrice);
        $salesOrderItem->setQuantity($quantity);
        $salesOrderItem->setSku('123');
        $salesOrderItem->setName('test1');
        $salesOrderItem->setTaxRate($taxRate);
        $salesOrderItem->setFkOmsOrderItemState($omsStateEntity->getIdOmsOrderItemState());
        $salesOrderItem->setProcess($omsOrderProcessEntity);
        $salesOrderItem->setFkSalesOrder($salesOrderEntity->getIdSalesOrder());
        $salesOrderItem->save();

        return $salesOrderItem;
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderAddress $salesOrderAddressEntity
     *
     * @throws \Propel\Runtime\Exception\PropelException
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrder
     */
    protected function createSpySalesOrderEntity(SpySalesOrderAddress $salesOrderAddressEntity)
    {
        $shipmentMethodEntity = SpyShipmentMethodQuery::create()->findOne();

        $salesOrderEntity = new SpySalesOrder();
        $salesOrderEntity->setBillingAddress($salesOrderAddressEntity);
        $salesOrderEntity->setShippingAddress(clone $salesOrderAddressEntity);
        $salesOrderEntity->setShipmentMethod($shipmentMethodEntity);
        $salesOrderEntity->setOrderReference('123');
        $salesOrderEntity->save();

        return $salesOrderEntity;
    }

    /**
     * @throws \Propel\Runtime\Exception\PropelException
     * @return \Orm\Zed\Oms\Persistence\SpyOmsOrderItemState
     */
    protected function createOmsState()
    {
        $omsStateEntity = new SpyOmsOrderItemState();
        $omsStateEntity->setName(self::DEFAULT_ITEM_STATE);
        $omsStateEntity->save();

        return $omsStateEntity;
    }

    /**
     * @throws \Propel\Runtime\Exception\PropelException
     * @return \Orm\Zed\Oms\Persistence\SpyOmsOrderProcess
     */
    protected function createOmsProcess()
    {
        $omsProcessEntity = new SpyOmsOrderProcess();
        $omsProcessEntity->setName(self::DEFAULT_OMS_PROCESS_NAME);
        $omsProcessEntity->save();

        return $omsProcessEntity;
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $salesOrderEntity
     * @throws \Propel\Runtime\Exception\PropelException
     * @return void
     */
    protected function createSalesExpense(SpySalesOrder $salesOrderEntity)
    {
        $salesExpenseEntity = new SpySalesExpense();
        $salesExpenseEntity->setName('shiping test');
        $salesExpenseEntity->setTaxRate(19);
        $salesExpenseEntity->setGrossPrice(100);
        $salesExpenseEntity->setFkSalesOrder($salesOrderEntity->getIdSalesOrder());
        $salesExpenseEntity->save();
    }

    /**
     * @throws \Propel\Runtime\Exception\PropelException
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrderAddress
     */
    protected function createSalesOrderAddress()
    {
        $salesOrderAddressEntity = new SpySalesOrderAddress();
        $salesOrderAddressEntity->setAddress1(1);
        $salesOrderAddressEntity->setAddress2(2);
        $salesOrderAddressEntity->setSalutation('Mr');
        $salesOrderAddressEntity->setCellPhone('123456789');
        $salesOrderAddressEntity->setCity('City');
        $salesOrderAddressEntity->setCreatedAt(new \DateTime());
        $salesOrderAddressEntity->setUpdatedAt(new \DateTime());
        $salesOrderAddressEntity->setComment('comment');
        $salesOrderAddressEntity->setDescription('describtion');
        $salesOrderAddressEntity->setCompany('company');
        $salesOrderAddressEntity->setFirstName('First name');
        $salesOrderAddressEntity->setLastName('Last Name');
        $salesOrderAddressEntity->setFkCountry(1);
        $salesOrderAddressEntity->setEmail('email');
        $salesOrderAddressEntity->setZipCode(10405);
        $salesOrderAddressEntity->save();

        return $salesOrderAddressEntity;
    }

}
