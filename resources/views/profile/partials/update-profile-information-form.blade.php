<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('معلومات الملف الشخصي') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("قم بتحديث معلومات ملفك الشخصي وعنوان بريدك الإلكتروني وتفاصيل الحساب البنكي.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="first_name" :value="__('الاسم الأول')" />
                <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name', $user->first_name)" required autofocus autocomplete="given-name" />
                <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
            </div>

            <div>
                <x-input-label for="last_name" :value="__('اسم العائلة')" />
                <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $user->last_name)" required autocomplete="family-name" />
                <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
            </div>
        </div>

        <div>
            <x-input-label for="email" :value="__('البريد الإلكتروني')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="phone" :value="__('رقم الجوال')" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" required autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        @if($user->hasRole('expert'))
        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('تفاصيل الحساب البنكي (للخبراء)') }}</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="bank_name" :value="__('اسم البنك')" />
                    <x-text-input id="bank_name" name="bank_name" type="text" class="mt-1 block w-full" :value="old('bank_name', $user->bank_name)" />
                    <x-input-error class="mt-2" :messages="$errors->get('bank_name')" />
                </div>

                <div>
                    <x-input-label for="iban" :value="__('IBAN')" />
                    <x-text-input id="iban" name="iban" type="text" class="mt-1 block w-full" :value="old('iban', $user->iban)" />
                    <x-input-error class="mt-2" :messages="$errors->get('iban')" />
                </div>

                <div>
                    <x-input-label for="account_number" :value="__('رقم الحساب')" />
                    <x-text-input id="account_number" name="account_number" type="text" class="mt-1 block w-full" :value="old('account_number', $user->account_number)" />
                    <x-input-error class="mt-2" :messages="$errors->get('account_number')" />
                </div>

                <div>
                    <x-input-label for="swift" :value="__('رمز السويفت (SWIFT)')" />
                    <x-text-input id="swift" name="swift" type="text" class="mt-1 block w-full" :value="old('swift', $user->swift)" />
                    <x-input-error class="mt-2" :messages="$errors->get('swift')" />
                </div>
            </div>

            <div class="mt-4">
                <x-input-label for="expertise" :value="__('التخصص')" />
                <x-text-input id="expertise" name="expertise" type="text" class="mt-1 block w-full" :value="old('expertise', $user->expertise)" />
                <x-input-error class="mt-2" :messages="$errors->get('expertise')" />
            </div>

            <div class="mt-4">
                <x-input-label for="experience" :value="__('الخبرة')" />
                <textarea id="experience" name="experience" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" rows="3">{{ old('experience', $user->experience) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('experience')" />
            </div>
        </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('حفظ') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('تم الحفظ.') }}</p>
            @endif
        </div>
    </form>
</section>
