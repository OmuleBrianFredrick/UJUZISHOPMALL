<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;

class AdminPromotionController extends Controller
{
    private function authorizeAdmin(Request $request): void
    {
        abort_unless(($request->user()->role ?? null) === 'admin', 403);
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin($request);
        $promotions = Promotion::latest()->paginate(20);
        return view('admin.promotions.index', compact('promotions'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin($request);
        $data = $request->validate([
            'code' => ['required','string','max:50','alpha_dash','unique:promotions,code'],
            'type' => ['required','in:fixed,percentage'],
            'value' => ['required','numeric','gt:0'],
            'minimum_order' => ['nullable','numeric','min:0'],
            'usage_limit' => ['nullable','integer','min:1'],
            'starts_at' => ['nullable','date'],
            'ends_at' => ['nullable','date','after_or_equal:starts_at'],
            'active' => ['nullable','boolean'],
        ]);
        if ($data['type'] === 'percentage' && $data['value'] > 100) {
            return back()->withErrors(['value' => 'Percentage promotions cannot exceed 100%.'])->withInput();
        }
        $data['code'] = strtoupper($data['code']);
        $data['active'] = $request->boolean('active');
        Promotion::create($data);
        return back()->with('success', 'Promotion created successfully.');
    }

    public function toggle(Request $request, Promotion $promotion)
    {
        $this->authorizeAdmin($request);
        $promotion->update(['active' => !$promotion->active]);
        return back()->with('success', 'Promotion status updated.');
    }
}
