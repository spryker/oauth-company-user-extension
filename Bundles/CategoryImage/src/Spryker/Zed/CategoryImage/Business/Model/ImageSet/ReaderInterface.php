<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CategoryImage\Business\Model\ImageSet;

use Generated\Shared\Transfer\CategoryTransfer;

interface ReaderInterface
{
    /**
     * @param int $idCategory
     *
     * @return \Generated\Shared\Transfer\CategoryImageSetTransfer[]
     */
    public function getCategoryImagesSetCollectionByIdCategory(int $idCategory): array;

    /**
     * @param \Generated\Shared\Transfer\CategoryTransfer $categoryTransfer
     *
     * @return \Generated\Shared\Transfer\CategoryTransfer
     */
    public function expandCategoryWithImageSetCollection(CategoryTransfer $categoryTransfer): CategoryTransfer;
}
