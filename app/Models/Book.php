<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author_id',
        'price',
        'stock',
        'genre',
        'year',
        'description',
        'image_url',
    ];

    /**
     * Janrlarni umumiy kategoriyalarga guruhlash.
     * Kalit — kategoriya nomi (UI'da ko'rinadi),
     * qiymat — bazadagi janrlar ro'yxati (xom genre qiymatlari).
     *
     * Yangi janr qo'shilsa, mos kategoriyaga qo'shing.
     * Mos kategoriyasi bo'lmagan janrlar avtomatik
     * o'z nomi bilan alohida kategoriya sifatida ko'rinadi.
     */
    public const CATEGORIES = [
        'Roman'                => ['Roman'],
        'Sarguzasht'           => ['Sarguzasht'],
        'Detektiv'             => ['Detektiv'],
        'Antiutopiya'          => ['Antiutopiya'],
        'Falsafa'              => ['Falsafa'],
        'Hikoya va qissa'      => ['Hikoya', 'Qissa'],
        'Shaxsiy rivojlanish'  => ['Shaxsiy rivojlanish'],
        'Biografiya'           => ['Biografiya'],
        'Ilm-fan'              => ['Ilm-fan'],
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * Berilgan kategoriyaga tegishli barcha xom janrlarni qaytaradi.
     * Kategoriya topilmasa, qiymatning o'zini bitta janr sifatida qaytaradi.
     */
    public static function genresForCategory(string $category): array
    {
        return self::CATEGORIES[$category] ?? [$category];
    }

    /**
     * Hozirda bazada kitoblari mavjud bo'lgan kategoriyalar ro'yxati.
     * Mappingda yo'q, ammo bazada uchragan janrlar oxirida alohida ko'rinadi.
     */
    public static function activeCategories(): array
    {
        $existingGenres = static::query()
            ->select('genre')
            ->distinct()
            ->pluck('genre')
            ->all();

        $result = [];

        foreach (self::CATEGORIES as $category => $genres) {
            if (array_intersect($genres, $existingGenres)) {
                $result[] = $category;
            }
        }

        $mappedGenres = array_merge(...array_values(self::CATEGORIES));
        foreach (array_diff($existingGenres, $mappedGenres) as $orphan) {
            $result[] = $orphan;
        }

        return $result;
    }
}
