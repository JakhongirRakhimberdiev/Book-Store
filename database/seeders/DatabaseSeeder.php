<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $books = [
        [
            'title' => 'O\'tkan kunlar',
            'author' => 'Abdulla Qodiriy',
            'price' => 45000,
            'stock' => 10,
            'genre' => 'Tarixiy roman',
            'year' => 1922,
            'description' => 'Otabek va Kumushning fojiaviy muhabbati haqida hikoya qiluvchi ilk o‘zbek romani.',
            'image_url' => 'images\otkan-kunlar.webp'
        ],
        [
            'title' => 'Sariq devni minib',
            'author' => 'Xudoyberdi To\'xtaboyev',
            'price' => 38000,
            'stock' => 25,
            'genre' => 'Sarguzasht',
            'year' => 1968,
            'description' => 'Hoshimjonning sehrli qalpoqcha yordamida qilgan qiziqarli sarguzashtlari.',
            'image_url' => 'images\sariq-devni-minib.webp'
        ],
        [
            'title' => 'Yulduzli tunlar',
            'author' => 'Pirimqul Qodirov',
            'price' => 52000,
            'stock' => 15,
            'genre' => 'Tarixiy',
            'year' => 1978,
            'description' => 'Zahiriddin Muhammad Bobur hayoti va faoliyati haqida yirik polotno.',
            'image_url' => 'images\yulduzli-tunlar.webp'
        ],
        [
            'title' => 'Al-kimyogar',
            'author' => 'Paulo Koelo',
            'price' => 30000,
            'stock' => 40,
            'genre' => 'Falsafiy',
            'year' => 1988,
            'description' => 'O\'z orzulari ortidan dunyoni kezib chiqqan Santyago hikoyasi.',
            'image_url' => 'images\al-kimyogar.webp'
        ],
        [
            'title' => 'Ikki eshik orasi',
            'author' => 'O\'tkir Hoshimov',
            'price' => 42000,
            'stock' => 20,
            'genre' => 'Roman',
            'year' => 1986,
            'description' => 'Urush va undan keyingi davr insonlar taqdiri, sadoqat va xiyonat haqida.',
            'image_url' => 'images\ikki-eshik-orasi.webp'
        ],
        [
            'title' => 'Dunyoning ishlari',
            'author' => 'O\'tkir Hoshimov',
            'price' => 35000,
            'stock' => 30,
            'genre' => 'Qissa',
            'year' => 1982,
            'description' => 'Ona siymosi va uning cheksiz mehri tarannum etilgan qalb amri asari.',
            'image_url' => 'images\dunyoning-ishlari.webp'
        ],
        [
            'title' => 'Kecha va kunduz',
            'author' => 'Cho\'lpon',
            'price' => 40000,
            'stock' => 12,
            'genre' => 'Badiiy',
            'year' => 1936,
            'description' => 'Zebining fojiali taqdiri orqali davr muammolari yoritilgan asar.',
            'image_url' => 'images\kecha-va-kunduz.webp'
        ],
        [
            'title' => 'Shaytanat',
            'author' => 'Tohir Malik',
            'price' => 150000,
            'stock' => 8,
            'genre' => 'Detektiv',
            'year' => 1994,
            'description' => 'Jinoyat olami va insoniylik kurashi haqidagi mashhur asar.',
            'image_url' => 'images\shaytanat.webp'
        ],
        [
            'title' => 'Kichkina shahzoda',
            'author' => 'Antuan de Sent-Ekzyuperi',
            'price' => 25000,
            'stock' => 50,
            'genre' => 'Ertak-falsafa',
            'year' => 1943,
            'description' => 'Bolalik nigohi bilan hayot haqiqatlarini anglash haqida.',
            'image_url' => 'images\kichkina-shahzoda.webp'
        ],
        [
            'title' => 'Molxona',
            'author' => 'Jorj Oruell',
            'price' => 28000,
            'stock' => 35,
            'genre' => 'Antiutopiya',
            'year' => 1945,
            'description' => 'Jamiyat va hokimiyatning o\'zgarishi haqidagi ramziy asar.',
            'image_url' => 'images\molxona.webp'
        ],
        [
            'title' => '1984',
            'author' => 'Jorj Oruell',
            'price' => 32000,
            'stock' => 18,
            'genre' => 'Antiutopiya',
            'year' => 1949,
            'description' => 'Totalitar tuzum va shaxs erkinligi cheklanishi haqida ogohlantiruvchi asar.',
            'image_url' => 'images\1984.webp'
        ],
        [
            'title' => 'Graf Monte-Kristo',
            'author' => 'Aleksandr Dyuma',
            'price' => 120000,
            'stock' => 10,
            'genre' => 'Sarguzasht',
            'year' => 1844,
            'description' => 'Xiyonat, qamoq va adolatli qasos hikoyasi.',
            'image_url' => 'images\graf-monte-kristo.webp'
        ],
        [
            'title' => 'Jinoyat va jazo',
            'author' => 'Fyodor Dostoyevskiy',
            'price' => 55000,
            'stock' => 14,
            'genre' => 'Psixologik roman',
            'year' => 1866,
            'description' => 'Raskolnikovning vijdon azobi va gunohidan qutulish yo\'li.',
            'image_url' => 'images\jinoyat-va-jazo.webp'
        ],
        [
            'title' => 'Boy ota, kambag\'al ota',
            'author' => 'Robert Kiyosaki',
            'price' => 45000,
            'stock' => 60,
            'genre' => 'Biznes/Moliya',
            'year' => 1997,
            'description' => 'Moliyaviy erkinlik va investitsiya asoslari haqida qo\'llanma.',
            'image_url' => 'images\boy-ota-kambagal-ota.webp'
        ],
        [
            'title' => 'Stiv Jobs',
            'author' => 'Uolter Ayzekson',
            'price' => 85000,
            'stock' => 5,
            'genre' => 'Biografiya',
            'year' => 2011,
            'description' => 'Apple asoschisining hayoti va innovatsion g\'oyalari tarixi.',
            'image_url' => 'images\stiv-jobs.webp'
        ],
        [
            'title' => 'Sapiens: Insoniyatning qisqacha tarixi',
            'author' => 'Yuval Noy Xarari',
            'price' => 70000,
            'stock' => 22,
            'genre' => 'Ilmiy-ommabop',
            'year' => 2011,
            'description' => 'Insoniyatning paydo bo\'lishidan hozirgi kungacha bosib o\'tgan yo\'li.',
            'image_url' => 'images\sapiens.webp'
        ],
        [
            'title' => 'Muvaffaqiyatli insonlarning 7 ko\'nikmasi',
            'author' => 'Stiven Kovi',
            'price' => 50000,
            'stock' => 15,
            'genre' => 'Shaxsiy rivojlanish',
            'year' => 1989,
            'description' => 'Samaradorlikni oshirish va maqsadlarga erishish qoidalari.',
            'image_url' => 'images\muvaffaqiyatli-insonlarning-7-konikmasi.webp'
        ],
        [
            'title' => 'Sherlok Xolms sarguzashtlari',
            'author' => 'Artur Konan Doyl',
            'price' => 48000,
            'stock' => 25,
            'genre' => 'Detektiv',
            'year' => 1892,
            'description' => 'Dunyodagi eng mashhur detektivning mantiqiy sirlarni ochishi.',
            'image_url' => 'images\sherlok-xolmsning-sarguzashtlari.webp'
        ],
        [
            'title' => 'Martin Iden',
            'author' => 'Jek London',
            'price' => 38000,
            'stock' => 12,
            'genre' => 'Roman',
            'year' => 1909,
            'description' => 'Oddiy dengizchining yozuvchi bo\'lish yo\'lidagi mashaqqatlari va sevgisi.',
            'image_url' => 'images\martin-iden.webp'
        ],
        [
            'title' => 'Dahshat',
            'author' => 'Abdulla Qahhor',
            'price' => 22000,
            'stock' => 10,
            'genre' => 'Hikoya',
            'year' => 1933,
            'description' => 'Inson ruhiyatidagi qo\'rquv va ijtimoiy muhitning ta\'siri haqida.',
            'image_url' => 'images\daxshat.webp'
        ]
    ];

    foreach ($books as $book) {
        \App\Models\Book::create($book);
    }
    }
}
