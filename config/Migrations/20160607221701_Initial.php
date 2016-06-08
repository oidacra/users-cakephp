<?php
use Migrations\AbstractMigration;

class Initial extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('permissions_actions');
        $table
            ->addColumn('action', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('permission_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addIndex(
                [
                    'permission_id',
                ]
            )
            ->create();

        $table = $this->table('permissions');
        $table
            ->addColumn('entity', 'string', [
                'default' => null,
                'limit' => 200,
                'null' => false,
            ])
            ->addColumn('domain', 'string', [
                'default' => '',
                'limit' => 100,
                'null' => false,
            ])
            ->create();

        $table = $this->table('roles_permissions');
        $table
            ->addColumn('actions', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('rol_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('permission_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addIndex(
                [
                    'rol_id',
                ]
            )
            ->addIndex(
                [
                    'permission_id',
                ]
            )
            ->create();

        $table = $this->table('roles');
        $table
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 110,
                'null' => false,
            ])
            ->create();

        $table = $this->table('users_roles');
        $table
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('rol_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->addIndex(
                [
                    'rol_id',
                ]
            )
            ->create();

        $table = $this->table('users');
        $table
            ->addColumn('email', 'string', [
                'default' => null,
                'limit' => 75,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 90,
                'null' => true,
            ])
            ->addColumn('last_name', 'string', [
                'default' => null,
                'limit' => 90,
                'null' => true,
            ])
            ->addColumn('active', 'integer', [
                'default' => 1,
                'limit' => 4,
                'null' => false,
            ])
            ->addColumn('administrator', 'integer', [
                'default' => 0,
                'limit' => 4,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('password', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->create();

    }

    public function down()
    {
        $this->dropTable('permissions_actions');
        $this->dropTable('permissions');
        $this->dropTable('roles_permissions');
        $this->dropTable('roles');
        $this->dropTable('users_roles');
        $this->dropTable('users');
    }
}
