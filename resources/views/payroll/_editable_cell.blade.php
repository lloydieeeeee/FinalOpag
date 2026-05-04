@php
    $displayVal = (float)($val ?? 0);
    $isLocked   = $locked ?? false;
    $isAuto     = $auto ?? false;
@endphp

<div class="ec {{ $isLocked ? 'locked' : '' }}"
     data-pid="{{ $pid }}"
     data-field="{{ $field }}"
     data-orig="{{ number_format($displayVal, 2, '.', '') }}">
    <input
        type="text"
        inputmode="decimal"
        autocomplete="off"
        autocorrect="off"
        spellcheck="false"
        value="{{ $displayVal > 0 ? number_format($displayVal, 2, '.', '') : '' }}"
        placeholder="0.00"
        class="{{ $cls ?? '' }} {{ $isAuto && !$isLocked ? 'auto-computed' : '' }}"
        @if($isLocked) readonly tabindex="-1" @endif
        onfocus="onCellFocus(this)"
        onblur="onCellBlur(this)"
        onkeydown="onCellKeydown(this,event)"
        onclick="event.stopPropagation()"
        title="{{ $isAuto && !$isLocked ? 'Auto-computed — click to override' : ($isLocked ? 'Period is finalized' : 'Click to edit') }}"
    >
</div>