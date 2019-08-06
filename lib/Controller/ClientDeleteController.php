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

use SimpleSAML\Modules\OpenIDConnect\Controller\Traits\GetClientFromRequestTrait;
use SimpleSAML\Modules\OpenIDConnect\Factories\TemplateFactory;
use SimpleSAML\Modules\OpenIDConnect\Repositories\ClientRepository;
use SimpleSAML\Modules\OpenIDConnect\Services\SessionMessagesService;
use SimpleSAML\Utils\HTTP;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\ServerRequest;

class ClientDeleteController
{
    use GetClientFromRequestTrait;

    /**
     * @var TemplateFactory
     */
    private $templateFactory;
    /**
     * @var SessionMessagesService
     */
    private $messages;

    public function __construct(
        ClientRepository $clientRepository,
        TemplateFactory $templateFactory,
        SessionMessagesService $messages
    ) {
        $this->clientRepository = $clientRepository;
        $this->templateFactory = $templateFactory;
        $this->messages = $messages;
    }

    public function __invoke(ServerRequest $request)
    {
        $client = $this->getClientFromRequest($request);
        $body = $request->getParsedBody();
        $clientSecret = $body['secret'] ?? null;

        if ('POST' === mb_strtoupper($request->getMethod())) {
            if (!$clientSecret) {
                throw new \SimpleSAML_Error_BadRequest('Client secret is missing.');
            }

            if ($clientSecret !== $client->getSecret()) {
                throw new \SimpleSAML_Error_BadRequest('Client secret is invalid.');
            }

            $this->clientRepository->delete($client);
            $this->messages->addMessage('{oidc:client:removed}');

            return new RedirectResponse(HTTP::addURLParameters('index.php', []));
        }

        return $this->templateFactory->render('oidc:clients/delete.twig', [
            'client' => $client,
        ]);
    }
}
