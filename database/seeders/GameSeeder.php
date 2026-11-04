<?php

namespace Database\Seeders;

use App\Enums\GameType;
use App\Models\Game;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /** Tile art comes from the icon sprite already in the UI. */
    private const ICONS = [
        'multiple' => 'i-maths',
        'gap' => 'i-english',
        'picture' => 'i-island',
        'truefalse' => 'i-thumb-up',
        'match' => 'i-geo',
        'label' => 'i-plant',
        'wordsearch' => 'i-moon',
        'riddle' => 'i-star',
    ];

    private const DESCRIPTIONS = [
        'multiple' => 'აირჩიე სწორი პასუხი ოთხიდან.',
        'gap' => 'ჩასვი გამოტოვებულ ადგილას შესაბამისი სიტყვა.',
        'picture' => 'შეხედე სურათს და იპოვე სწორი პასუხი.',
        'truefalse' => 'გადაწყვიტე, მართალია თუ არა მოცემული წინადადება.',
        'match' => 'შეუხამე ერთმანეთს შესაბამისი წყვილები.',
        'label' => 'დაასახელე სურათზე მონიშნული ნაწილი.',
        'wordsearch' => 'იპოვე ასოების ბადეში დამალული სიტყვები.',
        'riddle' => 'გამოიცანი, ვინ ვარ მე. მინიშნებები გეხმარება!',
    ];

    public function run(): void
    {
        foreach (GameType::cases() as $sort => $type) {
            Game::updateOrCreate(['type' => $type->value], [
                'name' => $type->label(),
                'description' => self::DESCRIPTIONS[$type->value],
                'config' => ['icon' => self::ICONS[$type->value], 'accent' => 'a'.($sort % 4 + 1), 'questions_per_session' => 10],
                'sort' => $sort,
            ]);
        }
    }
}
