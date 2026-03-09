<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Update Task Monitoring') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" x-data="{ isNoteModalOpen: false, isSubmissionModalOpen: @js($errors->has('date_of_submission') || $errors->has('receiving_officer') || $errors->has('acknowledgement_receipt_reference_number')), isSubmissionDecisionModalOpen: @js($errors->has('submission_decision') || $errors->has('submission_notes') || $errors->has('submission_notes_input')), selectedFormId: '', selectedFormName: '', noteDate: '', noteStatus: 'pending', existingRemarks: '', notesRemarksInput: '', openNoteModal(button) { this.selectedFormId = button.dataset.formId; this.selectedFormName = button.dataset.formName; this.noteDate = button.dataset.noteDate || '{{ now()->format('Y-m-d') }}'; this.noteStatus = button.dataset.noteStatus || 'pending'; this.existingRemarks = button.dataset.notesRemarks || ''; this.notesRemarksInput = ''; this.isNoteModalOpen = true; }, closeNoteModal() { this.isNoteModalOpen = false; }, openSubmissionModal() { this.isSubmissionModalOpen = true; }, closeSubmissionModal() { this.isSubmissionModalOpen = false; }, openSubmissionDecisionModal() { this.isSubmissionDecisionModalOpen = true; }, closeSubmissionDecisionModal() { this.isSubmissionDecisionModalOpen = false; } }">
                    @if (session('status') === 'form-note-saved')
                        <p class="mb-4 text-sm text-green-600">{{ __('Form note saved successfully.') }}</p>
                    @endif

                    <form method="POST" action="{{ route('bookings.update', $monitoring) }}" class="grid grid-cols-1 gap-6 md:grid-cols-2" data-confirm="Are you sure you want to update this entry?">
                        @csrf
                        @method('patch')

                        <div>
                            <x-input-label for="date_task_received" :value="__('Date Task Received')" />
                            <x-text-input id="date_task_received" name="date_task_received" type="date" class="mt-1 block w-full" :value="old('date_task_received', $monitoring->date_task_received?->format('Y-m-d'))" disabled />
                            <input type="hidden" name="date_task_received" value="{{ old('date_task_received', $monitoring->date_task_received?->format('Y-m-d')) }}">
                            <x-input-error class="mt-2" :messages="$errors->get('date_task_received')" />
                        </div>

                        <div>
                            <x-input-label for="client_name" :value="__('Client Name')" />
                            <select id="client_name" name="client_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" disabled>
                                <option value="">{{ __('Select Client') }}</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" @selected((string) old('client_name', $monitoring->client_id) === (string) $client->id)>{{ $client->client_name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="client_name" value="{{ old('client_name', $monitoring->client_id) }}">
                            <x-input-error class="mt-2" :messages="$errors->get('client_name')" />
                        </div>

                        <div>
                            <x-input-label for="type_of_task" :value="__('Type of Task')" />
                            <select id="type_of_task" name="type_of_task" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" disabled>
                                <option value="">{{ __('Select Task') }}</option>
                                @foreach ($tasks as $task)
                                    <option value="{{ $task->id }}" @selected((string) old('type_of_task', $monitoring->task_id) === (string) $task->id)>{{ $task->task_name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="type_of_task" value="{{ old('type_of_task', $monitoring->task_id) }}">
                            <x-input-error class="mt-2" :messages="$errors->get('type_of_task')" />
                        </div>

                        <div>
                            <x-input-label for="assigned_responsible_person" :value="__('Assigned Responsible Person')" />
                            <select id="assigned_responsible_person" name="assigned_responsible_person" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" disabled>
                                <option value="">{{ __('Select Contact Person') }}</option>
                                @foreach ($contactPersons as $contactPerson)
                                    <option value="{{ $contactPerson->id }}" @selected((string) old('assigned_responsible_person', $monitoring->assigned_responsible_person_id) === (string) $contactPerson->id)>{{ $contactPerson->contact_person }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="assigned_responsible_person" value="{{ old('assigned_responsible_person', $monitoring->assigned_responsible_person_id) }}">
                            <x-input-error class="mt-2" :messages="$errors->get('assigned_responsible_person')" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="required_forms_documents" :value="__('List of Required Forms and Documents')" />
                            @php
                                $selectedFormIds = array_map('strval', old('required_forms_documents', $monitoring->required_forms_documents ?? []));
                                $allRequiredFormsCompleted = ! empty($selectedFormIds)
                                    && collect($selectedFormIds)->every(function (string $formId) use ($notesByForm): bool {
                                        return strtolower((string) ($notesByForm[(int) $formId]['note_status'] ?? 'pending')) === 'completed';
                                    });
                                $submissionDateValue = old('date_of_submission', $monitoring->date_of_submission?->format('Y-m-d'));
                                $receivingOfficerValue = old('receiving_officer', $monitoring->receiving_officer);
                                $acknowledgementReferenceValue = old('acknowledgement_receipt_reference_number', $monitoring->acknowledgement_receipt_reference_number);
                                $submissionDecisionValue = old('submission_decision', $monitoring->submission_decision);
                                $submissionNotesValue = old('submission_notes', $monitoring->submission_notes);
                                $existingSubmissionNotesValue = old('existing_submission_notes', $monitoring->submission_notes);
                                $submissionNotesInputValue = old('submission_notes_input', '');
                                $hasSubmissionEntryDetails = ! empty(trim((string) ($monitoring->submission_decision ?? '')))
                                    || ! empty(trim((string) ($monitoring->submission_notes ?? '')));
                            @endphp

                            <div id="required_forms_documents" class="mt-1 overflow-x-auto rounded-md border border-gray-300">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Form Name') }}</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Notes/Remarks') }}</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($forms->filter(fn ($form) => in_array((string) $form->id, $selectedFormIds, true)) as $form)
                                            @php
                                                $note = $notesByForm[$form->id] ?? null;
                                                $noteStatus = strtolower($note['note_status'] ?? 'pending');
                                                $isCompleted = $noteStatus === 'completed';
                                            @endphp
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-900">{{ $form->form_name }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-900">
                                                    @if ($noteStatus === 'completed')
                                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700">{{ __('Completed') }}</span>
                                                    @elseif ($noteStatus === 'pending')
                                                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700">{{ __('Pending') }}</span>
                                                    @else
                                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-700">{{ ucfirst($noteStatus) }}</span>
                                                    @endif
                                                    @if (!empty($note['updated_at'] ?? null))
                                                        <div class="mt-1 text-xs text-gray-500">
                                                            <div>{{ __('Last updated:') }}</div>
                                                            <div>{{ $note['updated_at'] }}</div>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-900 whitespace-pre-line">{{ $note['notes_remarks'] ?? '—' }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-900">
                                                    @if (blank($monitoring->receiving_officer))
                                                        <button type="button" x-on:click="openNoteModal($el)" data-form-id="{{ $form->id }}" data-form-name="{{ $form->form_name }}" data-note-date="{{ $note['note_date'] ?? '' }}" data-note-status="{{ $note['note_status'] ?? 'pending' }}" data-notes-remarks="{{ $note['notes_remarks'] ?? '' }}" class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-semibold text-white focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $isCompleted ? 'bg-green-600 hover:bg-green-500 focus:ring-green-500' : 'bg-gray-800 hover:bg-gray-700 focus:ring-gray-500' }}">
                                                            {{ __('Update') }}
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                            <input type="hidden" name="required_forms_documents[]" value="{{ $form->id }}">
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-4 py-2 text-sm text-gray-500 text-center">{{ __('No required forms selected.') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('required_forms_documents')" />
                        </div>

                        <div class="md:col-span-2">
                            @if ($allRequiredFormsCompleted)
                                <div class="w-full">
                                    <x-input-label :value="__('Submission Details')" />
                                    <div class="mt-1 overflow-x-auto rounded-md border border-gray-300">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date of Submission') }}</th>
                                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Recieving Officer') }}</th>
                                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Acknowledgement Reciept/Reference Number') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white">
                                                <tr>
                                                    <td class="px-4 py-3 align-top text-sm text-gray-900">
                                                        {{ $submissionDateValue ? \Illuminate\Support\Carbon::parse($submissionDateValue)->format('F d, Y') : '—' }}
                                                    </td>
                                                    <td class="px-4 py-3 align-top text-sm text-gray-900">
                                                        {{ $receivingOfficerValue ?: '—' }}
                                                    </td>
                                                    <td class="px-4 py-3 align-top text-sm text-gray-900">
                                                        {{ $acknowledgementReferenceValue ?: '—' }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-4">
                                        <button type="button" x-on:click="openSubmissionModal()" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                            {{ __('Edit Submission Details') }}
                                        </button>
                                    </div>
                                    <br>
                                    <x-input-label :value="__('Submission Action')" />

                                    <div class="mt-1 overflow-x-auto rounded-md border border-gray-300">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Submission Decision') }}</th>
                                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Submission Notes') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white">
                                                <tr>
                                                    <td class="px-4 py-3 align-top text-sm text-gray-900">
                                                        @if ($submissionDecisionValue === 'accepted')
                                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700">{{ __('Accepted') }}</span>
                                                        @elseif ($submissionDecisionValue === 'declined')
                                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700">{{ __('Declined') }}</span>
                                                        @elseif (!empty($submissionDecisionValue))
                                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-700">{{ ucfirst($submissionDecisionValue) }}</span>
                                                        @else
                                                            {{ '—' }}
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 align-top text-sm text-gray-900">
                                                        <div class="whitespace-pre-line">{{ $submissionNotesValue ?: '—' }}</div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-4">
                                        <button type="button" x-on:click="openSubmissionDecisionModal()" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                            {{ __('Edit Submission Decision') }}
                                        </button>
                                    </div>
                                </div>
                            @endif

                                    <div id="submission-action" class="mt-4">
                                <a href="{{ route('bookings.index', ['tab' => 'monitoring']) }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Back to Monitoring') }}</a>
                            </div>

                            <div x-show="isSubmissionModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 px-4" style="display: none;">
                                <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Edit Submission Details') }}</h3>
                                        <button type="button" x-on:click="closeSubmissionModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
                                    </div>

                                    <div class="mt-6 space-y-4">
                                        <div>
                                            <x-input-label for="date_of_submission" :value="__('Date of Submission')" />
                                            <x-text-input id="date_of_submission" name="date_of_submission" type="date" class="mt-1 block w-full text-sm" :value="$submissionDateValue" />
                                            <x-input-error class="mt-2" :messages="$errors->get('date_of_submission')" />
                                        </div>

                                        <div>
                                            <x-input-label for="receiving_officer" :value="__('Recieving Officer')" />
                                            <x-text-input id="receiving_officer" name="receiving_officer" type="text" class="mt-1 block w-full text-sm" :value="$receivingOfficerValue" />
                                            <x-input-error class="mt-2" :messages="$errors->get('receiving_officer')" />
                                        </div>

                                        <div>
                                            <x-input-label for="acknowledgement_receipt_reference_number" :value="__('Acknowledgement Reciept/Reference Number')" />
                                            <x-text-input id="acknowledgement_receipt_reference_number" name="acknowledgement_receipt_reference_number" type="text" class="mt-1 block w-full text-sm" :value="$acknowledgementReferenceValue" />
                                            <x-input-error class="mt-2" :messages="$errors->get('acknowledgement_receipt_reference_number')" />
                                        </div>
                                    </div>

                                    <div class="mt-6 flex items-center justify-end gap-3">
                                        <button type="button" x-on:click="closeSubmissionModal()" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                            {{ __('Cancel') }}
                                        </button>
                                        <x-primary-button>{{ __('Submit Form') }}</x-primary-button>
                                    </div>
                                </div>
                            </div>

                            <div x-show="isSubmissionDecisionModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 px-4" style="display: none;">
                                <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Edit Submission Decision') }}</h3>
                                        <button type="button" x-on:click="closeSubmissionDecisionModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
                                    </div>

                                    <div class="mt-6 space-y-4">
                                        <div>
                                            <x-input-label for="submission_decision" :value="__('Submission Decision')" />
                                            <select id="submission_decision" name="submission_decision" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="">{{ __('Select Decision') }}</option>
                                                <option value="declined" @selected((string) $submissionDecisionValue === 'declined')>{{ __('Declined') }}</option>
                                                <option value="accepted" @selected((string) $submissionDecisionValue === 'accepted')>{{ __('Accepted') }}</option>
                                            </select>
                                            <x-input-error class="mt-2" :messages="$errors->get('submission_decision')" />
                                        </div>

                                        <div>
                                            <input type="hidden" name="existing_submission_notes" value="{{ $existingSubmissionNotesValue }}">

                                            <x-input-label for="existing_submission_notes_preview" :value="__('Existing Notes/Remarks')" />
                                            <textarea id="existing_submission_notes_preview" rows="4" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-700 shadow-sm" readonly>{{ $existingSubmissionNotesValue }}</textarea>

                                            <x-input-label class="mt-4" for="submission_notes_input" :value="__('Add Notes/Remarks')" />
                                            <textarea id="submission_notes_input" name="submission_notes_input" rows="4" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $submissionNotesInputValue }}</textarea>
                                            <x-input-error class="mt-2" :messages="$errors->get('submission_notes_input')" />
                                        </div>
                                    </div>

                                    <div class="mt-6 flex items-center justify-end gap-3">
                                        <button type="button" x-on:click="closeSubmissionDecisionModal()" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                            {{ __('Cancel') }}
                                        </button>
                                        <x-primary-button>{{ __('Submit') }}</x-primary-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div x-show="isNoteModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 px-4" style="display: none;">
                        <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">{{ __('Update Form Note') }}</h3>
                                <button type="button" x-on:click="closeNoteModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
                            </div>

                            <p class="mt-2 text-sm text-gray-600">
                                {{ __('Form:') }} <span class="font-medium" x-text="selectedFormName"></span>
                            </p>

                            <form method="POST" action="{{ route('bookings.form-note.save', $monitoring) }}" class="mt-6 space-y-6" data-confirm="Are you sure you want to save this form note?">
                                @csrf
                                <input type="hidden" name="form_id" x-model="selectedFormId">
                                <input type="hidden" name="existing_notes_remarks" x-model="existingRemarks">

                                <div>
                                    <x-input-label for="note_date" :value="__('Note Date')" />
                                    <x-text-input id="note_date" name="note_date" type="date" class="mt-1 block w-full" x-model="noteDate" />
                                </div>

                                <div>
                                    <x-input-label for="note_status" :value="__('Status')" />
                                    <select id="note_status" name="note_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" x-model="noteStatus">
                                        <option value="completed">{{ __('Completed') }}</option>
                                        <option value="pending">{{ __('Pending') }}</option>
                                    </select>
                                </div>

                                <div>
                                    <x-input-label for="existing_notes_remarks_preview" :value="__('Existing Notes/Remarks')" />
                                    <textarea id="existing_notes_remarks_preview" rows="4" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-700 shadow-sm" x-model="existingRemarks" readonly></textarea>
                                </div>

                                <div>
                                    <x-input-label for="notes_remarks_input" :value="__('Add Notes/Remarks')" />
                                    <textarea id="notes_remarks_input" name="notes_remarks_input" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" x-model="notesRemarksInput"></textarea>
                                </div>

                                <div class="flex items-center justify-end gap-3">
                                    <button type="button" x-on:click="closeNoteModal()" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        {{ __('Cancel') }}
                                    </button>
                                    <x-primary-button>{{ __('Save Note') }}</x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
