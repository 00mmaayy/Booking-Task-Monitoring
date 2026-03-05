<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bookings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6" x-data="{ activeMenu: '{{ request('tab') === 'monitoring' ? 'monitoring' : 'entry' }}' }">
                    <div class="max-w-7xl mx-auto border border-gray-200 rounded-lg p-4">
                        <div class="flex flex-wrap items-center gap-6 text-sm">
                            <a href="{{ route('bookings.index', ['tab' => 'entry']) }}" x-on:click.prevent="activeMenu = 'entry'" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                {{ __('Job/Task Entry') }}
                            </a>
                            <a href="{{ route('bookings.index', ['tab' => 'monitoring']) }}" x-on:click.prevent="activeMenu = 'monitoring'" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                {{ __('Job/Task Monitoring') }}
                            </a>
                        </div>
                    </div>

                    @if (session('status') === 'task-created')
                        <p class="text-sm text-green-600">{{ __('Task created successfully.') }}</p>
                    @endif

                    @if (session('status') === 'task-updated')
                        <p class="text-sm text-green-600">{{ __('Task entry updated successfully.') }}</p>
                    @endif

                    <div id="job-task-entry-section" class="max-w-7xl mx-auto" x-show="activeMenu === 'entry'">
                        <div class="border border-gray-200 rounded-lg p-6" x-data="{ selectedForms: [], initializeSelectedForms() { const checked = this.$root.querySelectorAll(`input[name='required_forms_documents[]']:checked`); this.selectedForms = Array.from(checked).map((item) => ({ value: item.value, text: item.dataset.formName })); }, toggleForm(event) { const value = event.target.value; const text = event.target.dataset.formName; if (event.target.checked) { if (!this.selectedForms.find((item) => item.value === value)) { this.selectedForms.push({ value, text }); } return; } this.selectedForms = this.selectedForms.filter((item) => item.value !== value); }, removeSelectedForm(value) { const checkbox = this.$root.querySelector(`input[name='required_forms_documents[]'][value='${value}']`); if (checkbox) { checkbox.checked = false; } this.selectedForms = this.selectedForms.filter((item) => item.value !== value); } }" x-init="initializeSelectedForms()">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Job/Task Monitoring Form') }}</h3>

                        <form method="POST" action="{{ route('bookings.store') }}" class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-3" data-confirm="Are you sure you want to create this task?">
                            @csrf

                            <div>
                                <x-input-label for="date_task_received" :value="__('Date Task Received')" />
                                <x-text-input id="date_task_received" name="date_task_received" type="date" class="mt-1 block w-full" :value="old('date_task_received')" />
                                <x-input-error class="mt-2" :messages="$errors->get('date_task_received')" />
                            </div>

                            <div>
                                <x-input-label for="client_name" :value="__('Client Name')" />
                                <select id="client_name" name="client_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('Select Client') }}</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" @selected((string) old('client_name') === (string) $client->id)>{{ $client->client_name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('client_name')" />
                            </div>

                            <div>
                                <x-input-label for="type_of_task" :value="__('Type of Task')" />
                                <select id="type_of_task" name="type_of_task" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('Select Task') }}</option>
                                    @foreach ($tasks as $task)
                                        <option value="{{ $task->id }}" @selected((string) old('type_of_task') === (string) $task->id)>{{ $task->task_name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('type_of_task')" />
                            </div>

                            <div class="md:col-span-3">
                                <x-input-label for="required_forms_documents" :value="__('List of Required Forms and Documents')" />
                                <div id="required_forms_documents" class="mt-1 max-h-48 overflow-y-auto rounded-md border border-gray-300 p-3">
                                    <div class="space-y-2">
                                        @foreach ($forms as $form)
                                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                                <input type="checkbox" name="required_forms_documents[]" value="{{ $form->id }}" data-form-name="{{ $form->form_name }}" @checked(in_array((string) $form->id, array_map('strval', old('required_forms_documents', [])), true)) x-on:change="toggleForm($event)" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                <span>{{ $form->form_name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <x-input-error class="mt-2" :messages="$errors->get('required_forms_documents')" />

                                <div class="mt-3 space-y-1" x-show="selectedForms.length > 0">
                                    <p class="text-sm font-medium text-gray-700">{{ __('Selected Forms:') }}</p>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="form in selectedForms" :key="form.value">
                                            <span class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                                                <span x-text="form.text"></span>
                                                <button type="button" x-on:click="removeSelectedForm(form.value)" class="text-gray-500 hover:text-gray-700">&times;</button>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="md:col-span-3">
                                <x-primary-button>{{ __('Create Task') }}</x-primary-button>
                            </div>
                            </form>
                        </div>
                    </div>

                    <div id="job-task-monitoring-section" x-show="activeMenu === 'monitoring'">
                        <div id="job-task-monitoring" class="border border-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Job/Task Monitoring') }}</h3>

                        <div class="mt-6 overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Task ID') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date Task Received') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Client Name') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Type of Task') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Assigned Responsible Person') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('List of Required Forms and Documents') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Required Docs Status') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Submission Status') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($monitorings as $monitoring)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $monitoring->id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $monitoring->date_task_received?->format('F d, Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $monitoring->client?->client_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $monitoring->task?->task_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $monitoring->assignedResponsiblePerson?->contact_person }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                @php
                                                    $requiredFormIds = collect($monitoring->required_forms_documents ?? [])->map(fn ($id) => (int) $id)->values();
                                                    $allRequiredFormsCompleted = $requiredFormIds->isNotEmpty()
                                                        && $requiredFormIds->every(fn ($formId) => strtolower(trim((string) ($formStatusesByMonitoringAndForm[$monitoring->id.'-'.$formId] ?? 'pending'))) === 'completed');
                                                @endphp

                                                @if ($requiredFormIds->isEmpty())
                                                    {{ '—' }}
                                                @else
                                                    <div class="space-y-1">
                                                        @foreach ($requiredFormIds as $formId)
                                                            @php
                                                                $formName = $formNamesById[$formId] ?? null;
                                                                $formStatus = strtolower(trim((string) ($formStatusesByMonitoringAndForm[$monitoring->id.'-'.$formId] ?? 'pending')));
                                                                $formStatusClass = $formStatus === 'completed'
                                                                    ? 'text-green-600 font-semibold'
                                                                    : 'text-red-600 font-semibold';
                                                            @endphp

                                                            @if ($formName)
                                                                <div class="{{ $formStatusClass }}">{{ $formName }}</div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if ($requiredFormIds->isEmpty())
                                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-700">{{ __('N/A') }}</span>
                                                @elseif ($allRequiredFormsCompleted)
                                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700">{{ __('Completed') }}</span>
                                                @else
                                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700">{{ __('Pending') }}</span>
                                                @endif

                                                @if (!empty($latestFormNoteUpdatedAtByMonitoring[$monitoring->id] ?? null))
                                                    <div class="mt-1 text-xs text-gray-500">
                                                        {{ __('Last updated:') }} {{ $latestFormNoteUpdatedAtByMonitoring[$monitoring->id] }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @php
                                                    $submissionStatus = strtolower((string) ($monitoring->submission_status ?? 'pending'));
                                                @endphp

                                                @if ($submissionStatus === 'completed')
                                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700">{{ __('Completed') }}</span>
                                                @elseif ($submissionStatus === 'pending')
                                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700">{{ __('Pending') }}</span>
                                                @else
                                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-700">{{ ucfirst($submissionStatus) }}</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <div class="flex flex-col items-start gap-2">
                                                    @if ($allRequiredFormsCompleted)
                                                        <a href="{{ route('bookings.edit', ['monitoring' => $monitoring, 'show_submission_form' => 1]) }}" class="inline-flex items-center rounded-md bg-green-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                            {{ __('Start Submission Process') }}
                                                        </a>
                                                    @endif

                                                    @unless ($allRequiredFormsCompleted)
                                                        <a href="{{ route('bookings.edit', $monitoring) }}" class="inline-flex items-center rounded-md bg-gray-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                                            {{ __('Update') }}
                                                        </a>
                                                    @endunless
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="px-6 py-4 text-sm text-gray-500 text-center">{{ __('No monitoring records found.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                            <div class="mt-4">
                                {{ $monitorings->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
