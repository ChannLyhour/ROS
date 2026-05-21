<?php

namespace App\Http\Controllers\admin;

use App\Models\Setting;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\SystemHelper;
use App\Helper\UploadImageHelper;

class SettingController extends Controller
{
    /**
     * Display a listing of settings.
     */
    public function index()
    {
        $this->authorize('viewAny', Setting::class);
        $defaults = [
            'business_name' => 'ROS POS',
            'currency_symbol' => '$',
            'tax_percentage' => '0',
            'enable_reservations' => '1',
            'auto_print_receipt' => '0',
            'currency_exchange_rate' => '4100',
        ];

        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $settings = array_merge($defaults, $settings);

        $currencies = Currency::where('is_active', true)->get();

        return view('admin.settings.index', compact('settings', 'currencies'));
    }

    /**
     * Store or update settings.
     */
    public function update(Request $request)
    {
        $this->authorize('update', new Setting());
        $data = $request->except('_token', '_method', 'business_logo');

        // Handle Business Logo
        if ($request->hasFile('business_logo')) {
            $oldLogo = Setting::where('key', 'business_logo')->first()?->value;
            $data['business_logo'] = UploadImageHelper::store($request->file('business_logo'), 'business', 'public', $oldLogo);
        }

        // Handle Business Favicon
        if ($request->hasFile('business_favicon')) {
            $oldFavicon = Setting::where('key', 'business_favicon')->first()?->value;
            $data['business_favicon'] = UploadImageHelper::store($request->file('business_favicon'), 'business', 'public', $oldFavicon);
        }

        // Define expected boolean fields that might be missing if unchecked
        $booleanFields = ['enable_reservations', 'auto_print_receipt'];
        
        foreach ($booleanFields as $field) {
            if (!$request->has($field)) {
                $data[$field] = '0';
            }
        }

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
            SystemHelper::forgetSetting($key);
        }

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}
