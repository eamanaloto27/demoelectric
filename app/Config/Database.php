<?php

namespace Config;

use CodeIgniter\Database\Config;

class Database extends Config
{
    public string $defaultGroup = 'default';
    
    public array $default = [
        'DSN'      => '',
        'hostname' => '127.0.0.1',
        'username' => '',
        'password' => '',
        'database' => '',
        'DBDriver' => 'Postgre',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug'  => false,
        'charset'  => 'utf8',
        'DBCollat' => '',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 5432,
        'schema'   => 'public',
        'sslmode'  => 'prefer',
    ];

    public array $tests = [
        'DSN'      => '',
        'hostname' => '127.0.0.1',
        'username' => '',
        'password' => '',
        'database' => ':memory:',
        'DBDriver' => 'SQLite3',
        'DBPrefix' => 'db_',
        'pConnect' => false,
        'DBDebug'  => true,
        'charset'  => 'utf8',
        'DBCollat' => '',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 5432,
    ];

    public function __construct()
    {
        parent::__construct();

        $url = getenv('DATABASE_URL') ?: '';

        if ($url !== '') {
            $parts = parse_url($url);

            if ($parts !== false) {
                $this->default['hostname'] = $parts['host'] ?? $this->default['hostname'];
                $this->default['port']     = isset($parts['port']) ? (int) $parts['port'] : 5432;
                $this->default['username'] = isset($parts['user']) ? rawurldecode($parts['user']) : '';
                $this->default['password'] = isset($parts['pass']) ? rawurldecode($parts['pass']) : '';
                $this->default['database'] = isset($parts['path']) ? ltrim($parts['path'], '/') : '';

                $query = [];
                if (! empty($parts['query'])) {
                    parse_str($parts['query'], $query);
                }

                if (isset($query['sslmode'])) {
                    $this->default['sslmode'] = $query['sslmode'];
                }
            }
        }
    }
}
