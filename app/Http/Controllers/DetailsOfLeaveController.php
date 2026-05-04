<?php

namespace App\Http\Controllers;

use App\Models\LeaveDetailGroup;
use App\Models\LeaveDetailItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DetailsOfLeaveController extends Controller
{
    /* ── Main page ── */
    public function index()
    {
        $groups = LeaveDetailGroup::with(['items' => fn($q) => $q->orderBy('sort_order')])
                                  ->orderBy('sort_order')
                                  ->get();
        return view('management.details-of-leave', compact('groups'));
    }

    /* ══════════════════════════════
       GROUPS
    ══════════════════════════════ */
    public function storeGroup(Request $request)
    {
        $v = Validator::make($request->all(), [
            'group_name' => 'required|string|max:120',
            'color'      => 'nullable|string|max:20',
        ]);
        if ($v->fails()) {
            return response()->json(['success' => false, 'message' => $v->errors()->first()], 422);
        }

        $max = LeaveDetailGroup::max('sort_order') ?? -1;
        $group = LeaveDetailGroup::create([
            'group_name' => $request->group_name,
            'color'      => $request->color ?? '#6366f1',
            'sort_order' => $max + 1,
        ]);

        return response()->json(['success' => true, 'data' => $group, 'message' => 'Group added successfully.']);
    }

    public function updateGroup(Request $request, $id)
    {
        $group = LeaveDetailGroup::findOrFail($id);
        $v = Validator::make($request->all(), [
            'group_name' => 'required|string|max:120',
            'color'      => 'nullable|string|max:20',
        ]);
        if ($v->fails()) {
            return response()->json(['success' => false, 'message' => $v->errors()->first()], 422);
        }

        $group->update([
            'group_name' => $request->group_name,
            'color'      => $request->color ?? $group->color,
        ]);

        return response()->json(['success' => true, 'data' => $group, 'message' => 'Group updated successfully.']);
    }

    public function destroyGroup($id)
    {
        $group = LeaveDetailGroup::findOrFail($id);
        // Items deleted via cascade (defined in migration)
        $group->delete();
        return response()->json(['success' => true, 'message' => 'Group and its options deleted.']);
    }

    /* ══════════════════════════════
       ITEMS
    ══════════════════════════════ */
    public function storeItem(Request $request, $groupId)
    {
        LeaveDetailGroup::findOrFail($groupId); // 404 if group missing

        $v = Validator::make($request->all(), [
            'label'          => 'required|string|max:200',
            'has_text_input' => 'nullable|boolean',
        ]);
        if ($v->fails()) {
            return response()->json(['success' => false, 'message' => $v->errors()->first()], 422);
        }

        $max  = LeaveDetailItem::where('group_id', $groupId)->max('sort_order') ?? -1;
        $item = LeaveDetailItem::create([
            'group_id'       => $groupId,
            'label'          => $request->label,
            'has_text_input' => $request->boolean('has_text_input'),
            'sort_order'     => $max + 1,
        ]);

        return response()->json(['success' => true, 'data' => $item, 'message' => 'Option added successfully.']);
    }

    public function updateItem(Request $request, $id)
    {
        $item = LeaveDetailItem::findOrFail($id);
        $v = Validator::make($request->all(), [
            'label'          => 'required|string|max:200',
            'has_text_input' => 'nullable|boolean',
        ]);
        if ($v->fails()) {
            return response()->json(['success' => false, 'message' => $v->errors()->first()], 422);
        }

        $item->update([
            'label'          => $request->label,
            'has_text_input' => $request->has('has_text_input')
                                    ? $request->boolean('has_text_input')
                                    : $item->has_text_input,
        ]);

        return response()->json(['success' => true, 'data' => $item, 'message' => 'Option updated successfully.']);
    }

    public function destroyItem($id)
    {
        LeaveDetailItem::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Option deleted.']);
    }

    public function reorderItems(Request $request, $groupId)
    {
        $v = Validator::make($request->all(), [
            'order'   => 'required|array',
            'order.*' => 'integer',
        ]);
        if ($v->fails()) {
            return response()->json(['success' => false, 'message' => $v->errors()->first()], 422);
        }

        foreach ($request->order as $index => $itemId) {
            LeaveDetailItem::where('id', $itemId)
                           ->where('group_id', $groupId)
                           ->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true, 'message' => 'Order saved.']);
    }
}