<?php
use App\Exports\ArticulosExport;
use App\Exports\PostExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('client/index');
});

// Nosotros
Route::get('/nosotros', function () {
    return view('client/nosotros');
})->name("nosotros.index");

// Fin nosotros

// Servicios
Route::get('/servicios', function () {
    return view('client/servicios');
})->name("servicios.index");
// Fin servicios

// Blog
Route::get('/blog', function () {
    return view('client/blog');
})->name("blog.index");
// Fin blog

// Contactenos
Route::get('/contactenos', function () {
    return view('client/contactenos');
})->name("contactenos.index");
// Fin contactenos