<?php

namespace App;

use Faker\Factory;
use Faker\Generator as GeneratorAlias;
use Symfony\Component\Ldap\Adapter\EntryManagerInterface;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;

class LdapLoader
{
    private string $ldapHost;
    private string $bindDN;
    private string $bindPassword;
    private string $ldapOuPeopleDN;
    private string $password = '123';
    private array $towns = ['Clermont-Ferrand', 'Paris', 'New-York', 'Aurillac', 'San Francisco'];
    private SluggerInterface $slugger;
    private Ldap $ldap;
    private EntryManagerInterface $entryManager;
    private GeneratorAlias $faker;


    public function __construct(Ldap $ldap, string $bindDN, string $bindPassword, SluggerInterface $slugger, GeneratorAlias $faker)
    {
        $this->bindDN = $bindDN;
        $this->bindPassword = $bindPassword;
        $this->ldap = $ldap;
        $this->ldap->bind($bindDN, $bindPassword);
        $this->entryManager = $this->ldap->getEntryManager();
        $this->ldapOuPeopleDN = 'ou=People,dc=mycompany,dc=com';
        $this->slugger = $slugger;
        $this->faker = $faker;
    }

    public function load(int $quantity)
    {
        try {
            $this->purge();
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

        $ldapOuPeople = new Entry($this->ldapOuPeopleDN, [
            'objectClass' => ['organizationalUnit'],
            'ou' => ['People'],
        ]);
        $this->entryManager->add($ldapOuPeople);

        foreach ($this->towns as $town) {
            echo $this->getTownDn($town);
            $entry = new Entry($this->getTownDn($town), [
                'objectClass' => ['organizationalUnit'],
                'ou' => ['People'],
            ]);
            $this->entryManager->add($entry);
        }

        for ($i = 1; $i <= $quantity; $i++) {
            $padIndex = str_pad("$i", 3, '0', STR_PAD_LEFT);
            $uid = sprintf('%s_%s', 'user', $padIndex);
            echo sprintf('creating uid=%s', $uid) . PHP_EOL;
            $firstName = strtolower($this->slugger->slug($this->faker->firstName));
            $lastName = strtolower($this->slugger->slug($this->faker->lastName));
            $email = sprintf('%s.%s@mail.com', $firstName, $lastName);
            $dn = sprintf('uid=%s,%s', $uid, $this->getRandomTownDn());
            $entry = new Entry($dn, [
                'objectClass' => [
                    'inetOrgPerson',
                ],
                'sn' => [$firstName],
                'uid' => [$uid],
                'cn' => [sprintf('%s %s', $firstName, $lastName)],
                'mail' => [$email],
                'userPassword' => $this->password,
            ]);
            $this->entryManager->add($entry);
        }
    }

    private function purge()
    {
        try {
            foreach ($this->towns as $town) {
                $query = $this->ldap->query($this->getTownDn($town), '(&(objectclass=inetOrgPerson))');
                $results = $query->execute();
                foreach ($results as $entry) {
                    $this->entryManager->remove($entry);
                }
                $this->entryManager->remove(new Entry($this->getTownDn($town), [
                    'objectClass' => ['organizationalUnit'],
                    'ou' => ['People'],
                ]));
            }
            $this->entryManager->remove(new Entry($this->ldapOuPeopleDN, [
                'objectClass' => ['organizationalUnit'],
                'ou' => ['People'],
            ]));
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

    private function getTownDn(string $town): string
    {
        return sprintf('%s,%s', 'ou=' . $town, $this->ldapOuPeopleDN);
    }

    private function getRandomTownDn(): string
    {
        $town = $this->towns[array_rand($this->towns)];
        return sprintf('%s,%s', 'ou=' . $town, $this->ldapOuPeopleDN);
    }
}