<?php
namespace Acciona\Users\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;
/**
 * User Entity.
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 */
class User extends Entity
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

    /**
     * Fields that are excluded from JSON an array versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password',
    ];

    /**
     * Set password and salt
     *
     * @param $password
     * @return mixed
     */
    protected function _setPassword($password)
    {
        return (new DefaultPasswordHasher())->hash($password);
    }

    protected function _setRetypePassword($password)
    {
        return (new DefaultPasswordHasher())->hash($password);
    }

    protected function _getFullName()
    {
        return $this->_properties['name'] . ' ' . $this->_properties['last_name'];
    }
}
