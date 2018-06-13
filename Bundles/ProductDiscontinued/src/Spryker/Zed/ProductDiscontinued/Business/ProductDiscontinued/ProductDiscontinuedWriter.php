<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductDiscontinued\Business\ProductDiscontinued;

use Generated\Shared\Transfer\ProductDiscontinuedRequestTransfer;
use Generated\Shared\Transfer\ProductDiscontinuedResponseTransfer;
use Generated\Shared\Transfer\ProductDiscontinuedTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use Spryker\Zed\ProductDiscontinued\Persistence\ProductDiscontinuedEntityManagerInterface;
use Spryker\Zed\ProductDiscontinued\Persistence\ProductDiscontinuedRepositoryInterface;
use Spryker\Zed\ProductDiscontinued\ProductDiscontinuedConfig;

class ProductDiscontinuedWriter implements ProductDiscontinuedWriterInterface
{
    use TransactionTrait;

    /**
     * @var \Spryker\Zed\ProductDiscontinued\Persistence\ProductDiscontinuedEntityManagerInterface
     */
    protected $productDiscontinuedEntityManager;

    /**
     * @var \Spryker\Zed\ProductDiscontinued\ProductDiscontinuedConfig
     */
    protected $productDiscontinuedConfig;

    /**
     * @var \Spryker\Zed\ProductDiscontinued\Persistence\ProductDiscontinuedRepositoryInterface
     */
    protected $productDiscontinuedRepository;

    /**
     * @var array|\Spryker\Zed\ProductDiscontinuedExtension\Dependency\Plugin\PostProductDiscontinuePluginInterface[]
     */
    protected $postCreateProductDiscontinuePlugins;

    /**
     * @param \Spryker\Zed\ProductDiscontinued\Persistence\ProductDiscontinuedEntityManagerInterface $productDiscontinuedEntityManager
     * @param \Spryker\Zed\ProductDiscontinued\Persistence\ProductDiscontinuedRepositoryInterface $productDiscontinuedRepository
     * @param \Spryker\Zed\ProductDiscontinuedExtension\Dependency\Plugin\PostProductDiscontinuePluginInterface[] $postCreateProductDiscontinuePlugins
     * @param \Spryker\Zed\ProductDiscontinued\ProductDiscontinuedConfig $productDiscontinuedConfig
     */
    public function __construct(
        ProductDiscontinuedEntityManagerInterface $productDiscontinuedEntityManager,
        ProductDiscontinuedRepositoryInterface $productDiscontinuedRepository,
        array $postCreateProductDiscontinuePlugins,
        ProductDiscontinuedConfig $productDiscontinuedConfig
    ) {
        $this->productDiscontinuedEntityManager = $productDiscontinuedEntityManager;
        $this->productDiscontinuedConfig = $productDiscontinuedConfig;
        $this->productDiscontinuedRepository = $productDiscontinuedRepository;
        $this->postCreateProductDiscontinuePlugins = $postCreateProductDiscontinuePlugins;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductDiscontinuedRequestTransfer $productDiscontinuedRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ProductDiscontinuedResponseTransfer
     */
    public function create(ProductDiscontinuedRequestTransfer $productDiscontinuedRequestTransfer): ProductDiscontinuedResponseTransfer
    {
        $productDiscontinuedTransfer = (new ProductDiscontinuedTransfer())
            ->setFkProduct($productDiscontinuedRequestTransfer->getIdProduct());
        if ($this->productDiscontinuedRepository->findProductDiscontinuedByProductId($productDiscontinuedTransfer)) {
            return (new ProductDiscontinuedResponseTransfer)->setIsSuccessful(false);
        }

        return $this->getTransactionHandler()->handleTransaction(function () use ($productDiscontinuedRequestTransfer) {
            return $this->executeCreateTransaction($productDiscontinuedRequestTransfer);
        });
    }

    /**
     * @param \Generated\Shared\Transfer\ProductDiscontinuedRequestTransfer $productDiscontinuedRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ProductDiscontinuedResponseTransfer
     */
    protected function executeCreateTransaction(
        ProductDiscontinuedRequestTransfer $productDiscontinuedRequestTransfer
    ): ProductDiscontinuedResponseTransfer {
        $productDiscontinuedTransfer = (new ProductDiscontinuedTransfer())
            ->setFkProduct($productDiscontinuedRequestTransfer->getIdProduct())
            ->setActiveUntil($this->getActiveUntilDate());

        $productDiscontinuedTransfer = $this->productDiscontinuedEntityManager
            ->saveProductDiscontinued($productDiscontinuedTransfer);
        $this->executePostCreatePlugins($productDiscontinuedTransfer);

        return (new ProductDiscontinuedResponseTransfer)
            ->setProductDiscontinued($productDiscontinuedTransfer)
            ->setIsSuccessful(true);
    }

    /**
     * @param \Generated\Shared\Transfer\ProductDiscontinuedTransfer $productDiscontinuedTransfer
     *
     * @return void
     */
    protected function executePostCreatePlugins(ProductDiscontinuedTransfer $productDiscontinuedTransfer): void
    {
        foreach ($this->postCreateProductDiscontinuePlugins as $postDeleteProductDiscontinuePlugin) {
            $postDeleteProductDiscontinuePlugin->execute($productDiscontinuedTransfer);
        }
    }

    /**
     * @return string
     */
    protected function getActiveUntilDate(): string
    {
        return date(
            'Y-m-d',
            strtotime(sprintf('+%s Days', $this->productDiscontinuedConfig->getDaysAmountBeforeProductDeactivate()))
        );
    }
}
