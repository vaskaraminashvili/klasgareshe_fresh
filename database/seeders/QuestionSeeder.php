<?php

namespace Database\Seeders;

use App\Enums\GameType;
use App\Models\Game;
use App\Models\Question;
use Illuminate\Database\Seeder;

/**
 * Starter content. Maths questions are generated for every level 1-10; the other types are
 * hand-written for several levels and the selector falls back to the nearest level that has any.
 * Skipped when questions already exist so re-seeding never wipes session history.
 */
class QuestionSeeder extends Seeder
{
    private const FILL_LETTERS = 'აბგდევზთიკლმნოპჟრსტუფქღყშჩცძწჭხჯჰ';

    /** @var array<string, int> */
    private array $gameIds = [];

    public function run(): void
    {
        if (Question::exists()) {
            return;
        }

        $this->gameIds = Game::pluck('id', 'type')->mapWithKeys(fn ($id, $type) => [$type instanceof GameType ? $type->value : $type => $id])->all();

        foreach (range(1, 10) as $level) {
            foreach (range(1, 12) as $i) {
                [$text, $answer] = $this->mathExpression($level);
                $this->add('multiple', $level, 'რამდენია '.$text.'?', [
                    'options' => $this->numberOptions($answer, $level),
                    'answer' => 0,
                ], "სწორია! $text = $answer.", "სწორი პასუხია $answer, რადგან $text = $answer.");
            }

            foreach (range(1, 10) as $i) {
                [$text, $answer] = $this->mathExpression($level);
                $isTrue = (bool) random_int(0, 1);
                $shown = $isTrue ? $answer : $answer + [-2, -1, 1, 2][random_int(0, 3)] * max(1, intdiv($level, 4));
                $this->add('truefalse', $level, "$text = $shown", ['answer' => $isTrue],
                    'სწორია! '.($isTrue ? "$text = $answer." : "ეს არასწორია, $text = $answer."),
                    'ეს '.($isTrue ? 'მართალია' : 'არასწორია').": $text = $answer.");
            }
        }

        $this->handWritten();
    }

    /** @return array{0: string, 1: int} */
    private function mathExpression(int $level): array
    {
        return match ($level) {
            1 => $this->sum(1, 10),
            2 => $this->sum(5, 20),
            3 => $this->difference(5, 20),
            4 => $this->product(2, 5),
            5 => $this->product(2, 10),
            6 => $this->sum(20, 99),
            7 => $this->difference(30, 99),
            8 => $this->product(11, 15, 3, 9),
            9 => $this->quotient(),
            default => $this->mixed(),
        };
    }

    /** @return array{0: string, 1: int} */
    private function sum(int $min, int $max): array
    {
        $a = random_int($min, $max);
        $b = random_int($min, $max);

        return ["$a + $b", $a + $b];
    }

    /** @return array{0: string, 1: int} */
    private function difference(int $min, int $max): array
    {
        [$a, $b] = [random_int($min, $max), random_int(1, $min)];

        return ["$a − $b", $a - $b];
    }

    /** @return array{0: string, 1: int} */
    private function product(int $min, int $max, int $bMin = 2, int $bMax = 9): array
    {
        $a = random_int($min, $max);
        $b = random_int($bMin, $bMax);

        return ["$a × $b", $a * $b];
    }

    /** @return array{0: string, 1: int} */
    private function quotient(): array
    {
        $b = random_int(2, 12);
        $answer = random_int(2, 12);

        return [($b * $answer).' ÷ '.$b, $answer];
    }

    /** @return array{0: string, 1: int} */
    private function mixed(): array
    {
        $a = random_int(3, 12);
        $b = random_int(3, 12);
        $c = random_int(5, 40);

        return ["$a × $b + $c", $a * $b + $c];
    }

    /**
     * The right answer first (shuffled per session); wrong ones sit closer to it on higher levels.
     *
     * @return list<string>
     */
    private function numberOptions(int $answer, int $level): array
    {
        $spread = max(2, 6 - intdiv($level, 2)); // at least 2, or there are fewer than 3 wrong values to pick
        $options = [$answer];

        while (count($options) < 4) {
            $candidate = $answer + random_int(-$spread, $spread) + ($level > 5 ? 0 : random_int(0, 2));

            if ($candidate >= 0 && ! in_array($candidate, $options, true)) {
                $options[] = $candidate;
            }
        }

        return array_map('strval', $options);
    }

    private function handWritten(): void
    {
        foreach ([
            [1, 'ღამით ცაზე ანათებს', '.', ['მთვარე', 'თოვლი', 'ნიჟარა'], 'მთვარე'],
            [1, 'ძაღლი ყეფს, კატა კი', '.', ['კნავის', 'ყეფს', 'ხვიხვინებს'], 'კნავის'],
            [4, 'საქართველოს დედაქალაქია', '.', ['თბილისი', 'ბათუმი', 'ქუთაისი'], 'თბილისი'],
            [7, 'წყალი ყინულად იქცევა, როცა ძალიან', 'ხდება.', ['ცივი', 'ცხელი', 'მსუბუქი'], 'ცივი'],
            [10, 'ფოტოსინთეზის დროს მცენარე შთანთქავს', 'და გამოყოფს ჟანგბადს.', ['ნახშირორჟანგს', 'აზოტს', 'წყალბადს'], 'ნახშირორჟანგს'],
        ] as [$level, $before, $after, $words, $answer]) {
            $this->add('gap', $level, 'აირჩიე სიტყვა, რომელიც ჯდება გამოტოვებულ ადგილას.',
                compact('before', 'after', 'words', 'answer'),
                "სწორია! სწორი სიტყვაა „{$answer}“.", "სწორი სიტყვაა „{$answer}“.");
        }

        foreach ([
            [1, 'i-sun', false, 'მზე', ['მზე', 'მთვარე', 'ვარსკვლავი', 'ღრუბელი'], 'ყვითელი წრე სხივებით.'],
            [2, 'i-rocket', true, 'რაკეტა', ['რაკეტა', 'თვითმფრინავი', 'ხომალდი', 'ნავი'], 'მაღალი გემი ფრთებითა და ალით.'],
            [4, 'i-saturn', true, 'სატურნი', [['label' => 'სატურნი', 'icon' => 'i-saturn'], ['label' => 'მთვარე', 'icon' => 'i-moon'], ['label' => 'მზე', 'icon' => 'i-sun'], ['label' => 'კომეტა', 'icon' => 'i-comet']], 'მრგვალი ფორმა გრძელი რგოლით.'],
            [7, 'i-volcano', true, 'ვულკანი', [['label' => 'კუნძული', 'icon' => 'i-island'], ['label' => 'უდაბნო', 'icon' => 'i-desert'], ['label' => 'ვულკანი', 'icon' => 'i-volcano'], ['label' => 'აისბერგი', 'icon' => 'i-glacier']], 'წვეტიანი მთა, რომლის თავზე კვამლი ადის.'],
            [10, 'i-earth', false, 'დედამიწა', ['დედამიწა', 'მარსი', 'ვენერა', 'იუპიტერი'], 'ლურჯი პლანეტა მწვანე კონტინენტებით.'],
        ] as [$level, $picture, $shadow, $correct, $options, $alt]) {
            $labels = array_map(fn ($o) => is_array($o) ? $o['label'] : $o, $options);
            $this->add('picture', $level, 'რა არის სურათზე?', [
                'picture' => $picture, 'shadow' => $shadow, 'alt' => $alt, 'options' => $options, 'answer' => array_search($correct, $labels, true),
            ], "სწორია! ეს არის: $correct.", "სწორი პასუხია: $correct.");
        }

        $plant = [['x' => 50, 'y' => 20, 'label' => 'ყვავილი'], ['x' => 33, 'y' => 47, 'label' => 'ფოთოლი'], ['x' => 50, 'y' => 64, 'label' => 'ღერო'], ['x' => 50, 'y' => 88, 'label' => 'ფესვები']];
        foreach ([[2, [$plant[0], $plant[3]]], [5, $plant], [8, [$plant[3], $plant[1], $plant[0], $plant[2]]]] as [$level, $spots]) {
            $this->add('label', $level, 'დაასახელე წერტილით მონიშნული ნაწილი.', [
                'picture' => 'i-plant', 'alt' => 'მცენარე: ყვავილი ზემოთ, ფოთლები ღეროზე და ფესვები მიწის ქვეშ.', 'spots' => $spots,
            ], 'ყველა ნაწილი დაასახელე! ფესვები სვამს, ღერო ატარებს, ფოთლები საკვებს ამზადებს.', 'ცოტა ივარჯიშე და ყველა ნაწილს ისწავლი!');
        }

        $pairs = [
            1 => [['2 + 3', '5'], ['4 × 2', '8'], ['10 − 7', '3']],
            3 => [['2 + 3', '5'], ['4 × 2', '8'], ['10 − 7', '3'], ['6 + 6', '12']],
            5 => [['საქართველო', 'თბილისი'], ['საფრანგეთი', 'პარიზი'], ['იაპონია', 'ტოკიო'], ['იტალია', 'რომი']],
            7 => [['საქართველო', 'თბილისი'], ['საფრანგეთი', 'პარიზი'], ['იაპონია', 'ტოკიო'], ['იტალია', 'რომი'], ['ეგვიპტე', 'კაირო']],
            10 => [['საქართველო', 'თბილისი'], ['საფრანგეთი', 'პარიზი'], ['იაპონია', 'ტოკიო'], ['იტალია', 'რომი'], ['ეგვიპტე', 'კაირო'], ['გერმანია', 'ბერლინი']],
        ];
        foreach ($pairs as $level => $set) {
            $this->add('match', $level, 'შეუხამე ერთმანეთს შესაბამისი წყვილები.', ['pairs' => $set],
                'ყველა წყვილი სწორად შეუხამე. ბრავო!', 'ყველა შეუხამე! ცოტა ივარჯიშე და გამოგივა.');
        }

        foreach ([
            [1, 4, ['კატა'], [[0, 1]]],
            [3, 5, ['მზე', 'ზღვა'], [[0, 1], [1, 0]]],
            [5, 6, ['მზე', 'მთვარე', 'ცა'], [[0, 1], [1, 0], [1, 1]]],
            [8, 7, ['კომეტა', 'რაკეტა', 'სატურნი', 'მარსი'], [[0, 1], [1, 0], [1, 1], [1, -1]]],
            [10, 8, ['დედამიწა', 'იუპიტერი', 'ვენერა', 'ნეპტუნი', 'მერკური'], [[0, 1], [1, 0], [1, 1], [1, -1]]],
        ] as [$level, $size, $words, $directions]) {
            $this->add('wordsearch', $level, 'იპოვე დამალული სიტყვები: '.implode(', ', $words).'.',
                ['grid' => $this->buildGrid($words, $size, $directions), 'words' => $words],
                'ყველა სიტყვა იპოვე! მშვენიერია!', 'ყველა სიტყვა იპოვე. ცოტა ივარჯიშე და გამოგივა!');
        }

        foreach ([
            [1, ['ცაში ვფრინავ.', 'ფრთები მაქვს და ბუდეში ვცხოვრობ.', 'ვმღერი.'], ['ჩიტი', 'თევზი', 'ძაღლი', 'ცხენი'], 0, 'ეს ჩიტია!'],
            [2, ['ოთხი ფეხი მაქვს და ბეწვი.', 'მიყვარს რძე და თაგვების დაჭერა.', 'ვკნავი.'], ['კატა', 'ძაღლი', 'ფრინველი', 'თევზი'], 0, 'ეს კატაა!'],
            [5, ['პლანეტა ვარ და მზის გარშემო ვბრუნავ.', 'ჩემი უმეტესი ნაწილი წყლით არის დაფარული.', 'შენ ჩემზე ცხოვრობ!'], [['label' => 'მთვარე', 'icon' => 'i-moon'], ['label' => 'დედამიწა', 'icon' => 'i-earth'], ['label' => 'სატურნი', 'icon' => 'i-saturn'], ['label' => 'მზე', 'icon' => 'i-sun']], 1, 'ეს დედამიწაა!'],
            [8, ['ბუნებრივი თანამგზავრი ვარ.', 'დედამიწის გარშემო ვტრიალებ.', 'ღამით ვანათებ, მაგრამ საკუთარი სინათლე არ მაქვს.'], ['მთვარე', 'მზე', 'კომეტა', 'მარსი'], 0, 'ეს მთვარეა!'],
            [10, ['ვარსკვლავი ვარ.', 'ჩემგან მოდის სინათლე და სითბო.', 'დედამიწა ჩემს გარშემო ბრუნავს.'], ['მზე', 'მთვარე', 'სირიუსი', 'იუპიტერი'], 0, 'ეს მზეა!'],
        ] as [$level, $clues, $options, $answer, $praise]) {
            $this->add('riddle', $level, 'ვინ ვარ მე?', compact('clues', 'options', 'answer'), $praise, $praise.' მინიშნებები გამოგადგა.');
        }

        foreach ([[1, 'მზე ვარსკვლავია.', true], [3, 'თევზი ჰაერით სუნთქავს ხმელეთზე.', false], [6, 'დედამიწა მზის გარშემო ბრუნავს.', true], [9, 'ბაყაყი ქვეწარმავალია.', false]] as [$level, $statement, $answer]) {
            $this->add('truefalse', $level, $statement, ['answer' => $answer],
                'სწორია! '.($answer ? 'ეს მართალია.' : 'ეს არასწორია.'), 'ეს '.($answer ? 'მართალია.' : 'არასწორია.'));
        }
    }

    /**
     * Places the words (frontwards or backwards) along the allowed directions and fills the rest.
     *
     * @param  list<string>  $words
     * @param  list<array{0: int, 1: int}>  $directions  [row step, column step]
     * @return list<string>
     */
    private function buildGrid(array $words, int $size, array $directions): array
    {
        for ($attempt = 0; $attempt < 100; $attempt++) {
            if ($grid = $this->placeWords($words, $size, $directions)) {
                return $grid;
            }
        }

        throw new \RuntimeException("Could not fit the words in a {$size}x{$size} grid.");
    }

    /**
     * @param  list<string>  $words
     * @param  list<array{0: int, 1: int}>  $directions
     * @return list<string>|null null when the words painted themselves into a corner
     */
    private function placeWords(array $words, int $size, array $directions): ?array
    {
        $grid = array_fill(0, $size, array_fill(0, $size, ''));

        foreach ($words as $word) {
            $letters = mb_str_split($word);

            for ($try = 0; $try < 500; $try++) {
                [$dr, $dc] = $directions[array_rand($directions)];
                $sequence = random_int(0, 1) ? $letters : array_reverse($letters);
                $length = count($sequence);
                $row = random_int(0, $size - 1);
                $col = random_int(0, $size - 1);
                $endRow = $row + $dr * ($length - 1);
                $endCol = $col + $dc * ($length - 1);

                if ($endRow < 0 || $endRow >= $size || $endCol < 0 || $endCol >= $size) {
                    continue;
                }

                $fits = true;
                foreach ($sequence as $k => $letter) {
                    $cell = $grid[$row + $dr * $k][$col + $dc * $k];
                    $fits = $fits && ($cell === '' || $cell === $letter);
                }

                if ($fits) {
                    foreach ($sequence as $k => $letter) {
                        $grid[$row + $dr * $k][$col + $dc * $k] = $letter;
                    }

                    continue 2;
                }
            }

            return null;
        }

        $fill = mb_str_split(self::FILL_LETTERS);

        return array_map(
            fn (array $row) => implode('', array_map(fn (string $cell) => $cell === '' ? $fill[array_rand($fill)] : $cell, $row)),
            $grid,
        );
    }

    /** @param  array<string, mixed>  $payload */
    private function add(string $type, int $level, string $prompt, array $payload, string $praise, string $hint): void
    {
        Question::create([
            'game_id' => $this->gameIds[$type],
            'difficulty' => $level,
            'prompt' => $prompt,
            'payload' => $payload,
            'praise' => $praise,
            'hint' => $hint,
        ]);
    }
}
