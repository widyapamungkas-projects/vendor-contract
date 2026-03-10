<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\GuideLanguage;
use App\Models\GuideService;
use App\Models\GuideServiceTier;
use Illuminate\Http\Request;

class GuideLanguageController extends Controller
{
    public function index(Request $request)
    {
        $destination = $request->get('destination');
        $destinations = GuideLanguage::whereNotNull('destination')
            ->where('destination', '!=', '')
            ->distinct()->orderBy('destination')->pluck('destination');

        $languages = GuideLanguage::with(['services.tiers'])
            ->withCount('services')
            ->when($destination, fn($q) => $q->where('destination', $destination))
            ->orderBy('destination')->orderBy('language_name')
            ->get();

        return view('guide-languages.index', compact('languages', 'destinations', 'destination'));
    }

    public function create()
    {
        $destinations = GuideLanguage::whereNotNull('destination')
            ->where('destination','!=','')->distinct()->pluck('destination');
        return view('guide-languages.create', compact('destinations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'language_name' => 'required|string|max:100',
            'destination'   => 'nullable|string|max:100',
            'currency'      => 'required|string|max:10',
            'notes'         => 'nullable|string',
        ]);
        $lang = GuideLanguage::create($request->only('language_name','destination','currency','notes'));
        return redirect()->route('guide-languages.show', $lang)->with('success', 'Guide language created.');
    }

    public function show(GuideLanguage $guideLanguage)
    {
        $guideLanguage->load(['services.tiers']);
        $serviceTypes = ['airport_transfer','full_day','half_day','overtime','tipping','package'];
        return view('guide-languages.show', compact('guideLanguage', 'serviceTypes'));
    }

    public function edit(GuideLanguage $guideLanguage)
    {
        $destinations = GuideLanguage::whereNotNull('destination')
            ->where('destination','!=','')->distinct()->pluck('destination');
        return view('guide-languages.edit', compact('guideLanguage','destinations'));
    }

    public function update(Request $request, GuideLanguage $guideLanguage)
    {
        $request->validate([
            'language_name' => 'required|string|max:100',
            'destination'   => 'nullable|string|max:100',
            'currency'      => 'required|string|max:10',
            'notes'         => 'nullable|string',
        ]);
        $guideLanguage->update($request->only('language_name','destination','currency','notes'));
        return redirect()->route('guide-languages.show', $guideLanguage)->with('success', 'Updated.');
    }

    public function destroy(GuideLanguage $guideLanguage)
    {
        $guideLanguage->delete();
        return redirect()->route('guide-languages.index')->with('success', 'Deleted.');
    }

    public function storeService(Request $request, GuideLanguage $guideLanguage)
    {
        $request->validate([
            'service_name'    => 'required|string|max:100',
            'service_type'    => 'required|string',
            'unit_label'      => 'nullable|string|max:50',
            'notes'           => 'nullable|string',
            'tiers'           => 'required|array|min:1',
            'tiers.*.min_pax' => 'required|integer|min:1',
            'tiers.*.max_pax' => 'nullable|integer',
            'tiers.*.rate'    => 'required|numeric|min:0',
        ]);

        $service = $guideLanguage->services()->create([
            'service_name' => $request->service_name,
            'service_type' => $request->service_type,
            'unit_label'   => $request->unit_label ?? 'per trip',
            'notes'        => $request->notes,
        ]);

        foreach ($request->tiers as $tier) {
            $service->tiers()->create([
                'min_pax' => $tier['min_pax'],
                'max_pax' => $tier['max_pax'] ?: null,
                'rate'    => $tier['rate'],
            ]);
        }

        return redirect()->route('guide-languages.show', $guideLanguage)->with('success', 'Service added.');
    }

    public function destroyService(GuideLanguage $guideLanguage, GuideService $service)
    {
        $service->delete();
        return redirect()->route('guide-languages.show', $guideLanguage)->with('success', 'Service removed.');
    }

    public function apiRate(Request $request)
    {
        $service = GuideService::with('tiers')->findOrFail($request->service_id);
        $rate = $service->getRateForPax((int) $request->pax);
        return response()->json(['rate' => $rate, 'service_name' => $service->service_name]);
    }

    public function updateInfo(Request $request, GuideLanguage $guideLanguage)
    {
        $request->validate([
            'language_name' => 'required|string|max:100',
            'destination'   => 'nullable|string|max:100',
            'currency'      => 'required|string|max:10',
            'notes'         => 'nullable|string',
        ]);
        $guideLanguage->update($request->only('language_name','destination','currency','notes'));
        return redirect()->route('guide-languages.show', $guideLanguage)->with('success', 'Info updated.');
    }

    public function updateService(Request $request, GuideLanguage $guideLanguage, GuideService $service)
    {
        $request->validate([
            'service_name' => 'required|string|max:100',
            'service_type' => 'required|string',
            'unit_label'   => 'nullable|string|max:50',
            'tiers'        => 'required|array|min:1',
            'tiers.*.id'   => 'nullable',
            'tiers.*.min_pax' => 'required|integer|min:1',
            'tiers.*.max_pax' => 'nullable|integer',
            'tiers.*.rate'    => 'required|numeric|min:0',
        ]);

        $service->update([
            'service_name' => $request->service_name,
            'service_type' => $request->service_type,
            'unit_label'   => $request->unit_label ?? 'per trip',
        ]);

        // Delete old tiers and recreate
        $service->tiers()->delete();
        foreach ($request->tiers as $tier) {
            $service->tiers()->create([
                'min_pax' => $tier['min_pax'],
                'max_pax' => $tier['max_pax'] ?: null,
                'rate'    => $tier['rate'],
            ]);
        }

        return redirect()->route('guide-languages.show', $guideLanguage)->with('success', 'Service updated.');
    }

    public function destroyTier(GuideLanguage $guideLanguage, GuideServiceTier $tier)
    {
        $tier->delete();
        return redirect()->route('guide-languages.show', $guideLanguage)->with('success', 'Tier removed.');
    }

    public function storeTier(Request $request, GuideLanguage $guideLanguage, GuideService $service)
    {
        $request->validate([
            'min_pax' => 'required|integer|min:1',
            'max_pax' => 'nullable|integer',
            'rate'    => 'required|numeric|min:0',
        ]);
        $service->tiers()->create([
            'min_pax' => $request->min_pax,
            'max_pax' => $request->max_pax ?: null,
            'rate'    => $request->rate,
        ]);
        return redirect()->route('guide-languages.show', $guideLanguage)->with('success', 'Tier added.');
    }
}