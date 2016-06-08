<div class="users form large-9 medium-8 columns content">
    <?= $this->Flash->render('auth') ?>
    <?= $this->Form->create($user) ?>
    <fieldset>
        <legend><?= __('Login') ?></legend>
        <?php
        echo $this->Form->input('email');
        echo $this->Form->input('password');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Login')) ?>
    <?= $this->Form->end() ?>
</div>