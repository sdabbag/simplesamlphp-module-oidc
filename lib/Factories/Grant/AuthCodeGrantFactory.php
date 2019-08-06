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

namespace SimpleSAML\Modules\OpenIDConnect\Factories\Grant;

use League\OAuth2\Server\Grant\AuthCodeGrant;
use SimpleSAML\Modules\OpenIDConnect\Repositories\AuthCodeRepository;
use SimpleSAML\Modules\OpenIDConnect\Repositories\RefreshTokenRepository;

class AuthCodeGrantFactory
{
    /**
     * @var AuthCodeRepository
     */
    private $authCodeRepository;
    /**
     * @var RefreshTokenRepository
     */
    private $refreshTokenRepository;
    /**
     * @var \DateInterval
     */
    private $refreshTokenDuration;
    /**
     * @var \DateInterval
     */
    private $authCodeDuration;
    /**
     * @var bool
     */
    private $enablePKCE;

    public function __construct(
        AuthCodeRepository $authCodeRepository,
        RefreshTokenRepository $refreshTokenRepository,
        \DateInterval $refreshTokenDuration,
        \DateInterval $authCodeDuration,
        bool $enablePKCE
    ) {
        $this->authCodeRepository = $authCodeRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->refreshTokenDuration = $refreshTokenDuration;
        $this->authCodeDuration = $authCodeDuration;
        $this->enablePKCE = $enablePKCE;
    }

    /**
     * @return AuthCodeGrant
     */
    public function build()
    {
        $authCodeGrant = new AuthCodeGrant(
            $this->authCodeRepository,
            $this->refreshTokenRepository,
            $this->authCodeDuration
        );
        $authCodeGrant->setRefreshTokenTTL($this->refreshTokenDuration);

        if ($this->enablePKCE) {
            $authCodeGrant->enableCodeExchangeProof();
        }

        return $authCodeGrant;
    }
}
