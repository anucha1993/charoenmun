<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Truck Icons - ระบบจัดการขนส่ง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Sarabun', sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .main-card {
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .card-header-custom {
            background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
            padding: 30px;
            color: white;
        }
        .truck-showcase {
            padding: 25px;
            border: 2px solid #e5e7eb;
            border-radius: 15px;
            margin: 15px 0;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            transition: all 0.3s ease;
        }
        .truck-showcase:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            border-color: #3b82f6;
        }
        .emoji-truck {
            font-size: 3rem;
            filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.1));
        }
        .weight-demo {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin: 10px 0;
            border-left: 4px solid #3b82f6;
        }
        .icon-comparison {
            display: flex;
            align-items: center;
            justify-content: space-around;
            padding: 20px;
            background: white;
            border-radius: 12px;
            margin: 10px 0;
        }
        .section-title {
            color: #1e293b;
            font-weight: 600;
            margin: 30px 0 20px 0;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="card main-card">
                    <div class="card-header-custom text-center">
                        <h1 class="mb-2"><i class="ri-truck-line me-3"></i>ระบบจัดการประเภทรถขนส่ง</h1>
                        <p class="mb-0 opacity-90">แสดงผล Icon และการใช้งานในระบบจริง</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <h2 class="section-title"><i class="ri-palette-line me-2"></i>แสดงผล Icon แต่ละประเภท</h2>
                        
                        <div class="row">
                            @php
                                $trucks = \App\Enums\TruckType::getAllTrucks();
                            @endphp
                            
                            @foreach($trucks as $truck)
                                <div class="col-lg-6 col-xl-3 mb-4">
                                    <div class="truck-showcase text-center">
                                        <div class="emoji-truck mb-3">{{ $truck->icon() }}</div>
                                        <h5 class="fw-bold text-primary">{{ $truck->label() }}</h5>
                                        <p class="text-muted small mb-3">{{ $truck->description() }}</p>
                                        
                                        <div class="mb-2">
                                            {!! truck_type_badge($truck, false) !!}
                                        </div>
                                        <div>
                                            {!! truck_type_badge($truck, true) !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <h2 class="section-title"><i class="ri-scales-3-line me-2"></i>การแนะนำรถตามน้ำหนัก</h2>
                        
                        <div class="row">
                            @php
                                $weightTests = [
                                    ['weight' => 2500, 'description' => 'สินค้าน้ำหนักเบา'],
                                    ['weight' => 4800, 'description' => 'สินค้าน้ำหนักปานกลาง'],
                                    ['weight' => 7200, 'description' => 'สินค้าน้ำหนักหนัก'],
                                    ['weight' => 12000, 'description' => 'สินค้าน้ำหนักหนักมาก'],
                                    ['weight' => 16000, 'description' => 'สินค้าเกินขีดจำกัด']
                                ];
                            @endphp
                            
                            @foreach($weightTests as $test)
                                @php
                                    $recommended = \App\Enums\TruckType::getRecommendedTruck($test['weight']);
                                @endphp
                                <div class="col-md-6 col-xl-4 mb-3">
                                    <div class="weight-demo">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="me-3" style="font-size: 2rem;">
                                                @if($recommended)
                                                    {{ truck_type_icon($recommended) }}
                                                @else
                                                    ❓
                                                @endif
                                            </span>
                                            <div>
                                                <h6 class="mb-1 fw-bold">{{ weight_display($test['weight']) }}</h6>
                                                <small class="text-muted">{{ $test['description'] }}</small>
                                            </div>
                                        </div>
                                        
                                        @if($recommended)
                                            <div class="mb-2">
                                                {!! truck_type_badge($recommended, true) !!}
                                            </div>
                                            <div>
                                                {!! weight_status_badge($test['weight'], $recommended) !!}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <h2 class="section-title"><i class="ri-comparison-line me-2"></i>เปรียบเทียบ Icon รูปแบบต่างๆ</h2>
                        
                        @foreach($trucks as $truck)
                            <div class="icon-comparison">
                                <div class="text-center">
                                    <div class="mb-2" style="font-size: 2.5rem;">{{ $truck->icon() }}</div>
                                    <small class="text-muted">Emoji Icon</small>
                                </div>
                                
                                <div class="text-center">
                                    <div class="mb-2" style="font-size: 2.5rem;">
                                        {!! truck_type_icon_html($truck) !!}
                                    </div>
                                    <small class="text-muted">Font Awesome</small>
                                </div>
                                
                                <div class="text-center">
                                    <div class="mb-2">
                                        {!! truck_type_badge($truck, false) !!}
                                    </div>
                                    <small class="text-muted">Badge Normal</small>
                                </div>
                                
                                <div class="text-center">
                                    <div class="mb-2">
                                        {!! truck_type_badge($truck, true) !!}
                                    </div>
                                    <small class="text-muted">Badge Recommended</small>
                                </div>
                            </div>
                        @endforeach

                        <h2 class="section-title"><i class="ri-table-line me-2"></i>ตัวอย่างการใช้งานในตาราง</h2>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th><i class="ri-scales-line me-1"></i>น้ำหนักรวม</th>
                                        <th><i class="ri-truck-line me-1"></i>รถที่แนะนำ</th>
                                        <th><i class="ri-check-line me-1"></i>สถานะ</th>
                                        <th><i class="ri-information-line me-1"></i>หมายเหตุ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach([1800, 3200, 5500, 8200, 11500, 15000] as $weight)
                                        @php
                                            $recommended = \App\Enums\TruckType::getRecommendedTruck($weight);
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ weight_display($weight) }}</strong>
                                            </td>
                                            <td>
                                                @if($recommended)
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2" style="font-size: 1.3rem;">
                                                            {{ truck_type_icon($recommended) }}
                                                        </span>
                                                        <span>{{ $recommended->label() }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-muted">ไม่ระบุ</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($recommended)
                                                    {!! weight_status_badge($weight, $recommended) !!}
                                                @endif
                                            </td>
                                            <td>
                                                @if($recommended)
                                                    @php
                                                        $capacity = $recommended->capacity();
                                                        $percentage = ($weight / $capacity['max']) * 100;
                                                    @endphp
                                                    <small class="text-muted">
                                                        ใช้งาน {{ number_format($percentage, 1) }}% ของขีดจำกัด
                                                    </small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info mt-4">
                            <h5><i class="ri-lightbulb-line me-2"></i>การใช้งาน</h5>
                            <p class="mb-2">ระบบจะแสดง icon ประเภทรถขนส่งในหน้าต่างๆ ดังนี้:</p>
                            <ul class="mb-0">
                                <li><strong>Order Show:</strong> แสดงในส่วนสรุปการขนส่งและตารางรายการจัดส่ง</li>
                                <li><strong>Transport Summary:</strong> แสดงรถที่แนะนำสำหรับการขนส่งทั้งหมด</li>
                                <li><strong>Delivery Table:</strong> แสดงรถที่แนะนำและรถที่เลือกใช้แต่ละรอบ</li>
                                <li><strong>Weight Status:</strong> แสดงสถานะน้ำหนักเทียบกับขีดจำกัดรถ</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
