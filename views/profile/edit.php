<?php

use components\Form;

$this->title = "Edit";

$formId = "editFrom";
$form = Form::begin($this, [
    'id' => $formId,
]);

$this->registerJsFile("profile-edit.js", [
    'formId' => $formId,
    'userId' => $model->primaryKey,
]);
?>

<main id="profile-edit">
    <div class="container no-padding">
        <div class="row">
            <div class="col-lg-5 offset-lg-1 edit-content">
                <div class="row">
                    <div class="col-lg-12">
                        <?= $form->field($model, 'first_name')->textInput()->icon("user"); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <?= $form->field($model, 'last_name')->textInput()->icon("user"); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <?= $form->field($model, 'email')->emailInput(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <?= $form->field($model, 'avatar')->radioList(['values' => [
                            'user' => 'User name',
                            'name' => 'Name',
                            'img' => 'Picture',
                            'imguser' => 'Image and username',
                            'imgname' => 'Image and name',
                        ]]); ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 offset-lg-7 fixed">
                <div class="row">
                    <div class="col-lg-12">
                        <?php if ($model->image !== "null") {
                            $visibleDelete = "visible";
                            $addClass = "change";
                        } else {
                            $visibleDelete = "";
                            $addClass = "add";
                        } ?>
                        <?= $form->field($model, 'image', ['class' => 'hidden'])->pictureInput(['disabled' => 'disabled']); ?>
                        <div class="imageHolder">
                            <a href="#" id="add-image" class="<?= $addClass; ?>" data-add="Add" data-change="Change"></a>
                            <div class="imgShadow">
                                <img src="<?= $model->image !== "null" ? USER_IMG_URL . "/" . $model->image : IMG_URL . "/blank-male.png"; ?>"
                                     data-default="<?= IMG_URL . "/blank-male.png"; ?>" class="avatar-form-image" alt="avatar">
                            </div>
                            <a href="#" id="delete-image" class="btn btn-danger <?= $visibleDelete; ?>">Delete</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-10 offset-lg-1">
                <div class="row">
                    <div class="col-lg-12 fixed buttonHolder">
                        <a href="/profile/index" class="btn btn-default">Back</a>
                        <button id="chngPswd" class="btn btn-secondary">Change password</button>
                        <?= $form->submitBtn("Save"); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php Form::end();

include_once (MODAL_DIR . "/chngPswd.php");