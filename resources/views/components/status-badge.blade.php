@props(['status' => ''])
@php
$s = strtolower((string) $status);
$ok = ['approved','verified','granted','enrolled','paid','received','active','published','completed','accepted','issued','confirmed','converted','claimed','success','enabled','open'];
$info = ['submitted','in_progress','processing','contacted','acknowledged','review','interview','requested','scheduled','confirmed','sent','partial'];
$warn = ['pending','unpaid','draft','new','lead','awaiting','deposit_required','conditional','offer_received'];
$bad = ['refused','rejected','overdue','cancelled','failed','expired','lost','withdrawn','infected','disabled','breached'];
if (in_array($s, $ok, true)) $c = 'ok';
elseif (in_array($s, $info, true)) $c = 'info';
elseif (in_array($s, $warn, true)) $c = 'warn';
elseif (in_array($s, $bad, true)) $c = 'bad';
else $c = 'mute';
$sealed = in_array($s, ['approved','visa_approved','enrolled','granted'], true);
@endphp
<span class="gc-stamp {{ $c }}{{ $sealed ? ' sealed' : '' }}">{{ ucwords(str_replace('_',' ',$status)) }}</span>
