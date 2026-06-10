<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>وثيقة الشروط والأحكام وإقرار السرية - {{ $declaration->full_name }}</title>
    <style>
        body {
            font-family: 'tajawal', sans-serif;
            font-size: 12px;
            direction: rtl;
            background: white;
            color: #1a1a1a;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        /* ====== HEADER ====== */
        .doc-header {
            background-color: #f5ede0;
            padding: 15px 25px;
            border-bottom: 3px solid #c9933a;
            margin-bottom: 20px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .header-table td {
            vertical-align: middle;
        }

        .header-logo {
            width: 120px;
            text-align: right;
        }

        .header-logo img {
            height: 60px;
            width: auto;
        }

        .header-text {
            text-align: right;
            padding-right: 15px;
        }

        .main-title {
            font-size: 18px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 5px;
        }

        .main-title .gold { color: #c9933a; }

        .sub-desc {
            font-size: 11px;
            color: #666;
            line-height: 1.5;
        }

        /* ====== FIELDS BOX ====== */
        .fields-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #c8bfaf;
            margin-bottom: 15px;
            table-layout: fixed;
        }

        .fields-table td {
            width: 33.33%;
            padding: 8px 10px;
            border: 1px solid #c8bfaf;
            background: #fdf9f4;
            vertical-align: middle;
        }

        .field-label {
            font-size: 11px;
            font-weight: bold;
            color: #5a4420;
        }

        .field-value {
            font-size: 11px;
            color: #1a1a1a;
            border-bottom: 1px dotted #a08060;
            padding-right: 5px;
            display: inline-block;
        }

        /* ====== DECL BOX ====== */
        .decl-box {
            border: 1.5px solid #c9933a;
            padding: 10px 15px;
            margin-bottom: 15px;
            background: #fffdf8;
            font-size: 11.5px;
            font-weight: bold;
            line-height: 1.7;
            text-align: justify;
        }

        /* ====== TWO-COL ARTICLES ====== */
        .article-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #c8bfaf;
            margin-bottom: 10px;
        }

        .article-table td {
            border: 1px solid #c8bfaf;
        }

        .art-content {
            padding: 10px 12px;
            font-size: 11px;
            line-height: 1.6;
            color: #2a2a2a;
            vertical-align: top;
        }

        .art-label {
            width: 130px;
            vertical-align: top;
            background: #fdf9f4;
            padding: 10px;
            font-size: 10.5px;
            font-weight: bold;
            color: #5a4420;
            line-height: 1.5;
            text-align: right;
        }

        .art-num {
            display: inline-block;
            background: #c9933a;
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 2px;
            margin-bottom: 5px;
        }

        .art-content p { margin-bottom: 5px; margin-top: 0; }
        .art-content .item { margin-bottom: 4px; }
        .art-content b { color: #1a1a1a; }

        /* How box */
        .how-box {
            background: #fdf9f4;
            border: 1px solid #e0d0b0;
            padding: 8px 10px;
            margin-top: 5px;
        }

        /* ====== SIGNATURE SECTION ====== */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #c8bfaf;
            margin-top: 15px;
            table-layout: fixed;
        }

        .sig-table td {
            width: 50%;
            vertical-align: top;
            padding: 12px 15px;
            border: 1px solid #c8bfaf;
        }

        .sig-label {
            font-size: 11.5px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 10px;
        }

        .sig-img {
            max-height: 60px;
            max-width: 180px;
            display: block;
            margin: 0 auto 10px auto;
        }

        .sig-name {
            font-size: 10.5px;
            color: #666;
            text-align: center;
        }

        /* ====== FOOTER ====== */
        .doc-footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid #c9933a;
            text-align: center;
            font-size: 10px;
            color: #888;
        }
    </style>
</head>
<body>

<!-- ====== HEADER ====== -->
<div class="doc-header">
    <table class="header-table">
        <tr>
            <td class="header-text">
                <div class="main-title">
                    وثيقة <span class="gold">الشروط والأحكام</span> وإقرار السرية للمحكمين المستقلين
                </div>
                <div class="sub-desc">
                    تحدد هذه الوثيقة الإطار القانوني والمهني للتعاون بين التطبيق والمحكم المستقل في مجال تثمين السلع المستعملة. يرجى قراءة بنود السرية، المسؤولية المهنية، وآلية العمل بعناية قبل الموافقة والانضمام.
                </div>
            </td>
            <td class="header-logo" style="text-align: left; padding-left: 10px;">
                <img src="{{ public_path('assets/img/Logo.png') }}" alt="ثمن">
            </td>
        </tr>
    </table>
</div>

<!-- ====== BODY ====== -->
<div style="padding: 0 25px;">

    <!-- Fields -->
    <table class="fields-table">
        <tr>
            <td>
                <span class="field-label">اقر أنا/</span>
                <span class="field-value">{{ $declaration->full_name }}</span>
            </td>
            <td>
                <span class="field-label">الجنسية /</span>
                <span class="field-value">{{ $declaration->nationality ?? '.........' }}</span>
            </td>
            <td>
                <span class="field-label">المدينة /</span>
                <span class="field-value">{{ $declaration->city ?? '.........' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="field-label">رقم الهوية /</span>
                <span class="field-value">{{ $declaration->national_id }}</span>
            </td>
            <td>
                <span class="field-label">الجوال/</span>
                <span class="field-value">{{ $declaration->phone }}</span>
            </td>
            <td>
                <span class="field-label">البريد الإلكتروني /</span>
                <span class="field-value">{{ $declaration->email }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <span class="field-label">مجال الخبرة /</span>
                <span class="field-value">{{ $declaration->expertise ?? '.........' }}</span>
            </td>
        </tr>
    </table>

    <!-- Declaration Box -->
    <div class="decl-box">
        الموقع أدناه بالاطلاع على محتوى الوثيقة والإقرار فيها بما ورد فيها بالبنود الواردة في سياق التعاون مع التطبيق في مجال ( التثمين للسلع المستعملة ) وأن يكون مؤهلاً للتحكيم التسعيري للمنتج المستعمل إما بتزكية رسمية أو أكاديمية أو فخرية وعليه إرفاق مايثبت ويعزز ذلك.
    </div>

    <!-- Article 1 -->
    <table class="article-table">
        <tr>
            <td class="art-label">
                <span class="art-num">1</span><br>
                اتفاقية السرية وعدم الإفصاح (NDA) للمحكمين الذين يعملون عن بعد
            </td>
            <td class="art-content">
                <p><b>بند "السرية وحماية البيانات" للمحكمين المستقلين :</b></p>
                <div class="item"><b>(1) السرية وحماية خصوصية البيانات ( Confidentiality & Data Protection )</b><br>
                    • تعريف المعلومات السرية : تشمل المعلومات السرية كل ما يطلع عليه المحكم أثناء عمله، بما في ذلك (بيانات المستخدمين الشخصية، صور المنتجات، فواتير الشراء، تقارير الفحص الفني، آليات التسعير الخاصة بالتطبيق، أو أي معلومات برمجية أو تقنية داخل المنصة ).</div>
                <div class="item">(2) حظر الاستخدام الشخصي: يلتزم المحكم بعدم استخدام أي معلومة حصل عليها بطريقة مباشرة لمنفعته الشخصية أو لمنفعة أطراف ثالثة. كما يُحظر عليه التواصل مع أصحاب المنتجات مباشرة خارج إطار التطبيق لأي سبب كان.</div>
                <div class="item">(3) حظر النسخ والتخزين: يُمنع المحكم منعاً باتاً من أخذ لقطات شاشة (Screenshots) لبيانات المستخدمين أو تحميل وحفظ صور المنتجات والتقارير في أجهزته الشخصية إلا للضرورة القصوى التي يقتضيها التحكيم، ويلتزم بحذفها فور انتهاء العملية.</div>
                <div class="item">(4) أمن الضرورة: المحكم مسؤول مسؤولية كاملة عن سرية بيانات دخوله، وأنه لن يشاركها مع أي جهة.</div>
                <div class="item">(5) مدة الالتزام: يظل هذا البند سارياً طوال مدة تعاقده مع التطبيق، وبعده (3 سنوات) بعد إنهاء العلاقة التعاقدية لأي سبب كان.</div>
                <div class="item">(6) التعويض عن الإفصاح: في حال ثبت تسريب، المحكم ملزم بدفع تعويض يُقدر بـ ( 10,000 ريال )، بالإضافة إلى حق التطبيق في المطالبة بالتعويض عن الأضرار الفعلية.</div>
            </td>
        </tr>
    </table>

    <!-- Article 2 -->
    <table class="article-table">
        <tr>
            <td class="art-label">
                <span class="art-num">2</span><br>
                بنود الشروط العامة
            </td>
            <td class="art-content">
                <p>تأسيس علاقة مهنية مع محكمين بنظام العمل الحر ( Freelance ) يتطلب بنوداً تجمع بين الدقة التقنية والالتزام القانوني. إليك هيكل مقترح للبنود الأساسية:</p>
                <div class="item"><b>(1) معايير التقييم والنزاهة (Technical Integrity):</b><br>
                    • الالتزام بالدليل الإرشادي: يجب أن يعتمد المحكم دائماً الدليل الإرشادي الصادر عن إدارة التطبيق.<br>
                    • دقة البيانات: المحكم مسؤول عن مراجعة الصور والمستندات المرفوعة.<br>
                    • الحيادية: يُمنع التلاعب بالتثمين لرفع أو خفض قيمة المنتج.</div>
                <div class="item"><b>(2) نطاق العمل والمسؤولية (Scope of Work):</b><br>
                    • زمن الاستجابة (SLA): اكتمال الطلب خلال 30 دقيقة من وصول الطلب.<br>
                    • التوثيق: يصدر قرار المحكم توثيقياً في حال ثبت وجود خطأ جسيم.</div>
                <div class="item"><b>(3) السرية وخصوصية البيانات (Confidentiality):</b><br>
                    • حماية البيانات: يحظر على المحكم الاحتفاظ بصور المنتجات أو بيانات المستخدمين.<br>
                    • الملكية الفكرية: جميع التقارير والنتائج هي ملك حصري للتطبيق.</div>
                <div class="item"><b>(4) آلية المحاسبة المالية (Payment Terms):</b><br>
                    • نظام العمولة: تحديد أجر معلوم مقابل كل عملية تحكيم ناجحة ومكتملة.<br>
                    • الخصومات: يحق للتطبيق خصم قيمة العمولة أو إلغائها في حال ثبوت أخطاء.</div>
                <div class="item"><b>(5) بند "العمل الحر" والقانون (Legal Status):</b><br>
                    • استقلالية المحكم: تعاقد مستقل وليس علاقة توظيف.<br>
                    • عدم المنافسة: منع المحكم من العمل مع تطبيقات منافسة.</div>
                <div class="item"><b>(6) إنهاء التعاقد (Termination):</b> يحق للتطبيق إيقاف حساب المحكم فوراً في حالات التجاوزات.</div>
            </td>
        </tr>
    </table>

    <!-- Article 3 -->
    <table class="article-table">
        <tr>
            <td class="art-label">
                <span class="art-num">3</span><br>
                آلية العمل والمستحقات
            </td>
            <td class="art-content">
                يقوم المحكم بالدخول على رابط الانضمام والإقرار بالوثيقة وبعد ذلك يتم تمكينه من استقبال عروض التثمين ومراجعتها ثم تقييم العرض وفق الآلية التثمينية للمنتج بتقدير ثلاث قيم ( الحد الأدنى / السعر العادل / الحد الأعلى ). وسيحصل إزاء كل بطاقة تثمين بهامش عمولة تقدر بـ ( SR 10 ) عن كل بطاقة منجزة بنجاح بعد مرور ( 24 - 48 ساعة ) من عملية التثمين وذلك بتحويلها إلى حسابه المدون في صفحته وفق آلية التطبيق.
            </td>
        </tr>
    </table>

    <!-- Article 4 -->
    <table class="article-table">
        <tr>
            <td class="art-label">
                <span class="art-num">4</span><br>
                المسؤولية المهنية والتعويض
            </td>
            <td class="art-content">
                <div class="item">(1) دقة التقييم : يُقر "المحكم" بأن التقارير والنتائج الصادرة عنه مبنية على أسس مهنية وموضوعية.</div>
                <div class="item">(2) حدود المسؤولية : لا يتحمل المحكم المسؤولية عن العيوب "الخفية" التي لا يمكن كشفها.</div>
                <div class="item">(3) الخطأ المهني الجسيم : في حال ثبت وجود "خطأ مهني جسيم" أدى إلى تثمين المنتج بسعر أعلى أو أقل من قيمته الحقيقية بنسبة تتجاوز (10%) ، يحق للتطبيق تحميل المحكم قيمة الضرر.</div>
                <div class="item">(4) آلية التظلم : يحق للمحكم الاعتراض على قرارات الخصم خلال ( 3 أيام عمل ).</div>
                
                <div class="how-box">
                    <b>كيف يطبق هذا البند تقنياً داخل التطبيق ؟</b><br>
                    • نظام التقييم التراكمي ( Accuracy Score ) : إذا بلغ التقييم أقل من 90% يتم إيقافه.<br>
                    • تجميد الاستحقاقات خلال فترة الاختبار.
                </div>
            </td>
        </tr>
    </table>

    <!-- Signature Section -->
    <table class="sig-table">
        <tr>
            <td>
                <div class="sig-label">توقيع المحكم المستقل :</div>
                <div style="text-align: center; height: 70px;">
                    @if($declaration->signature)
                        <img src="{{ $declaration->signature }}" class="sig-img" alt="توقيع">
                    @endif
                </div>
                <div class="sig-name">{{ $declaration->full_name }}</div>
                <div class="sig-name" style="margin-top:5px; font-size:10px;">{{ $declaration->signed_at->format('Y-m-d H:i') }}</div>
            </td>
            <td>
                <div class="sig-label">ختم ومهر المنصة :</div>
                <div style="text-align: center; height: 70px;">
                    <!-- Platform Stamp Area -->
                </div>
                <div class="sig-name" style="color:#c9933a; font-weight:bold;">منصة ثمن للتثمين المهني</div>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="doc-footer">
        هذه الوثيقة صادرة رسمياً من منصة ثمن للتثمين المهني &copy; {{ date('Y') }} — جميع الحقوق محفوظة
        <br>
        رقم الوثيقة: {{ strtoupper(substr($declaration->token, 0, 16)) }}
    </div>

</div>

</body>
</html>
