<?php
use components\Form;

$this->title = "Register";
?>
<main id="register">
    <h1>Hackathon</h1>
    <section class="form">
		<?php $form = Form::begin($this, [
			'action' => "/index/register",
			'id' => 'registerForm'
		]); ?>

		<?= $form->field($this->model, 'username')->textInput(['placeholder' => 'Username'])->label(false); ?>

		<?= $form->field($this->model, 'email')->emailInput(['placeholder' => 'Email'])->label(false); ?>

		<?= $form->field($this->model, 'password')->passwordInput(['placeholder' => 'Password'])->label(false); ?>

		<?= $form->field($this->model, 'confirm_password')->passwordInput(['placeholder' => 'Confirm password'])->label(false); ?>

		<?= $form->field($this->model, 'image', ['class' => 'imageHolder'])->pictureInput(['class' => 'hidden', 'placeholder' => 'Add picture'])->label(false); ?>

	    <div class="submintBtn">
		    <?= $form->submitBtn("Register"); ?>
	    </div>

		<?php Form::end(); ?>
    </section>
	<section class="login">
		<span class="login-txt">Already have an account? <a href="/index/login">Sign in</a></span>
	</section>
</main>
