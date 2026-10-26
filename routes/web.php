<?php

use Illuminate\Support\Facades\Route;
use App\Models\Employee;

Route::get('/', function () {
    return redirect('/admin');
});


Route::get('/calisan/{id}/tutanak', function ($id) {
    //datayi bul
    $calisan = Employee::with('assets')->findOrFail($id);

    //tasarima gonder (view)
    return view('tutanak', compact('calisan'));

})->name('zimmet.tutanagi');