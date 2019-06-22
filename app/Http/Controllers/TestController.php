<?php

namespace App\Http\Controllers;

class TestController extends Controller
{
    public function index()
    {
        $f = new \FastDFS();

        dd($f->tracker_query_storage_store());
    }
}
