# About

A repo to get started with [openLDAP](https://en.wikipedia.org/wiki/OpenLDAP).

## LDAP concepts

[See this resource to get started with ldap main concepts.](doc/concepts.md)

## Docker

### Usage

    docker-compose up -d --build
        
### GUI

phpLDAPadmin is available, details below: 

* URL: http://localhost:8089/
* Login DN: `cn=admin,dc=mycompany,dc=com`
* Password: `admin`

## CLI

### Loading data using LDIF

    ldapadd -x -D "cn=admin,dc=mycompany,dc=com" -w admin -H ldap:// -f openldap/1_people.ldif 
    ldapadd -x -D "cn=admin,dc=mycompany,dc=com" -w admin -H ldap:// -f openldap/2_groups.ldif 
    ldapadd -x -D "cn=admin,dc=mycompany,dc=com" -w admin -H ldap:// -f openldap/3_affect_people2group.ldif 

### Queries

Options meaning from `man ldapsearch`:

* [-D binddn]
* [-w passwd]
* [-p ldapport]
* [-h ldaphost]
* [-b searchbase]  


#### Examples

    ldapsearch -D "cn=admin,dc=mycompany,dc=com" -w admin -p 389 -h localhost -b "ou=Paris, ou=France,ou=People,dc=mycompany,dc=com"
    
    ldapsearch -D "cn=admin,dc=mycompany,dc=com" -w admin -p 389 -h localhost -b "ou=People,dc=mycompany,dc=com" "uid=clermont-ferrand_001"
    
    ldapsearch -D "cn=admin,dc=mycompany,dc=com" -w admin -p 389 -h localhost -b "ou=France,ou=People,dc=mycompany,dc=com" "(objectclass=person)"
    
    ldapsearch -D "cn=admin,dc=mycompany,dc=com" -w admin -p 389 -h localhost -b "dc=mycompany,dc=com" "uid=clermont-ferrand_002" dn mail memberOf


## Docker image for development purpose

The `benit/openldap` image is available [at dockerhub](https://hub.docker.com/r/benit/openldap).

This image loads `OU` and users account in a ldap instance for development purpose. 
    
## Resources

### General concepts

* https://ldap.com/basic-ldap-concepts/
* https://ldapwiki.com/wiki/LDAP%20Query%20Basic%20Examples
* http://articles.mongueurs.net/magazines/linuxmag65.html

### Groups

* https://www.techrepublic.com/article/how-to-populate-an-ldap-server-with-users-and-groups-via-phpldapadmin/
* https://serverfault.com/a/275832/453315

###  `memberOf` overlay

[The OpenLDAP memberOf overlay automatically creates and removes attributes when attributes of other entries that refer to their DN are added and removed](https://tylersguides.com/guides/openldap-memberof-overlay/)

The `osixia/docker-openldap` has enabled the `memberOf` overlay thanks [to this file](https://github.com/osixia/docker-openldap/blob/master/image/service/slapd/assets/config/bootstrap/ldif/03-memberOf.ldif).

To use this cool feature:

* groups must be of class: `groupOfUniqueNames`
* `uniqueMember` attribute mus be use to add user using full `DN`


