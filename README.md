MultiTenancyBundle
==================

## Installation

### Download MultiTenancyBundle using composer

Add MultiTenancy by running the command:

``` bash
$ php composer.phar require "tahoelimited/multitenant": "dev-master"
```

Composer will install the bundle to your project's `vendor/tahoelimited` directory.

### Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Tahoe\Bundle\MultiTenancyBundle\TahoeMultiTenancyBundle(),
    );
}
```
### Configure the bundle

Add the following settings to your ```config.yml```, you must preserve existing values, don't just overwrite an entire ```doctrine```

parameters.yml

``` yml
parameters:
    tahoe_multi_tenancy.user.class: Tahoe\JobcostifyBundle\Entity\User
    tahoe_multi_tenancy.organization.class: Tahoe\JobcostifyBundle\Entity\Organization
    domain: yourdomain.com
```

config.yml

``` yml
doctrine:
    orm:
        resolve_target_entities:
            Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantUserInterface: %tahoe_multi_tenancy.user.class%
            Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantOrganizationInterface: %tahoe_multi_tenancy.organization.class%
        entity_managers:
            default:
                filters:
                    organizationAware:
                        class: Tahoe\Bundle\MultiTenancyBundle\Query\Filter\SQLFilter\OrganizationAwareFilter
                        enabled: true
```

### Create your own organization entity

You must create Organization entity inside your bundle that extends one provided with the bundle


``` php
<?php

namespace Tahoe\JobcostifyBundle\Entity;

use Tahoe\Bundle\MultiTenancyBundle\Entity\Organization as BaseOrganization;
use Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantOrganizationInterface;

class Organization extends BaseOrganization implements MultiTenantOrganizationInterface
{
    // your custom properties and methods
}


```


``` yml

# file is extending base organization from multi tenancy bundle
Tahoe\JobcostifyBundle\Entity\Organization:
    type: entity
    table: th_jc_organization
    fields:
        # your custom fields

```


### Making entity organization aware

``` php
<?php

namespace Tahoe\JobcostifyBundle\Entity;

use Tahoe\Bundle\MultiTenancyBundle\Model\OrganizationAwareInterface;
use Tahoe\Bundle\MultiTenancyBundle\Model\OrganizationTrait;

class Customer implements OrganizationAwareInterface
{
    use OrganizationTrait;
}


```


``` yml
Tahoe\JobcostifyBundle\Entity\Customer:
    type: entity
    table: th_jc_customer
    fields:
        id:
            type: integer
            id: true
            generator:
                strategy: AUTO
        name:
            type: string
    manyToOne:
        organization:
            targetEntity: Organization

```
