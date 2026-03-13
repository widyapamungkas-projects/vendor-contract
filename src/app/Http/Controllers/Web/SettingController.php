<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::allKeyed();
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $fields = [
            'company_name', 'company_tagline', 'company_address',
            'company_phone', 'company_email', 'company_website',
            'proposal_footer_text',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                Setting::set($field, $request->input($field));
            }
        }

        // Handle brand_logo upload
        if ($request->hasFile('brand_logo')) {
            $old = Setting::get('brand_logo');
            if ($old) Storage::disk('public')->delete($old);
            $path = $request->file('brand_logo')->store('logos', 'public');
            Setting::set('brand_logo', $path);
        }

        // Handle company_logo upload
        if ($request->hasFile('company_logo')) {
            $old = Setting::get('company_logo');
            if ($old) Storage::disk('public')->delete($old);
            $path = $request->file('company_logo')->store('logos', 'public');
            Setting::set('company_logo', $path);
        }

        return redirect()->route('settings.index')->with('success', 'Settings saved successfully.');
    }

    public function deleteLogo(Request $request, string $type)
    {
        if (!in_array($type, ['brand_logo', 'company_logo'])) abort(404);
        $old = Setting::get($type);
        if ($old) Storage::disk('public')->delete($old);
        Setting::set($type, null);
        return redirect()->route('settings.index')->with('success', ucfirst(str_replace('_', ' ', $type)) . ' removed.');
    }
}