## LDAP concepts

The section below has been fully copied / pasted from [this great resource](https://ldap.com/basic-ldap-concepts/).

### DIT

Directory Information Tree. 

### Directory Servers

A directory server (more technically referred to as a Directory Server Agent, a Directory System Agent, or a DSA) is a type of network database that stores information represented as trees of entries. This is different from a relational database, which uses tables comprised of rows and columns, so directory servers may be considered a type of NoSQL database 

### Entries

An LDAP entry is a collection of information about an entity. Each entry consists of three primary components: a distinguished name, a collection of attributes, and a collection of object classes. Each of these is described in more detail below. 

### DN: Distinguished Name

A distinguished name (usually just shortened to “DN”) uniquely identifies an entry and describes its position in the DIT. 

A DN is much like an absolute path on a filesystem, except whereas filesystem paths usually start with the root of the filesystem and descend the tree from left to right, LDAP DNs ascend the tree from left to right. 

For example, the DN “uid=john.doe,ou=People,dc=example,dc=com” represents an entry that is immediately subordinate to “ou=People,dc=example,dc=com” which is itself immediately subordinate to the entry “dc=example,dc=com”.

DNs are comprised of zero or more comma-separated components called relative distinguished names, or RDNs. For example, the DN “uid=john.doe,ou=People,dc=example,dc=com” has four RDNs:

    uid=john.doe
    ou=People
    dc=example
    dc=com

### Object Classes

Object classes are schema elements that specify collections of attribute types that may be related to a particular type of object, process, or other entity. Every entry has a structural object class, which indicates what kind of object an entry represents (e.g., whether it is information about a person, a group, a device, a service, etc.), and may also have zero or more auxiliary object classes that suggest additional characteristics for that entry.

### Search Filters 

[see dedicated resource](https://ldap.com/ldap-filters/)

#### AND Filters

he string representation of an AND filter is constructed by starting with the string “(&” followed by the string representations of all of the encapsulated filters concatenated together, and then the string “)” at the end. 

For example, the filter “(&(givenName=John)(sn=Doe))” will only match entries that contain both a givenName attribute with a value of John and an sn attribute with a value of Doe.

#### OR Filters 

he string representation of an OR filter is constructed by starting with the string “(|” followed by the string representations of all of the encapsulated filters concatenated together, and then the string “)” at the end. 

For example, the filter “(|(givenName=John)(givenName=Jon)(givenName=Johnathan)(givenName=Jonathan))” will match any entry that has one or more of the givenName values contained in that filter. 

#### NOT Filters 

The string representation of a NOT filter is constructed by starting with “(!” followed by the string representation of the encapsulated filter, and then the string “)” at the end. 

For example, the filter “(!(givenName=John))” will match any entry that does not match “(givenName=John)”, which is to say any entry that does not include a givenName attribute at all, or any entry that includes a givenName attribute that does not have a value of John.

###  LDIF: See LDAP Data Interchange Format.  

The LDAP data interchange format (LDIF) is a standard way of representing LDAP entries and change records in text format. LDIF may be used for purposes like backup and restore, representing entries retrieved from a server, and representing changes to apply to a server. 

### Scope

The scope of a search operation is used to identify the portion of the subtree (as specified by the base DN for the search request) that will be allowed to contain matching entries. Search scope values include baseObject (only consider the entry specified by the base DN), singleLevel (only consider entries immediately below the entry specified by the base DN), wholeSubtree (consider the entry specified by the base DN and all of its subordinates, to any depth), and subordinateSubtree (consider all subordinates of the entry specified by the base DN, to any depth, but not the base entry itself).
