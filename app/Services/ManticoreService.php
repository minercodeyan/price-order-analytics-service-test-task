<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ManticoreService
{
    protected $connection;

    public function __construct()
    {
        $this->connection = new \mysqli(
            env('MANTICORE_HOST', 'manticore'),
            '',     // user пустой
            '',     // password пустой
            '',
            9306
        );
    }

    public function searchOrders(string $query): array
    {
        $q = $this->connection->real_escape_string($query);

        $sql = "
            SELECT id, number, email, client_name, client_surname
            FROM orders
            WHERE MATCH('$q')
            LIMIT 20
        ";

        $result = $this->connection->query($sql);

        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }
}
