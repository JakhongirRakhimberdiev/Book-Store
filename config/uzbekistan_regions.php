<?php

/**
 * Yetkazib berish: viloyat (yoki shahar/stat) va tumanlar.
 * Kodlar sahifada `<select>` uchun ishlatiladi.
 */
return [
    'regions' => [
        ['code' => 'toshkent_sh', 'name' => 'Toshkent shahri'],
        ['code' => 'toshkent_v', 'name' => 'Toshkent viloyati'],
        ['code' => 'andijon', 'name' => 'Andijon viloyati'],
        ['code' => 'buxoro', 'name' => 'Buxoro viloyati'],
        ['code' => 'fargona', 'name' => "Farg'ona viloyati"],
        ['code' => 'jizzax', 'name' => 'Jizzax viloyati'],
        ['code' => 'xorazm', 'name' => 'Xorazm viloyati'],
        ['code' => 'namangan', 'name' => 'Namangan viloyati'],
        ['code' => 'navoiy', 'name' => 'Navoiy viloyati'],
        ['code' => 'qashqadaryo', 'name' => 'Qashqadaryo viloyati'],
        ['code' => 'qoraqalpoq', 'name' => "Qoraqalpog'iston Respublikasi"],
        ['code' => 'samarqand', 'name' => 'Samarqand viloyati'],
        ['code' => 'sirdaryo', 'name' => 'Sirdaryo viloyati'],
        ['code' => 'surxondaryo', 'name' => 'Surxondaryo viloyati'],
    ],

    /**
     * @var array<string, list<string>>
     */
    'districts' => [
        'toshkent_sh' => [
            'Bektemir tumani', 'Chilonzor tumani', 'Mirobod tumani',
            'Mirzo Ulugʻbek tumani', 'Olmazor tumani', 'Sergeli tumani',
            'Shayxontohur tumani', 'Uchtepa tumani', 'Yakkasaroy tumani',
            'Yangihayot tumani', 'Yashnobod tumani', 'Yunusobod tumani',
        ],
        'toshkent_v' => [
            'Angren shahri', 'Bekobod shahri', 'Boʻka tumani', 'Boʻstonliq tumani',
            'Chinoz tumani', 'Ohangaron shahri', 'Oqqoʻrgʻon tumani', 'Parkent tumani',
            'Piskent tumani', 'Qibray tumani', 'Quyichirchiq tumani', 'Oʻrta Chirchiq tumani',
            'Yangiyoʻl shahri', 'Yuqorichirchiq tumani', 'Zangiota tumani',
        ],
        'andijon' => [
            'Andijon shahri', 'Asaka tumani', 'Baliqchi tumani', 'Boʻz tumani',
            'Buloqboshi tumani', 'Izboskan tumani', 'Jalaquduq tumani',
            'Marhamat tumani', 'Oltinkoʻl tumani', 'Paxtaobod tumani',
            'Shahrixon tumani', 'Ulugʻnor tumani', 'Xoʻjaobod tumani',
        ],
        'buxoro' => [
            'Buxoro shahri', 'Gʻijduvon tumani', 'Jondor tumani', 'Kogon tumani',
            'Olot tumani', 'Peshku tumani', 'Qorakoʻl tumani', 'Qorovulbozor tumani',
            'Romitan tumani', 'Shofirkon tumani', 'Vobkent tumani',
        ],
        'fargona' => [
            'Fargʻona shahri', 'Beshariq tumani', 'Bogʻdod tumani', 'Buvayda tumani',
            'Dangʻara tumani', 'Furqat tumani', 'Margʻilon shahri', 'Oʻzbekiston tumani',
            'Quva tumani', 'Rishton tumani', 'Soʻx tumani', 'Toshloq tumani',
            'Uchkoʻprik tumani', 'Yozyovon tumani',
        ],
        'jizzax' => [
            'Jizzax shahri', 'Arnasoy tumani', 'Baxmal tumani', 'Doʻstlik tumani',
            'Forish tumani', 'Gʻallaorol tumani', 'Jizzax tumani', 'Mirzachoʻl tumani',
            'Paxtakor tumani', 'Yangiobod tumani', 'Zafarobod tumani', 'Zarbdor tumani',
        ],
        'xorazm' => [
            'Urganch shahri', 'Bogʻot tumani', 'Gurlan tumani', 'Hazorasp tumani',
            'Xonqa tumani', 'Xiva shahri', 'Shovot tumani', 'Yangiariq tumani',
            'Yangibozor tumani',
        ],
        'namangan' => [
            'Namangan shahri', 'Chortoq tumani', 'Chust tumani', 'Kosonsoy tumani',
            'Mingbuloq tumani', 'Namangan tumani', 'Norin tumani', 'Pop tumani',
            'Toʻraqoʻrgʻon tumani', 'Uychi tumani', 'Yangiqoʻrgʻon tumani',
        ],
        'navoiy' => [
            'Navoiy shahri', 'Konimex tumani', 'Karmana tumani', 'Qiziltepa tumani',
            'Navbahor tumani', 'Nurota tumani', 'Tomdi tumani', 'Uchquduq tumani',
            'Xatirchi tumani', 'Zarafshon shahri',
        ],
        'qashqadaryo' => [
            'Qarshi shahri', 'Chiroqchi tumani', 'Dehqonobod tumani', 'Gʻuzor tumani',
            'Kasbi tumani', 'Kitob tumani', 'Koson tumani', 'Muborak tumani',
            'Nishon tumani', 'Qamashi tumani', 'Shahrisabz shahri', 'Yakkabogʻ tumani',
        ],
        'qoraqalpoq' => [
            'Nukus shahri', 'Amudaryo tumani', 'Beruniy tumani', 'Boʻzatov tumani',
            'Chimboy tumani', 'Ellikqalʻa tumani', 'Kegeyli tumani', 'Moʻynoq tumani',
            'Nukus tumani', 'Qanlikoʻl tumani', 'Qońirqala radaryo tumani', 'Qoraoʻzak tumani',
            'Shumanay tumani', 'Taxtakupir tumani', 'Toʻrtkuʻl tumani', 'Xoʻjayli tumani',
        ],
        'samarqand' => [
            'Samarqand shahri', 'Bulungʻur tumani', 'Ishtixon tumani', 'Jomboy tumani',
            'Kattaqoʻrgʻon shahri', 'Narpay tumani', 'Nurobod tumani', 'Oqdaryo tumani',
            'Pastdargʻom tumani', 'Paxtachi tumani', 'Payariq tumani', 'Samarqand tumani',
            'Toyloq tumani', 'Urgut tumani',
        ],
        'sirdaryo' => [
            'Guliston shahri', 'Boyovut tumani', 'Guliston tumani', 'Mirzaobod tumani',
            'Oqoltin tumani', 'Sayxunobod tumani', 'Sardoba tumani', 'Sirdaryo tumani',
            'Xovos tumani', 'Yangiyer shahri',
        ],
        'surxondaryo' => [
            'Termiz shahri', 'Angor tumani', 'Bandixon tumani', 'Boysun tumani',
            'Denov tumani', 'Jarqoʻrgʻon tumani', 'Muzrabot tumani', 'Oltinsoy tumani',
            'Qiziriq tumani', 'Qumqoʻrgʻon tumani', 'Sariosiyo tumani', 'Sherobod tumani',
            'Shoʻrchi tumani', 'Termiz tumani', 'Uzun tumani',
        ],
    ],
];
