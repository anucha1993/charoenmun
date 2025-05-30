<!-- resources/views/livewire/quotations/quotations-index.blade.php -->
<div>
    <a class="btn btn-success mb-2" href="{{ route('quotations.create') }}">➕ เพิ่มใบเสนอราคา</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>เลขที่</th><th>ลูกค้า</th><th>วันที่</th><th class="text-end">จำนวนเงิน</th><th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotes as $q)
                <tr>
                    <td>{{ $q->quotation_number }}</td>
                    <td>{{ $q->customer->customer_name }}</td>
                    <td>{{ $q->quote_date->format('d/m/Y') }}</td>
                    <td class="text-end">{{ number_format($q->grand_total,2) }}</td>
                    <td class="text-nowrap">
                        <a href="{{ route('quotations.edit',$q) }}" class="btn btn-sm btn-info">แก้ไข</a>
                        <button wire:click="delete({{ $q->id }})" class="btn btn-sm btn-danger"
                            onclick="return confirm('ยืนยันลบ?')">ลบ</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $quotes->links() }}
</div>
