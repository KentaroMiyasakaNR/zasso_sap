<x-guest-layout>
    <div style="display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f7fafc;">
        <div style="background-color: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 400px;">
            <h2 style="text-align: center; font-size: 24px; color: #333; margin-bottom: 30px;">新規ユーザー登録</h2>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div style="margin-bottom: 20px;">
                    <x-input-label for="name" :value="__('名前')" style="display: block; margin-bottom: 5px; color: #333;" />
                    <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" />
                    <x-input-error :messages="$errors->get('name')" style="color: #e3342f; margin-top: 5px;" />
                </div>

                <!-- Email Address -->
                <div style="margin-bottom: 20px;">
                    <x-input-label for="email" :value="__('メールアドレス')" style="display: block; margin-bottom: 5px; color: #333;" />
                    <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" />
                    <x-input-error :messages="$errors->get('email')" style="color: #e3342f; margin-top: 5px;" />
                </div>

                <!-- Password -->
                <div style="margin-bottom: 20px;">
                    <x-input-label for="password" :value="__('パスワード')" style="display: block; margin-bottom: 5px; color: #333;" />
                    <x-text-input id="password" type="password" name="password" required autocomplete="new-password" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" />
                    <x-input-error :messages="$errors->get('password')" style="color: #e3342f; margin-top: 5px;" />
                </div>

                <!-- Confirm Password -->
                <div style="margin-bottom: 20px;">
                    <x-input-label for="password_confirmation" :value="__('パスワード（確認）')" style="display: block; margin-bottom: 5px; color: #333;" />
                    <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" />
                    <x-input-error :messages="$errors->get('password_confirmation')" style="color: #e3342f; margin-top: 5px;" />
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 30px;">
                    <a href="{{ route('login') }}" style="color: #3490dc; text-decoration: none;">
                        {{ __('既にアカウントをお持ちですか？') }}
                    </a>

                    <button type="submit" style="display: inline-block; padding: 12px 24px; background-color: #4CAF50; color: white; text-decoration: none; text-align: center; border-radius: 25px; font-weight: bold; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; cursor: pointer;">
                        {{ __('登録') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
