<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ทดสอบ Icon ประเภทรถ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Sarabun', Arial, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .truck-demo {
            padding: 20px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            margin: 10px 0;
            background: white;
        }
        .emoji-large {
            font-size: 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0"><i class="fas fa-truck me-2"></i>ทดสอบ Icon ประเภทรถขนส่ง</h2>
                    </div>
                    <div class="card-body">
                        @php
                            $trucks = \App\Enums\TruckType::getAllTrucks();
                        @endphp

                        <h4>🚛 แบบ Emoji Icons</h4>
                        <div class="row">
                            @foreach($trucks as $truck)
                                <div class="col-md-6 mb-3">
                                    <div class="truck-demo">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="emoji-large me-3">{{ $truck->icon() }}</span>
                                            <div>
                                                <h5 class="mb-1">{{ $truck->label() }}</h5>
                                                <small class="text-muted">{{ $truck->description() }}</small>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            {!! truck_type_badge($truck, false) !!}
                                            {!! truck_type_badge($truck, true) !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <hr class="my-4">

                        <h4><i class="fas fa-icons me-2"></i>แบบ Font Awesome Icons</h4>
                        <div class="row">
                            @foreach($trucks as $truck)
                                <div class="col-md-6 mb-3">
                                    <div class="truck-demo">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="me-3" style="font-size: 2rem;">
                                                {!! truck_type_icon_html($truck) !!}
                                            </span>
                                            <div>
                                                <h5 class="mb-1">{{ $truck->label() }}</h5>
                                                <small class="text-muted">{{ $truck->description() }}</small>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <span class="me-2">{!! truck_type_icon_html($truck, true) !!}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <hr class="my-4">

                        <h4>🎯 ตัวอย่างการใช้งานจริง</h4>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>น้ำหนัก</th>
                                        <th>รถที่แนะนำ</th>
                                        <th>Badge</th>
                                        <th>สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $testWeights = [2500, 4500, 7000, 12000, 16000];
                                    @endphp
                                    @foreach($testWeights as $weight)
                                        @php
                                            $recommended = \App\Enums\TruckType::getRecommendedTruck($weight);
                                        @endphp
                                        <tr>
                                            <td>{{ weight_display($weight) }}</td>
                                            <td>
                                                @if($recommended)
                                                    {{ truck_type_icon($recommended, true) }}
                                                @else
                                                    <span class="text-muted">ไม่ระบุ</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($recommended)
                                                    {!! truck_type_badge($recommended, true) !!}
                                                @endif
                                            </td>
                                            <td>
                                                @if($recommended)
                                                    {!! weight_status_badge($weight, $recommended) !!}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
