<?php

namespace Rubol\DB;

class DB
{
    private $capsule;

    public function __construct($capsule) {
        $this->capsule = $capsule;
    }

    public function getQueryLog() {
        $queries = $this->capsule->getConnection()->getQueryLog();

        echo '<strong>Total queries: ' . count($queries) . '</strong><hr/>';

        foreach ($queries as $query) {
            echo $query['query']. '<br>';
        }
    }
}