<div class="users form large-9 medium-8 columns content">
    <?= $this->Form->create('Users') ?>
    <fieldset>
        <legend><?= __('Reset Password') ?></legend>
        <?php
        echo $this->Form->input('email', ['type' => 'email']);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
