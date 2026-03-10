<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\HotelContract;
use App\Models\TransportContract;
use App\Models\ActivityContract;
use App\Models\EntranceTicket;
use App\Models\RestaurantContract;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $modules = [
            "hotel" => [
                "label" => "Hotel",
                "icon"  => "fa-hotel",
                "color" => "blue",
                "route" => "hotel-contracts.index",
                "stats" => $this->getStats(HotelContract::query()),
            ],
            "transport" => [
                "label" => "Transport",
                "icon"  => "fa-bus",
                "color" => "yellow",
                "route" => "transport-contracts.index",
                "stats" => $this->getStats(TransportContract::query()),
            ],
            "activity" => [
                "label" => "Activity",
                "icon"  => "fa-person-hiking",
                "color" => "green",
                "route" => "activity-contracts.index",
                "stats" => $this->getStats(ActivityContract::query()),
            ],
            "entrance" => [
                "label" => "Entrance Fee",
                "icon"  => "fa-ticket",
                "color" => "purple",
                "route" => "entrance-contracts.index",
                "stats" => $this->getEntranceStats(),
            ],
            "restaurant" => [
                "label" => "Restaurant",
                "icon"  => "fa-utensils",
                "color" => "red",
                "route" => "restaurant-contracts.index",
                "stats" => $this->getStats(RestaurantContract::query()),
            ],
        ];

        return view("dashboard", compact("modules"));
    }

    private function getStats($query): array
    {
        $today    = Carbon::today();
        $expiring = Carbon::today()->addDays(30);

        return [
            "active"        => (clone $query)->where("status", "active")->count(),
            "expiring_soon" => (clone $query)->where("status", "expiring_soon")->count(),
            "expired"       => (clone $query)->where("status", "expired")->count(),
            "total"         => (clone $query)->count(),
        ];
    }

    private function getEntranceStats(): array
    {
        $total = EntranceTicket::count();
        return [
            "active"        => $total,
            "expiring_soon" => 0,
            "expired"       => 0,
            "total"         => $total,
        ];
    }
}
