# About

A repo to get started with [openLDAP](https://en.wikipedia.org/wiki/OpenLDAP).

## LDAP concepts

[See this resource to get started with ldap main concepts.](doc/concepts.md)

## Docker

### Usage

    docker-compose build

    docker-compose up -d
    
#### Load data fixtures

    docker exec openldap-101_php_1 composer install
    docker exec openldap-101_php_1 php load.php
    
### GUI

phpLDAPadmin is available, details below: 

* URL: http://localhost:8089/
* Login DN: `cn=admin,dc=mycompany,dc=com`
* Password: `admin`

## CLI

### Loading using a LDIF file

    ldapadd -x -D "cn=admin,dc=mycompany,dc=com" -w admin -H ldap:// -f openldap/data-500-users.ldif
        
### Queries

Options meaning from `man ldapsearch`:

* [-D binddn]
* [-w passwd]
* [-p ldapport]
* [-h ldaphost]
* [-b searchbase]  


#### Examples

    ldapsearch -D "cn=admin,dc=mycompany,dc=com" -w admin -p 389 -h localhost -b "ou=Paris,ou=People,dc=mycompany,dc=com"
    
    ldapsearch -D "cn=admin,dc=mycompany,dc=com" -w admin -p 389 -h localhost -b "ou=People,dc=mycompany,dc=com" "uid=user_001"
    
    ldapsearch -D "cn=admin,dc=mycompany,dc=com" -w admin -p 389 -h localhost -b "ou=People,dc=mycompany,dc=com" "(objectclass=person)"


## Docker image for development purpose

The `benit/openldap` image is available [at dockerhub](https://hub.docker.com/r/benit/openldap).

This image loads `OU` and users account in a ldap instance for development purpose. 
    
## Resources
    
* https://ldap.com/basic-ldap-concepts/
* https://ldapwiki.com/wiki/LDAP%20Query%20Basic%20Examples
* http://articles.mongueurs.net/magazines/linuxmag65.html