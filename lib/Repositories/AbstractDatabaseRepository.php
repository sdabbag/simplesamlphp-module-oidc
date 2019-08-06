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

namespace SimpleSAML\Modules\OpenIDConnect\Repositories;

use SimpleSAML\Database;
use SimpleSAML\Modules\OpenIDConnect\Services\ConfigurationService;

abstract class AbstractDatabaseRepository
{
    /**
     * @var \SimpleSAML_Configuration
     */
    protected $config;
    /**
     * @var Database
     */
    protected $database;
    /**
     * @var ConfigurationService|null
     */
    protected $configurationService;

    /**
     * ClientRepository constructor.
     */
    public function __construct(ConfigurationService $configurationService = null)
    {
        $this->config = \SimpleSAML_Configuration::getOptionalConfig('module_oidc.php');
        $this->database = Database::getInstance();
        $this->configurationService = $configurationService;
    }

    abstract public function getTableName();
}
