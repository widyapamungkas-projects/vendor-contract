<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use App\Models\TourPackageLaCost;
use App\Models\TourPackageFixedCost;
use App\Models\TourPackageItinerary;
use App\Models\TourPackageHotel;
use App\Models\TourPackageRoomConfig;
use App\Models\HotelContract;
use App\Models\ActivityContract;
use App\Models\EntranceTicket;
use App\Models\RestaurantContract;
use App\Models\GuideLanguage;
use App\Models\TransportContract;
use Illuminate\Http\Request;

class TourPackageController extends Controller
{
    public function index()
    {
        $packages = TourPackage::latest()->paginate(20);
        return view('tour-packages.index', compact('packages'));
    }

    public function create()
    {
        $hotelContracts      = HotelContract::where('status','active')->orderBy('hotel_name')->get();
        $activityContracts   = ActivityContract::where('status','active')->orderBy('vendor_name')->get();
        $entranceTickets     = EntranceTicket::orderBy('attraction_name')->get();
        $restaurantContracts = RestaurantContract::where('status','active')->orderBy('vendor_name')->get();
        $guideLanguages      = GuideLanguage::with('services.tiers')->orderBy('destination')->orderBy('language_name')->get();
        $transportContracts  = TransportContract::where('status','active')->orderBy('vendor_name')->get();
        $package     = null;
        $packageCode = TourPackage::generateCode();

        return view('tour-packages.form', compact(
            'package','packageCode',
            'hotelContracts','activityContracts','entranceTickets',
            'restaurantContracts','guideLanguages','transportContracts'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'agent'       => 'required|string|max:200',
            'destination' => 'nullable|string|max:100',
            'pax'         => 'required|integer|min:1',
            'currency'    => 'required|string|max:10',
        ]);

        $package = TourPackage::create([
            'package_code'       => TourPackage::generateCode(),
            'agent'              => $request->agent,
            'destination'        => $request->destination,
            'duration'           => $request->duration,
            'period_from'        => $request->period_from ?: null,
            'period_to'          => $request->period_to ?: null,
            'pax'                => $request->pax,
            'actual_pax'         => $request->actual_pax ?: null,
            'currency'           => $request->currency,
            'margin'             => $request->margin ?? 0.85,
            'rate_usd'           => $request->rate_usd ?? 16000,
            'rate_myr'           => $request->rate_myr ?? 4.70,
            'rate_sgd'           => $request->rate_sgd ?? 1.35,
            'rate_eur'           => $request->rate_eur ?? 17000,
            'mw_price_per_dus'   => $request->mw_price_per_dus ?? 30000,
            'mw_bottles_per_day' => $request->mw_bottles_per_day ?? 2,
            'fg_garland_price'   => $request->fg_garland_price ?? 50000,
            'fg_flower_girl_price'=> $request->fg_flower_girl_price ?? 150000,
            'notes'              => $request->notes,
            'prop_inclusion'     => $request->prop_inclusion,
            'prop_exclusion'     => $request->prop_exclusion,
            'prop_tnc'           => $request->prop_tnc,
            'prop_itinerary'     => $request->prop_itinerary,
            'prop_menu'          => $request->prop_menu,
            'prop_custom_tables' => $request->prop_custom_tables,
            'prop_itin_briefs'   => $request->prop_itin_briefs,
            'created_by'         => auth()->user()->name ?? 'system',
        ]);

        $this->saveFixedCosts($package, $request);
        $this->saveHotels($package, $request);
        $this->saveRoomConfig($package, $request);

        return redirect()->route('tour-packages.edit', $package)
            ->with('success', 'Package created!')
            ->with('active_tab', 'la');
    }

    public function show(TourPackage $tourPackage)
    {
        $tourPackage->load(['laCosts','itinerary','hotels','roomConfig','fixedCosts']);
        return view('tour-packages.show', compact('tourPackage'));
    }

    public function edit(TourPackage $tourPackage)
    {
        $tourPackage->load(['laCosts','itinerary','hotels','roomConfig','fixedCosts']);
        $hotelContracts      = HotelContract::where('status','active')->orderBy('hotel_name')->get();
        $activityContracts   = ActivityContract::where('status','active')->orderBy('vendor_name')->get();
        $entranceTickets     = EntranceTicket::orderBy('attraction_name')->get();
        $restaurantContracts = RestaurantContract::where('status','active')->orderBy('vendor_name')->get();
        $guideLanguages      = GuideLanguage::with('services.tiers')->orderBy('language_name')->get();
        $transportContracts  = TransportContract::where('status','active')->orderBy('vendor_name')->get();
        $packageCode         = $tourPackage->package_code;
        $package             = $tourPackage;

        return view('tour-packages.form', compact(
            'package','packageCode',
            'hotelContracts','activityContracts','entranceTickets',
            'restaurantContracts','guideLanguages','transportContracts'
        ));
    }

    public function update(Request $request, TourPackage $tourPackage)
    {
        $request->validate([
            'agent'    => 'required|string|max:200',
            'pax'      => 'required|integer|min:1',
            'currency' => 'required|string|max:10',
        ]);

        $tourPackage->update([
            'agent'               => $request->agent,
            'destination'         => $request->destination,
            'duration'            => $request->duration,
            'period_from'         => $request->period_from ?: null,
            'period_to'           => $request->period_to ?: null,
            'pax'                 => $request->pax,
            'actual_pax'          => $request->actual_pax ?: null,
            'currency'            => $request->currency,
            'margin'              => $request->margin ?? 0.85,
            'rate_usd'            => $request->rate_usd ?? 16000,
            'rate_myr'            => $request->rate_myr ?? 4.70,
            'rate_sgd'            => $request->rate_sgd ?? 1.35,
            'rate_eur'            => $request->rate_eur ?? 17000,
            'mw_price_per_dus'    => $request->mw_price_per_dus ?? 30000,
            'mw_bottles_per_day'  => $request->mw_bottles_per_day ?? 2,
            'fg_garland_price'    => $request->fg_garland_price ?? 50000,
            'fg_flower_girl_price'=> $request->fg_flower_girl_price ?? 150000,
            'notes'               => $request->notes,
            'prop_inclusion'      => $request->prop_inclusion,
            'prop_exclusion'      => $request->prop_exclusion,
            'prop_tnc'            => $request->prop_tnc,
            'prop_itinerary'      => $request->prop_itinerary,
            'prop_menu'           => $request->prop_menu,
            'prop_custom_tables'  => $request->prop_custom_tables,
            'prop_itin_briefs'    => $request->prop_itin_briefs,
        ]);

        $this->saveFixedCosts($tourPackage, $request);

        // Rebuild Itinerary
        $tourPackage->itinerary()->delete();
        $this->saveItinerary($tourPackage, $request);

        // Rebuild Hotels
        $tourPackage->hotels()->delete();
        $this->saveHotels($tourPackage, $request);

        // Rebuild Room Config
        $tourPackage->roomConfig()->delete();
        $this->saveRoomConfig($tourPackage, $request);

        $activeTab = $request->input('active_tab', 'la');
        return redirect()->route('tour-packages.edit', $tourPackage)
            ->with('success', 'Package updated!')
            ->with('active_tab', $activeTab);
    }

    private function saveFixedCosts(TourPackage $package, Request $request): void
    {
        // Delete existing fixed costs
        $package->fixedCosts()->delete();

        // Transport rows (from JSON)
        $transportRows = json_decode($request->fc_transport_json ?? '[]', true) ?: [];
        foreach ($transportRows as $i => $row) {
            if (empty($row['price']) && empty($row['manual_amount'])) continue;
            TourPackageFixedCost::create([
                'tour_package_id' => $package->id,
                'cost_type'       => 'transport',
                'label'           => 'Transportation',
                'amount'          => ($row['price'] ?? 0) * ($row['qty'] ?? 1),
                'meta'            => json_encode($row),
                'sort_order'      => $i,
            ]);
        }

        // Transport manual (if provided directly)
        if ($request->filled('fc_transport_manual') && empty($transportRows)) {
            TourPackageFixedCost::create([
                'tour_package_id' => $package->id,
                'cost_type'       => 'transport',
                'label'           => 'Transportation',
                'amount'          => $request->fc_transport_manual,
                'meta'            => json_encode(['manual' => true]),
                'sort_order'      => 0,
            ]);
        }

        // Guide rows (from JSON)
        $guideRows = json_decode($request->fc_guide_json ?? '[]', true) ?: [];
        foreach ($guideRows as $i => $row) {
            if (empty($row['rate']) && empty($row['manual_amount'])) continue;
            TourPackageFixedCost::create([
                'tour_package_id' => $package->id,
                'cost_type'       => 'guide',
                'label'           => 'Guide Fee',
                'amount'          => ($row['rate'] ?? 0) * ($row['qty'] ?? 1),
                'meta'            => json_encode($row),
                'sort_order'      => $i,
            ]);
        }

        // Guide manual
        if ($request->filled('fc_guide_manual') && empty($guideRows)) {
            TourPackageFixedCost::create([
                'tour_package_id' => $package->id,
                'cost_type'       => 'guide',
                'label'           => 'Guide Fee',
                'amount'          => $request->fc_guide_manual,
                'meta'            => json_encode(['manual' => true]),
                'sort_order'      => 0,
            ]);
        }

        // Mineral Water
        $mwAmount = $request->fc_mw_amount ?? 0;
        if ($mwAmount > 0) {
            TourPackageFixedCost::create([
                'tour_package_id' => $package->id,
                'cost_type'       => 'mineral_water',
                'label'           => 'Mineral Water',
                'amount'          => $mwAmount,
                'meta'            => json_encode([
                    'price_per_dus'   => $request->mw_price_per_dus,
                    'bottles_per_day' => $request->mw_bottles_per_day,
                ]),
                'sort_order'      => 10,
            ]);
        }

        // Flower Garland
        $fgAmount = $request->fc_fg_amount ?? 0;
        if ($fgAmount > 0) {
            TourPackageFixedCost::create([
                'tour_package_id' => $package->id,
                'cost_type'       => 'flower_garland',
                'label'           => 'Flower Garland',
                'amount'          => $fgAmount,
                'meta'            => json_encode([
                    'garland_price'    => $request->fg_garland_price,
                    'flower_girl_price'=> $request->fg_flower_girl_price,
                ]),
                'sort_order'      => 11,
            ]);
        }

        // Guide Allowance
        $gaAmount = $request->fc_guide_allowance ?? 0;
        if ($gaAmount > 0) {
            TourPackageFixedCost::create([
                'tour_package_id' => $package->id,
                'cost_type'       => 'guide_allowance',
                'label'           => 'Guide Allowance',
                'amount'          => $gaAmount,
                'sort_order'      => 12,
            ]);
        }

        // Luggage Truck
        $ltAmount = $request->fc_luggage_truck ?? 0;
        if ($ltAmount > 0) {
            TourPackageFixedCost::create([
                'tour_package_id' => $package->id,
                'cost_type'       => 'luggage_truck',
                'label'           => 'Luggage Truck',
                'amount'          => $ltAmount,
                'sort_order'      => 13,
            ]);
        }
    }

    private function saveHotels(TourPackage $package, Request $request): void
    {
        if ($request->hotel_names) {
            foreach ($request->hotel_names as $i => $name) {
                if (!$name) continue;
                TourPackageHotel::create([
                    'tour_package_id'   => $package->id,
                    'hotel_contract_id' => $request->hotel_contract_ids[$i] ?: null,
                    'hotel_name'        => $name,
                    'room_type'         => $request->hotel_room_types[$i] ?? '',
                    'room_rate'         => $request->hotel_room_rates[$i] ?? 0,
                    'nights'            => $request->hotel_nights[$i] ?? 0,
                    'sort_order'        => $i,
                    'surcharge_nights'  => $request->hotel_surcharge_nights[$i] ?? 0,
                    'surcharge_rate'    => $request->hotel_surcharge_rates[$i]  ?? 0,
                    'meta'              => json_encode([
                        'extra_bed'  => (float)($request->hotel_extra_bed[$i]  ?? 0),
                        'hd_meeting' => (float)($request->hotel_hd_meeting[$i] ?? 0),
                        'fd_meeting' => (float)($request->hotel_fd_meeting[$i] ?? 0),
                        'dinner'     => (float)($request->hotel_dinner[$i]     ?? 0),
                    ]),
                ]);
            }
        }
    }

    private function saveRoomConfig(TourPackage $package, Request $request): void
    {
        // Parse nights from duration string e.g. "4D3N" → 3
        $duration = $request->duration ?? '';
        preg_match('/D(\d+)N/', $duration, $m);
        $nights = isset($m[1]) ? (int)$m[1] : 0;

        TourPackageRoomConfig::create([
            'tour_package_id' => $package->id,
            'sgl'             => $request->room_sgl    ?? 0,
            'twn'             => $request->room_twn    ?? 0,
            'trp'             => $request->room_trp    ?? 0,
            'foc'             => $request->room_foc    ?? 0,
            'margin_twn'      => $request->room_margin ?? 0,
            'nights'          => $nights,
            'margin_room'     => $request->margin_twn_flat ?? $request->margin_room ?? 25000,
            'hd_meeting'      => $request->hd_meeting    ?? 0,
            'fd_meeting'      => $request->fd_meeting    ?? 0,
            'dinner_cost'     => $request->dinner_cost   ?? 0,
            'with_tl'         => $request->with_tl       ?? 0,
        ]);
    }

    public function destroy(TourPackage $tourPackage)
    {
        $tourPackage->delete();
        return redirect()->route('tour-packages.index')->with('success', 'Package deleted.');
    }

    // AJAX: get hotel rooms
    public function apiHotelRooms(Request $request)
    {
        $hotel = HotelContract::with('roomRates')->find($request->id);
        if (!$hotel) return response()->json([]);
        return response()->json($hotel->roomRates->map(fn($r) => [
            'type'      => $r->room_type,
            'rate'      => $r->rate,
            'extra_bed' => $r->extra_bed_rate ?? 0,
        ]));
    }

    // API: transport drill-down
    // AJAX: get hotel surcharge for given period
    public function apiHotelSurcharge(Request $request)
    {
        $hotel = HotelContract::with('seasonSurcharges')->find($request->id);
        if (!$hotel) return response()->json(['surcharge_nights' => 0, 'surcharge_rate' => 0]);

        $from = $request->from ? \Carbon\Carbon::parse($request->from) : null;
        $to   = $request->to   ? \Carbon\Carbon::parse($request->to)   : null;
        if (!$from || !$to) return response()->json(['surcharge_nights' => 0, 'surcharge_rate' => 0]);

        $surchargeNights = 0;
        $surchargeRate   = 0;

        if ($hotel->seasonSurcharges) {
            foreach ($hotel->seasonSurcharges as $sp) {
                // Surcharge period: spFrom (inclusive) to spEnd (inclusive)
                // Hotel stay: $from (check-in, inclusive) to $to (check-out, exclusive for nights)
                // A night is surcharge if the night START date is within surcharge period
                // e.g. stay 1-4 Jul = nights on 1,2,3 Jul. Surcharge 1Jul-31Aug → all 3 nights
                // stay 30Jun-3Jul = nights on 30Jun,1Jul,2Jul. Surcharge 1Jul-31Aug → 2 nights
                $spFrom = \Carbon\Carbon::parse($sp->start_date);
                $spTo   = \Carbon\Carbon::parse($sp->end_date)->addDay(); // end is inclusive, make exclusive
                $overlapStart = $from->max($spFrom);
                $overlapEnd   = $to->min($spTo);  // $to = check-out = already exclusive
                if ($overlapEnd > $overlapStart) {
                    $nights = $overlapStart->diffInDays($overlapEnd);
                    $surchargeNights += $nights;
                    $surchargeRate    = $sp->surcharge_amount ?? 0;
                }
            }
        }

        return response()->json([
            'surcharge_nights' => $surchargeNights,
            'surcharge_rate'   => $surchargeRate,
        ]);
    }



    public function apiRestaurantMenus(Request $request)
    {
        $contractId = $request->input('contract_id', '');
        if (!$contractId) return response()->json([]);
        $menus = \App\Models\RestaurantMenu::where('restaurant_contract_id', $contractId)
            ->whereNull('deleted_at')
            ->get(['id','menu_name','serving_style','adult_price','child_price','menu_details','notes']);
        return response()->json($menus);
    }

    public function apiRestaurantMenuDetail(Request $request)
    {
        $menuId = $request->input('menu_id', '');
        if (!$menuId) return response()->json([]);
        $menu = \App\Models\RestaurantMenu::find($menuId);
        return response()->json($menu);
    }

    public function apiGenerateBrief(Request $request)
    {
        $name   = $request->input('name', '');
        $apiKey = env('ANTHROPIC_API_KEY', $request->input('api_key', ''));
        if (!$name || !$apiKey) return response()->json(['error' => 'Missing params'], 400);

        $prompt = 'Write exactly 2-3 sentences about: "' . $name . '" for a tour itinerary. '
		. 'Use more tour itinerary language. '
                . 'Be concise and engaging. No bullet points, flowing paragraph only.';

        $res = \Illuminate\Support\Facades\Http::withHeaders([
            'x-api-key'         => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model'      => 'claude-haiku-4-5-20251001',
            'max_tokens' => 300,
            'messages'   => [['role' => 'user', 'content' => $prompt]],
        ]);
        $data = $res->json();
        $text = $data['content'][0]['text'] ?? null;
        if (!$text) return response()->json(['error' => $data['error']['message'] ?? 'No content'], 500);
        return response()->json(['text' => trim($text)]);
    }
    public function apiTransportContracts()
    {
        $contracts = \DB::table('transport_contracts')
            ->whereNull('deleted_at')
            ->where('status','active')
            ->select('id','vendor_name','currency')
            ->orderBy('vendor_name')
            ->get();
        return response()->json($contracts);
    }

    public function apiTransportVehicles(Request $req)
    {
        $vehicles = \DB::table('transport_vehicles')
            ->where('transport_contract_id', $req->contract_id)
            ->whereNull('deleted_at')
            ->select('id','vehicle_name','category','capacity')
            ->get();
        return response()->json($vehicles);
    }

    public function apiTransportRates(Request $req)
    {
        $rates = \DB::table('transport_rates')
            ->where('transport_vehicle_id', $req->vehicle_id)
            ->whereNull('deleted_at')
            ->select('id','route_name','route_type','price')
            ->get();
        return response()->json($rates);
    }

    // API: guide drill-down
    public function apiGuideLanguages()
    {
        $langs = \DB::table('guide_languages')
            ->whereNull('deleted_at')
            ->select('id','language_name','destination','currency')
            ->orderBy('language_name')
            ->get();
        return response()->json($langs);
    }

    public function apiGuideServices(Request $req)
    {
        $services = \DB::table('guide_services')
            ->where('guide_language_id', $req->language_id)
            ->whereNull('deleted_at')
            ->select('id','service_name','service_type','unit_label')
            ->get();
        return response()->json($services);
    }

    public function apiGuideRate(Request $req)
    {
        $pax  = (int)$req->pax;
        $tier = \DB::table('guide_service_tiers')
            ->where('guide_service_id', $req->service_id)
            ->where('min_pax', '<=', $pax)
            ->where(function($q) use ($pax) {
                $q->whereNull('max_pax')->orWhere('max_pax','>=',$pax);
            })
            ->orderByDesc('min_pax')
            ->first();
        return response()->json(['rate' => $tier ? $tier->rate : 0]);
    }
    private function saveItinerary(TourPackage $package, Request $request): void
    {
        $days    = $request->input('itin_days',    []);
        $names   = $request->input('itin_names',   []);
        $types   = $request->input('itin_types',   []);
        $prices  = $request->input('itin_prices',  []);
        $refIds  = $request->input('itin_ref_ids', []);

        foreach ($days as $i => $day) {
            $name = trim($names[$i] ?? '');
            if ($name === '' && empty($refIds[$i])) continue;

            TourPackageItinerary::create([
                'tour_package_id' => $package->id,
                'day'             => (int) $day,
                'item_name'       => $name ?: ($types[$i] ?? 'item'),
                'item_type'       => $types[$i]  ?? 'manual',
                'ref_id'          => $refIds[$i] ?? null,
                'price_per_pax'   => (float) ($prices[$i] ?? 0),
                'sort_order'      => $i,
            ]);
        }
    }


}