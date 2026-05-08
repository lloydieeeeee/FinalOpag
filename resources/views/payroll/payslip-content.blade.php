@php
    $user    = auth()->user();
    $isAdmin = $user && method_exists($user, 'hasRole')
                   ? $user->hasRole('admin')
                   : false;
@endphp

{{--
    IMPORTANT:
    The "Edit Payslip" toggle button is NO LONGER rendered here.
    It lives in the parent payslip.blade.php topbar so it sits inline
    with the other topbar action buttons.

    This partial renders:
      1. All CSS for edit-mode styles
      2. The yellow edit-mode toolbar (hidden by default, shown by JS)
      3. The full payslip document  (#payslipDoc)
      4. The admin JS (togglePayslipEdit / cancelPayslipEdit / savePayslipEdit)
--}}

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap');

*, *::before, *::after { box-sizing: border-box; }

/* ── Edit Mode Toolbar ─────────────────────────────────────────────────────── */
.payslip-edit-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 14px 20px;
    margin-bottom: 20px;
    background: linear-gradient(135deg, #fffbeb, #fef3c7);
    border: 1.5px solid #f59e0b;
    border-radius: 14px;
    flex-wrap: wrap;
}

.payslip-edit-toolbar.hidden { display: none; }

.edit-toolbar-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.edit-toolbar-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    background: #f59e0b;
    color: #fff;
    font-size: 11px;
    font-weight: 800;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: .5px;
}

.edit-toolbar-badge svg { width: 12px; height: 12px; }

.edit-toolbar-hint {
    font-size: 12px;
    color: #92400e;
    font-weight: 500;
}

.edit-toolbar-actions {
    display: flex;
    gap: 8px;
}

.btn-edit-cancel {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    font-size: 12px;
    font-weight: 700;
    color: #6b7280;
    background: #fff;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    cursor: pointer;
    transition: all .15s;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

.btn-edit-cancel:hover { border-color: #d1d5db; background: #f9fafb; }

.btn-edit-save {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    font-size: 12px;
    font-weight: 700;
    color: #fff;
    background: linear-gradient(135deg, #059669, #10b981);
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: all .2s;
    box-shadow: 0 3px 10px rgba(5, 150, 105, .3);
    font-family: 'Plus Jakarta Sans', sans-serif;
}

.btn-edit-save:hover { transform: translateY(-1px); box-shadow: 0 5px 16px rgba(5, 150, 105, .4); }
.btn-edit-save svg { width: 14px; height: 14px; }

/* ── Inline Edit Inputs ────────────────────────────────────────────────────── */
.payslip-doc.edit-mode .edit-field {
    border: 1.5px solid #d1fae5;
    border-radius: 6px;
    padding: 3px 8px;
    background: #f0fdf4;
    font-family: inherit;
    font-size: inherit;
    font-weight: inherit;
    color: inherit;
    outline: none;
    width: 100%;
    transition: border-color .15s, box-shadow .15s;
    display: inline-block;
}

.payslip-doc.edit-mode .edit-field:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, .12);
    background: #fff;
}

.payslip-doc.edit-mode .edit-field.mono {
    font-family: 'JetBrains Mono', monospace;
    font-size: 13px;
}

.payslip-doc.edit-mode .amount-cell .edit-field {
    text-align: right;
    max-width: 140px;
    margin-left: auto;
}

.payslip-doc.edit-mode .net-pay-amount .edit-field {
    font-size: 22px;
    font-weight: 800;
    color: #92400e;
    font-family: 'JetBrains Mono', monospace;
    max-width: 220px;
    background: rgba(255,255,255,.6);
    border-color: #f59e0b;
}

.payslip-doc.edit-mode .net-pay-amount .edit-field:focus {
    border-color: #d97706;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, .15);
}

.payslip-doc:not(.edit-mode) .edit-field {
    border: none;
    background: transparent;
    padding: 0;
    pointer-events: none;
}

.payslip-doc.edit-mode .emp-info-value .edit-field {
    font-size: 13px;
    font-weight: 600;
}

.payslip-doc.edit-mode .signature-name .edit-field {
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    max-width: 200px;
    text-align: center;
    margin: 0 auto;
}

.payslip-doc.edit-mode .signature-title .edit-field {
    font-size: 11px;
    color: #6b7280;
    text-align: center;
    margin: 0 auto;
    max-width: 200px;
}

.payslip-doc.edit-mode .emp-info-value.designation-wrap .edit-field {
    display: inline-block;
    width: auto;
    min-width: 120px;
}

.edit-mode-hint {
    display: none;
    font-size: 9.5px;
    color: #10b981;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .4px;
    margin-top: 2px;
}

.payslip-doc.edit-mode .edit-mode-hint { display: block; }

.payslip-doc.edit-mode .payslip-table tr:not(.total-row):not(.subtotal-row):hover {
    background: #f0fdf4;
}

.payslip-doc {
    transition: box-shadow .2s;
}

.payslip-doc.edit-mode {
    box-shadow: 0 0 0 3px rgba(16, 185, 129, .15), 0 4px 24px rgba(0,0,0,.08);
    border-color: #a7f3d0;
}
</style>

{{-- ── Edit Mode Toolbar (hidden until JS activates it) ── --}}
@if($isAdmin)
<div class="payslip-edit-toolbar hidden" id="editToolbar">
    <div class="edit-toolbar-info">
        <span class="edit-toolbar-badge">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Mode
        </span>
        <span class="edit-toolbar-hint">Click any field on the payslip to edit it. Press Save when done.</span>
    </div>
    <div class="edit-toolbar-actions">
        <button type="button" class="btn-edit-cancel" onclick="cancelPayslipEdit()">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Cancel
        </button>
        <button type="button" class="btn-edit-save" onclick="savePayslipEdit()">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Save Changes
        </button>
    </div>
</div>
@endif

{{-- ── Payslip Document ── --}}
<div class="payslip-doc" id="payslipDoc">

    {{-- Header --}}
    <div class="payslip-header">
        <h1>Republic of the Philippines</h1>
        <h2>PROVINCE OF CAMARINES NORTE</h2>
        <h3>D A E T</h3>
        <h4>OFFICE OF THE PROVINCIAL AGRICULTURIST</h4>
        <h4>PAY SLIP</h4>
        <div class="pay-period">For the Period: <strong>{{ $record->period->period_label }}</strong></div>
    </div>

    {{-- Employee Info Box --}}
    <div class="emp-info-box">
        <div class="emp-info-grid">
            <div class="emp-info-item">
                <span class="emp-info-label">Name:</span>
                <span class="emp-info-value">
                    <input class="edit-field" name="employee_name_display"
                        value="{{ strtoupper($record->employee->last_name) }}, {{ strtoupper($record->employee->first_name) }}{{ $record->employee->extension_name ? ' '.strtoupper($record->employee->extension_name) : '' }}"
                        readonly>
                    <span class="edit-mode-hint">Display only — edit in Employees</span>
                </span>
            </div>
            <div class="emp-info-item">
                <span class="emp-info-label">Employee ID:</span>
                <span class="emp-info-value">
                    <input class="edit-field" name="employee_id_display"
                        value="{{ $record->user_id }}"
                        readonly>
                    <span class="edit-mode-hint">Display only — edit in Employees</span>
                </span>
            </div>
            <div class="emp-info-item">
                <span class="emp-info-label">Position:</span>
                <span class="emp-info-value designation-wrap">
                    <input class="edit-field" name="designation"
                        value="{{ $record->designation ?? optional($record->employee->position)->position_code ?? 'N/A' }}">
                </span>
            </div>
            <div class="emp-info-item">
                <span class="emp-info-label">Gross Salary:</span>
                <span class="emp-info-value">
                    ₱<input class="edit-field mono" name="gross_salary"
                        type="number" step="0.01" min="0"
                        value="{{ $record->gross_salary }}">
                </span>
            </div>
        </div>
    </div>

    {{-- Allowances --}}
    <div class="payslip-section">
        <div class="section-title earnings">Allowances</div>
        <table class="payslip-table">
            <tr>
                <td class="label-cell">PERA</td>
                <td class="amount-cell">
                    <input class="edit-field mono" name="allowance_pera"
                        type="number" step="0.01" min="0"
                        value="{{ $record->allowance_pera ?? 0 }}"
                        placeholder="0.00">
                </td>
            </tr>
            <tr>
                <td class="label-cell">RATA</td>
                <td class="amount-cell">
                    <input class="edit-field mono" name="allowance_rata"
                        type="number" step="0.01" min="0"
                        value="{{ $record->allowance_rata ?? 0 }}"
                        placeholder="0.00">
                </td>
            </tr>
            <tr>
                <td class="label-cell">TA</td>
                <td class="amount-cell">
                    <input class="edit-field mono" name="allowance_ta"
                        type="number" step="0.01" min="0"
                        value="{{ $record->allowance_ta ?? 0 }}"
                        placeholder="0.00">
                </td>
            </tr>
            <tr>
                <td class="label-cell">Other Allowance</td>
                <td class="amount-cell">
                    <input class="edit-field mono" name="allowance_other"
                        type="number" step="0.01" min="0"
                        value="{{ $record->allowance_other ?? 0 }}"
                        placeholder="0.00">
                </td>
            </tr>
        </table>
    </div>

    {{-- Deductions --}}
    <div class="payslip-section">
        <div class="section-title deductions">Less Deductions</div>
        <table class="payslip-table">

            {{-- GSIS Group --}}
            <tr class="subtotal-row">
                <td class="label-cell">GSIS</td>
                <td class="amount-cell"></td>
            </tr>
            <tr class="sub-item">
                <td class="label-cell">Employee Share (9%)</td>
                <td class="amount-cell">
                    <input class="edit-field mono" name="gsis_ee"
                        type="number" step="0.01" min="0"
                        value="{{ $record->gsis_ee ?? 0 }}" placeholder="0.00">
                </td>
            </tr>
            <tr class="sub-item">
                {{--
                    ECF (₱100) is paid by the EMPLOYER — it is NOT deducted from the employee's salary.
                    It is shown here for reference only and does not affect net pay.
                --}}
                <td class="label-cell" style="color:#9ca3af;">ECF <small style="font-size:10px;">(Employer-paid, not deducted)</small></td>
                <td class="amount-cell">
                    <input class="edit-field mono" name="gsis_ec"
                        type="number" step="0.01" min="0"
                        value="{{ $record->gsis_ec ?? 0 }}" placeholder="0.00"
                        style="color:#9ca3af;">
                </td>
            </tr>
            <tr class="sub-item">
                <td class="label-cell">GSIS Policy Loan</td>
                <td class="amount-cell">
                    <input class="edit-field mono" name="gsis_policy"
                        type="number" step="0.01" min="0"
                        value="{{ $record->gsis_policy ?? 0 }}" placeholder="0.00">
                </td>
            </tr>
            <tr class="sub-item">
                <td class="label-cell">GSIS Emergency Loan</td>
                <td class="amount-cell">
                    <input class="edit-field mono" name="gsis_emergency"
                        type="number" step="0.01" min="0"
                        value="{{ $record->gsis_emergency ?? 0 }}" placeholder="0.00">
                </td>
            </tr>
            <tr class="sub-item">
                <td class="label-cell">GSIS Real Estate Loan</td>
                <td class="amount-cell">
                    <input class="edit-field mono" name="gsis_real_estate"
                        type="number" step="0.01" min="0"
                        value="{{ $record->gsis_real_estate ?? 0 }}" placeholder="0.00">
                </td>
            </tr>
            <tr class="sub-item">
                <td class="label-cell">GSIS MPL</td>
                <td class="amount-cell">
                    <input class="edit-field mono" name="gsis_mpl"
                        type="number" step="0.01" min="0"
                        value="{{ $record->gsis_mpl ?? 0 }}" placeholder="0.00">
                </td>
            </tr>
            <tr class="sub-item">
                <td class="label-cell">GSIS MPL Lite</td>
                <td class="amount-cell">
                    <input class="edit-field mono" name="gsis_mpl_lite"
                        type="number" step="0.01" min="0"
                        value="{{ $record->gsis_mpl_lite ?? 0 }}" placeholder="0.00">
                </td>
            </tr>
            <tr class="sub-item">
                <td class="label-cell">GSIS GFAL</td>
                <td class="amount-cell">
                    <input class="edit-field mono" name="gsis_gfal"
                        type="number" step="0.01" min="0"
                        value="{{ $record->gsis_gfal ?? 0 }}" placeholder="0.00">
                </td>
            </tr>
            <tr class="sub-item">
                <td class="label-cell">GSIS Computer Loan</td>
                <td class="amount-cell">
                    <input class="edit-field mono" name="gsis_computer"
                        type="number" step="0.01" min="0"
                        value="{{ $record->gsis_computer ?? 0 }}" placeholder="0.00">
                </td>
            </tr>
            <tr class="sub-item">
                <td class="label-cell">GSIS Consolidated Loan</td>
                <td class="amount-cell">
                    <input class="edit-field mono" name="gsis_conso"
                        type="number" step="0.01" min="0"
                        value="{{ $record->gsis_conso ?? 0 }}" placeholder="0.00">
                </td>
            </tr>

            {{-- PAG-IBIG Group --}}
            <tr class="subtotal-row">
                <td class="label-cell">PAG-IBIG</td>
                <td class="amount-cell"></td>
            </tr>
            <tr class="sub-item">
                {{--
                    pagibig_govt stores the EMPLOYEE ₱200 flat contribution (deducted from salary).
                    pagibig_ee stores the employer ₱200 match (NOT deducted — never shown on payslip).
                --}}
                <td class="label-cell">Employee Share (₱200)</td>
                <td class="amount-cell">
                    <input class="edit-field mono" name="pagibig_govt"
                        type="number" step="0.01" min="0"
                        value="{{ $record->pagibig_govt ?? 0 }}" placeholder="0.00">
                </td>
            </tr>
            <tr class="sub-item">
                <td class="label-cell">PAG-IBIG MPL</td>
                <td class="amount-cell">
                    <input class="edit-field mono" name="pagibig_mpl"
                        type="number" step="0.01" min="0"
                        value="{{ $record->pagibig_mpl ?? 0 }}" placeholder="0.00">
                </td>
            </tr>
            <tr class="sub-item">
                <td class="label-cell">PAG-IBIG Calamity Loan</td>
                <td class="amount-cell">
                    <input class="edit-field mono" name="pagibig_calamity"
                        type="number" step="0.01" min="0"
                        value="{{ $record->pagibig_calamity ?? 0 }}" placeholder="0.00">
                </td>
            </tr>

            {{-- PhilHealth --}}
            <tr class="subtotal-row">
                <td class="label-cell">PhilHealth</td>
                <td class="amount-cell"></td>
            </tr>
            <tr class="sub-item">
                <td class="label-cell">Employee Share (2.5%)</td>
                <td class="amount-cell">
                    <input class="edit-field mono" name="philhealth_ee"
                        type="number" step="0.01" min="0"
                        value="{{ $record->philhealth_ee ?? 0 }}" placeholder="0.00">
                </td>
            </tr>

            {{-- Other Deductions --}}
            <tr>
                <td class="label-cell">Withholding Tax</td>
                <td class="amount-cell">
                    <input class="edit-field mono" name="withholding_tax"
                        type="number" step="0.01" min="0"
                        value="{{ $record->withholding_tax ?? 0 }}" placeholder="0.00">
                </td>
            </tr>
            <tr>
                <td class="label-cell">DBP Loan</td>
                <td class="amount-cell">
                    <input class="edit-field mono" name="loan_dbp"
                        type="number" step="0.01" min="0"
                        value="{{ $record->loan_dbp ?? 0 }}" placeholder="0.00">
                </td>
            </tr>
            <tr>
                <td class="label-cell">LBP Loan</td>
                <td class="amount-cell">
                    <input class="edit-field mono" name="loan_lbp"
                        type="number" step="0.01" min="0"
                        value="{{ $record->loan_lbp ?? 0 }}" placeholder="0.00">
                </td>
            </tr>
            <tr>
                <td class="label-cell">CNGWMPC</td>
                <td class="amount-cell">
                    <input class="edit-field mono" name="loan_cngwmpc"
                        type="number" step="0.01" min="0"
                        value="{{ $record->loan_cngwmpc ?? 0 }}" placeholder="0.00">
                </td>
            </tr>
            <tr>
                <td class="label-cell">PARACLE</td>
                <td class="amount-cell">
                    <input class="edit-field mono" name="loan_paracle"
                        type="number" step="0.01" min="0"
                        value="{{ $record->loan_paracle ?? 0 }}" placeholder="0.00">
                </td>
            </tr>
            <tr>
                <td class="label-cell">Overpayment</td>
                <td class="amount-cell">
                    <input class="edit-field mono" name="overpayment"
                        type="number" step="0.01" min="0"
                        value="{{ $record->overpayment ?? 0 }}" placeholder="0.00">
                </td>
            </tr>
            @if(($record->other_deduction ?? 0) > 0 || true)
            <tr style="{{ ($record->other_deduction ?? 0) > 0 ? 'background:#fffbeb;' : '' }}">
                <td class="label-cell" style="{{ ($record->other_deduction ?? 0) > 0 ? 'color:#92400e;font-weight:700;' : '' }}">
                    <input class="edit-field" name="other_deduction_label"
                        value="{{ $record->other_deduction_label ?? 'Other Deduction' }}" placeholder="Other Deduction"
                        style="font-weight:{{ ($record->other_deduction ?? 0) > 0 ? '700' : '400' }};color:{{ ($record->other_deduction ?? 0) > 0 ? '#92400e' : 'inherit' }};">
                </td>
                <td class="amount-cell" style="{{ ($record->other_deduction ?? 0) > 0 ? 'color:#92400e;font-weight:700;' : '' }}">
                    <input class="edit-field mono" name="other_deduction"
                        type="number" step="0.01" min="0"
                        value="{{ $record->other_deduction ?? 0 }}" placeholder="0.00"
                        style="{{ ($record->other_deduction ?? 0) > 0 ? 'color:#92400e;font-weight:700;' : '' }}">
                </td>
            </tr>
            @endif

            {{-- Total Deductions --}}
            <tr class="total-row">
                <td class="label-cell">TOTAL DEDUCTION</td>
                <td class="amount-cell" id="totalDeductionDisplay">
                    {{ number_format($record->total_deductions, 2) }}
                </td>
            </tr>
        </table>
    </div>

    {{-- Net Pay --}}
    <div class="net-pay-box">
        <div class="net-pay-label">NET PAY</div>
        <div class="net-pay-amount">
            ₱<input class="edit-field mono" name="net_pay"
                type="number" step="0.01" min="0"
                value="{{ $record->net_pay }}"
                style="width: 180px; font-size: 28px; font-weight: 800; color: #92400e;">
        </div>
    </div>

    {{-- Signatories --}}
    <div class="payslip-footer">
        <div class="signature-grid">
            <div class="signature-box">
                <div class="signature-line">
                    <div class="signature-name">
                        <input class="edit-field" name="signatory_name"
                            value="{{ strtoupper(optional($record->period->createdBy)->full_name ?? 'MELINDA R. BARCELONA') }}"
                            style="text-align:center;">
                    </div>
                    <div class="signature-title">
                        <input class="edit-field" name="signatory_title"
                            value="AO V / Payroll Clerk"
                            style="text-align:center;">
                    </div>
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    <div class="signature-name">
                        <input class="edit-field" name="employee_sig_display"
                            value="{{ strtoupper($record->employee->last_name) }}, {{ strtoupper($record->employee->first_name) }}{{ $record->employee->extension_name ? ' '.strtoupper($record->employee->extension_name) : '' }}"
                            readonly style="text-align:center;">
                        <span class="edit-mode-hint">Display only</span>
                    </div>
                    <div class="signature-title">Employee Signature</div>
                </div>
            </div>
        </div>
    </div>

</div>{{-- /.payslip-doc --}}

@if($isAdmin)
<script>
(function () {
    const doc       = document.getElementById('payslipDoc');
    const toolbar   = document.getElementById('editToolbar');
    // btnToggle lives in the parent topbar (payslip.blade.php)
    const btnToggle = document.getElementById('btnToggleEdit');
    const btnLabel  = document.getElementById('btnToggleEditLabel');

    let originalValues = {};

    function collectValues() {
        const out = {};
        doc.querySelectorAll('.edit-field').forEach(el => {
            if (el.name) out[el.name] = el.value;
        });
        return out;
    }

    window.togglePayslipEdit = function () {
        const isEditing = doc.classList.toggle('edit-mode');
        toolbar.classList.toggle('hidden', !isEditing);

        if (isEditing) {
            originalValues = collectValues();
            if (btnLabel) btnLabel.textContent = 'Exit Edit Mode';
            doc.querySelectorAll('.edit-field[readonly]').forEach(el => {
                el.style.background = '#f3f4f6';
                el.style.color = '#9ca3af';
                el.style.cursor = 'not-allowed';
            });
            doc.querySelectorAll('.edit-field:not([readonly])').forEach(el => {
                el.removeAttribute('disabled');
            });
        } else {
            cancelPayslipEdit();
        }
    };

    window.cancelPayslipEdit = function () {
        Object.entries(originalValues).forEach(([name, val]) => {
            const el = doc.querySelector(`.edit-field[name="${name}"]`);
            if (el) el.value = val;
        });
        doc.classList.remove('edit-mode');
        toolbar.classList.add('hidden');
        if (btnLabel) btnLabel.textContent = 'Edit Payslip';
    };

    window.savePayslipEdit = function () {
        const btn = document.querySelector('.btn-edit-save');
        btn.disabled = true;
        btn.innerHTML = `
            <svg style="width:14px;height:14px;animation:spin 1s linear infinite;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Saving…`;

        const payload = { _method: 'PATCH' };
        doc.querySelectorAll('.edit-field:not([readonly])').forEach(el => {
            if (el.name) payload[el.name] = el.value;
        });

        const recordId  = {{ $record->id ?? $record->record_id ?? 'null' }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        fetch(`/payroll/record/${recordId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-HTTP-Method-Override': 'PATCH',
            },
            body: JSON.stringify(payload),
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message ?? 'Save failed');
            return data;
        })
        .then(data => {
            if (data.record) {
                const td = document.getElementById('totalDeductionDisplay');
                if (td && data.record.total_deductions !== undefined) {
                    td.textContent = parseFloat(data.record.total_deductions)
                        .toLocaleString('en-PH', { minimumFractionDigits: 2 });
                }
                if (data.record.net_pay !== undefined) {
                    const npInput = doc.querySelector('.edit-field[name="net_pay"]');
                    if (npInput) npInput.value = data.record.net_pay;
                }
            }

            originalValues = collectValues();
            doc.classList.remove('edit-mode');
            toolbar.classList.add('hidden');
            if (btnLabel) btnLabel.textContent = 'Edit Payslip';
            showToast('Payslip saved successfully!', 'success');
        })
        .catch(err => {
            showToast(err.message ?? 'An error occurred. Please try again.', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = `
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Save Changes`;
        });
    };

    function showToast(message, type = 'success') {
        const existing = document.getElementById('payslipToast');
        if (existing) existing.remove();

        const colors = type === 'success'
            ? { bg: '#d1fae5', border: '#10b981', text: '#065f46', icon: '✓' }
            : { bg: '#fee2e2', border: '#ef4444', text: '#991b1b', icon: '✕' };

        const toast = document.createElement('div');
        toast.id = 'payslipToast';
        toast.style.cssText = `
            position: fixed; bottom: 24px; right: 24px; z-index: 9999;
            display: flex; align-items: center; gap: 10px;
            padding: 14px 20px;
            background: ${colors.bg}; border: 1.5px solid ${colors.border};
            color: ${colors.text}; border-radius: 12px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13px; font-weight: 600;
            box-shadow: 0 8px 24px rgba(0,0,0,.12);
            transform: translateY(20px); opacity: 0;
            transition: all .3s cubic-bezier(.34,1.56,.64,1);
        `;
        toast.innerHTML = `<span style="font-size:16px;">${colors.icon}</span>${message}`;
        document.body.appendChild(toast);

        requestAnimationFrame(() => {
            toast.style.transform = 'translateY(0)';
            toast.style.opacity = '1';
        });

        setTimeout(() => {
            toast.style.transform = 'translateY(20px)';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }

    if (!document.getElementById('payslip-edit-spin-style')) {
        const s = document.createElement('style');
        s.id = 'payslip-edit-spin-style';
        s.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
        document.head.appendChild(s);
    }
})();
</script>
@endif