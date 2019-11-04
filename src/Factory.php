<?php declare(strict_types=1);

namespace App;

use Doctrine\DBAL;
use Psr\Http\Client\ClientInterface;
use RicardoFiorani\GuzzlePsr18Adapter\Client;

/**
 * @codeCoverageIgnore
 */
class Factory
{
    private ?DBAL\Connection $dbConnection = null;


    public function createHttpClient(int $timeout): ClientInterface
    {
        return new Client(['timeout' => $timeout]);
    }

    public function createDbConnection(string $dbName, string $dbUser, string $dbPassword, string $dbHost, string $dbDriver): DBAL\Connection
    {
        if ($this->dbConnection === null) {
            $this->dbConnection = DBAL\DriverManager::getConnection(
                [
                    'dbname' => $dbName,
                    'user' => $dbUser,
                    'password' => $dbPassword,
                    'host' => $dbHost,
                    'driver' => $dbDriver
                ]
            );
        }

        return $this->dbConnection;
    }
}