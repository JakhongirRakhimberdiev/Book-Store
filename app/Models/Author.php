<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'birth_date',
        'death_date',
        'birth_place',
        'nationality',
        'biography',
        'legacy',
        'image_url',
    ];

    protected $appends = ['age', 'is_alive'];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    /**
     * Muallif vafot etganmi yoki tirikmi.
     */
    protected function isAlive(): Attribute
    {
        return Attribute::get(fn () => empty($this->death_date));
    }

    /**
     * Muallifning umri (yillar bilan).
     * - Vafot etgan bo'lsa: tug'ilgan va vafot sanalari farqi.
     * - Tirik bo'lsa: tug'ilgan sana bilan bugungi kun farqi.
     * - Tug'ilgan sana noma'lum bo'lsa: null.
     *
     * To'liq sana ("DD.MM.YYYY") berilgan bo'lsa, tug'ilgan kun o'sha
     * yili kelganmi-yo'qligini ham hisobga oladi.
     */
    protected function age(): Attribute
    {
        return Attribute::get(function () {
            $birth = $this->parseDate($this->birth_date);
            if ($birth === null) {
                return null;
            }

            if (!empty($this->death_date)) {
                $death = $this->parseDate($this->death_date);
                return $death ? $this->yearsBetween($birth, $death) : null;
            }

            $today = [
                'year'  => (int) date('Y'),
                'month' => (int) date('n'),
                'day'   => (int) date('j'),
            ];

            return $this->yearsBetween($birth, $today);
        });
    }

    /**
     * Sana satrini massivga aylantiradi.
     * Formatlar: "DD.MM.YYYY", "YYYY-MM-DD", "YYYY".
     */
    private function parseDate(?string $date): ?array
    {
        if (empty($date)) {
            return null;
        }

        if (preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{4})$/', $date, $m)) {
            return ['day' => (int) $m[1], 'month' => (int) $m[2], 'year' => (int) $m[3]];
        }

        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $date, $m)) {
            return ['year' => (int) $m[1], 'month' => (int) $m[2], 'day' => (int) $m[3]];
        }

        if (preg_match('/^(\d{4})$/', $date, $m)) {
            return ['year' => (int) $m[1], 'month' => null, 'day' => null];
        }

        return null;
    }

    /**
     * Ikki sana orasidagi to'liq yillar sonini hisoblaydi.
     * Agar ikkalasida ham oy/kun bor bo'lsa va tug'ilgan kun hali kelmagan
     * bo'lsa, yoshdan 1 yilni ayiradi.
     */
    private function yearsBetween(array $start, array $end): int
    {
        $years = $end['year'] - $start['year'];

        $hasFullStart = !empty($start['month']) && !empty($start['day']);
        $hasFullEnd   = !empty($end['month'])   && !empty($end['day']);

        if ($hasFullStart && $hasFullEnd) {
            $birthdayPassed = $end['month'] > $start['month']
                || ($end['month'] === $start['month'] && $end['day'] >= $start['day']);
            if (!$birthdayPassed) {
                $years--;
            }
        }

        return $years;
    }
}
