<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\FormItem;
use App\Models\Task;
use App\Models\TaskMonitoring;
use App\Models\TaskMonitoringFormNote;
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

        $contactPersons = Client::query()
            ->select(['id', 'contact_person'])
            ->whereNotNull('contact_person')
            ->where('contact_person', '!=', '')
            ->orderBy('contact_person')
            ->get();

        $tasks = Task::query()
            ->select(['id', 'task_name'])
            ->orderBy('task_name')
            ->get();

        $forms = FormItem::query()
            ->select(['id', 'form_name'])
            ->orderBy('form_name')
            ->get();

        $monitorings = TaskMonitoring::query()
            ->with([
                'client:id,client_name',
                'task:id,task_name',
                'assignedResponsiblePerson:id,contact_person',
            ])
            ->latest('created_at')
            ->paginate(10, ['*'], 'monitorings_page');

        $formNamesById = $forms->pluck('form_name', 'id');

        $formStatusesByMonitoringAndForm = TaskMonitoringFormNote::query()
            ->whereIn('task_monitoring_id', $monitorings->pluck('id'))
            ->get()
            ->mapWithKeys(fn (TaskMonitoringFormNote $note) => [
                $note->task_monitoring_id.'-'.$note->form_id => strtolower((string) $note->note_status),
            ]);

        return view('bookings', compact('clients', 'contactPersons', 'tasks', 'forms', 'monitorings', 'formNamesById', 'formStatusesByMonitoringAndForm'));
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
            'required_forms_documents' => ['nullable', 'array'],
            'required_forms_documents.*' => ['integer', 'exists:forms,id'],
        ]);

        TaskMonitoring::create([
            'date_task_received' => $validated['date_task_received'],
            'client_id' => $validated['client_name'],
            'task_id' => $validated['type_of_task'],
            'assigned_responsible_person_id' => $validated['client_name'],
            'required_forms_documents' => $validated['required_forms_documents'] ?? [],
            'submission_status' => 'pending',
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

        $contactPersons = Client::query()
            ->select(['id', 'contact_person'])
            ->whereNotNull('contact_person')
            ->where('contact_person', '!=', '')
            ->orderBy('contact_person')
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
                'note_status' => $note->note_status,
            ]);

        return view('task-monitorings.edit', compact('monitoring', 'clients', 'tasks', 'contactPersons', 'forms', 'notesByForm'));
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
            'assigned_responsible_person' => ['required', 'integer', 'exists:clients,id'],
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
            'notes_remarks_input' => ['nullable', 'string'],
            'existing_notes_remarks' => ['nullable', 'string'],
            'note_date' => ['nullable', 'date'],
            'note_status' => ['required', 'string', 'in:completed,pending'],
        ]);

        $existingRemarks = trim((string) ($validated['existing_notes_remarks'] ?? ''));
        $newRemark = trim((string) ($validated['notes_remarks_input'] ?? ''));

        $combinedRemarks = $existingRemarks;

        if ($newRemark !== '') {
            $timestampedRemark = '['.now()->format('F d, Y h:i A').'] '.$newRemark;

            $combinedRemarks = $existingRemarks === ''
                ? $timestampedRemark
                : $existingRemarks."\n".$timestampedRemark;
        }

        TaskMonitoringFormNote::updateOrCreate(
            [
                'task_monitoring_id' => $monitoring->id,
                'form_id' => $validated['form_id'],
            ],
            [
                'notes_remarks' => $combinedRemarks !== '' ? $combinedRemarks : null,
                'note_date' => $validated['note_date'] ?? null,
                'note_status' => $validated['note_status'],
            ]
        );

        return Redirect::route('bookings.edit', $monitoring)->with('status', 'form-note-saved');
    }
}
