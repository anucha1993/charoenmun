# สรุประบบ Order และ Order Delivery

## 📋 โครงสร้างระบบ

### 🗂️ โมเดลและความสัมพันธ์

#### 1. **OrderModel** (ใบสั่งซื้อหลัก)
```php
Table: orders
Fields:
- id, quote_id (FK), order_number, order_date
- customer_id (FK), delivery_address_id (FK)
- order_subtotal, order_discount, order_vat, order_grand_total
- payment_status, order_status
- created_by, updated_by

Relationships:
- belongsTo: QuotationModel, customerModel, deliveryAddressModel
- hasMany: OrderItemsModel, OrderDeliverysModel
```

#### 2. **OrderDeliverysModel** (บิลจัดส่งแต่ละครั้ง)
```php
Table: order_deliveries
Fields:
- id, order_id (FK), order_delivery_number
- delivery_address_id (FK), order_delivery_date
- order_delivery_status (0=รอจัดส่ง, 1=จัดส่งสำเร็จ, 2=ยกเลิก)
- payment_status (pending, paid, partial, overpayment, waiting_confirmation)
- order_delivery_status_order (1=ส่งครบแล้ว, 0=ยังไม่ครบ)
- order_delivery_note, created_by, updated_by
- order_delivery_subtotal, order_delivery_vat, order_delivery_discount
- order_delivery_grand_total, order_delivery_enable_vat, order_delivery_vat_included

Relationships:
- belongsTo: OrderModel, deliveryAddressModel, User (sale)
- hasMany: OrderDeliveryItems, OrderPayment
```

#### 3. **OrderDeliveryItems** (รายการสินค้าในแต่ละบิลจัดส่ง)
```php
Table: order_delivery_items
Fields:
- id, order_delivery_id (FK), order_item_id (FK)
- quantity, unit_price, product_calculation, total

Relationships:
- belongsTo: OrderDeliverysModel, OrderItemsModel
```

#### 4. **OrderPayment** (การชำระเงินสำหรับแต่ละบิลจัดส่ง)
```php
Table: order_payments
Fields:
- id, order_id (FK), order_delivery_id (FK), user_id (FK)
- slip_path, amount, reference_id, trans_ref
- sender_name, receiver_name, bank_name, transfer_at
- status (ชำระเงินแล้ว, รอยืนยันยอด)
- sender_account_no, receiver_account_no

Relationships:
- belongsTo: OrderModel, OrderDeliverysModel
```

---

## 🔄 ขั้นตอนการทำงาน

### 1. **การสร้าง Order**
1. สร้างจาก Quotation ที่ได้รับการอนุมัติ
2. คัดลอกข้อมูลลูกค้า รายการสินค้า ยอดเงิน
3. กำหนดสถานะเริ่มต้น: `payment_status = 'pending'`, `order_status = 'pending'`

### 2. **การจัดส่งสินค้า (Order Delivery)**
1. **สร้างบิลจัดส่ง**: ระบบแยกการจัดส่งออกเป็นครั้งๆ
2. **เลือกสินค้า**: สามารถเลือกส่งบางรายการ หรือ ปริมาณบางส่วน
3. **Running Number**: `{ORDER_NUMBER}-001`, `{ORDER_NUMBER}-002`
4. **Stock Management**: ติดตามสต็อกที่เหลือจากการส่งครั้งก่อน

### 3. **การชำระเงิน**
1. **Upload Slip**: ลูกค้าส่งหลักฐานการโอนเงิน
2. **Confirm Payment**: เจ้าหน้าที่ยืนยันการชำระ
3. **Auto Update Status**: ระบบคำนวณสถานะการชำระอัตโนมัติ

---

## 💰 ระบบการชำระเงิน

### Payment Status Logic:
```php
// OrderDeliverysModel::updatePaymentStatus()
$confirmedAmount = payments->where('status', 'ชำระเงินแล้ว')->sum('amount');
$total = order_delivery_grand_total;
$hasPendingSlip = payments->where('status', 'รอยืนยันยอด')->exists();

if ($confirmedAmount > $total) {
    $this->payment_status = 'overpayment'; // เกินยอด
} elseif ($confirmedAmount == $total) {
    $this->payment_status = 'paid'; // ชำระครบ
} elseif ($confirmedAmount > 0 && $confirmedAmount < $total) {
    $this->payment_status = 'partial'; // ชำระบางส่วน
} elseif ($hasPendingSlip) {
    $this->payment_status = 'waiting_confirmation'; // รอยืนยัน
} else {
    $this->payment_status = 'pending'; // ยังไม่ชำระ
}
```

### Order Level Payment Status:
```php
// OrderModel::updatePaymentStatus()
- คำนวณจากทุก OrderDelivery ภายใต้ Order นั้น
- รวมยอดที่ยืนยันแล้วทั้งหมด เทียบกับยอด Order รวม
```

---

## 📊 การแสดงผลและรายงาน

### 1. **Order Index** (`order-index.blade.php`)
- แสดงรายการ Order ทั้งหมด
- สถานะการชำระเงิน และ สถานะการจัดส่ง
- Summary cards: จำนวน Order, สถานะต่างๆ

### 2. **Order Show** (`order-show.blade.php`)
- รายละเอียด Order แต่ละใบ
- รายการ Delivery ทั้งหมดภายใต้ Order
- สรุปการชำระเงิน: ยืนยันแล้ว, รอยืนยัน
- ปุ่มการจัดการ: จัดส่งสินค้า, อัปโหลดสลิป

### 3. **Order Delivery** (`order-delivery.blade.php`)
- ฟอร์มสร้าง/แก้ไข บิลจัดส่ง
- เลือกสินค้าและปริมาณที่จะส่ง
- คำนวณราคา VAT ส่วนลด
- จัดการที่อยู่จัดส่ง

### 4. **Order Delivery Print** (`order-delivery-print.blade.php`)
- ใบส่งสินค้าสำหรับพิมพ์
- QR Code สำหรับติดตาม
- รายละเอียดการจัดส่งครบถ้วน

---

## 🎛️ Livewire Controllers

### 1. **OrderIndex** - หน้ารายการ Order
- Pagination
- Delete Order
- Summary statistics

### 2. **OrderShow** - รายละเอียด Order
- แสดงข้อมูล Order และ Deliveries
- จัดการการชำระเงิน
- อนุมัติ Order

### 3. **OrderDelivery** - จัดการการจัดส่ง
- Stock management
- Item selection
- Calculation engine
- Save/Update delivery

### 4. **OrderDeliveryService** - Business Logic
- `storeDelivery()` - สร้างบิลจัดส่งใหม่
- `updateDelivery()` - แก้ไขบิลจัดส่ง
- Running number generation
- Database transactions

---

## 🔧 ฟีเจอร์สำคัญ

### ✅ **ที่ทำได้แล้ว**
1. **Multi-delivery**: แยกการจัดส่งเป็นครั้งๆ
2. **Stock tracking**: ติดตามสต็อกที่เหลือ
3. **Payment management**: จัดการการชำระแบบ multi-slip
4. **Auto calculation**: คำนวณสถานะอัตโนมัติ
5. **Print system**: ใบส่งสินค้าพร้อมพิมพ์
6. **Address management**: ที่อยู่จัดส่งแบบง่าย (ตามการปรับปรุงล่าสุด)

### 🔄 **Flow การทำงาน**
```
Quotation → Order → Order Delivery(s) → Payment(s) → Complete
     ↓         ↓            ↓               ↓           ↓
   อนุมัติ   สั่งซื้อ   จัดส่งแต่ละครั้ง   ชำระเงิน   เสร็จสิ้น
```

### 🎯 **การใช้งานหลัก**
1. **สร้าง Order** จาก Quotation ที่อนุมัติ
2. **จัดส่งสินค้า** แบบแยกครั้ง (partial delivery)
3. **รับชำระเงิน** ผ่านการอัปโหลดสลิป
4. **ติดตามสถานะ** การจัดส่งและการชำระ
5. **พิมพ์เอกสาร** ใบส่งสินค้า

---

## 📈 ข้อมูลสำคัญสำหรับการพัฒนา

### Models Location:
- `app/Models/Orders/OrderModel.php`
- `app/Models/Orders/OrderDeliverysModel.php`
- `app/Models/Orders/OrderDeliveryItems.php`
- `app/Models/Orders/OrderPayment.php`

### Controllers Location:
- `app/Livewire/Orders/OrderIndex.php`
- `app/Livewire/Orders/OrderShow.php`
- `app/Livewire/Orders/OrderDelivery.php`

### Views Location:
- `resources/views/livewire/orders/order-index.blade.php`
- `resources/views/livewire/orders/order-show.blade.php`
- `resources/views/livewire/orders/order-delivery.blade.php`
- `resources/views/livewire/orders/order-delivery-print.blade.php`

### Services:
- `app/services/OrderDeliveryService.php`

---

*ระบบ Order และ Order Delivery เป็นส่วนสำคัญที่จัดการการขาย การจัดส่ง และการชำระเงินแบบครบวงจร*
