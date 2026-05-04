@extends('layouts.app')
@section('title', 'Management Settings — Details of Leave')
@section('page-title', 'Management Settings')

@section('content')
<style>
/* ── Layout (same pattern as other management pages) ── */
.mgmt-page{display:flex;flex-direction:column;height:calc(100vh - 120px);overflow:hidden;}
.breadcrumb{flex-shrink:0;display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:14px;}
.breadcrumb a{color:#6b7280;text-decoration:none;}.breadcrumb a:hover{color:#1a3a1a;}
.breadcrumb .sep{color:#d1d5db;}.breadcrumb .current{color:#1a3a1a;font-weight:600;}
.sub-tab-bar{flex-shrink:0;display:flex;align-items:center;background:#fff;padding:0 24px;overflow-x:auto;border-radius:14px 14px 0 0;border:1px solid #e5e7eb;border-bottom:none;}
.sub-tab-btn{padding:13px 18px;font-size:13px;font-weight:500;color:#6b7280;border:none;background:none;border-bottom:2.5px solid transparent;cursor:pointer;transition:all .2s;white-space:nowrap;text-decoration:none;display:inline-flex;align-items:center;}
.sub-tab-btn:hover{color:#1a3a1a;}.sub-tab-btn.active{color:#1a3a1a;border-bottom-color:#2d5a1b;font-weight:700;}.sub-tab-btn.muted{cursor:default;opacity:.38;pointer-events:none;}
.mgmt-card{flex:1;min-height:0;display:flex;flex-direction:column;background:#fff;border:1px solid #e5e7eb;border-top:none;border-radius:0 0 16px 16px;box-shadow:0 1px 4px rgba(0,0,0,.05);overflow:hidden;}
.ch{flex-shrink:0;padding:16px 24px 14px;border-bottom:1px solid #f3f4f6;display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;}
.ct{font-size:15px;font-weight:700;color:#111827;margin:0 0 3px;}.cd{font-size:12px;color:#9ca3af;margin:0;}
.tsa{flex:1;min-height:0;overflow-y:auto;overflow-x:auto;scrollbar-width:thin;scrollbar-color:#e5e7eb transparent;}
.tsa::-webkit-scrollbar{width:5px;}.tsa::-webkit-scrollbar-thumb{background:#e5e7eb;border-radius:99px;}
.mgmt-footer{flex-shrink:0;padding:10px 24px;font-size:12px;color:#9ca3af;border-top:1px solid #f9fafb;background:#fff;}

/* ── Toolbar ── */
.sw{position:relative;}.sw svg{position:absolute;left:10px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:#9ca3af;pointer-events:none;}
.sw input{padding:8px 12px 8px 32px;font-size:13px;border:1.5px solid #e5e7eb;border-radius:9px;outline:none;width:220px;transition:border-color .15s;color:#374151;}.sw input:focus{border-color:#2d5a1b;}
.btn-add{padding:8px 18px;font-size:13px;font-weight:700;border:none;border-radius:9px;background:#1a3a1a;color:#fff;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:background .15s;}.btn-add:hover{background:#2d5a1b;}

/* ── Groups layout (scrollable body) ── */
.groups-body { padding:20px 24px; display:flex; flex-direction:column; gap:20px; }

/* ── Category group card ── */
.group-card {
    background:#fff; border:1px solid #e5e7eb; border-radius:14px;
    overflow:hidden; transition:box-shadow .15s;
}
.group-card:hover { box-shadow:0 2px 12px rgba(0,0,0,.06); }
.group-header {
    display:flex; align-items:center; justify-content:space-between;
    padding:13px 18px 13px 16px;
    background:#fafafa; border-bottom:1px solid #f3f4f6;
    cursor:pointer; user-select:none;
    gap:12px;
}
.group-header-left { display:flex; align-items:center; gap:10px; min-width:0; }
.group-color-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
.group-name { font-size:13px; font-weight:700; color:#111827; }
.group-meta { font-size:11px; color:#9ca3af; margin-left:4px; }
.group-chevron { width:16px; height:16px; color:#9ca3af; transition:transform .2s; flex-shrink:0; }
.group-card.collapsed .group-chevron { transform:rotate(-90deg); }
.group-card.collapsed .group-body { display:none; }
.group-header-right { display:flex; align-items:center; gap:8px; flex-shrink:0; }

/* ── Items inside group ── */
.group-body { padding:0; }
.detail-item {
    display:flex; align-items:center; gap:12px;
    padding:11px 18px; border-bottom:1px solid #f9fafb;
    transition:background .1s;
}
.detail-item:last-child { border-bottom:none; }
.detail-item:hover { background:#fafafa; }

/* Drag handle */
.drag-handle {
    color:#d1d5db; cursor:grab; flex-shrink:0; padding:2px;
    border-radius:4px; transition:color .15s;
}
.drag-handle:hover { color:#9ca3af; }
.drag-handle:active { cursor:grabbing; }

/* Item icon (checkbox preview) */
.item-checkbox-preview {
    width:16px; height:16px; border:2px solid #d1d5db; border-radius:3px;
    flex-shrink:0; background:#fff; display:flex; align-items:center; justify-content:center;
}

.item-label { flex:1; font-size:13px; color:#374151; min-width:0; }
.item-label-input {
    flex:1; font-size:13px; color:#374151; border:none; outline:none;
    background:transparent; padding:0; min-width:0;
}
.item-label-input:focus { background:#f0fdf4; border-radius:4px; padding:2px 6px; }

/* Has text input indicator */
.item-has-input {
    font-size:11px; color:#9ca3af; background:#f3f4f6; padding:2px 7px;
    border-radius:5px; white-space:nowrap; flex-shrink:0;
}

.item-actions { display:flex; align-items:center; gap:2px; flex-shrink:0; }
.ico-btn{width:28px;height:28px;border-radius:7px;border:none;background:none;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background .15s;color:#9ca3af;}
.ico-btn:hover{background:#f3f4f6;color:#374151;}
.ico-btn.del:hover{background:#fee2e2;color:#dc2626;}

/* ── Add item row inside group ── */
.add-item-row {
    display:flex; align-items:center; gap:10px;
    padding:10px 18px; border-top:1px solid #f3f4f6;
    background:#fafafa;
}
.add-item-input {
    flex:1; font-size:13px; border:1.5px solid #e5e7eb; border-radius:8px;
    padding:7px 12px; outline:none; color:#374151; transition:border-color .15s;
    background:#fff;
}
.add-item-input:focus { border-color:#2d5a1b; }
.add-item-has-input {
    display:flex; align-items:center; gap:6px;
    font-size:12px; color:#6b7280; white-space:nowrap; cursor:pointer;
}
.add-item-has-input input { accent-color:#2d5a1b; }
.btn-add-item {
    padding:7px 14px; font-size:12px; font-weight:700;
    border:none; border-radius:8px; background:#1a3a1a; color:#fff;
    cursor:pointer; transition:background .15s; white-space:nowrap;
}
.btn-add-item:hover { background:#2d5a1b; }
.btn-add-item:disabled { opacity:.4; cursor:default; }

/* ── Add group section ── */
.add-group-bar {
    background:#f8fafc; border:2px dashed #e5e7eb; border-radius:14px;
    padding:16px 20px;
    display:flex; align-items:center; gap:12px; flex-wrap:wrap;
}
.add-group-input {
    flex:1; min-width:180px; font-size:13px; border:1.5px solid #e5e7eb;
    border-radius:8px; padding:8px 12px; outline:none; color:#374151;
    transition:border-color .15s; background:#fff;
}
.add-group-input:focus { border-color:#2d5a1b; }
.color-picker-row { display:flex; align-items:center; gap:6px; }
.color-dot-option {
    width:18px; height:18px; border-radius:50%; cursor:pointer;
    border:2px solid transparent; transition:border-color .15s; flex-shrink:0;
}
.color-dot-option.selected { border-color:#1a3a1a; }

/* ── Modals ── */
.ov{position:fixed;inset:0;z-index:200;background:rgba(0,0,0,.45);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .22s;}
.ov.show{opacity:1;pointer-events:all;}
.mc{background:#fff;border-radius:20px;padding:32px;width:460px;max-width:94vw;box-shadow:0 24px 64px rgba(0,0,0,.2);transform:scale(.93) translateY(10px);transition:transform .26s cubic-bezier(.34,1.56,.64,1);}
.ov.show .mc{transform:scale(1) translateY(0);}
.mt{font-size:16px;font-weight:800;color:#111827;margin:0 0 4px;}.ms{font-size:12px;color:#9ca3af;margin:0 0 22px;}
.fl{display:block;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;}
.fi{width:100%;padding:10px 14px;font-size:13px;border:1.5px solid #e5e7eb;border-radius:10px;outline:none;color:#111827;transition:border-color .15s;background:#fff;box-sizing:border-box;}.fi:focus{border-color:#2d5a1b;}
.fr{margin-bottom:16px;}.mf{display:flex;justify-content:flex-end;gap:10px;margin-top:26px;}
.bcm{padding:9px 20px;font-size:13px;font-weight:600;border:1.5px solid #e5e7eb;border-radius:10px;background:#fff;color:#6b7280;cursor:pointer;}.bcm:hover{border-color:#9ca3af;color:#374151;}
.bsm{padding:9px 24px;font-size:13px;font-weight:700;border:none;border-radius:10px;background:#1a3a1a;color:#fff;cursor:pointer;}.bsm:hover{background:#2d5a1b;}
.bdm{padding:9px 24px;font-size:13px;font-weight:700;border:none;border-radius:10px;background:#dc2626;color:#fff;cursor:pointer;}.bdm:hover{background:#b91c1c;}
.dib{width:52px;height:52px;border-radius:16px;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin-bottom:16px;}

/* ── Toast ── */
#toast{position:fixed;bottom:24px;right:24px;z-index:400;min-width:280px;background:#fff;border-radius:14px;padding:14px 18px;box-shadow:0 8px 32px rgba(0,0,0,.15);display:flex;align-items:center;gap:12px;opacity:0;transform:translateY(14px);transition:all .28s;pointer-events:none;}
#toast.show{opacity:1;transform:translateY(0);}
.ti{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}

/* dragging state */
.detail-item.dragging { opacity:.4; background:#f0fdf4; }
.detail-item.drag-over { border-top:2px solid #2d5a1b; }
</style>

<div class="mgmt-page">

    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Management Settings</a>
        <span class="sep">›</span>
        <span class="current">Details of Leave</span>
    </div>

    <div class="sub-tab-bar">
        <a href="{{ route('settings.leaveType') }}"       class="sub-tab-btn">Leave Type</a>
        <a href="{{ route('settings.department') }}"      class="sub-tab-btn">Department</a>
        <a href="{{ route('settings.position') }}"        class="sub-tab-btn">Position</a>
        <a href="{{ route('settings.detailsOfLeave') }}"  class="sub-tab-btn active">Details of Leave</a>
        <a href="{{ route('settings.commutation') }}"     class="sub-tab-btn">Commutation</a>
        <a href="{{ route('settings.recommendation') }}"  class="sub-tab-btn">Recommendation</a>
        <a href="{{ route('settings.signatory') }}"       class="sub-tab-btn">Signatory</a>
        <a href="{{ route('settings.role') }}"            class="sub-tab-btn">Role</a>
    </div>

    <div class="mgmt-card">
        <div class="ch">
            <div>
                <p class="ct">Details of Leave</p>
                <p class="cd">Manage checkbox options shown in CS Form 6B — grouped by leave category</p>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <div class="sw">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input type="text" id="searchInput" placeholder="Search options..." oninput="filterGroups()">
                </div>
            </div>
        </div>

        <div class="tsa">
            <div class="groups-body" id="groupsBody">

                {{-- ═══ GROUPS loaded from DB or seeded defaults ═══ --}}
                @forelse($groups as $group)
                <div class="group-card" id="group_{{ $group->id }}" data-group-id="{{ $group->id }}">
                    <div class="group-header" onclick="toggleGroup({{ $group->id }})">
                        <div class="group-header-left">
                            <span class="group-color-dot" style="background:{{ $group->color }};"></span>
                            <span class="group-name">{{ $group->group_name }}</span>
                            <span class="group-meta">({{ $group->items->count() }} option{{ $group->items->count() !== 1 ? 's' : '' }})</span>
                        </div>
                        <div class="group-header-right" onclick="event.stopPropagation()">
                            <button class="ico-btn" title="Edit group name"
                                    onclick="openEditGroup({{ $group->id }}, '{{ addslashes($group->group_name) }}', '{{ $group->color }}')">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button class="ico-btn del" title="Delete group"
                                    onclick="openDelGroup({{ $group->id }}, '{{ addslashes($group->group_name) }}')">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                            <svg class="group-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                    <div class="group-body">
                        @forelse($group->items->sortBy('sort_order') as $item)
                        <div class="detail-item"
                             data-item-id="{{ $item->id }}"
                             data-label="{{ strtolower($item->label) }}"
                             draggable="true"
                             ondragstart="onDragStart(event, {{ $item->id }})"
                             ondragover="onDragOver(event)"
                             ondrop="onDrop(event, {{ $item->id }}, {{ $group->id }})"
                             ondragleave="onDragLeave(event)">
                            {{-- Drag handle --}}
                            <span class="drag-handle" title="Drag to reorder">
                                <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M8 6a2 2 0 100-4 2 2 0 000 4zm0 8a2 2 0 100-4 2 2 0 000 4zm0 8a2 2 0 100-4 2 2 0 000 4zm8-16a2 2 0 100-4 2 2 0 000 4zm0 8a2 2 0 100-4 2 2 0 000 4zm0 8a2 2 0 100-4 2 2 0 000 4z"/></svg>
                            </span>
                            {{-- Checkbox preview --}}
                            <div class="item-checkbox-preview">
                                <svg width="10" height="10" fill="none" stroke="#d1d5db" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            {{-- Label (inline editable on double-click) --}}
                            <span class="item-label" ondblclick="makeEditable(this, {{ $item->id }})">{{ $item->label }}</span>
                            {{-- Has text input badge --}}
                            @if($item->has_text_input)
                            <span class="item-has-input" title="Has a text field next to checkbox">+ text field</span>
                            @endif
                            {{-- Actions --}}
                            <div class="item-actions">
                                <button class="ico-btn" title="Edit"
                                        onclick="openEditItem({{ $item->id }}, '{{ addslashes($item->label) }}', {{ $item->has_text_input ? 'true' : 'false' }})">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button class="ico-btn del" title="Delete"
                                        onclick="openDelItem({{ $item->id }}, '{{ addslashes($item->label) }}', {{ $group->id }})">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                        @empty
                        <div style="padding:14px 18px;font-size:12px;color:#9ca3af;font-style:italic;">No options yet — add one below.</div>
                        @endforelse

                        {{-- Add item row (always visible at bottom of group) --}}
                        <div class="add-item-row" id="addRow_{{ $group->id }}">
                            <input class="add-item-input" type="text" id="newItem_{{ $group->id }}"
                                   placeholder="New option label…"
                                   onkeydown="if(event.key==='Enter')addItem({{ $group->id }})">
                            <label class="add-item-has-input">
                                <input type="checkbox" id="newItemHasInput_{{ $group->id }}">
                                + text field
                            </label>
                            <button class="btn-add-item" onclick="addItem({{ $group->id }})">Add</button>
                        </div>
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:48px;color:#9ca3af;font-size:13px;">
                    No groups yet. Add one below to get started.
                </div>
                @endforelse

                {{-- ═══ ADD NEW GROUP bar ═══ --}}
                <div class="add-group-bar" id="addGroupBar">
                    <svg width="18" height="18" fill="none" stroke="#9ca3af" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <input class="add-group-input" type="text" id="newGroupName"
                           placeholder="New group name (e.g. In case of Vacation Leave)…"
                           onkeydown="if(event.key==='Enter')addGroup()">
                    {{-- Color picker --}}
                    <div class="color-picker-row" id="colorPicker">
                        @foreach(['#6366f1','#3b82f6','#22c55e','#f59e0b','#ef4444','#8b5cf6','#14b8a6','#f97316'] as $clr)
                        <span class="color-dot-option {{ $loop->first ? 'selected' : '' }}"
                              style="background:{{ $clr }};"
                              data-color="{{ $clr }}"
                              onclick="selectColor(this, '{{ $clr }}')">
                        </span>
                        @endforeach
                    </div>
                    <button class="btn-add-item" onclick="addGroup()">Add Group</button>
                </div>

            </div>{{-- end groups-body --}}
        </div>{{-- end .tsa --}}

        <div class="mgmt-footer" id="footerCount">
            {{ $groups->sum(fn($g) => $g->items->count()) }} option(s) across {{ $groups->count() }} group(s)
        </div>
    </div>
</div>

{{-- ══ EDIT GROUP MODAL ══ --}}
<div id="editGroupOv" class="ov" onclick="if(event.target===this)closeEditGroup()">
    <div class="mc">
        <h3 class="mt">Edit Group</h3>
        <p  class="ms">Update the name and color of this group.</p>
        <div class="fr">
            <label class="fl">Group Name *</label>
            <input class="fi" id="egName" type="text" placeholder="e.g. In case of Vacation Leave">
        </div>
        <div class="fr">
            <label class="fl">Color</label>
            <div style="display:flex;gap:8px;flex-wrap:wrap;" id="egColorPicker">
                @foreach(['#6366f1','#3b82f6','#22c55e','#f59e0b','#ef4444','#8b5cf6','#14b8a6','#f97316'] as $clr)
                <span class="color-dot-option" style="background:{{ $clr }};width:22px;height:22px;"
                      data-color="{{ $clr }}" onclick="selectEditColor(this, '{{ $clr }}')"></span>
                @endforeach
            </div>
        </div>
        <div class="mf">
            <button class="bcm" onclick="closeEditGroup()">Cancel</button>
            <button class="bsm" onclick="saveEditGroup()">Save Changes</button>
        </div>
    </div>
</div>

{{-- ══ EDIT ITEM MODAL ══ --}}
<div id="editItemOv" class="ov" onclick="if(event.target===this)closeEditItem()">
    <div class="mc">
        <h3 class="mt">Edit Option</h3>
        <p  class="ms">Update the label and settings for this checkbox option.</p>
        <div class="fr">
            <label class="fl">Option Label *</label>
            <input class="fi" id="eiLabel" type="text" placeholder="e.g. Within the Philippines">
        </div>
        <div class="fr" style="display:flex;align-items:center;gap:10px;">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:#374151;">
                <input type="checkbox" id="eiHasInput" style="accent-color:#2d5a1b;width:15px;height:15px;">
                <span>Has a text field next to checkbox</span>
            </label>
            <span style="font-size:11px;color:#9ca3af;">(e.g. "Specify Illness ___")</span>
        </div>
        <div class="mf">
            <button class="bcm" onclick="closeEditItem()">Cancel</button>
            <button class="bsm" onclick="saveEditItem()">Save Changes</button>
        </div>
    </div>
</div>

{{-- ══ DELETE GROUP CONFIRM ══ --}}
<div id="delGroupOv" class="ov" onclick="if(event.target===this)closeDelGroup()">
    <div class="mc" style="width:400px;">
        <div class="dib"><svg width="24" height="24" fill="none" stroke="#dc2626" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div>
        <h3 class="mt" style="margin-bottom:8px;">Delete Group?</h3>
        <p style="font-size:13px;color:#6b7280;margin:0 0 24px;">You are about to delete <strong id="dgName"></strong> and all its options. This cannot be undone.</p>
        <div class="mf" style="margin-top:0;">
            <button class="bcm" onclick="closeDelGroup()">Cancel</button>
            <button class="bdm" onclick="execDelGroup()">Delete Group</button>
        </div>
    </div>
</div>

{{-- ══ DELETE ITEM CONFIRM ══ --}}
<div id="delItemOv" class="ov" onclick="if(event.target===this)closeDelItem()">
    <div class="mc" style="width:400px;">
        <div class="dib"><svg width="24" height="24" fill="none" stroke="#dc2626" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div>
        <h3 class="mt" style="margin-bottom:8px;">Delete Option?</h3>
        <p style="font-size:13px;color:#6b7280;margin:0 0 24px;">You are about to delete <strong id="diName"></strong>. This cannot be undone.</p>
        <div class="mf" style="margin-top:0;">
            <button class="bcm" onclick="closeDelItem()">Cancel</button>
            <button class="bdm" onclick="execDelItem()">Delete</button>
        </div>
    </div>
</div>

<div id="toast"><div class="ti" id="tI"></div><div><p style="font-size:13px;font-weight:700;color:#111827;margin:0;" id="tT"></p><p style="font-size:12px;color:#9ca3af;margin:3px 0 0;" id="tM"></p></div></div>

<script>
const CSRF = '{{ csrf_token() }}';
const BASE = '{{ route("settings.detailsOfLeave") }}';
let selectedColor = '{{ count($groups) ? "" : "#6366f1" }}';
let editGroupId = null, editGroupColor = '#6366f1';
let editItemId  = null;
let delGroupId  = null, delItemId = null, delItemGroupId = null;
let dragItemId  = null;

/* ── Search ── */
function filterGroups() {
    const q = document.getElementById('searchInput').value.toLowerCase().trim();
    document.querySelectorAll('.group-card').forEach(card => {
        if (!q) { card.style.display = ''; return; }
        const items = [...card.querySelectorAll('.detail-item')];
        let anyMatch = false;
        items.forEach(item => {
            const match = (item.dataset.label || '').includes(q);
            item.style.display = match ? '' : 'none';
            if (match) anyMatch = true;
        });
        const groupName = card.querySelector('.group-name')?.textContent.toLowerCase() || '';
        if (groupName.includes(q)) { anyMatch = true; items.forEach(i => i.style.display = ''); }
        card.style.display = anyMatch ? '' : 'none';
        if (anyMatch) card.classList.remove('collapsed');
    });
}

/* ── Toggle group collapse ── */
function toggleGroup(id) {
    document.getElementById('group_' + id).classList.toggle('collapsed');
}

/* ══ ADD GROUP ══ */
function selectColor(el, color) {
    document.querySelectorAll('#colorPicker .color-dot-option').forEach(d => d.classList.remove('selected'));
    el.classList.add('selected');
    selectedColor = color;
}
function addGroup() {
    const name = document.getElementById('newGroupName').value.trim();
    if (!name) { toast('Error','Group name is required.','error'); return; }
    const color = selectedColor || '#6366f1';
    fetch(`${BASE}/groups`, {
        method:'POST',
        headers:{'X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest','Content-Type':'application/json'},
        body: JSON.stringify({ group_name: name, color })
    })
    .then(r=>r.json()).then(d=>{
        if (d.success) { toast('Added', d.message, 'success'); setTimeout(()=>location.reload(), 800); }
        else toast('Error', d.message, 'error');
    });
}

/* ══ EDIT GROUP ══ */
function openEditGroup(id, name, color) {
    editGroupId = id; editGroupColor = color;
    document.getElementById('egName').value = name;
    document.querySelectorAll('#egColorPicker .color-dot-option').forEach(d => {
        d.classList.toggle('selected', d.dataset.color === color);
    });
    document.getElementById('editGroupOv').classList.add('show');
    setTimeout(()=>document.getElementById('egName').focus(), 150);
}
function closeEditGroup() { document.getElementById('editGroupOv').classList.remove('show'); }
function selectEditColor(el, color) {
    document.querySelectorAll('#egColorPicker .color-dot-option').forEach(d => d.classList.remove('selected'));
    el.classList.add('selected');
    editGroupColor = color;
}
function saveEditGroup() {
    const name = document.getElementById('egName').value.trim();
    if (!name) { toast('Error','Group name is required.','error'); return; }
    fetch(`${BASE}/groups/${editGroupId}`, {
        method:'PUT',
        headers:{'X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest','Content-Type':'application/json'},
        body: JSON.stringify({ group_name: name, color: editGroupColor })
    })
    .then(r=>r.json()).then(d=>{
        if (d.success) { closeEditGroup(); toast('Updated', d.message, 'success'); setTimeout(()=>location.reload(), 800); }
        else toast('Error', d.message, 'error');
    });
}

/* ══ DELETE GROUP ══ */
function openDelGroup(id, name) {
    delGroupId = id;
    document.getElementById('dgName').textContent = name;
    document.getElementById('delGroupOv').classList.add('show');
}
function closeDelGroup() { document.getElementById('delGroupOv').classList.remove('show'); delGroupId=null; }
function execDelGroup() {
    if (!delGroupId) return;
    fetch(`${BASE}/groups/${delGroupId}`, {
        method:'DELETE',
        headers:{'X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest'}
    })
    .then(r=>r.json()).then(d=>{
        closeDelGroup();
        if (d.success) { toast('Deleted', d.message, 'error'); setTimeout(()=>location.reload(), 800); }
        else toast('Error', d.message, 'error');
    });
}

/* ══ ADD ITEM ══ */
function addItem(groupId) {
    const labelEl    = document.getElementById('newItem_' + groupId);
    const hasInputEl = document.getElementById('newItemHasInput_' + groupId);
    const label      = labelEl.value.trim();
    if (!label) { toast('Error','Option label is required.','error'); return; }
    fetch(`${BASE}/groups/${groupId}/items`, {
        method:'POST',
        headers:{'X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest','Content-Type':'application/json'},
        body: JSON.stringify({ label, has_text_input: hasInputEl.checked })
    })
    .then(r=>r.json()).then(d=>{
        if (d.success) { toast('Added', d.message, 'success'); setTimeout(()=>location.reload(), 800); }
        else toast('Error', d.message, 'error');
    });
}

/* ══ EDIT ITEM ══ */
function openEditItem(id, label, hasInput) {
    editItemId = id;
    document.getElementById('eiLabel').value      = label;
    document.getElementById('eiHasInput').checked = hasInput;
    document.getElementById('editItemOv').classList.add('show');
    setTimeout(()=>document.getElementById('eiLabel').focus(), 150);
}
function closeEditItem() { document.getElementById('editItemOv').classList.remove('show'); editItemId=null; }
function saveEditItem() {
    const label    = document.getElementById('eiLabel').value.trim();
    const hasInput = document.getElementById('eiHasInput').checked;
    if (!label) { toast('Error','Label is required.','error'); return; }
    fetch(`${BASE}/items/${editItemId}`, {
        method:'PUT',
        headers:{'X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest','Content-Type':'application/json'},
        body: JSON.stringify({ label, has_text_input: hasInput })
    })
    .then(r=>r.json()).then(d=>{
        if (d.success) { closeEditItem(); toast('Updated', d.message, 'success'); setTimeout(()=>location.reload(), 800); }
        else toast('Error', d.message, 'error');
    });
}

/* ── Inline double-click edit ── */
function makeEditable(span, itemId) {
    const orig = span.textContent.trim();
    const inp  = document.createElement('input');
    inp.className = 'item-label-input';
    inp.value     = orig;
    span.replaceWith(inp);
    inp.focus(); inp.select();
    function save() {
        const val = inp.value.trim() || orig;
        fetch(`${BASE}/items/${itemId}`, {
            method:'PUT',
            headers:{'X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest','Content-Type':'application/json'},
            body: JSON.stringify({ label: val })
        })
        .then(r=>r.json()).then(d=>{
            const newSpan = document.createElement('span');
            newSpan.className = 'item-label';
            newSpan.textContent = val;
            newSpan.ondblclick = () => makeEditable(newSpan, itemId);
            inp.replaceWith(newSpan);
            if (d.success) toast('Updated','Label saved.','success');
        });
    }
    inp.onblur = save;
    inp.onkeydown = e => { if (e.key==='Enter') { e.preventDefault(); inp.blur(); } if (e.key==='Escape') { inp.value=orig; inp.blur(); } };
}

/* ══ DELETE ITEM ══ */
function openDelItem(id, name, groupId) {
    delItemId = id; delItemGroupId = groupId;
    document.getElementById('diName').textContent = name;
    document.getElementById('delItemOv').classList.add('show');
}
function closeDelItem() { document.getElementById('delItemOv').classList.remove('show'); delItemId=null; }
function execDelItem() {
    if (!delItemId) return;
    fetch(`${BASE}/items/${delItemId}`, {
        method:'DELETE',
        headers:{'X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest'}
    })
    .then(r=>r.json()).then(d=>{
        closeDelItem();
        if (d.success) { toast('Deleted', d.message, 'error'); setTimeout(()=>location.reload(), 800); }
        else toast('Error', d.message, 'error');
    });
}

/* ══ DRAG-TO-REORDER ══ */
function onDragStart(e, itemId) {
    dragItemId = itemId;
    e.currentTarget.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
}
function onDragOver(e) {
    e.preventDefault();
    e.currentTarget.classList.add('drag-over');
}
function onDragLeave(e) { e.currentTarget.classList.remove('drag-over'); }
function onDrop(e, targetItemId, groupId) {
    e.preventDefault();
    e.currentTarget.classList.remove('drag-over');
    document.querySelectorAll('.detail-item.dragging').forEach(el => el.classList.remove('dragging'));
    if (dragItemId === targetItemId) return;
    /* Collect new order from DOM after visual reorder */
    const tbody = e.currentTarget.closest('.group-body');
    const items = [...tbody.querySelectorAll('.detail-item')];
    const dragEl = items.find(el => el.dataset.itemId == dragItemId);
    const targetEl = e.currentTarget;
    if (!dragEl || !targetEl) return;
    tbody.insertBefore(dragEl, targetEl);
    /* Send new order to server */
    const newOrder = [...tbody.querySelectorAll('.detail-item')].map(el => parseInt(el.dataset.itemId));
    fetch(`${BASE}/groups/${groupId}/items/reorder`, {
        method:'POST',
        headers:{'X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest','Content-Type':'application/json'},
        body: JSON.stringify({ order: newOrder })
    })
    .then(r=>r.json()).then(d => { if (!d.success) toast('Error', d.message, 'error'); });
}

/* ══ TOAST ══ */
function toast(t,m,type='success'){
    const mp={success:{bg:'#dcfce7',c:'#16a34a',p:'M5 13l4 4L19 7'},error:{bg:'#fee2e2',c:'#dc2626',p:'M6 18L18 6M6 6l12 12'},info:{bg:'#dbeafe',c:'#2563eb',p:'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'}};
    const s=mp[type]||mp.info;
    document.getElementById('tT').textContent=t;document.getElementById('tM').textContent=m;
    document.getElementById('tI').innerHTML=`<svg width="18" height="18" fill="none" stroke="${s.c}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="${s.p}"/></svg>`;
    document.getElementById('tI').style.background=s.bg;
    const el=document.getElementById('toast');el.classList.add('show');
    clearTimeout(window._tt);window._tt=setTimeout(()=>el.classList.remove('show'),3400);
}

document.addEventListener('keydown', e => {
    if (e.key==='Escape') {
        closeEditGroup(); closeDelGroup();
        closeEditItem();  closeDelItem();
    }
});

/* Init selected color */
const firstDot = document.querySelector('#colorPicker .color-dot-option');
if (firstDot) selectedColor = firstDot.dataset.color;
</script>
@endsection