<?php

namespace Config;

use CodeIgniter\Config\BaseService;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. These are often used by the core classes to
 * do the heavy lifting they need to do. The Services class provides
 * a convenient way to get and set these classes.
 *
 * @see http://codeigniter4.github.io/CodeIgniter4/concepts/services.html
 */
class Services extends BaseService
{
    /*
     | -------------------------------------------------------------------
     | Factories
     | -------------------------------------------------------------------
     |
     | You can change the namespace of the ORM factory by setting the
     | $factoriesNamespace property. The factory method can then be
     | called with the short class name, or the full class name.
     |
     | $factoriesNamespace = 'App\Factories';
     | $db = Factories::database();
     |
     */

    /**
     * The permission service for handling access control
     */
    public static function permission($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('permission');
        }

        return new \App\Services\PermissionService();
    }
}
