<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages.
    |
    */

    'accepted' => 'پێویستە فیلد پەسەند بکرێت attribute:.',
    'active_url' => "فیلد attribute: ناونیشانێک نیە URL دروستە.",
    'after' => 'دەبێت فیلدەکە بێت attribute: بەرواری دوای... date:.',
    'after_or_equal' => 'دەبێت مەیدانەکە بێت attribute: تبەرواری دواتر یان یەکسانە بە date:.',
    'alpha' => 'فیلدەکە پێویستە لەخۆبگرێت attribute: تەنها پیتەکان.',
    'alpha_dash' => 'دەبێت فیلدیکی تێدابێت attribute: تەنها پیت و ژمارە و هێڵکاری تێدایە.',
    'alpha_num' => 'فیلدەکە دەبێت لەخۆبگرێت attribute:تەنها ژمارە و پیت.',
    'array' => 'دەبێت فیلدەکە بێت attribute: ماتریکس.',
    'before' => 'پێویستە فیلدەکە attribute: بەرواری پێشتر: date:.',
    'before_or_equal' => 'دەبێت فیلدەکە بێت attribute: تبەروار زووترە یان یەکسانە بە...: date:.',
    'between' => [
        'numeric' => 'نرخەکە دەبێت مەودای خۆی بێت attribute: نێوان min: و max:.',
        'file' => 'قەبارەی فایلەکە دەبێت... attribute: نێوان min: و max: كيلوبايت .',
        'string' => 'دەقەکە دەبێت لە نێوانی دا بێت min: و max: پیت.',
        'array' => 'دەبێت ماتریکسەکە لەخۆ بگرێت attribute: نێوان min: و max: عنصر.',
    ],
    'boolean' => 'تایبەتمەندی خانە پێویستە attribute: ڕاست یان هەڵە.',
    'confirmed' => 'خانەی دووپاتکردنەوە attribute: ناگونجێت.',
    'date' => "فیلد attribute: لبەروارێکی دروست نییە.",
    'date_format' => 'خانە ناگونجێت attribute: لەگەڵ فۆرماتکردن format:.',
    'different' => 'خانەکان attribute: و other: دەبێت جیاواز بێت.',
    'digits' => 'فیلدەکە پێویستە لەخۆبگرێت digits: رقم(ژمارەکان)).',
    'digits_between' => 'فیلدەکە دەبێت لە نێوانی دا بێت min: و max:  رقم(ژمارەکان).',
    'dimensions' => "قەبارەی وێنە attribute: ناگونجێت.",
    'distinct' => 'خانەکە پێکدێت لە attribute: لەسەر بەهایەکی دووبارە.',
    'email' => 'پێویستە بوارەکە attribute: ناونیشانی ئیمەیڵ.',
    'exists' => 'الحقل attribute: المحدد غير صالح.',
    'file' => 'يجب أن يكون الحقل attribute: ملفًا.',
    'filled' => 'يجب أن يحتوي الحقل attribute: على قيمة.',
    'image' => 'يجب أن يكون الحقل attribute: صورة.',
    'in' => 'الحقل attribute: غير صالح.',
    'in_array' => "الحقل attribute: غير موجود في other:.",
    'integer' => 'يجب أن يكون الحقل attribute: عددًا صحيحًا.',
    'ip' => 'يجب أن يكون الحقل attribute: عنوان IP صالح.',
    'ipv4' => 'يجب أن يكون الحقل attribute: عنوان IPv4 صالح.',
    'ipv6' => 'يجب أن يكون الحقل attribute: عنوان IPv6 صالح.',
    'json' => 'يجب أن يكون الحقل attribute: مستند JSON صالحً.',
    'max' => [
        'numeric' => 'لا يمكن أن تكون قيمة attribute: أكبر من max:.',
        'file' => 'لا يمكن أن يتعدى حجم الملف attribute: (السمة القصوى) max: كيلوبايت .',
        'string' => 'لا يمكن أن يحتوي النص attribute: على أكثر من max: حرف(أحرف).',
        'array' => 'لا يمكن أن تحتوي المصفوفة attribute: على أكثر من max: عنصر.',
    ],
    'mimes' => 'يجب أن يكون الحقل attribute: ملفًا من نوع values:.',
    'mimetypes' => 'يجب أن يكون الحقل attribute: ملفًا من نوع values:.',
    'min' => [
        'numeric' => 'يجب أن تكون قيمة attribute: أكبر من أو تساوي min:.',
        'file' => 'يجب أن يكون حجم ملف attribute: أكبر من min: كيلوبايت.',
        'string' => 'يجب أن يحتوي النص  على الأقل min: حرف(أحرف).',
        'array' => 'ماتریکسەکە دەبێت لەخۆبگرێت attribute: بەلایەنی کەمەوە min: بڕگە(کان)',
    ],
    'not_in' => "خانەکان attribute: دەسنیشانکەری نادروست.",
    'numeric' => 'فیلدەکە پێویستە لەخۆبگرێت attribute: لەسەر ژمارە.',
    'present' => 'يجب أن يكون الحقل attribute: بەردەستە.',
    'regex' => 'فۆرماتی خانە attribute: نادروستە',
    'required' => 'ئەم خانەیە پێویستە.',
    'required_if' => 'خانەکان attribute: ناچاری کاتێک بەهای other: ئەوە value:.',
    'required_unless' => 'خانەکان attribute: ناچاری مەگەر other: ئەوە values:.',
    'required_with' => 'خانەکان attribute: ئیجباری کاتێک کە تۆ values: بەردەستە.',
    'required_with_all' => 'خانەکان attribute: ئیجباری کاتێک کە تۆ... values: بەردەستە',
    'required_without' => "خانەکان attribute: ئیجباری کاتێک کە تۆ values: بوونی نییە",
    'required_without_all' => "خانەکان attribute: ئیجباری کاتێک کە تۆ... values: بوونی نییە",
    'same' => 'خانەکان attribute: و other: دەبێت هەمان شت بێت.',
    'size' => [
        'numeric' => 'دەبێت بەنرخی هەبێت attribute: ئەو size:.',
        'file' => 'قەبارەی فایل دەبێت attribute: ئەو size: كيلوبايت.',
        'string' => 'دەقەکە دەبێت لەخۆبگرێت attribute: لەسەر size: پیت(أحرف).',
        'array' => 'ماتریکسەکە پێویستە لەخۆبگرێت attribute: لەسەر size: بڕگە(کان).',
    ],
    'string' => 'پێویستە بوارەکە attribute: زنجیرەی پیتەکان.',
    'timezone' => 'پێویستە کاتەکە attribute: ناوچەی کاتیی دروست.',
    'unique' => ' خانە attribute: پێشتر بەکارهاتووە.',
    'uploaded' => "خانەی فایل attribute: ناتوانرێت دابەزێنرێت",
    'url' => " ناونیشان URL لـ attribute: نادروستە.",

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'name' => 'ناو',
        'username' => "ناوی بەکارهێنەر",
        'email' => 'ئیمەیل',
        'first_name' => 'ناو',
        'last_name' => 'ناوی باوک',
        'password' => 'وشەی نهێنی',
        'password_confirmation' => 'پشتڕاستکردنەوەی وشەی نهێنی',
        'city' => 'شار',
        'country' => 'وڵات',
        'address' => 'ناونیشان',
        'phone' => 'مۆبایل',
        'mobile' => 'مۆبایل',
        'age' => 'تەمەن',
        'sex' => 'رەگەز',
        'gender' => 'رەگەز',
        'day' => 'رۆژ',
        'month' => 'مانگ',
        'year' => 'ساڵ',
        'hour' => 'سەعات',
        'minute' => 'خولەک',
        'second' => 'چرکە',
        'title' => 'ناونیشان',
        'content' => 'ناوەڕۆک',
        'description' => 'وەسف',
        'excerpt' => 'دەرهێنان',
        'date' => 'بەروار',
        'time' => 'کات',
        'available' => 'بەردەستە',
        'size' => 'قەبارە',
    ],
];