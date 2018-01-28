<?php
use components\Form;

$this->title = "Login";
?>
<main id="login">
    <h1>Hackathon</h1>
    <section class="form">
	    <?php
	    $form = Form::begin($this, [
		    'action' => "/index/login",
		    'id' => 'loginForm'
	    ]);
	    ?>
	    <?= $form->field($this->model, 'username')->textInput(['placeholder' => 'Username'])->label(false); ?>

	    <?= $form->field($this->model, 'password')->passwordInput(['placeholder' => 'Password'])->label(false); ?>

        <div class="submintBtn">
	        <?= $form->submitBtn("Login"); ?>
        </div>

	    <?php Form::end(); ?>
    </section>
    <section class="register">
        <span class="register-txt">New to the hackathon? <a href="/index/register">Sign up</a></span>
    </section>
</main>