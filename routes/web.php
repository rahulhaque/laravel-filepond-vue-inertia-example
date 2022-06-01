<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use RahulHaque\Filepond\Facades\Filepond;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Filepond demo route
    Route::put('/user/info', function (Request $request) {
        // Validate the fields
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|max:100',
            'avatar' => [
                'required',
                Rule::filepond([
                    'max:1024',
                    'image:jpeg,png'
                ])
            ],
            'gallery' => 'required',
            'gallery.*' => [
                'required',
                Rule::filepond([
                    'max:1024',
                    'image:jpeg,png'
                ])
            ],
        ]);

        // Move the file to permanent storage (./storage/app/public/avatars)
        $fileInfo = Filepond::field($request->avatar)->moveTo('avatars/avatar-'.auth()->id());
        $galleryInfo = Filepond::field($request->gallery)->moveTo('galleries/gallery-'.auth()->id());

        // Save location in the user table
        auth()->user()->update([
            'name' => $request->name,
            'email' => $request->email,
            'profile_photo_path' => $fileInfo['location']
        ]);

        // Flash success message
        session()->flash('flash', [
            'bannerStyle'=> 'success',
            'banner' => 'Profile information updated successfully!',
        ]);

        return redirect()->back();
    })->name('update-user-info');
});
