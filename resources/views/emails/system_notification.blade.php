<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Cairo', Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f9f9f9; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 15px; border: 1px solid #eee; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .header { text-align: center; margin-bottom: 30px; }
        .header img { height: 60px; }
        .content { margin-bottom: 30px; font-size: 16px; text-align: right; }
        .footer { text-align: center; color: #999; font-size: 12px; border-top: 1px solid #eee; padding-top: 20px; }
        .btn { display: inline-block; padding: 12px 25px; background-color: #c1953e; color: #fff !important; text-decoration: none; border-radius: 8px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>منصة ثمن</h2>
        </div>
        <div class="content">
            <p>{!! nl2br(e($messageBody)) !!}</p>
            @if($actionUrl)
                <div style="text-align: center;">
                    <a href="{{ $actionUrl }}" class="btn">اضغط هنا للمتابعة</a>
                </div>
            @endif
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} منصة ثمن. جميع الحقوق محفوظة.</p>
        </div>
    </div>
</body>
</html>
