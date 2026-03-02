<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FormItemController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Models\Client;
use App\Models\FormItem;
use App\Models\Task;
use App\Models\User;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{monitoring}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
    Route::patch('/bookings/{monitoring}', [BookingController::class, 'update'])->name('bookings.update');
    Route::post('/bookings/{monitoring}/form-note', [BookingController::class, 'saveFormNote'])->name('bookings.form-note.save');

    Route::get('/settings', function () {
        $users = User::query()
            ->select(['id', 'name', 'email', 'role', 'status', 'created_at'])
            ->latest('created_at')
            ->paginate(10, ['*'], 'users_page');

        $clients = Client::query()
            ->select(['id', 'client_name', 'contact_person', 'address', 'tin', 'tel_phone_number', 'created_at'])
            ->latest('created_at')
            ->paginate(10, ['*'], 'clients_page');

        $tasks = Task::query()
            ->select(['id', 'task_name', 'created_at'])
            ->latest('created_at')
            ->paginate(10, ['*'], 'tasks_page');

        $forms = FormItem::query()
            ->select(['id', 'form_name', 'created_at'])
            ->latest('created_at')
            ->paginate(10, ['*'], 'forms_page');

        return view('settings', compact('users', 'clients', 'tasks', 'forms'));
    })->name('settings.index');

    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::patch('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');

    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');

    Route::post('/forms', [FormItemController::class, 'store'])->name('forms.store');
    Route::get('/forms/{formItem}/edit', [FormItemController::class, 'edit'])->name('forms.edit');
    Route::patch('/forms/{formItem}', [FormItemController::class, 'update'])->name('forms.update');

    Route::middleware('can:manage-users')->group(function () {
        Route::get('/users/register', [UserController::class, 'create'])->name('users.create');
        Route::post('/users/register', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
