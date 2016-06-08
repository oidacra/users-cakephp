<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Permission'), ['action' => 'edit', $permission->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Permission'), ['action' => 'delete', $permission->id], ['confirm' => __('Are you sure you want to delete # {0}?', $permission->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Permissions'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Permission'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Permissions Actions'), ['controller' => 'PermissionsActions', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Permissions Action'), ['controller' => 'PermissionsActions', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Roles'), ['controller' => 'Roles', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Role'), ['controller' => 'Roles', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="permissions view large-9 medium-8 columns content">
    <h3><?= h($permission->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Entity') ?></th>
            <td><?= h($permission->entity) ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($permission->id) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Permissions Actions') ?></h4>
        <?php if (!empty($permission->permissions_actions)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Action') ?></th>
                <th><?= __('Permission Id') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($permission->permissions_actions as $permissionsActions): ?>
            <tr>
                <td><?= h($permissionsActions->id) ?></td>
                <td><?= h($permissionsActions->action) ?></td>
                <td><?= h($permissionsActions->permission_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'PermissionsActions', 'action' => 'view', $permissionsActions->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'PermissionsActions', 'action' => 'edit', $permissionsActions->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'PermissionsActions', 'action' => 'delete', $permissionsActions->id], ['confirm' => __('Are you sure you want to delete # {0}?', $permissionsActions->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Roles') ?></h4>
        <?php if (!empty($permission->roles)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Name') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($permission->roles as $roles): ?>
            <tr>
                <td><?= h($roles->id) ?></td>
                <td><?= h($roles->name) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Roles', 'action' => 'view', $roles->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Roles', 'action' => 'edit', $roles->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Roles', 'action' => 'delete', $roles->id], ['confirm' => __('Are you sure you want to delete # {0}?', $roles->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
