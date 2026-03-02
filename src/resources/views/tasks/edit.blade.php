<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Task') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <header>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Update Task Details') }}</h3>
                    </header>

                    <form method="POST" action="{{ route('tasks.update', $task) }}" class="mt-6 space-y-6" data-confirm="Are you sure you want to update this task?">
                        @csrf
                        @method('patch')

                        <div>
                            <x-input-label for="task_name" :value="__('Task Name')" />
                            <x-text-input id="task_name" name="task_name" type="text" class="mt-1 block w-full" :value="old('task_name', $task->task_name)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('task_name')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Save') }}</x-primary-button>
                            <a href="{{ route('settings.index') }}#tasks-lists" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Back to Settings') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
