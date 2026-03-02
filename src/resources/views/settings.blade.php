<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6" x-data="{ activeMenu: @js(in_array(request('tab'), ['users', 'clients', 'tasks', 'forms']) ? request('tab') : 'users'), showClientForm: @js($errors->has('client_name') || $errors->has('contact_person') || $errors->has('address') || $errors->has('tin') || $errors->has('tel_phone_number')), showTaskForm: @js($errors->has('task_name')), showFormEntry: @js($errors->has('form_name')) }">
                    <div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex flex-wrap items-center gap-6 text-sm">
                                <a href="{{ route('settings.index', ['tab' => 'users']) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">{{ __('User Settings') }}</a>
                                <a href="{{ route('settings.index', ['tab' => 'clients']) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">{{ __('Clients List') }}</a>
                                <a href="{{ route('settings.index', ['tab' => 'tasks']) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">{{ __('Tasks List') }}</a>
                                <a href="{{ route('settings.index', ['tab' => 'forms']) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">{{ __('Forms List') }}</a>
                            </div>
                        </div>
                    </div>

                    <div x-show="activeMenu === 'users'">
                        <div class="flex items-center justify-between gap-4">
                            @can('manage-users')
                                <a href="{{ route('users.create') }}" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                    {{ __('Register New User') }}
                                </a>
                            @endcan
                        </div>
                    </div>

                    <div x-show="activeMenu === 'users'">
                        @if (session('status') === 'user-created')
                            <p class="mb-3 text-sm text-green-600">{{ __('New user registered successfully.') }}</p>
                        @endif

                        @if (session('status') === 'user-updated')
                            <p class="mb-3 text-sm text-green-600">{{ __('User details updated successfully.') }}</p>
                        @endif

                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ID') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Name') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Email') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Role') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Registered At') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($users as $user)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($user->role) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($user->status) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->created_at?->format('F d, Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @can('manage-users')
                                                    <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center rounded-md bg-gray-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                                        {{ __('Edit') }}
                                                    </a>
                                                @else
                                                    <span class="text-gray-400">{{ __('—') }}</span>
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-6 py-4 text-sm text-gray-500 text-center">{{ __('No registered users found.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $users->appends(['tab' => 'users', 'clients_page' => request('clients_page'), 'tasks_page' => request('tasks_page'), 'forms_page' => request('forms_page')])->links() }}
                        </div>
                    </div>

                    <div id="clients-lists" class="space-y-4" x-show="activeMenu === 'clients'">
                        @if (session('status') === 'client-created')
                            <p class="text-sm text-green-600">{{ __('Client added successfully.') }}</p>
                        @endif

                        @if (session('status') === 'client-updated')
                            <p class="text-sm text-green-600">{{ __('Client updated successfully.') }}</p>
                        @endif
                        <button type="button" x-on:click="showClientForm = true" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            {{ __('ADD CLIENT') }}
                        </button>

                            
                            <form method="POST" action="{{ route('clients.store') }}" class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2" x-show="showClientForm" data-confirm="Are you sure you want to save this client?">
                                @csrf

                                <div>
                                    <x-input-label for="client_name" :value="__('Client Name')" />
                                    <x-text-input id="client_name" name="client_name" type="text" class="mt-1 block w-full" :value="old('client_name')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('client_name')" />
                                </div>

                                <div>
                                    <x-input-label for="address" :value="__('Address')" />
                                    <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                                </div>

                                <div>
                                    <x-input-label for="contact_person" :value="__('Contact Person')" />
                                    <x-text-input id="contact_person" name="contact_person" type="text" class="mt-1 block w-full" :value="old('contact_person')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('contact_person')" />
                                </div>

                                <div>
                                    <x-input-label for="tin" :value="__('TIN')" />
                                    <x-text-input id="tin" name="tin" type="text" class="mt-1 block w-full" :value="old('tin')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('tin')" />
                                </div>

                                <div>
                                    <x-input-label for="tel_phone_number" :value="__('Tel/Phone Number')" />
                                    <x-text-input id="tel_phone_number" name="tel_phone_number" type="text" class="mt-1 block w-full" :value="old('tel_phone_number')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('tel_phone_number')" />
                                </div>

                                <div class="md:col-span-2 flex items-center gap-3">
                                    <x-primary-button>{{ __('Save Client') }}</x-primary-button>
                                    <button type="button" x-on:click="showClientForm = false" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        {{ __('Cancel') }}
                                    </button>
                                </div>
                            </form>
                        

                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ID') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Client Name') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Address') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Contact Person') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('TIN') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Tel/Phone Number') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($clients as $client)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $client->id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $client->client_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $client->address }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $client->contact_person }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $client->tin }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $client->tel_phone_number }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <a href="{{ route('clients.edit', $client) }}" class="inline-flex items-center rounded-md bg-gray-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                                    {{ __('Edit') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-6 py-4 text-sm text-gray-500 text-center">{{ __('No clients found.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $clients->appends(['tab' => 'clients', 'users_page' => request('users_page'), 'tasks_page' => request('tasks_page'), 'forms_page' => request('forms_page')])->links() }}
                        </div>
                    </div>

                    <div id="tasks-lists" class="space-y-4" x-show="activeMenu === 'tasks'">
                        @if (session('status') === 'task-created')
                            <p class="text-sm text-green-600">{{ __('Task added successfully.') }}</p>
                        @endif

                        @if (session('status') === 'task-updated')
                            <p class="text-sm text-green-600">{{ __('Task updated successfully.') }}</p>
                        @endif

                        <button type="button" x-on:click="showTaskForm = true" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            {{ __('ADD TASK') }}
                        </button>

                        <form method="POST" action="{{ route('tasks.store') }}" class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2" x-show="showTaskForm" data-confirm="Are you sure you want to save this task?">
                            @csrf

                            <div>
                                <x-input-label for="task_name" :value="__('Task Name')" />
                                <x-text-input id="task_name" name="task_name" type="text" class="mt-1 block w-full" :value="old('task_name')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('task_name')" />
                            </div>

                            <div class="md:col-span-2 flex items-center gap-3">
                                <x-primary-button>{{ __('Save Task') }}</x-primary-button>
                                <button type="button" x-on:click="showTaskForm = false" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    {{ __('Cancel') }}
                                </button>
                            </div>
                        </form>

                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ID') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Task Name') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($tasks as $task)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $task->id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $task->task_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <a href="{{ route('tasks.edit', $task) }}" class="inline-flex items-center rounded-md bg-gray-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                                    {{ __('Edit') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-sm text-gray-500 text-center">{{ __('No tasks found.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $tasks->appends(['tab' => 'tasks', 'users_page' => request('users_page'), 'clients_page' => request('clients_page'), 'forms_page' => request('forms_page')])->links() }}
                        </div>
                    </div>

                    <div id="forms-lists" class="space-y-4" x-show="activeMenu === 'forms'">
                        @if (session('status') === 'form-created')
                            <p class="text-sm text-green-600">{{ __('Form added successfully.') }}</p>
                        @endif

                        @if (session('status') === 'form-updated')
                            <p class="text-sm text-green-600">{{ __('Form updated successfully.') }}</p>
                        @endif

                        <button type="button" x-on:click="showFormEntry = true" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            {{ __('ADD FORM') }}
                        </button>

                        <form method="POST" action="{{ route('forms.store') }}" class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2" x-show="showFormEntry" data-confirm="Are you sure you want to save this form?">
                            @csrf

                            <div>
                                <x-input-label for="form_name" :value="__('Form Name')" />
                                <x-text-input id="form_name" name="form_name" type="text" class="mt-1 block w-full" :value="old('form_name')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('form_name')" />
                            </div>

                            <div class="md:col-span-2 flex items-center gap-3">
                                <x-primary-button>{{ __('Save Form') }}</x-primary-button>
                                <button type="button" x-on:click="showFormEntry = false" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    {{ __('Cancel') }}
                                </button>
                            </div>
                        </form>

                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ID') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Form Name') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($forms as $form)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $form->id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $form->form_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <a href="{{ route('forms.edit', $form) }}" class="inline-flex items-center rounded-md bg-gray-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                                    {{ __('Edit') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-sm text-gray-500 text-center">{{ __('No forms found.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $forms->appends(['tab' => 'forms', 'users_page' => request('users_page'), 'clients_page' => request('clients_page'), 'tasks_page' => request('tasks_page')])->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
