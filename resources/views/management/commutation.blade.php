@extends('layouts.app')
@section('title', 'Management Settings — Commutation')
@section('page-title', 'Management Settings')

@section('content')
<style>
.mgmt-page{display:flex;flex-direction:column;height:calc(100vh - 120px);overflow:hidden;}
.breadcrumb{flex-shrink:0;display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:14px;}
.breadcrumb a{color:#6b7280;text-decoration:none;}.breadcrumb a:hover{color:#1a3a1a;}
.breadcrumb .sep{color:#d1d5db;}.breadcrumb .current{color:#1a3a1a;font-weight:600;}
.sub-tab-bar{flex-shrink:0;display:flex;align-items:center;background:#fff;padding:0 24px;overflow-x:auto;border-radius:14px 14px 0 0;border:1px solid #e5e7eb;border-bottom:none;}
.sub-tab-btn{padding:13px 18px;font-size:13px;font-weight:500;color:#6b7280;border:none;background:none;border-bottom:2.5px solid transparent;cursor:pointer;transition:all .2s;white-space:nowrap;text-decoration:none;display:inline-flex;align-items:center;}
.sub-tab-btn:hover{color:#1a3a1a;}.sub-tab-btn.active{color:#1a3a1a;border-bottom-color:#2d5a1b;font-weight:700;}
.mgmt-card{flex:1;min-height:0;display:flex;flex-direction:column;background:#fff;border:1px solid #e5e7eb;border-top:none;border-radius:0 0 16px 16px;box-shadow:0 1px 4px rgba(0,0,0,.05);overflow:hidden;}
.ch{flex-shrink:0;padding:16px 24px 14px;border-bottom:1px solid #f3f4f6;display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;}
.ct{font-size:15px;font-weight:700;color:#111827;margin:0 0 3px;}.cd{font-size:12px;color:#9ca3af;margin:0;}
.tsa{flex:1;min-height:0;overflow-y:auto;overflow-x:auto;scrollbar-width:thin;scrollbar-color:#e5e7eb transparent;}
.tsa::-webkit-scrollbar{width:5px;}.tsa::-webkit-scrollbar-thumb{background:#e5e7eb;border-radius:99px;}
.mgmt-footer{flex-shrink:0;padding:10px 24px;font-size:12px;color:#9ca3af;border-top:1px solid #f9fafb;background:#fff;}
.sw{position:relative;}.sw svg{position:absolute;left:10px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:#9ca3af;pointer-events:none;}
.sw input{padding:8px 12px 8px 32px;font-size:13px;border:1.5px solid #e5e7eb;border-radius:9px;outline:none;width:220px;transition:border-color .15s;color:#374151;}.sw input:focus{border-color:#2d5a1b;}
.btn-add{padding:8px 18px;font-size:13px;font-weight:700;border:none;border-radius:9px;background:#1a3a1a;color:#fff;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:background .15s;}.btn-add:hover{background:#2d5a1b;}
.dt{width:100%;border-collapse:collapse;font-size:13px;}
.dt thead{position:sticky;top:0;z-index:2;}
.dt thead tr{background:#fafafa;border-bottom:1px solid #f3f4f6;}
.dt th{padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;white-space:nowrap;}
.dt td{padding:12px 16px;border-bottom:1px solid #f9fafb;color:#374151;vertical-align:middle;}
.dt tbody tr:last-child td{border-bottom:none;}.dt tbody tr:hover td{background:#fafafa;}
.ac{display:flex;align-items:center;justify-content:flex-end;gap:4px;}
.ib{width:32px;height:32px;border-radius:8px;border:none;background:none;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background .15s;color:#9ca3af;}
.ib.e:hover{background:#f0fdf4;color:#16a34a;}.ib.d:hover{background:#fee2e2;color:#dc2626;}
.ov{position:fixed;inset:0;z-index:200;background:rgba(0,0,0,.45);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .22s;}.ov.show{opacity:1;pointer-events:all;}
.mc{background:#fff;border-radius:20px;padding:32px;width:440px;max-width:94vw;box-shadow:0 24px 64px rgba(0,0,0,.2);transform:scale(.93) translateY(10px);transition:transform .26s cubic-bezier(.34,1.56,.64,1);}.ov.show .mc{transform:scale(1) translateY(0);}
.mt{font-size:16px;font-weight:800;color:#111827;margin:0 0 4px;}.ms{font-size:12px;color:#9ca3af;margin:0 0 22px;}
.fl{display:block;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;}
.fi{width:100%;padding:10px 14px;font-size:13px;border:1.5px solid #e5e7eb;border-radius:10px;outline:none;color:#111827;transition:border-color .15s;background:#fff;box-sizing:border-box;}.fi:focus{border-color:#2d5a1b;}
.fr{margin-bottom:16px;}.mf{display:flex;justify-content:flex-end;gap:10px;margin-top:26px;}
.bcm{padding:9px 20px;font-size:13px;font-weight:600;border:1.5px solid #e5e7eb;border-radius:10px;background:#fff;color:#6b7280;cursor:pointer;}.bcm:hover{border-color:#9ca3af;color:#374151;}
.bsm{padding:9px 24px;font-size:13px;font-weight:700;border:none;border-radius:10px;background:#1a3a1a;color:#fff;cursor:pointer;}.bsm:hover{background:#2d5a1b;}
.bdm{padding:9px 24px;font-size:13px;font-weight:700;border:none;border-radius:10px;background:#dc2626;color:#fff;cursor:pointer;}.bdm:hover{background:#b91c1c;}
.dib{width:52px;height:52px;border-radius:16px;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin-bottom:16px;}
#toast{position:fixed;bottom:24px;right:24px;z-index:400;min-width:280px;background:#fff;border-radius:14px;padding:14px 18px;box-shadow:0 8px 32px rgba(0,0,0,.15);display:flex;align-items:center;gap:12px;opacity:0;transform:translateY(14px);transition:all .28s;pointer-events:none;}#toast.show{opacity:1;transform:translateY(0);}
.ti{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
</style>

<div class="mgmt-page">
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Management Settings</a>
        <span class="sep">›</span>
        <span class="current">Commutation</span>
    </div>

    <div class="sub-tab-bar">
        <a href="{{ route('settings.leaveType') }}"       class="sub-tab-btn">Leave Type</a>
        <a href="{{ route('settings.department') }}"      class="sub-tab-btn">Department</a>
        <a href="{{ route('settings.position') }}"        class="sub-tab-btn">Position</a>
        <a href="{{ route('settings.detailsOfLeave') }}"  class="sub-tab-btn">Details of Leave</a>
        <a href="{{ route('settings.commutation') }}"     class="sub-tab-btn active">Commutation</a>
        <a href="{{ route('settings.recommendation') }}"  class="sub-tab-btn">Recommendation</a>
        <a href="{{ route('settings.signatory') }}"       class="sub-tab-btn">Signatory</a>
        <a href="{{ route('settings.role') }}"            class="sub-tab-btn">Role</a>
    </div>

    <div class="mgmt-card">
        <div class="ch">
            <div>
                <p class="ct">Commutation</p>
                <p class="cd">Manage commutation options shown on the leave application form</p>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <div class="sw">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input type="text" id="si" placeholder="Search..." oninput="filterRows()">
                </div>
                <button class="btn-add" onclick="openAdd()">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Option
                </button>
            </div>
        </div>

        <div class="tsa">
            <table class="dt">
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>Option Label</th>
                        <th style="text-align:right;padding-right:24px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    @forelse($options as $i => $opt)
                    <tr class="row" data-id="{{ $opt->id }}" data-label="{{ strtolower($opt->label) }}">
                        <td style="color:#9ca3af;font-family:monospace;font-size:12px;">{{ str_pad($i+1,2,'0',STR_PAD_LEFT) }}</td>
                        <td style="font-weight:600;color:#111827;">{{ $opt->label }}</td>
                        <td>
                            <div class="ac">
                                <button class="ib e" title="Edit" onclick="openEdit({{ $opt->id }},'{{ addslashes($opt->label) }}')">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button class="ib d" title="Delete" onclick="openDel({{ $opt->id }},'{{ addslashes($opt->label) }}')">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="padding:48px;text-align:center;color:#9ca3af;font-size:13px;">No commutation options yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mgmt-footer">{{ count($options) }} option(s)</div>
    </div>
</div>

<div id="fo" class="ov" onclick="if(event.target===this)closeForm()">
    <div class="mc">
        <h3 class="mt" id="mT">Add Commutation Option</h3>
        <p  class="ms" id="mS">Enter the option label below.</p>
        <div class="fr"><label class="fl">Label *</label><input class="fi" id="fN" type="text" placeholder="e.g. Not Requested"></div>
        <div class="mf">
            <button class="bcm" onclick="closeForm()">Cancel</button>
            <button class="bsm" id="bS" onclick="saveForm()">Add Option</button>
        </div>
    </div>
</div>

<div id="do" class="ov" onclick="if(event.target===this)closeDel()">
    <div class="mc" style="width:380px;">
        <div class="dib"><svg width="24" height="24" fill="none" stroke="#dc2626" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div>
        <h3 class="mt" style="margin-bottom:8px;">Delete Option?</h3>
        <p style="font-size:13px;color:#6b7280;margin:0 0 24px;">You are about to delete <strong id="dN"></strong>. This cannot be undone.</p>
        <div class="mf" style="margin-top:0;"><button class="bcm" onclick="closeDel()">Cancel</button><button class="bdm" onclick="execDel()">Delete</button></div>
    </div>
</div>

<div id="toast"><div class="ti" id="tI"></div><div><p style="font-size:13px;font-weight:700;color:#111827;margin:0;" id="tT"></p><p style="font-size:12px;color:#9ca3af;margin:3px 0 0;" id="tM"></p></div></div>

<script>
const CSRF='{{csrf_token()}}', BASE='{{route("settings.commutation")}}';
let eid=null, did=null;
function filterRows(){const q=document.getElementById('si').value.toLowerCase();document.querySelectorAll('.row').forEach(r=>r.style.display=r.dataset.label.includes(q)?'':'none');}
function openAdd(){eid=null;document.getElementById('mT').textContent='Add Commutation Option';document.getElementById('bS').textContent='Add Option';document.getElementById('fN').value='';document.getElementById('fo').classList.add('show');setTimeout(()=>document.getElementById('fN').focus(),150);}
function openEdit(id,nm){eid=id;document.getElementById('mT').textContent='Edit Option';document.getElementById('bS').textContent='Save Changes';document.getElementById('fN').value=nm;document.getElementById('fo').classList.add('show');}
function closeForm(){document.getElementById('fo').classList.remove('show');}
function saveForm(){const nm=document.getElementById('fN').value.trim();if(!nm){toast('Error','Label is required.','error');return;}const b=document.getElementById('bS');b.disabled=true;b.textContent='Saving…';fetch(eid?`${BASE}/${eid}`:BASE,{method:eid?'PUT':'POST',headers:{'X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest','Content-Type':'application/json'},body:JSON.stringify({label:nm})}).then(r=>r.json()).then(d=>{b.disabled=false;b.textContent=eid?'Save Changes':'Add Option';if(d.success){closeForm();toast(eid?'Updated':'Added',d.message,'success');setTimeout(()=>location.reload(),900);}else toast('Error',d.message,'error');});}
function openDel(id,nm){did=id;document.getElementById('dN').textContent=nm;document.getElementById('do').classList.add('show');}
function closeDel(){document.getElementById('do').classList.remove('show');did=null;}
function execDel(){if(!did)return;fetch(`${BASE}/${did}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest'}}).then(r=>r.json()).then(d=>{closeDel();if(d.success){toast('Deleted',d.message,'error');setTimeout(()=>location.reload(),900);}else toast('Error',d.message,'error');});}
function toast(t,m,type='success'){const mp={success:{bg:'#dcfce7',c:'#16a34a',p:'M5 13l4 4L19 7'},error:{bg:'#fee2e2',c:'#dc2626',p:'M6 18L18 6M6 6l12 12'},info:{bg:'#dbeafe',c:'#2563eb',p:'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'}};const s=mp[type]||mp.info;document.getElementById('tT').textContent=t;document.getElementById('tM').textContent=m;document.getElementById('tI').innerHTML=`<svg width="18" height="18" fill="none" stroke="${s.c}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="${s.p}"/></svg>`;document.getElementById('tI').style.background=s.bg;const el=document.getElementById('toast');el.classList.add('show');clearTimeout(window._tt);window._tt=setTimeout(()=>el.classList.remove('show'),3400);}
document.addEventListener('keydown',e=>{if(e.key==='Escape'){closeForm();closeDel();}});
</script>
@endsection