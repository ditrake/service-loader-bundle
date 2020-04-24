# Service loader bundle
Предоставляет мини сервис для получения сервиса из контейнера, в том случае, когда за ранее известна лишь часть алиаса.

# Install
```bash
composer require srr/service-loader-bundle
``` 
# Usage
```php
namespace App\Controller;

use srr\ServiceLoader\Service\Loader\ServiceLoaderInterface;

class Foo
{
    private $loader;

    public function __construct(ServiceLoaderInterface $loader) 
    {
        $this->loader = $loader;
    }
    
    public function doSomthing()
    {
        // You may get service ID
        $serviceId = $this->loader->getServiceId(['myService']);
        // or instance of your service
        $service = $this->loader->loadService(['myService'], MyServiceInterface::class);
        // or
        $service = $this->loader->loadService(['v1', 'one'], MyServiceInterface::class);
    }
}
```

# Configure

Для корректного использования нужно определить массив `srr_service_loader` с перечнем алиасов:

```yaml
# config/service.yaml
parameters:
    srr_service_loader:
      tdp:
        - 'one'
        - 'two'
        - 'three'
      adp:
        one:
          - 'v1'
          - 'v2'
          - 'v3'
        two:
          - 'v1'
          - 'v2'
          - 'v3'

services:
    tdp.one:
      class: App\Service\MyServiceOne
    # ...
    adp.one.v1:
      class: App\Service\MyServiceTwo
```

Поддерживается построение алиасов с разделителем через точку. Вложенность не ограничена. Если на пути должны быть уточнения, то их стоит передать в массиве. 