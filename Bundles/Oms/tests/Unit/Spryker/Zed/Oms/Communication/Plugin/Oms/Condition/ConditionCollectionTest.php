<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Zed\Oms\Communication\Plugin\Oms\Command;

use Spryker\Zed\Oms\Communication\Plugin\Oms\Condition\ConditionCollection;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Condition\ConditionCollectionInterface;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Condition\ConditionInterface;
use Spryker\Zed\Oms\Exception\ConditionNotFoundException;

/**
 * @group Spryker
 * @group Zed
 * @group Oms
 * @group Communication
 * @group ConditionCollection
 */
class ConditionCollectionTest extends \PHPUnit_Framework_TestCase
{

    const CONDITION_NAME = 'conditionName';

    /**
     * @return void
     */
    public function testAddShouldReturnInstance()
    {
        $conditionCollection = new ConditionCollection();
        $result = $conditionCollection->add($this->getConditionMock(), self::CONDITION_NAME);

        $this->assertInstanceOf(ConditionCollectionInterface::class, $result);
    }

    /**
     * @return void
     */
    public function testGetShouldReturnCommand()
    {
        $conditionCollection = new ConditionCollection();
        $condition = $this->getConditionMock();
        $conditionCollection->add($condition, self::CONDITION_NAME);

        $this->assertSame($condition, $conditionCollection->get(self::CONDITION_NAME));
    }

    /**
     * @return void
     */
    public function testHasShouldReturnFalse()
    {
        $conditionCollection = new ConditionCollection();

        $this->assertFalse($conditionCollection->has(self::CONDITION_NAME));
    }

    /**
     * @return void
     */
    public function testHasShouldReturnTrue()
    {
        $conditionCollection = new ConditionCollection();
        $condition = $this->getConditionMock();
        $conditionCollection->add($condition, self::CONDITION_NAME);

        $this->assertTrue($conditionCollection->has(self::CONDITION_NAME));
    }

    /**
     * @return void
     */
    public function testGetShouldThrowException()
    {
        $conditionCollection = new ConditionCollection();

        $this->setExpectedException(ConditionNotFoundException::class);

        $conditionCollection->get(self::CONDITION_NAME);
    }

    /**
     * @return void
     */
    public function testArrayAccess()
    {
        $conditionCollection = new ConditionCollection();
        $this->assertFalse(isset($conditionCollection[self::CONDITION_NAME]));

        $condition = $this->getConditionMock();
        $conditionCollection[self::CONDITION_NAME] = $condition;

        $this->assertTrue(isset($conditionCollection[self::CONDITION_NAME]));
        $this->assertSame($condition, $conditionCollection[self::CONDITION_NAME]);

        unset($conditionCollection[self::CONDITION_NAME]);
        $this->assertFalse(isset($conditionCollection[self::CONDITION_NAME]));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Zed\Oms\Communication\Plugin\Oms\Condition\ConditionInterface
     */
    private function getConditionMock()
    {
        return $this->getMock(ConditionInterface::class);
    }

}