@php($b = $booking)
{{ $b->type === 'penawaran' ? 'Ada permintaan penawaran baru' : 'Ada booking teknisi baru' }} di {{ config('site.name') }}.

@foreach (array_filter(['Nama' => $b->name, 'Instansi' => $b->organization, 'Perangkat' => $b->device, 'Jumlah unit' => $b->units, ($b->type === 'penawaran' ? 'Kebutuhan' : 'Keluhan') => $b->complaint, 'Lokasi' => $b->location, 'Dari halaman' => $b->source]) as $label => $value)
{{ $label }}: {{ $value }}
@endforeach

Buka di CMS: {{ route('admin.bookings.show', $b) }}
Segera hubungi pelanggan lewat WhatsApp.
