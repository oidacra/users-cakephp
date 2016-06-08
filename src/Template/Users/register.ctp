<div class="users form large-9 medium-8 columns content">
    <?= $this->Form->create($user) ?>
    <fieldset>
        <legend><?= __('Register User') ?></legend>
        <?php
            echo $this->Form->input('email');
            echo $this->Form->input('name');
            echo $this->Form->input('last_name');
            echo $this->Form->input('password');
            echo $this->Form->input('retype_password', ['type' => 'password']);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
