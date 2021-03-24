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
    private string $bindDN;
    private string $bindPassword;
    private string $ldapOuPeopleDN;
    private string $password = '123';
    private array $towns = [
        'France' => [
            'Paris',
            'Marseille',
            'Lyon',
            'Toulouse',
            'Nice',
            'Nantes',
            'Strasbourg',
            'Montpellier',
            'Bordeaux',
            'Clermont-Ferrand',
            'Aurillac'
        ],
        'United States' => [
            'New York',
            'Los Angeles',
            'Chicago',
            'Houston',
            'Phoenix',
            'Philadelphia',
            'San Antonio',
            'San Diego',
        ],
        'Belgium' => [
            'Antwerp',
            'Ghent',
            'Charleroi',
            'Liège',
            'Brussels',
            'Schaerbeek',
            'Anderlecht',
            'Bruges',
            'Namur',
            'Leuven',
        ],
    ];
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

        foreach ($this->towns as $country => $townsArray) {
            $entry = new Entry($this->getCountryDn($country), [
                'objectClass' => ['organizationalUnit'],
                'ou' => ['People'],
            ]);
            $this->entryManager->add($entry);

            foreach ($townsArray as $town) {

                $entry = new Entry($this->getTownDn($country, $town), [
                    'objectClass' => ['organizationalUnit'],
                    'ou' => ['People'],
                ]);
                $this->entryManager->add($entry);
                for ($i = 1; $i <= $quantity; $i++) {
                    $padIndex = str_pad("$i", 3, '0', STR_PAD_LEFT);
                    $uid = sprintf('%s_%s', $this->slugger->slug(strtolower($town)), $padIndex);
                    echo sprintf('creating uid=%s', $uid) . PHP_EOL;
                    $firstName = strtolower($this->slugger->slug($this->faker->firstName));
                    $lastName = strtolower($this->slugger->slug($this->faker->lastName));
                    $email = sprintf('%s.%s@mail.com', $firstName, $lastName);#todo mail with dc
                    $dn = sprintf('uid=%s,%s', $uid, $this->getTownDn($country, $town));
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
        }
    }

    private function purge()
    {
        foreach ($this->towns as $country => $townsArray) {

            foreach ($townsArray as $town) {
                try {
                    $query = $this->ldap->query($this->getTownDn($country, $town), '(&(objectclass=inetOrgPerson))');
                    $results = $query->execute();
                    foreach ($results as $entry) {
                        echo sprintf('removing entry %s',$entry->getDn().PHP_EOL);
                        $this->entryManager->remove($entry);
                    }
                    $this->entryManager->remove(new Entry($this->getTownDn($country, $town), [
                        'objectClass' => ['organizationalUnit'],
                        'ou' => ['People'],
                    ]));
                } catch (\Exception $exception) {
                    echo $exception->getMessage();
                }
            }
            $this->entryManager->remove(new Entry($this->getCountryDn($country), [
                'objectClass' => ['organizationalUnit'],
                'ou' => ['People'],
            ]));
        }
        $this->entryManager->remove(new Entry($this->ldapOuPeopleDN, [
            'objectClass' => ['organizationalUnit'],
            'ou' => ['People'],
        ]));
    }

    private function getTownDn(string $country, string $town): string
    {
        return sprintf('ou=%s,ou=%s,%s', $town, $country, $this->ldapOuPeopleDN);
    }

    private function getCountryDn(string $country): string
    {
        return sprintf('ou=%s,%s', $country, $this->ldapOuPeopleDN);
    }

}