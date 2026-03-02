<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Client') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <header>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Update Client Details') }}</h3>
                    </header>

                    <form method="POST" action="{{ route('clients.update', $client) }}" class="mt-6 space-y-6" data-confirm="Are you sure you want to update this client?">
                        @csrf
                        @method('patch')

                        <div>
                            <x-input-label for="client_name" :value="__('Client Name')" />
                            <x-text-input id="client_name" name="client_name" type="text" class="mt-1 block w-full" :value="old('client_name', $client->client_name)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('client_name')" />
                        </div>

                        <div>
                            <x-input-label for="address" :value="__('Address')" />
                            <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $client->address)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('address')" />
                        </div>

                        <div>
                            <x-input-label for="contact_person" :value="__('Contact Person')" />
                            <x-text-input id="contact_person" name="contact_person" type="text" class="mt-1 block w-full" :value="old('contact_person', $client->contact_person)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('contact_person')" />
                        </div>

                        <div>
                            <x-input-label for="tin" :value="__('TIN')" />
                            <x-text-input id="tin" name="tin" type="text" class="mt-1 block w-full" :value="old('tin', $client->tin)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('tin')" />
                        </div>

                        <div>
                            <x-input-label for="tel_phone_number" :value="__('Tel/Phone Number')" />
                            <x-text-input id="tel_phone_number" name="tel_phone_number" type="text" class="mt-1 block w-full" :value="old('tel_phone_number', $client->tel_phone_number)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('tel_phone_number')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Save') }}</x-primary-button>
                            <a href="{{ route('settings.index', ['tab' => 'clients']) }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Back to Settings') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
