<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\FormItem;
use App\Models\Task;
use App\Models\TaskMonitoring;
use App\Models\TaskMonitoringFormNote;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Show booking form.
     */
    public function index(): View
    {
        $clients = Client::query()
            ->select(['id', 'client_name'])
            ->orderBy('client_name')
            ->get();

        $tasks = Task::query()
            ->select(['id', 'task_name'])
            ->orderBy('task_name')
            ->get();

        $users = User::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        $forms = FormItem::query()
            ->select(['id', 'form_name'])
            ->orderBy('form_name')
            ->get();

        $monitorings = TaskMonitoring::query()
            ->with([
                'client:id,client_name',
                'task:id,task_name',
                'assignedResponsiblePerson:id,name',
            ])
            ->latest('created_at')
            ->paginate(10, ['*'], 'monitorings_page');

        $formNamesById = $forms->pluck('form_name', 'id');

        return view('bookings', compact('clients', 'tasks', 'users', 'forms', 'monitorings', 'formNamesById'));
    }

    /**
     * Store a newly created monitoring task entry.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date_task_received' => ['required', 'date'],
            'client_name' => ['required', 'integer', 'exists:clients,id'],
            'type_of_task' => ['required', 'integer', 'exists:tasks,id'],
            'assigned_responsible_person' => ['required', 'integer', 'exists:users,id'],
            'required_forms_documents' => ['nullable', 'array'],
            'required_forms_documents.*' => ['integer', 'exists:forms,id'],
        ]);

        TaskMonitoring::create([
            'date_task_received' => $validated['date_task_received'],
            'client_id' => $validated['client_name'],
            'task_id' => $validated['type_of_task'],
            'assigned_responsible_person_id' => $validated['assigned_responsible_person'],
            'required_forms_documents' => $validated['required_forms_documents'] ?? [],
        ]);

        return Redirect::route('bookings.index')->with('status', 'task-created');
    }

    /**
     * Show the form for editing the specified monitoring entry.
     */
    public function edit(TaskMonitoring $monitoring): View
    {
        $clients = Client::query()
            ->select(['id', 'client_name'])
            ->orderBy('client_name')
            ->get();

        $tasks = Task::query()
            ->select(['id', 'task_name'])
            ->orderBy('task_name')
            ->get();

        $users = User::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        $forms = FormItem::query()
            ->select(['id', 'form_name'])
            ->orderBy('form_name')
            ->get();

        $notesByForm = TaskMonitoringFormNote::query()
            ->where('task_monitoring_id', $monitoring->id)
            ->get()
            ->keyBy('form_id')
            ->map(fn (TaskMonitoringFormNote $note) => [
                'notes_remarks' => $note->notes_remarks,
                'note_date' => $note->note_date ? Carbon::parse($note->note_date)->format('Y-m-d') : null,
            ]);

        return view('task-monitorings.edit', compact('monitoring', 'clients', 'tasks', 'users', 'forms', 'notesByForm'));
    }

    /**
     * Update the specified monitoring entry.
     */
    public function update(Request $request, TaskMonitoring $monitoring): RedirectResponse
    {
        $validated = $request->validate([
            'date_task_received' => ['required', 'date'],
            'client_name' => ['required', 'integer', 'exists:clients,id'],
            'type_of_task' => ['required', 'integer', 'exists:tasks,id'],
            'assigned_responsible_person' => ['required', 'integer', 'exists:users,id'],
            'required_forms_documents' => ['nullable', 'array'],
            'required_forms_documents.*' => ['integer', 'exists:forms,id'],
        ]);

        $monitoring->update([
            'date_task_received' => $validated['date_task_received'],
            'client_id' => $validated['client_name'],
            'task_id' => $validated['type_of_task'],
            'assigned_responsible_person_id' => $validated['assigned_responsible_person'],
            'required_forms_documents' => $validated['required_forms_documents'] ?? [],
        ]);

        return Redirect::route('bookings.index', ['tab' => 'monitoring'])->with('status', 'task-updated');
    }

    /**
     * Save notes/remarks for a required form under a monitoring entry.
     */
    public function saveFormNote(Request $request, TaskMonitoring $monitoring): RedirectResponse
    {
        $validated = $request->validate([
            'form_id' => ['required', 'integer', 'exists:forms,id'],
            'notes_remarks' => ['nullable', 'string'],
            'note_date' => ['nullable', 'date'],
        ]);

        TaskMonitoringFormNote::updateOrCreate(
            [
                'task_monitoring_id' => $monitoring->id,
                'form_id' => $validated['form_id'],
            ],
            [
                'notes_remarks' => $validated['notes_remarks'] ?? null,
                'note_date' => $validated['note_date'] ?? null,
            ]
        );

        return Redirect::route('bookings.edit', $monitoring)->with('status', 'form-note-saved');
    }
}
