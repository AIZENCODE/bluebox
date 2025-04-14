<?php
use App\Exports\ArticulosExport;
use App\Exports\PostExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('client/index');
});
