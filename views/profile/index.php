<?php
?>
<main id="profile">
    <div class="container no-padding">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <h1>Profile</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <p class="profile-label text-bold">First name</p>
                <p><?= $model->first_name; ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <p class="profile-label text-bold">Last name</p>
                <p><?= $model->last_name; ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <p class="profile-label text-bold">Username</p>
                <p><?= $model->username; ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <p class="profile-label text-bold">E-mail address</p>
                <p><?= $model->email; ?></p>
            </div>
        </div>
        <a href="/profile/edit" class="btn btn-default">Edit</a>
    </div>
</main>