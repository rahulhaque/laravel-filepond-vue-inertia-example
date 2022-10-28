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

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // User profile update route
    Route::put('/user/profile', function (Request $request) {
        // Validate the submitted fields
        // Put this in Request class
        // or Controller method
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|max:100',
            'avatar' => [
                'required',
                Rule::filepond([
                    'max:1024',
                    'image:jpeg,png',
                ]),
            ],
            'gallery' => 'required',
            'gallery.*' => [
                'required',
                Rule::filepond([
                    'max:1024',
                    'image:jpeg,png',
                ]),
            ],
        ]);

        // Process the files using Filepond package
        // and move them to your preferred storage
        // i.e. "./storage/app/public/avatars"
        // dd($fileInfo) or dd($galleryInfo)
        $fileInfo = Filepond::field($request->avatar)->moveTo('avatars/avatar-'.auth()->id());
        $galleryInfo = Filepond::field($request->gallery)->moveTo('galleries/gallery-'.auth()->id());

        // Save the location in the database
        // Only avatar is saved as example
        auth()->user()->update([
            'name' => $request->name,
            'email' => $request->email,
            'profile_photo_path' => $fileInfo['location'],
        ]);

        // Save the gallery here
        // It's your job
        // dd($galleryInfo);

        // Do your chores and done
        session()->flash('flash', [
            'bannerStyle' => 'success',
            'banner' => 'Profile information updated successfully!',
        ]);

        return redirect()->back();
    })->name('update-profile');
});
