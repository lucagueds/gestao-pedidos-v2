<x-guest-layout>
    <div class="mb-4 text-center">
        <h1 class="text-2xl font-bold text-text-dark">Crie sua conta</h1>
        <p class="text-sm text-text-light">É rápido e fácil.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" value="Nome Completo" />
            <x-text-input id="name" class="block w-full mt-1" type="text" name="name" :value="old('name')" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" value="E-mail" />
            <x-text-input id="email" class="block w-full mt-1" type="email" name="email" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Senha" />
            <x-text-input id="password" class="block w-full mt-1" type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmar Senha" />
            <x-text-input id="password_confirmation" class="block w-full mt-1" type="password" name="password_confirmation" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <button type="submit" class="w-full py-2 font-semibold text-white uppercase tracking-widest text-xs rounded-md bg-tiny-blue hover:bg-tiny-blue-hover">
                Registrar
            </button>
        </div>

        <div class="mt-4 text-sm text-center text-text-light">
            Já possui uma conta?
            <a href="{{ route('login') }}" class="font-semibold underline text-tiny-blue hover:text-tiny-blue-hover">
                Faça o login.
            </a>
        </div>
    </form>
</x-guest-layout>
