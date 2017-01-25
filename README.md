# Users plugin for CakePHP

## Installation

En config/Bootstap.php

```
Plugin::load('Acciona/Users', ['bootstrap' => true, 'routes' => true]);
```


En AppController.php agregar

```
use Acciona\Users\Model\Table\UsersTable;
use App\Event\ProfileAuth;
use Cake\Event\Event;
use Cake\Event\EventManager;
```
y
```
public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Acciona/Users.AccionaAuth', [
            'authenticate' => 'Jwt'
        ]);

        // agregar el evento para cargue datos de profile
        $profileAuth = new ProfileAuth();
        EventManager::instance()->on(
            UsersTable::EVENT_BEFORE_AUTH, function ($event, $Users, $query) use ($profileAuth) {
                return $profileAuth->updateQuery($event, $Users, $query);
            }
        );

    }
```

### Migrations
Para crear todas las tablas necesarias para utilizar el plugin
```
 bin/cake migrations migrate -p Acciona/Users

```

### Cargar permisos
```
 bin/cake Acciona/Users.Permissions

```
