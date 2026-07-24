<?php
/**
 * รายงานลูกค้า ID 237 : บริษัท ชัยศิริวัฒน์ กรุ๊ป จำกัด (สำนักงานใหญ่)
 *  - จำนวนใบเสนอราคา
 *  - จำนวนใบสั่งซื้อ
 *  - ยอดรวม
 *  - ส่งออกเป็นไฟล์ Excel (.xlsx) 3 ชีต: สรุป / ใบเสนอราคา / ใบสั่งซื้อ
 *
 * ใช้เฉพาะ PDO + ZipArchive (ไม่พึ่ง vendor)
 * รันด้วย:  php report-customer-237.php
 */

/* ================= config ================= */
$CUSTOMER_ID = 237;

$DB = [
    'host' => '147.50.230.21',
    'port' => 3306,
    'name' => 'charoen_new',
    'user' => 'charoen_new',
    'pass' => 'md1J6@b62',
];

/* ================= connect ================= */
try {
    $pdo = new PDO(
        "mysql:host={$DB['host']};port={$DB['port']};dbname={$DB['name']};charset=utf8mb4",
        $DB['user'],
        $DB['pass'],
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (Throwable $e) {
    fwrite(STDERR, "DB connect failed: " . $e->getMessage() . "\n");
    exit(1);
}

/* ================= queries ================= */
$customer = $pdo->prepare("
    SELECT id, customer_code, customer_name, customer_taxid, customer_contract_name,
           customer_phone, customer_email, customer_address, customer_type
    FROM customers WHERE id = :id
");
$customer->execute([':id' => $CUSTOMER_ID]);
$customer = $customer->fetch();
if (! $customer) {
    fwrite(STDERR, "ไม่พบลูกค้า ID {$CUSTOMER_ID}\n");
    exit(1);
}

// สรุปใบเสนอราคา แยกตามสถานะ
$quoteSummary = $pdo->prepare("
    SELECT quote_status, COUNT(*) AS cnt, COALESCE(SUM(quote_grand_total),0) AS amt
    FROM quotations WHERE customer_id = :id GROUP BY quote_status
");
$quoteSummary->execute([':id' => $CUSTOMER_ID]);
$quoteByStatus = ['wait' => ['cnt' => 0, 'amt' => 0], 'success' => ['cnt' => 0, 'amt' => 0], 'cancel' => ['cnt' => 0, 'amt' => 0]];
$quoteCount = 0; $quoteTotal = 0.0;
foreach ($quoteSummary as $r) {
    $s = $r['quote_status'] ?: 'wait';
    if (! isset($quoteByStatus[$s])) $quoteByStatus[$s] = ['cnt' => 0, 'amt' => 0];
    $quoteByStatus[$s]['cnt'] += (int) $r['cnt'];
    $quoteByStatus[$s]['amt'] += (float) $r['amt'];
    $quoteCount += (int) $r['cnt'];
    $quoteTotal += (float) $r['amt'];
}

// สรุปใบสั่งซื้อ (นับ / ยอดรวม / แยกตามสถานะการชำระเงิน)
$orderTotals = $pdo->prepare("
    SELECT COUNT(*) c, COALESCE(SUM(order_grand_total),0) s
    FROM orders WHERE customer_id = :id
");
$orderTotals->execute([':id' => $CUSTOMER_ID]);
$row = $orderTotals->fetch();
$orderCount = (int) $row['c'];
$orderTotal = (float) $row['s'];

$paymentSummary = $pdo->prepare("
    SELECT payment_status, COUNT(*) cnt, COALESCE(SUM(order_grand_total),0) amt
    FROM orders WHERE customer_id = :id GROUP BY payment_status
");
$paymentSummary->execute([':id' => $CUSTOMER_ID]);
$paymentBy = [];
foreach ($paymentSummary as $r) {
    $paymentBy[$r['payment_status'] ?: '-'] = ['cnt' => (int) $r['cnt'], 'amt' => (float) $r['amt']];
}

// รายการใบเสนอราคา
$quotations = $pdo->prepare("
    SELECT q.id, q.quote_number, q.quote_date, q.quote_status,
           q.quote_subtotal, q.quote_vat, q.quote_discount, q.quote_grand_total,
           u.name AS sale_name
    FROM quotations q
    LEFT JOIN users u ON u.id = q.created_by
    WHERE q.customer_id = :id
    ORDER BY q.quote_date ASC, q.id ASC
");
$quotations->execute([':id' => $CUSTOMER_ID]);
$quotationRows = $quotations->fetchAll();

// รายการใบสั่งซื้อ
$orders = $pdo->prepare("
    SELECT o.id, o.order_number, o.order_date, o.order_status, o.payment_status,
           o.order_subtotal, o.order_vat, o.order_discount, o.order_grand_total,
           q.quote_number AS ref_quote_number,
           u.name AS sale_name
    FROM orders o
    LEFT JOIN quotations q ON q.id = o.quote_id
    LEFT JOIN users u ON u.id = o.created_by
    WHERE o.customer_id = :id
    ORDER BY o.order_date ASC, o.id ASC
");
$orders->execute([':id' => $CUSTOMER_ID]);
$orderRows = $orders->fetchAll();

/* ================= console output ================= */
$statusLabelTh = [
    'wait'    => 'รอดำเนินการ',
    'success' => 'ยืนยันแล้ว',
    'cancel'  => 'ยกเลิก',
];
$paymentLabelTh = [
    'paid'        => 'ชำระเงินแล้ว',
    'partial'     => 'ชำระบางส่วน',
    'unpaid'      => 'ยังไม่ชำระ',
    'overpayment' => 'ชำระเกิน',
    'pending'     => 'รอดำเนินการ',
];

echo str_repeat('=', 78) . "\n";
echo "รายงานลูกค้า ID {$CUSTOMER_ID}\n";
echo "ชื่อลูกค้า      : {$customer['customer_name']}\n";
echo "รหัสลูกค้า     : {$customer['customer_code']}\n";
echo str_repeat('-', 78) . "\n";
echo "จำนวนใบเสนอราคา : {$quoteCount} ใบ  (รวม " . number_format($quoteTotal, 2) . " บาท)\n";
foreach (['wait', 'success', 'cancel'] as $s) {
    $lbl = str_pad($statusLabelTh[$s], 14, ' ', STR_PAD_RIGHT);
    printf("   - %s : %3d ใบ  (%s บาท)\n", $lbl, $quoteByStatus[$s]['cnt'], number_format($quoteByStatus[$s]['amt'], 2));
}
echo "จำนวนใบสั่งซื้อ : {$orderCount} ใบ  (รวม " . number_format($orderTotal, 2) . " บาท)\n";
foreach ($paymentBy as $st => $v) {
    $lbl = $paymentLabelTh[$st] ?? $st;
    printf("   - %-20s : %3d ใบ  (%s บาท)\n", $lbl, $v['cnt'], number_format($v['amt'], 2));
}
echo str_repeat('=', 78) . "\n";

/* ================= XLSX writer (self-contained) ================= */

/**
 * รูปแบบสไตล์ index ที่จะใช้ใน sheet:
 *   0 = default
 *   1 = header (ตัวหนา, พื้นน้ำเงินเข้ม, ข้อความขาว, ขอบบาง)
 *   2 = data cell (ขอบบาง)
 *   3 = data number ทศนิยม 2 ตำแหน่ง (ขอบบาง)
 *   4 = title (ตัวหนา ขนาดใหญ่ สีขาว พื้นน้ำเงินเข้ม)
 *   5 = label bold (ตัวหนา พื้นเทาอ่อน ขอบบาง)
 *   6 = sum row (ตัวหนา พื้นเหลือง ขอบบาง)
 *   7 = sum row number (ตัวหนา พื้นเหลือง ทศนิยม 2 ตำแหน่ง ขอบบาง)
 *   8 = center cell (ขอบบาง จัดกลาง)
 */
function xlsxStylesXml(): string
{
    return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <numFmts count="1">
    <numFmt numFmtId="164" formatCode="#,##0.00"/>
  </numFmts>
  <fonts count="3">
    <font><sz val="11"/><name val="Tahoma"/></font>
    <font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Tahoma"/></font>
    <font><b/><sz val="16"/><color rgb="FFFFFFFF"/><name val="Tahoma"/></font>
  </fonts>
  <fills count="5">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FF1F4E79"/><bgColor indexed="64"/></patternFill></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FFF2F2F2"/><bgColor indexed="64"/></patternFill></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FFFFE699"/><bgColor indexed="64"/></patternFill></fill>
  </fills>
  <borders count="2">
    <border><left/><right/><top/><bottom/><diagonal/></border>
    <border>
      <left style="thin"><color rgb="FF808080"/></left>
      <right style="thin"><color rgb="FF808080"/></right>
      <top style="thin"><color rgb="FF808080"/></top>
      <bottom style="thin"><color rgb="FF808080"/></bottom>
      <diagonal/>
    </border>
  </borders>
  <cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>
  <cellXfs count="9">
    <xf numFmtId="0"   fontId="0" fillId="0" borderId="0" xfId="0"/>
    <xf numFmtId="0"   fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <xf numFmtId="0"   fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"/>
    <xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1"/>
    <xf numFmtId="0"   fontId="2" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <xf numFmtId="0"   fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1"><alignment vertical="center"/></xf>
    <xf numFmtId="0"   fontId="0" fillId="4" borderId="1" xfId="0" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center"/></xf>
    <xf numFmtId="164" fontId="0" fillId="4" borderId="1" xfId="0" applyNumberFormat="1" applyFill="1" applyBorder="1"/>
    <xf numFmtId="0"   fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="center"/></xf>
  </cellXfs>
  <cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>
</styleSheet>
XML;
}

/** convert 1-based column index to Excel letter (A, B, ..., Z, AA, AB, ...) */
function colLetter(int $col): string
{
    $letter = '';
    while ($col > 0) {
        $mod = ($col - 1) % 26;
        $letter = chr(65 + $mod) . $letter;
        $col = intdiv($col - $mod, 26);
    }
    return $letter;
}

/**
 * สร้าง XML ของ worksheet
 * @param array $rows ex: [ [ ['v'=>'A','s'=>1,'t'=>'inlineStr'], ... ], ... ]
 * @param array $colWidths ex: ['A'=>10, 'B'=>20]
 * @param array $merges ex: ['A1:D1']
 */
function buildSheetXml(array $rows, array $colWidths = [], array $merges = []): string
{
    $xml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
    $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';

    if ($colWidths) {
        $xml .= '<cols>';
        foreach ($colWidths as $letter => $w) {
            $idx = 0;
            for ($i = 0; $i < strlen($letter); $i++) {
                $idx = $idx * 26 + (ord($letter[$i]) - 64);
            }
            $xml .= sprintf('<col min="%d" max="%d" width="%s" customWidth="1"/>', $idx, $idx, number_format($w, 2, '.', ''));
        }
        $xml .= '</cols>';
    }

    $xml .= '<sheetData>';
    foreach ($rows as $r => $cells) {
        $rowNum = $r + 1;
        $xml   .= '<row r="' . $rowNum . '">';
        foreach ($cells as $c => $cell) {
            if ($cell === null) continue;
            $ref = colLetter($c + 1) . $rowNum;
            $s   = $cell['s'] ?? 0;
            $v   = $cell['v'];
            if (($cell['t'] ?? '') === 'inlineStr') {
                $val = htmlspecialchars((string) $v, ENT_QUOTES | ENT_XML1, 'UTF-8');
                $xml .= sprintf('<c r="%s" s="%d" t="inlineStr"><is><t xml:space="preserve">%s</t></is></c>', $ref, $s, $val);
            } elseif (($cell['t'] ?? '') === 'formula') {
                $xml .= sprintf('<c r="%s" s="%d"><f>%s</f></c>', $ref, $s, htmlspecialchars((string) $v, ENT_QUOTES | ENT_XML1, 'UTF-8'));
            } else {
                // numeric
                $xml .= sprintf('<c r="%s" s="%d"><v>%s</v></c>', $ref, $s, is_numeric($v) ? (string) $v : '0');
            }
        }
        $xml .= '</row>';
    }
    $xml .= '</sheetData>';

    if ($merges) {
        $xml .= '<mergeCells count="' . count($merges) . '">';
        foreach ($merges as $m) $xml .= '<mergeCell ref="' . $m . '"/>';
        $xml .= '</mergeCells>';
    }

    $xml .= '</worksheet>';
    return $xml;
}

/* ---------- ประกอบข้อมูลของแต่ละชีต ---------- */

function txt($v, int $style = 0): array { return ['v' => $v, 't' => 'inlineStr', 's' => $style]; }
function num($v, int $style = 0): array { return ['v' => (float) $v, 's' => $style]; }
function frm(string $f, int $style = 0): array { return ['v' => $f, 't' => 'formula', 's' => $style]; }

/* ----- Sheet 1: สรุป ----- */
$s1 = [];
$s1[] = [txt('รายงานสรุปลูกค้า', 4)];                          // A1 merge
$s1[] = [];                                                     // 2
$s1[] = [txt('ID ลูกค้า', 5),       txt($customer['id'], 2)];   // 3
$s1[] = [txt('รหัสลูกค้า', 5),     txt($customer['customer_code'], 2)];
$s1[] = [txt('ชื่อลูกค้า', 5),      txt($customer['customer_name'], 2)];
$s1[] = [txt('เลขผู้เสียภาษี', 5), txt($customer['customer_taxid'] ?: '-', 2)];
$s1[] = [txt('ผู้ติดต่อ', 5),       txt($customer['customer_contract_name'] ?: '-', 2)];
$s1[] = [txt('เบอร์โทร', 5),       txt($customer['customer_phone'] ?: '-', 2)];
$s1[] = [txt('อีเมล', 5),           txt($customer['customer_email'] ?: '-', 2)];
$s1[] = [txt('ที่อยู่', 5),          txt($customer['customer_address'] ?: '-', 2)];
$s1[] = [];                                                     // 11
$s1[] = [txt('หัวข้อ', 1),         txt('จำนวน (ใบ)', 1), txt('ยอดรวม (บาท)', 1)];   // 12
$s1[] = [txt('ใบเสนอราคาทั้งหมด', 6), num($quoteCount, 6), num($quoteTotal, 7)];
$s1[] = [txt('   - รอดำเนินการ', 2), num($quoteByStatus['wait']['cnt'], 8),    num($quoteByStatus['wait']['amt'], 3)];
$s1[] = [txt('   - ยืนยันแล้ว', 2),  num($quoteByStatus['success']['cnt'], 8), num($quoteByStatus['success']['amt'], 3)];
$s1[] = [txt('   - ยกเลิก', 2),      num($quoteByStatus['cancel']['cnt'], 8),  num($quoteByStatus['cancel']['amt'], 3)];
$s1[] = [txt('ใบสั่งซื้อทั้งหมด', 6),  num($orderCount, 6), num($orderTotal, 7)];
foreach ($paymentBy as $st => $v) {
    $lbl = '   - ' . ($paymentLabelTh[$st] ?? $st);
    $s1[] = [txt($lbl, 2), num($v['cnt'], 8), num($v['amt'], 3)];
}

$s1Widths = ['A' => 30, 'B' => 22, 'C' => 22];
$s1Merges = ['A1:C1'];

/* ----- Sheet 2: ใบเสนอราคา ----- */
$s2 = [];
$s2[] = [
    txt('ลำดับ', 1), txt('เลขที่ใบเสนอราคา', 1), txt('วันที่', 1), txt('ผู้ขาย', 1),
    txt('สถานะ', 1), txt('ยอดก่อน VAT', 1), txt('VAT', 1), txt('ส่วนลด', 1), txt('ยอดรวมสุทธิ (บาท)', 1),
];
$i = 1;
foreach ($quotationRows as $q) {
    $s2[] = [
        txt($i++, 8),
        txt($q['quote_number'], 2),
        txt($q['quote_date'] ?: '-', 8),
        txt($q['sale_name'] ?: '-', 2),
        txt($statusLabelTh[$q['quote_status']] ?? $q['quote_status'], 8),
        num($q['quote_subtotal'], 3),
        num($q['quote_vat'], 3),
        num($q['quote_discount'], 3),
        num($q['quote_grand_total'], 3),
    ];
}
if ($quoteCount > 0) {
    $lastData = count($s2); // จำนวนแถวข้อมูล + header = แถวรวมอยู่ที่ index นี้
    $sumRow   = $lastData + 1;
    $s2[] = [
        txt('รวม', 6), txt('', 6), txt('', 6), txt('', 6), txt('', 6),
        frm("SUM(F2:F{$lastData})", 7),
        frm("SUM(G2:G{$lastData})", 7),
        frm("SUM(H2:H{$lastData})", 7),
        frm("SUM(I2:I{$lastData})", 7),
    ];
    $s2Merges = ["A{$sumRow}:E{$sumRow}"];
} else {
    $s2Merges = [];
}
$s2Widths = ['A' => 8, 'B' => 22, 'C' => 14, 'D' => 22, 'E' => 16, 'F' => 16, 'G' => 14, 'H' => 14, 'I' => 20];

/* ----- Sheet 3: ใบสั่งซื้อ ----- */
$s3 = [];
$s3[] = [
    txt('ลำดับ', 1), txt('เลขที่ใบสั่งซื้อ', 1), txt('วันที่', 1), txt('อ้างอิงใบเสนอราคา', 1),
    txt('ผู้ขาย', 1), txt('สถานะออเดอร์', 1), txt('สถานะการชำระเงิน', 1),
    txt('ยอดก่อน VAT', 1), txt('VAT', 1), txt('ส่วนลด', 1), txt('ยอดรวมสุทธิ (บาท)', 1),
];
$i = 1;
foreach ($orderRows as $o) {
    $s3[] = [
        txt($i++, 8),
        txt($o['order_number'], 2),
        txt($o['order_date'] ?: '-', 8),
        txt($o['ref_quote_number'] ?: '-', 2),
        txt($o['sale_name'] ?: '-', 2),
        txt($o['order_status'] ?: '-', 2),
        txt($paymentLabelTh[$o['payment_status']] ?? ($o['payment_status'] ?: '-'), 8),
        num($o['order_subtotal'], 3),
        num($o['order_vat'], 3),
        num($o['order_discount'], 3),
        num($o['order_grand_total'], 3),
    ];
}
if ($orderCount > 0) {
    $lastData = count($s3);
    $sumRow   = $lastData + 1;
    $s3[] = [
        txt('รวม', 6), txt('', 6), txt('', 6), txt('', 6), txt('', 6), txt('', 6), txt('', 6),
        frm("SUM(H2:H{$lastData})", 7),
        frm("SUM(I2:I{$lastData})", 7),
        frm("SUM(J2:J{$lastData})", 7),
        frm("SUM(K2:K{$lastData})", 7),
    ];
    $s3Merges = ["A{$sumRow}:G{$sumRow}"];
} else {
    $s3Merges = [];
}
$s3Widths = ['A' => 8, 'B' => 22, 'C' => 14, 'D' => 22, 'E' => 20, 'F' => 20, 'G' => 22, 'H' => 16, 'I' => 14, 'J' => 14, 'K' => 20];

/* ---------- แพ็ก ZIP เป็น .xlsx ---------- */
$outDir  = __DIR__ . '/storage/app/reports';
if (! is_dir($outDir)) mkdir($outDir, 0777, true);
$fileName = 'report-customer-' . $CUSTOMER_ID . '-' . date('Ymd-His') . '.xlsx';
$fullPath = $outDir . '/' . $fileName;

if (is_file($fullPath)) unlink($fullPath);

$zip = new ZipArchive();
if ($zip->open($fullPath, ZipArchive::CREATE) !== true) {
    fwrite(STDERR, "ไม่สามารถสร้างไฟล์ zip ได้: {$fullPath}\n");
    exit(1);
}

// [Content_Types].xml
$zip->addFromString('[Content_Types].xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/worksheets/sheet3.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>
XML);

// _rels/.rels
$zip->addFromString('_rels/.rels', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>
XML);

// xl/workbook.xml
$zip->addFromString('xl/workbook.xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"
          xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="สรุป" sheetId="1" r:id="rId1"/>
    <sheet name="ใบเสนอราคา" sheetId="2" r:id="rId2"/>
    <sheet name="ใบสั่งซื้อ" sheetId="3" r:id="rId3"/>
  </sheets>
</workbook>
XML);

// xl/_rels/workbook.xml.rels
$zip->addFromString('xl/_rels/workbook.xml.rels', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet2.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet3.xml"/>
  <Relationship Id="rId4" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>
XML);

$zip->addFromString('xl/styles.xml',                xlsxStylesXml());
$zip->addFromString('xl/worksheets/sheet1.xml',     buildSheetXml($s1, $s1Widths, $s1Merges));
$zip->addFromString('xl/worksheets/sheet2.xml',     buildSheetXml($s2, $s2Widths, $s2Merges));
$zip->addFromString('xl/worksheets/sheet3.xml',     buildSheetXml($s3, $s3Widths, $s3Merges));

$zip->close();

echo "\nสร้างไฟล์ Excel เรียบร้อย:\n  {$fullPath}\n";
echo "ขนาด: " . number_format(filesize($fullPath)) . " bytes\n";
