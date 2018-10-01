<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyUserGui\Communication\Controller;

use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\CompanyUserGui\Communication\CompanyUserGuiCommunicationFactory getFactory()
 */
class EditCompanyUserController extends AbstractController
{
    protected const PARAM_ID_COMPANY_USER = 'id-company-user';
    protected const PARAM_REDIRECT_URL = 'redirect-url';
    /**
     * @see \Spryker\Zed\CompanyUserGui\Communication\Controller\ListCompanyUserController::indexAction()
     */
    protected const URL_USER_LIST = '/company-user-gui/list-company-user';

    protected const MESSAGE_SUCCESS_COMPANY_USER_UPDATE = 'Company User "%s" has been updated.';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction(Request $request)
    {
        $idCompanyUser = $this->castId($request->query->get(static::PARAM_ID_COMPANY_USER));

        $dataProvider = $this->getFactory()->createCompanyUserFormDataProvider();
        $companyUserForm = $this->getFactory()
            ->getCompanyUserEditForm(
                $dataProvider->getData($idCompanyUser),
                $dataProvider->getOptions()
            )
            ->handleRequest($request);

        if ($companyUserForm->isSubmitted() && $companyUserForm->isValid()) {
            return $this->updateCompanyUser(
                $companyUserForm,
                $request->query->get(static::PARAM_REDIRECT_URL, static::URL_USER_LIST)
            );
        }

        return $this->viewResponse([
            'form' => $companyUserForm->createView(),
            'idCompany' => $idCompanyUser,
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $companyUserForm
     * @param string $redirectUrl
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function updateCompanyUser(FormInterface $companyUserForm, string $redirectUrl)
    {
        /** @var \Generated\Shared\Transfer\CompanyUserTransfer $companyUserTransfer */
        $companyUserTransfer = $companyUserForm->getData();
        $companyResponseTransfer = $this->getFactory()
            ->getCompanyUserFacade()
            ->update($companyUserTransfer);

        if (!$companyResponseTransfer->getIsSuccessful()) {
            foreach ($companyResponseTransfer->getMessages() as $message) {
                $this->addErrorMessage($message->getText());
            }

            return $this->viewResponse([
                'form' => $companyUserForm->createView(),
                'idCompanyUser' => $companyUserTransfer->getIdCompanyUser(),
            ]);
        }

        foreach ($companyResponseTransfer->getMessages() as $message) {
            $this->addSuccessMessage($message->getText());
        }

        $this->addSuccessMessage(sprintf(
            static::MESSAGE_SUCCESS_COMPANY_USER_UPDATE,
            $companyUserTransfer->getCustomer()->getFirstName() . ' ' . $companyUserTransfer->getCustomer()->getLastName()
        ));

        return $this->redirectResponse($redirectUrl);
    }
}
