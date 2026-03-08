<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\EntranceTicket;
use Illuminate\Http\Request;

class EntranceContractController extends Controller {

    public function index(Request $request) {
        $query = EntranceTicket::query();
        if (!empty($request->search)) {
            $query->where('attraction_name', 'like', '%'.$request->search.'%');
        }
        if (!empty($request->attraction_type)) {
            $query->where('attraction_type', $request->attraction_type);
        }
        $tickets = $query->orderBy('attraction_name')->paginate(20);
        return view('entrance-contracts.index', compact('tickets'));
    }

    public function create() {
        return view('entrance-contracts.create');
    }

    public function store(Request $request) {
        $request->validate([
            'attraction_type' => 'required|string',
            'attraction_name' => 'required|string|max:255',
            'currency'        => 'required|string|max:10',
            'adult_price'     => 'required|numeric|min:0',
            'child_price'     => 'required|numeric|min:0',
            'infant_price'    => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string',
        ]);

        EntranceTicket::create($request->only([
            'attraction_type','attraction_name','currency',
            'adult_price','child_price','infant_price','notes'
        ]));

        return redirect()->route('entrance-contracts.index')
            ->with('success', 'Tiket masuk berhasil ditambahkan!');
    }

    public function edit(EntranceTicket $entranceContract) {
        return view('entrance-contracts.edit', ['ticket' => $entranceContract]);
    }

    public function update(Request $request, EntranceTicket $entranceContract) {
        $request->validate([
            'attraction_type' => 'required|string',
            'attraction_name' => 'required|string|max:255',
            'currency'        => 'required|string|max:10',
            'adult_price'     => 'required|numeric|min:0',
            'child_price'     => 'required|numeric|min:0',
            'infant_price'    => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string',
        ]);

        $entranceContract->update($request->only([
            'attraction_type','attraction_name','currency',
            'adult_price','child_price','infant_price','notes'
        ]));

        return redirect()->route('entrance-contracts.index')
            ->with('success', 'Tiket masuk berhasil diupdate!');
    }

    public function destroy(EntranceTicket $entranceContract) {
        $entranceContract->delete();
        return redirect()->route('entrance-contracts.index')
            ->with('success', 'Tiket masuk berhasil dihapus.');
    }
}
