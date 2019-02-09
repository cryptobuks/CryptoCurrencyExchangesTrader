# Create own provider
Just extend BaseProvider and implement ProviderInterface, describe provider endpoint with constants
> Constant needs because base class provide some nice output via Reflection API which used by several commands e.g providers:search 

```php
<?php

class MyOwnProvider extends BaseProvider implements ProviderInterface
{
    public const API_ENDPOINT   = '...';
    public const PROVIDER_URI   = '...';
    public const PROVIDER_DOC   = '...';
    public const PROVIDER_FEES  = '...';
}
```

Actually that's all, all lines below should contains specific logic to interact wih provider api.