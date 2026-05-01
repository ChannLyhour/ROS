<div class="order-type-container mb-3">
    <label class="extra-small fw-black text-muted text-uppercase mb-2 d-block">Service Type</label>
    <div class="d-flex gap-2">
        <input type="radio" class="btn-check" name="orderType" id="dine_in" value="dine_in"
            {{ ($existingOrder && $existingOrder->order_type == 'dine_in') || (!$existingOrder) ? 'checked' : '' }} onchange="toggleTable()">
        <label class="btn btn-premium-toggle flex-grow-1 py-2" for="dine_in">
            Dine In
        </label>

        <input type="radio" class="btn-check" name="orderType" id="takeaway" value="takeaway"
            {{ $existingOrder && $existingOrder->order_type == 'takeaway' ? 'checked' : '' }} onchange="toggleTable()">
        <label class="btn btn-premium-toggle flex-grow-1 py-2" for="takeaway">
            Takeaway
        </label>

        <input type="radio" class="btn-check" name="orderType" id="delivery" value="delivery"
            {{ $existingOrder && $existingOrder->order_type == 'delivery' ? 'checked' : '' }} onchange="toggleTable()">
        <label class="btn btn-premium-toggle flex-grow-1 py-2" for="delivery">
            Delivery
        </label>
    </div>
</div>

<div id="tableContainer" class="p-3 bg-white rounded-lg border shadow-sm transition-all" style="border-style: dashed !important;">
    <label class="extra-small fw-black text-primary text-uppercase mb-2 d-block">Table Assignment</label>
    <select id="tableId" class="form-select select2" data-placeholder="Choose Table...">
        <option value=""></option>
        @foreach($tables as $table)
        <option value="{{ $table->id }}" {{ $existingOrder && $existingOrder->table_id == $table->id ? 'selected' : '' }}>
            {{ $table->name }} ({{ $table->capacity }}p)
        </option>
        @endforeach
    </select>
</div>