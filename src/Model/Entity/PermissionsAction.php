<?php
namespace Acciona\Users\Model\Entity;

use Cake\ORM\Entity;

/**
 * PermissionsAction Entity.
 *
 * @property int $id
 * @property string $action
 * @property int $permission_id
 * @property \Acciona\Users\Model\Entity\Permission $permission
 */
class PermissionsAction extends Entity
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
