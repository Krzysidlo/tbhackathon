<?php
$this->title = "Homepage";
?>
<main id="index">
    <section class="title">
        <h1>Good day <?= $user->name; ?></h1>
    </section>
    <section class="boxes">
        <a href="/index/weather" class="box weather">
            <h2>Weather</h2>
            <div class="content">

            </div>
        </a>
        <a href="/index/news" class="box news">
            <h2>News</h2>
            <div class="content">

            </div>
        </a>
        <a href="/index/sport" class="box sport">
            <h2>Sport</h2>
            <div class="content">

            </div>
        </a>
        <a href="/index/photos" class="box photos">
            <h2>Photos</h2>
            <div class="content">

            </div>
        </a>
        <a href="/index/tasks" class="box tasks">
            <h2>Tasks</h2>
            <div class="content">

            </div>
        </a>
        <a href="/index/clothes" class="box clothes">
            <h2>Clothes</h2>
            <div class="content">

            </div>
        </a>
    </section>
</main>