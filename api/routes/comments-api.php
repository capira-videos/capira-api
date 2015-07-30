<?php

include_once 'libs/comments.php';


$app->get('/comments/unit/:id', function ($id) {
    fetch_comments_unit($id);
});