<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\ProductsRestApi\Processor\Mapper;

use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface;

interface AbstractProductsResourceMapperInterface
{
    /**
     * @param array $abstractProductData
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface
     */
    public function mapAbstractProductsResponseAttributesTransferToRestResponse(array $abstractProductData): RestResourceInterface;
}