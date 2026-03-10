<?php
namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\EntranceTicket;
use Illuminate\Http\Request;

class EntranceContractController extends Controller {
    public function index(Request $request) {
        $query = EntranceTicket::query();
        if (!empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('attraction_name','like','%'.$request->search.'%')
                  ->orWhere('destination','like','%'.$request->search.'%')
                  ->orWhere('area','like','%'.$request->search.'%');
            });
        }
        if (!empty($request->attraction_type)) $query->where('attraction_type',$request->attraction_type);
        if (!empty($request->destination)) $query->where('destination','like','%'.$request->destination.'%');
        if (!empty($request->area)) $query->where('area','like','%'.$request->area.'%');
        $tickets = $query->orderBy('destination')->orderBy('attraction_name')->paginate(20);
        return view('entrance-contracts.index', compact('tickets'));
    }
    public function create() { return view('entrance-contracts.create'); }
    public function store(Request $request) {
        $request->validate([
            'attraction_type'=>'required|string','attraction_name'=>'required|string|max:255',
            'destination'=>'nullable|string|max:100','area'=>'nullable|string|max:100',
            'currency'=>'required|string|max:10','adult_price'=>'required|numeric|min:0',
            'child_price'=>'required|numeric|min:0','infant_price'=>'nullable|numeric|min:0','notes'=>'nullable|string',
        ]);
        EntranceTicket::create($request->only(['attraction_type','attraction_name','destination','area','currency','adult_price','child_price','infant_price','notes']));
        return redirect()->route('entrance-contracts.index')->with('success','Tiket masuk berhasil ditambahkan!');
    }
    public function edit(EntranceTicket $entranceContract) { return view('entrance-contracts.edit',['ticket'=>$entranceContract]); }
    public function update(Request $request, EntranceTicket $entranceContract) {
        $request->validate([
            'attraction_type'=>'required|string','attraction_name'=>'required|string|max:255',
            'destination'=>'nullable|string|max:100','area'=>'nullable|string|max:100',
            'currency'=>'required|string|max:10','adult_price'=>'required|numeric|min:0',
            'child_price'=>'required|numeric|min:0','infant_price'=>'nullable|numeric|min:0','notes'=>'nullable|string',
        ]);
        $entranceContract->update($request->only(['attraction_type','attraction_name','destination','area','currency','adult_price','child_price','infant_price','notes']));
        return redirect()->route('entrance-contracts.index')->with('success','Tiket masuk berhasil diupdate!');
    }
    public function destroy(EntranceTicket $entranceContract) {
        $entranceContract->delete();
        return redirect()->route('entrance-contracts.index')->with('success','Tiket masuk berhasil dihapus.');
    }
}
