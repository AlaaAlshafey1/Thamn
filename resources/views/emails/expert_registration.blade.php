<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.8;
            color: #333;
            background-color: #f9f7f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .header {
            background: linear-gradient(135deg, #8B6914, #c1953e, #D4AF37);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header img {
            width: 80px;
            height: auto;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
        }
        .header p {
            margin: 8px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 35px 30px;
            text-align: right;
        }
        .content h3 {
            color: #8B6914;
            margin-bottom: 5px;
            font-size: 18px;
        }
        .content p {
            margin: 10px 0;
            color: #555;
            font-size: 15px;
        }
        .steps {
            background: #faf6ee;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .steps h4 {
            color: #8B6914;
            margin: 0 0 12px;
            font-size: 15px;
        }
        .step-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 10px;
            font-size: 14px;
            color: #555;
        }
        .step-num {
            background: #D4AF37;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            flex-shrink: 0;
        }
        .footer {
            text-align: center;
            padding: 25px 30px;
            background: #faf6ee;
            border-top: 1px solid #eee;
        }
        .footer p {
            font-size: 13px;
            color: #888;
            margin: 5px 0;
        }
        .footer a {
            color: #8B6914;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🎉 مرحباً بك في فريق خبراء ثمن!</h2>
            <p>شكراً لتسجيلك كخبير تقييم معتمد</p>
        </div>
        <div class="content">
            <h3>أهلاً {{ $user->first_name }} {{ $user->last_name }}،</h3>
            <p>شكراً لك على ثقتك في منصة <strong>ثمن</strong> وتسجيلك كخبير تقييم معنا. نحن سعداء جداً بانضمامك لفريقنا المتميز.</p>
            <p>لقد استلمنا طلبك بنجاح وسيقوم فريقنا المختص بمراجعة بياناتك ومؤهلاتك في أقرب وقت ممكن.</p>
            
            <div style="background: #fdfdfd; border: 1px dashed #D4AF37; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <h4 style="margin: 0 0 10px; color: #8B6914;">بيانات الدخول الأولية:</h4>
                <p style="margin: 5px 0;"><strong>البريد الإلكتروني:</strong> {{ $user->email }}</p>
                <p style="margin: 5px 0;"><strong>كلمة المرور:</strong> {{ $password }}</p>
                <p style="font-size: 12px; color: #888; margin-top: 10px;">* يمكنك تغيير كلمة المرور بعد تفعيل حسابك من لوحة التحكم.</p>
            </div>

            <div class="steps">
                <h4>ماذا بعد؟</h4>
                <div class="step-item">
                    <div class="step-num">1</div>
                    <div>مراجعة بياناتك ومؤهلاتك من قبل فريقنا</div>
                </div>
                <div class="step-item">
                    <div class="step-num">2</div>
                    <div>التواصل معك للتأكد من المعلومات إذا لزم الأمر</div>
                </div>
                <div class="step-item">
                    <div class="step-num">3</div>
                    <div>تفعيل حسابك والبدء في استلام طلبات التقييم</div>
                </div>
            </div>

            <p>سنقوم بالرد عليك خلال <strong>48 ساعة</strong> عبر البريد الإلكتروني أو الجوال المُسجل.</p>
            <p style="color:#8B6914;font-weight:600;">نتطلع للعمل معك 🚀</p>
        </div>
        <div class="footer">
            <p>فريق <strong>ثمن</strong> للتقييم الذكي</p>
            <p>لأي استفسار تواصل معنا: <a href="mailto:info@package.sa">info@package.sa</a></p>
            <p style="font-size:11px;color:#aaa;margin-top:15px;">تم إرسال هذا البريد تلقائياً من منصة ثمن.</p>
        </div>
    </div>
</body>
</html>
