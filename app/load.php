<?php

use App\LdapLoader;
use Faker\Factory;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\String\Slugger\AsciiSlugger;

require_once __DIR__ . '/vendor/autoload.php';

$loader = new LdapLoader(
    Ldap::create('ext_ldap', ['connection_string' => 'ldap://ldap:389']),
    'cn=admin,dc=mycompany,dc=com',
    'admin',
    new AsciiSlugger(),
    Factory::create('fr_FR'),
);

$loader->load(500);