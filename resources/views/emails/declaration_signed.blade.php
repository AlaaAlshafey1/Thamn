<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="utf-8">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;700;900&display=swap');
        body {
            font-family: 'Cairo', Arial, sans-serif;
            line-height: 1.8;
            color: #333;
            background-color: #f5ede0;
            padding: 20px;
            direction: rtl;
        }

        .container {
            max-width: 620px;
            margin: 0 auto;
            background: #fff;
            padding: 0;
            border-radius: 16px;
            border: 1px solid #e0d5c5;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #f5ede0, #efe3cc);
            text-align: center;
            padding: 28px 20px;
            border-bottom: 3px solid #c9933a;
        }

        .header h2 {
            font-size: 22px;
            font-weight: 900;
            color: #1a1a1a;
            margin: 0 0 6px;
        }

        .header h2 span {
            color: #c9933a;
        }

        .header p {
            font-size: 13px;
            color: #777;
            margin: 0;
        }

        .content {
            padding: 28px 30px;
            font-size: 15px;
            text-align: right;
        }

        .greeting {
            font-size: 17px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 14px;
        }

        .message-text {
            font-size: 14px;
            color: #444;
            line-height: 2;
            margin-bottom: 20px;
        }

        .info-box {
            background: #fdf9f4;
            border: 1px solid #e8d5b0;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 22px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px dashed #e0d0b8;
            font-size: 14px;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #999;
        }

        .info-value {
            font-weight: 700;
            color: #1a1a1a;
        }

        .notice-box {
            background: #fffbf0;
            border: 1px solid #f0dca0;
            border-radius: 8px;
            padding: 14px 18px;
            margin-bottom: 22px;
            font-size: 13px;
            color: #7a5c1e;
            line-height: 1.9;
        }

        .notice-box strong {
            color: #5a4420;
        }

        .btn-wrap {
            text-align: center;
            margin: 24px 0;
        }

        .btn {
            display: inline-block;
            padding: 14px 36px;
            background: linear-gradient(135deg, #d4af37, #c9933a);
            color: #fff !important;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 800;
            font-size: 15px;
            box-shadow: 0 5px 18px rgba(201, 147, 58, 0.35);
        }

        .footer {
            text-align: center;
            color: #aaa;
            font-size: 12px;
            border-top: 2px solid #c9933a;
            padding: 18px 20px;
            background: #fdf9f4;
        }

        .footer strong {
            color: #c9933a;
        }
    </style>
</head>

<body>
    <div class="container">

        {{-- Header --}}
        <div class="header">
            <h2>الاتفاقية <span>القانونية</span> للتعاون مع الخبير</h2>
            <p>تطبيق ثمن للتثمين المهني</p>
        </div>

        {{-- Content --}}
        <div class="content">
            <div class="greeting">
                السلام عليكم ورحمة الله وبركاته، الخبير / {{ $declaration->full_name }} 👋
            </div>

            <div class="message-text">
                شكراً جزيلاً لانضمامك إلى شبكة خبراء "ثمن" وموافقتك على الاتفاقية القانونية للتعاون لتقديم خدمات التثمين
                للمستفيدين.
                <br><br>
                يسعدنا وجودك معنا، ونتطلع إلى تعاون مثمر وناجح.
                <br>
                تجدون برفقة هذا البريد، <strong>نسختكم المعتمدة من اتفاقية التعاون القانونية</strong> بصيغة PDF.
                يرجى الاحتفاظ بها كمرجع لحقوقك والتزاماتك المهنية.
            </div>

            {{-- Info Summary --}}
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">الاسم الكامل</span>
                    <span class="info-value">{{ $declaration->full_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">البريد الإلكتروني</span>
                    <span class="info-value">{{ $declaration->email }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">تاريخ التوثيق</span>
                    <span class="info-value">{{ $declaration->signed_at->format('d/m/Y - h:i A') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">حالة التوقيع</span>
                    <span class="info-value" style="color: #4CAF50;">✅ موقّع إلكترونياً</span>
                </div>
            </div>

            {{-- Notice --}}
            <div class="notice-box">
                📋 <strong>نسختك المرفقة:</strong>
                الملف المرفق في هذا الإيميل هو نسختك الرسمية. يمكنك تحميلها مباشرة أو الاحتفاظ بهذه الرسالة كمرجع.
            </div>

            {{-- Download Button --}}
            <div class="btn-wrap">
                <a href="{{ $downloadUrl }}" class="btn">⬇️ تحميل نسخة PDF</a>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>هذه الوثيقة صادرة رسمياً من <strong>تطبيق ثمن</strong> للتثمين المهني</p>
            <p>&copy; {{ date('Y') }} جميع الحقوق محفوظة</p>
        </div>

    </div>
</body>

</html>