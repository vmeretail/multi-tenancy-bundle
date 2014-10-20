MultiTenancyBundle
==================
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/a5e560c5-e5f5-46a0-ae5b-8f463e774f01/small.png)](https://insight.sensiolabs.com/projects/a5e560c5-e5f5-46a0-ae5b-8f463e774f01)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/tahoelimited/multi-tenancy-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/tahoelimited/multi-tenancy-bundle/?branch=master)

[![Code Coverage](https://scrutinizer-ci.com/g/tahoelimited/multi-tenancy-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/tahoelimited/multi-tenancy-bundle/?branch=master)

[![Build Status](https://scrutinizer-ci.com/g/tahoelimited/multi-tenancy-bundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/tahoelimited/multi-tenancy-bundle/build-status/master)

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
    tahoe_multi_tenancy.user.class: Tahoe\ExampleBundle\Entity\User
    tahoe_multi_tenancy.tenant.class: Tahoe\ExampleBundle\Entity\Tenant
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
            
tahoe_multi_tenancy:
    account_prefix: YOUR_ACCOUNT_PREFIX
    gateways:
        # for the moment, only recurly is supported
        recurly:
            subdomain: your-subdomain
            private_key: YOUR_PRIVATE_KEY
            plan_name: YOUR_PLAN_NAME
```

routing.yml

``` yml
tahoe_multi_tenancy:
    resource: "@TahoeMultiTenancyBundle/Resources/config/routing.yml"
    prefix:   /
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

### Update your existing user entity.

> Note: MultiTenancyBundle requires FOSUSERBundle.

``` php
<?php

namespace Tahoe\ExampleBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantUserInterface;

class User extends BaseUser implements MultiTenantUserInterface
{
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

### Ensure / is free
/ is used for redirecting to tenants, so you cannot have any routes setup with just /
