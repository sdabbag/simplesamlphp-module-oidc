<?php

/*
 * This file is part of the simplesamlphp-module-oidc.
 *
 * Copyright (C) 2018 by the Spanish Research and Academic Network.
 *
 * This code was developed by Universidad de Córdoba (UCO https://www.uco.es)
 * for the RedIRIS SIR service (SIR: http://www.rediris.es/sir)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleSAML\Modules\OpenIDConnect\Controller;

use SimpleSAML\Module;
use SimpleSAML\Modules\OpenIDConnect\Factories\TemplateFactory;
use SimpleSAML\Modules\OpenIDConnect\Services\DatabaseLegacyOAuth2Import;
use SimpleSAML\Modules\OpenIDConnect\Services\DatabaseMigration;
use SimpleSAML\Modules\OpenIDConnect\Services\SessionMessagesService;
use SimpleSAML\Utils\HTTP;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\ServerRequest;

class OpenIdConnectInstallerController
{
    /**
     * @var TemplateFactory
     */
    private $templateFactory;
    /**
     * @var SessionMessagesService
     */
    private $messages;
    /**
     * @var DatabaseMigration
     */
    private $databaseMigration;
    /**
     * @var DatabaseLegacyOAuth2Import
     */
    private $databaseLegacyOAuth2Import;

    public function __construct(
        TemplateFactory $templateFactory,
        SessionMessagesService $messages,
        DatabaseMigration $databaseMigration,
        DatabaseLegacyOAuth2Import $databaseLegacyOAuth2Import
    ) {
        $this->templateFactory = $templateFactory;
        $this->messages = $messages;
        $this->databaseMigration = $databaseMigration;
        $this->databaseLegacyOAuth2Import = $databaseLegacyOAuth2Import;
    }

    public function __invoke(ServerRequest $request)
    {
        if ($this->databaseMigration->isUpdated()) {
            return new RedirectResponse(HTTP::addURLParameters('clients/', []));
        }

        $oauth2Enabled = \in_array('oauth2', Module::getModules(), true);

        $parsedBody = $request->getParsedBody();
        if ('POST' === $request->getMethod() && $parsedBody['migrate'] ?? false) {
            $this->databaseMigration->migrate();
            $this->messages->addMessage('{oidc:install:finished}');

            if ($parsedBody['oauth2_migrate'] ?? false) {
                $this->databaseLegacyOAuth2Import->import();
                $this->messages->addMessage('{oidc:import:finished}');
            }

            return new RedirectResponse(HTTP::addURLParameters('clients/', []));
        }

        return $this->templateFactory->render('oidc:install.twig', [
            'oauth2_enabled' => $oauth2Enabled,
        ]);
    }
}
