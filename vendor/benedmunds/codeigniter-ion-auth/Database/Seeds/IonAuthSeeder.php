<?php
namespace IonAuth\Database\Seeds;

/**
 * @package CodeIgniter-Ion-Auth
 */

class IonAuthSeeder extends \CodeIgniter\Database\Seeder
{
	/**
	 * Dumping data for table 'groups', 'users, 'users_groups'
	 *
	 * @return void
	 */
	public function run()
	{
		$config = config('IonAuth\\Config\\IonAuth');
		$this->DBGroup = empty($config->databaseGroupName) ? '' : $config->databaseGroupName;
		$tables        = $config->tables;

		$groups = [
			[
				'id'          => 1,
				'name'        => 'root',
				'description' => 'Root - Highest Level Access',
			],
			[
				'id'          => 2,
				'name'        => 'superadmin',
				'description' => 'Super Administrator',
			],
			[
				'id'          => 3,
				'name'        => 'manager',
				'description' => 'Manager',
			],
			[
				'id'          => 4,
				'name'        => 'supervisor',
				'description' => 'Supervisor',
			],
		];
		$this->db->table($tables['groups'])->insertBatch($groups);

		$users = [
			[
				'ip_address'              => '127.0.0.1',
				'username'                => 'root',
				'password'                => '$2y$10$gQoEoZYp8Rz2iK9m.c1nZOQ3mJy53.Bb89WoV4m9/RxUTRVpY2FGW',
				'email'                   => 'root@admin.com',
				'activation_code'         => '',
				'forgotten_password_code' => null,
				'created_on'              => '1268889823',
				'last_login'              => '1268889823',
				'active'                  => '1',
				'first_name'              => 'Root',
				'last_name'               => 'User',
				'company'                 => 'ADMIN',
				'phone'                   => '0',
				'tipe'                    => '1',
			],
			[
				'ip_address'              => '127.0.0.1',
				'username'                => 'superadmin',
				'password'                => '$2y$10$gQoEoZYp8Rz2iK9m.c1nZOQ3mJy53.Bb89WoV4m9/RxUTRVpY2FGW',
				'email'                   => 'superadmin@admin.com',
				'activation_code'         => '',
				'forgotten_password_code' => null,
				'created_on'              => '1268889823',
				'last_login'              => '1268889823',
				'active'                  => '1',
				'first_name'              => 'Super',
				'last_name'               => 'Admin',
				'company'                 => 'ADMIN',
				'phone'                   => '0',
				'tipe'                    => '1',
			],
			[
				'ip_address'              => '127.0.0.1',
				'username'                => 'manager',
				'password'                => '$2y$10$YpTvAzjvC5BEr1tdFOg3wOoZPgk90zfHHOoNOsG7f.J8qWWHVnkZe',
				'email'                   => 'manager@admin.com',
				'activation_code'         => '',
				'forgotten_password_code' => null,
				'created_on'              => '1268889823',
				'last_login'              => '1268889823',
				'active'                  => '1',
				'first_name'              => 'Manager',
				'last_name'               => 'User',
				'company'                 => 'ADMIN',
				'phone'                   => '0',
				'tipe'                    => '1',
			],
			[
				'ip_address'              => '127.0.0.1',
				'username'                => 'supervisor',
				'password'                => '$2y$10$YpTvAzjvC5BEr1tdFOg3wOoZPgk90zfHHOoNOsG7f.J8qWWHVnkZe',
				'email'                   => 'supervisor@admin.com',
				'activation_code'         => '',
				'forgotten_password_code' => null,
				'created_on'              => '1268889823',
				'last_login'              => '1268889823',
				'active'                  => '1',
				'first_name'              => 'Supervisor',
				'last_name'               => 'User',
				'company'                 => 'ADMIN',
				'phone'                   => '0',
				'tipe'                    => '1',
			],
		];
		$this->db->table($tables['users'])->insertBatch($users);

		$usersGroups = [
			[
				'user_id'  => '1',
				'group_id' => '1',
			],
			[
				'user_id'  => '2',
				'group_id' => '2',
			],
			[
				'user_id'  => '3',
				'group_id' => '3',
			],
			[
				'user_id'  => '4',
				'group_id' => '4',
			],
		];
		$this->db->table($tables['users_groups'])->insertBatch($usersGroups);
	}
}
