<?php
namespace Civi\Micro\OAuth;

interface Persistence {

    public function saveKey($publicKey, $privateKey);

    public function listKeys();

    public function getPrivateKey();
}