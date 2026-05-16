<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('document.{id}', function ($user, $id) {
    return true;
});