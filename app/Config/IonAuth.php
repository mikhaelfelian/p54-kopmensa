<?php

namespace Config;

use IonAuth\Config\IonAuth as BaseIonAuth;

/**
 * IonAuth Configuration
 * 
 * This file overrides the default IonAuth configuration to use
 * the correct table names for this application.
 */
class IonAuth extends BaseIonAuth
{
    /**
     * Tables (Database table names)
     * Override to use the correct table names for this application
     *
     * @var array
     */
    public $tables = [
        'users'          => 'tbl_ion_users',
        'groups'         => 'tbl_ion_groups', 
        'users_groups'   => 'tbl_ion_users_groups',
        'login_attempts' => 'tbl_ion_login_attempts',
    ];

    /**
     * Users table column and Group table column you want to join WITH.
     * Joins from users.id
     * Joins from groups.id
     *
     * @var array
     */
    public $join = [
        'users'  => 'user_id',
        'groups' => 'group_id',
    ];

    /**
     * Site configuration
     */
    public $siteTitle                = 'Kopmensa POS';
    public $adminEmail               = 'admin@kopmensa.com';
    public $defaultGroup             = 'members';
    public $adminGroup               = 'admin';
    public $identity                 = 'username';
    public $minPasswordLength        = 6;
    public $emailActivation          = false;
    public $manualActivation         = false;
    public $rememberUsers            = true;
    public $userExpire               = 86400 * 2;
    public $userExtendonLogin        = false;
    public $trackLoginAttempts       = false;
    public $trackLoginIpAddress      = true;
    public $maximumLoginAttempts     = 6;
    public $lockoutTime              = 0;
    public $forgotPasswordExpiration = 1800;
    public $recheckTimer             = 0;

    /**
     * Cookie options
     */
    public $rememberCookieName = 'kopmensa_ingat';

    /**
     * Email options
     */
    public $useCiEmail  = false;
    public $emailConfig = [
        'mailType' => 'html',
    ];

    /**
     * Email templates
     */
    public $emailTemplates = 'IonAuth\\Views\\auth\\email\\';
    public $emailActivate = 'activate.tpl.php';
    public $emailForgotPassword = 'forgot_password.tpl.php';

    /**
     * Templates for errors and messages
     */
    public $templates = [
        'errors'   => [
            'list' => 'list',
        ],
        'messages' => [
            'list'   => 'IonAuth\\Views\\Messages\\list',
            'single' => 'IonAuth\\Views\\Messages\\single',
        ],
    ];
}
