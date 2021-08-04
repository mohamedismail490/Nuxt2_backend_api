<?php

namespace App\Traits;

trait Orderable {

    public function scopeLatestFirst($query) {
        return $query -> orderBy('created_at', 'DESC');
    }

    public function scopeOldestFirst($query) {
        return $query -> orderBy('created_at', 'ASC');
    }
}
