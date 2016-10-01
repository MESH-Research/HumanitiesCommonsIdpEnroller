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

During a Humanities Commons enrollment flow this plugin performs one of two functions
depending on whether or not the user chooses the Humanities Commons identity provider
(IdP) during IdP discovery.

If the user chooses the Humanities Commons IdP at discovery the discovery service
can invoke

```
/registry/humanities_commons_idp_enroller/humanities_commons_idp_enroller_accounts/provision
```

to display a form that collects the username and password that the user will use with
the Humanities Commons IdP. After submitting the form the username and password are
provisioned to the LDAP server and the flow continues. To continue the enrollment flow
the discovery service should invoke the URL above with the query string parameter
`target` and the value that the discovery page would have otherwise used after
an IdP is selected by the user, for example

```
?target=https%3A%2F%2Fregistry-dev.commons.mla.org%2FShibboleth.sso%2FLogin%3FSAMLDS%3D1%26target%3Dss%253Amem%253A58fd2928856cb1d50621cf34fa0614509f6e6e837dc0f3779fdc887a5f7cfa07%26entityID%3Dhttps%253A%252F%252Fhcommons-test.commons.mla.org%252Fidp%252Fshibboleth
```

Later during the flow after the user authenticates using her newly provisioned credentials
and the Humanities Commons IdP the plugin examines the authenticated identifier. 
If the authenticated identifier shows that the Humanities Commons IdP was used 
the plugin takes the username from the scoped eduPersonPrincipalName asserted by the 
IdP and attaches it to the CoPerson record as the WordPress identifier. It also
examines the petition to find the enrollee's name and email and updates the LDAP
record with them. The flow then continues.

If the user does **not** choose the Humanities Commons IdP at discovery the plugin
displays a form after authentication to allow the user to choose her WordPress
username or identifier. That identifier is attached to the CoPerson record and the
flow continues.

The functionality to examine the authenticated identifier and branch depending
on whether or not the user authenticated with the Humanities Commons IdP is
implemented in the `execute_plugin_collectIdentifier` method of the
`HumanitiesCommonsIdpEnrollerCoPetitionsController` and is invoked by the 
COmanage plugin mechanism.

The functionality to allow a user that chooses the Humanities Commons IdP
during discovery to provision a new username and password is implemented
as a "stand alone" controller.

The plugin also has a stand alone controller for configuration since normally
enrollment flow plugins do not implement direct configuration functionality
([they are non-instantiated](https://spaces.internet2.edu/display/COmanage/Writing+Registry+Plugins)).

--------

The plugin assumes:

(1) The identifier configured to be used as the WordPress username 
    is unique. TODO: Add fuctionality to enforce uniqueness.

(2) The enrollee name and email address are copied from the OrgIdentity
    to the CoPerson during the collection of enrollment attributes.

(3) The LDAP server has the pwmUser and inetOrgPerson objectClasses
    available.

(4) The LDAP server has the ppolicy overlay installed and configured so that
    the server hashes passwords before storing them. The passwords are sent
    to the server in plain text and must be hashed by the LDAP server.
