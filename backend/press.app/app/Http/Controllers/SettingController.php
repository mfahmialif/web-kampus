<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    public function general()
    {
        return response()->json($this->generalPayload());
    }

    public function updateGeneral(Request $request)
    {
        $data = $request->validate([
            'system_name' => 'required|string|max:120',
            'accent_color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'favicon' => 'nullable|file|mimes:ico,png,jpg,jpeg,svg,webp|max:1024',
        ]);

        AppSetting::setValue('system_name', $data['system_name']);
        AppSetting::setValue('accent_color', strtolower($data['accent_color']));

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('settings', 'public');
            AppSetting::setValue('favicon_path', $path);
            AppSetting::setValue('favicon_url', Storage::disk('public')->url($path));
        }

        return response()->json($this->generalPayload());
    }

    public function email()
    {
        return response()->json([
            'smtp_enabled' => AppSetting::getValue('smtp_enabled', false),
            'smtp_host' => AppSetting::getValue('smtp_host', ''),
            'smtp_port' => AppSetting::getValue('smtp_port', 587),
            'smtp_username' => AppSetting::getValue('smtp_username', ''),
            'smtp_password' => AppSetting::getValue('smtp_password', ''),
            'smtp_encryption' => AppSetting::getValue('smtp_encryption', 'tls'),
            'smtp_from_address' => AppSetting::getValue('smtp_from_address', ''),
            'smtp_from_name' => AppSetting::getValue('smtp_from_name', AppSetting::getValue('system_name', 'SPI UII Dalwa')),
            'email_registration_default_role_id' => AppSetting::getValue('email_registration_default_role_id'),
            'roles' => $this->dashboardRoles(),
        ]);
    }

    public function updateEmail(Request $request)
    {
        $data = $request->validate([
            'smtp_enabled' => 'required|boolean',
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'required|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:500',
            'smtp_encryption' => ['nullable', Rule::in(['', 'tls', 'ssl'])],
            'smtp_from_address' => 'required|email|max:255',
            'smtp_from_name' => 'required|string|max:255',
            'email_registration_default_role_id' => [
                'nullable',
                Rule::exists('roles', 'id')->where(fn ($query) => $query->where('status', 'Active')),
            ],
        ]);

        AppSetting::setValue('smtp_enabled', $data['smtp_enabled'], 'boolean');
        AppSetting::setValue('smtp_host', $data['smtp_host'] ?? '');
        AppSetting::setValue('smtp_port', $data['smtp_port'], 'integer');
        AppSetting::setValue('smtp_username', $data['smtp_username'] ?? '');
        AppSetting::setValue('smtp_password', $data['smtp_password'] ?? '');
        AppSetting::setValue('smtp_encryption', $data['smtp_encryption'] ?? '');
        AppSetting::setValue('smtp_from_address', $data['smtp_from_address']);
        AppSetting::setValue('smtp_from_name', $data['smtp_from_name']);
        AppSetting::setValue('email_registration_default_role_id', $data['email_registration_default_role_id'] ?? null, 'integer');

        return $this->email();
    }

    public function googleLogin()
    {
        return response()->json([
            'google_login_enabled' => AppSetting::getValue('google_login_enabled', false),
            'google_client_id' => AppSetting::getValue('google_client_id', ''),
            'google_client_secret' => AppSetting::getValue('google_client_secret', ''),
            'google_hosted_domain' => AppSetting::getValue('google_hosted_domain', ''),
            'google_auto_create_users' => AppSetting::getValue('google_auto_create_users', false),
            'google_default_role_id' => AppSetting::getValue('google_default_role_id'),
            'roles' => Role::query()
                ->where('status', 'Active')
                ->whereIn('name', ['Admin', 'Operator', 'Penulis', 'Kepala Penulis'])
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function updateGoogleLogin(Request $request)
    {
        $data = $request->validate([
            'google_login_enabled' => 'required|boolean',
            'google_client_id' => 'nullable|string|max:500',
            'google_client_secret' => 'nullable|string|max:500',
            'google_hosted_domain' => 'nullable|string|max:255',
            'google_auto_create_users' => 'required|boolean',
            'google_default_role_id' => [
                'nullable',
                Rule::exists('roles', 'id')->where(fn ($query) => $query->where('status', 'Active')),
            ],
        ]);

        AppSetting::setValue('google_login_enabled', $data['google_login_enabled'], 'boolean');
        AppSetting::setValue('google_client_id', $data['google_client_id'] ?? '');
        AppSetting::setValue('google_client_secret', $data['google_client_secret'] ?? '');
        AppSetting::setValue('google_hosted_domain', $data['google_hosted_domain'] ?? '');
        AppSetting::setValue('google_auto_create_users', $data['google_auto_create_users'], 'boolean');
        AppSetting::setValue('google_default_role_id', $data['google_default_role_id'] ?? null, 'integer');

        return $this->googleLogin();
    }

    private function generalPayload(): array
    {
        return [
            'system_name' => AppSetting::getValue('system_name', 'SPI UII Dalwa'),
            'accent_color' => AppSetting::getValue('accent_color', '#78350f'),
            'favicon_url' => AppSetting::getValue('favicon_url', ''),
        ];
    }

    private function dashboardRoles()
    {
        return Role::query()
            ->where('status', 'Active')
            ->whereIn('name', ['Admin', 'Operator', 'Penulis', 'Kepala Penulis'])
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
