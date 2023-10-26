<?php

class Database{
    public function __construct(
            private string $host,
            private string $name,
            private string $user,
            private string $pass
    )
    {}

    public function getConnection(){
        $dsn = "mysql:host={$this->host};dbname={$this->name};charset=utf8";
        return new PDO($dsn,$this->user , $this->pass,
        [PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false]
        );
    }



}