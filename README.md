COmanage Registry MLA HumanitiesCommonsIdPEnroller

Copyright (C) 2016 Modern Language Association

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file
except in compliance with the License. You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under
the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
KIND, either express or implied. See the License for the specific language governing
permissions and limitations under the License.

--------

This plugin must be configured by choosing "HC Idp Enroller Configuration" from
the Platform menu.

--------

The plugin assumes:

(1) The identifier configured to be used as the username for the account
    is unique and that uniqueness is enforced by other parts of the
    enrollment process.

(2) The enrollee name and email address are copied from the OrgIdentity
    to the CoPerson during the collection of enrollment attributes.

(3) The LDAP server has the pwmUser and inetOrgPerson objectClasses
    available.

(4) The LDAP server has the ppolicy overlay installed and configured so that
    the server hashes passwords before storing them. The passwords are sent
    to the server in plain text and must be hashed by the LDAP server.

--------

The core Registry enrollment plugin mechanism will invoke the first part of
the plugin functionality immediately after the user submits the form
with name, email, and identifier.

The second part of the plugin functionality must be invoked by directing
the browser to 

```
/registry/humanities_commons_idp_enroller/humanities_commons_idp_enroller_accounts/provision
```
as part of IdP discovery. The request MUST include the query parameter 'target' with
value the URL-encoded location to which the plugin will direct the browser after account
provisioning. That value is the same value that the discovery service would normally use
to direct the browser directly and should include the entityID for the Humanities Commons
IdP since the account is provisioned into that IdP. An example is

```
?target=https%3A%2F%2Fregistry-dev.commons.mla.org%2FShibboleth.sso%2FLogin%3FSAMLDS%3D1%26target%3Dss%253Amem%253A58fd2928856cb1d50621cf34fa0614509f6e6e837dc0f3779fdc887a5f7cfa07%26entityID%3Dhttps%253A%252F%252Fhcommons-test.commons.mla.org%252Fidp%252Fshibboleth
```
