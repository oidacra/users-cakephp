<?php
namespace Acciona\Users\Model\Entity;

use Cake\ORM\Entity;

/**
 * Permission Entity.
 *
 * @property int $id
 * @property string $entity
 * @property \Acciona\Users\Model\Entity\PermissionsAction[] $permissions_actions
 * @property \Acciona\Users\Model\Entity\Role[] $roles
 */
class Permission extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
