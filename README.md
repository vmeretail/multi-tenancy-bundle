MultiTenancyBundle
==================

## Installation

### Download MultiTenancyBundle using composer

Add MultiTenancy by running the command:

``` bash
$ php composer.phar require "tahoelimited/multi-tenancy-bundle": "dev-master"
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
    tahoe_multi_tenancy.tenant.class: Tahoe\JobcostifyBundle\Entity\Tenant
    domain: yourdomain.com
```

config.yml

``` yml
doctrine:
    orm:
        resolve_target_entities:
            Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantUserInterface: %tahoe_multi_tenancy.user.class%
            Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantTenantInterface: %tahoe_multi_tenancy.tenant.class%
        entity_managers:
            default:
                filters:
                    tenantAware:
                        class: Tahoe\Bundle\MultiTenancyBundle\Query\Filter\SQLFilter\TenantAwareFilter
                        enabled: true

fos_user:
    registration:
        form:
            type: tahoe_multitenancy_user_registration
```

### Create your own tenant entity

You must create Tenant entity inside your bundle that extends one provided with the bundle. For example, something like this:

``` php
<?php

namespace Tahoe\ExampleBundle\Entity;

use Tahoe\Bundle\MultiTenancyBundle\Entity\Tenant as BaseTenant;
use Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantTenantInterface;

class Tenant extends BaseTenant implements MultiTenantTenantInterface
{
    // your custom properties and methods
}


```


``` yml

# file is extending base tenant from multi tenancy bundle
Tahoe\ExampleBundle\Entity\Tenant:
    type: entity
    table: th_ex_tenant
    fields:
        # your custom fields

```

### Update your existing user entity. Note the Multi Tenancy Bundle requires FOSUSER Bundle.

``` php
<?php

namespace Tahoe\ExampleBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Tahoe\Bundle\MultiTenancyBundle\Model\TenantAwareInterface;
use Tahoe\Bundle\MultiTenancyBundle\Model\TenantTrait;

class User extends BaseUser implements TenantAwareInterface
{
    use TenantTrait;
    protected $id;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}
```

``` yml
Tahoe\ExampleBundle\Entity\User:
  type:  entity
  table: th_ex_user
  repositoryClass: Tahoe\ExampleBundle\Repository\UserRepository
  id:
      id:
          type: integer
          generator:
              strategy: AUTO
  manyToOne:
      tenant:
          targetEntity: Tenant
          cascade: ["all"]
```



### Making other entities tenant aware
All entities that are specific to the tenant should have the following applied. Any entities that are applicable to all tenants should be left alone.

``` php
<?php

namespace Tahoe\ExampleBundle\Entity;

use Tahoe\Bundle\MultiTenancyBundle\Model\TenantAwareInterface;
use Tahoe\Bundle\MultiTenancyBundle\Model\TenantTrait;

class Customer implements TenantAwareInterface
{
    use TenantTrait;
}


```


``` yml
Tahoe\ExampleBundle\Entity\Customer:
    type: entity
    table: th_ex_customer
    fields:
        id:
            type: integer
            id: true
            generator:
                strategy: AUTO
        name:
            type: string
    manyToOne:
        tenant:
            targetEntity: Tenant

```
