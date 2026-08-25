<form method="POST" action="{{ $formAction }}">
    @csrf
    @if($formMethod !== 'POST') @method($formMethod) @endif
    <div class="row g-4">
        <div class="col-lg-8">
            <h2 class="h6 text-uppercase text-secondary mb-3">Informasi utama</h2>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label fw-semibold">Asset tag *</label><input class="form-control @error('asset_tag') is-invalid @enderror" name="asset_tag" value="{{ old('asset_tag', $asset?->asset_tag ?? 'AST-'.str_pad((\App\Models\Asset::max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT)) }}" required>@error('asset_tag')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-8"><label class="form-label fw-semibold">Nama aset *</label><input class="form-control" name="name" value="{{ old('name', $asset?->name) }}" placeholder="Contoh: Lenovo ThinkPad T14" required></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Kategori *</label><select class="form-select" name="category_id" required><option value="">Pilih kategori</option>@foreach($categories as $item)<option value="{{ $item->id }}" @selected(old('category_id', $asset?->category_id) == $item->id)>{{ $item->name }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Serial number</label><input class="form-control" name="serial_number" value="{{ old('serial_number', $asset?->serial_number) }}"></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Brand</label><input class="form-control" name="brand" value="{{ old('brand', $asset?->brand) }}"></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Model</label><input class="form-control" name="model" value="{{ old('model', $asset?->model) }}"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <h2 class="h6 text-uppercase text-secondary mb-3">Penempatan</h2>
            <div class="mb-3"><label class="form-label fw-semibold">Lokasi</label><select class="form-select" name="location_id"><option value="">Belum diatur</option>@foreach($locations as $item)<option value="{{ $item->id }}" @selected(old('location_id', $asset?->location_id) == $item->id)>{{ $item->name }} {{ $item->floor ? '· '.$item->floor : '' }}</option>@endforeach</select></div>
            <div class="mb-3"><label class="form-label fw-semibold">Ditugaskan ke</label><select class="form-select" name="assigned_to"><option value="">Belum ditugaskan</option>@foreach($users as $item)<option value="{{ $item->id }}" @selected(old('assigned_to', $asset?->assigned_to) == $item->id)>{{ $item->name }}</option>@endforeach</select></div>
            <div class="mb-3"><label class="form-label fw-semibold">Status *</label><select class="form-select" name="status" required>@foreach($statuses as $status)<option value="{{ $status->slug }}" @selected(old('status', $asset?->status ?? 'available') === $status->slug)>{{ $status->name }}</option>@endforeach</select></div>
            <div><label class="form-label fw-semibold">Kondisi *</label><select class="form-select" name="condition" required>@foreach(['excellent' => 'Sangat baik', 'good' => 'Baik', 'fair' => 'Cukup', 'poor' => 'Rusak'] as $key => $label)<option value="{{ $key }}" @selected(old('condition', $asset?->condition ?? 'good') === $key)>{{ $label }}</option>@endforeach</select></div>
        </div>
        <div class="col-12"><hr><h2 class="h6 text-uppercase text-secondary mb-3">Finansial & catatan</h2><div class="row g-3"><div class="col-md-4"><label class="form-label fw-semibold">Tanggal pembelian</label><input class="form-control" type="date" name="purchase_date" value="{{ old('purchase_date', $asset?->purchase_date?->format('Y-m-d')) }}"></div><div class="col-md-4"><label class="form-label fw-semibold">Harga pembelian</label><input class="form-control" type="number" min="0" step="0.01" name="purchase_price" value="{{ old('purchase_price', $asset?->purchase_price) }}"></div><div class="col-md-4"><label class="form-label fw-semibold">Garansi sampai</label><input class="form-control" type="date" name="warranty_until" value="{{ old('warranty_until', $asset?->warranty_until?->format('Y-m-d')) }}"></div><div class="col-12"><label class="form-label fw-semibold">Catatan</label><textarea class="form-control" rows="3" name="notes">{{ old('notes', $asset?->notes) }}</textarea></div></div></div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top"><a href="{{ $asset ? route('assets.show', $asset) : route('assets.index') }}" class="btn btn-light">Batal</a><button class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>{{ $submitLabel }}</button></div>
</form>
