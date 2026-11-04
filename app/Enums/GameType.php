<?php

namespace App\Enums;

use App\Games\Handlers\GameTypeHandler;
use App\Games\Handlers\GapHandler;
use App\Games\Handlers\LabelHandler;
use App\Games\Handlers\MatchHandler;
use App\Games\Handlers\MultipleChoiceHandler;
use App\Games\Handlers\PictureHandler;
use App\Games\Handlers\RiddleHandler;
use App\Games\Handlers\TrueFalseHandler;
use App\Games\Handlers\WordSearchHandler;

enum GameType: string
{
    case Multiple = 'multiple';
    case Gap = 'gap';
    case Picture = 'picture';
    case TrueFalse = 'truefalse';
    case Match = 'match';
    case Label = 'label';
    case WordSearch = 'wordsearch';
    case Riddle = 'riddle';

    /** @return class-string<GameTypeHandler> */
    public function handlerClass(): string
    {
        return match ($this) {
            self::Multiple => MultipleChoiceHandler::class,
            self::Gap => GapHandler::class,
            self::Picture => PictureHandler::class,
            self::TrueFalse => TrueFalseHandler::class,
            self::Match => MatchHandler::class,
            self::Label => LabelHandler::class,
            self::WordSearch => WordSearchHandler::class,
            self::Riddle => RiddleHandler::class,
        };
    }

    public function handler(): GameTypeHandler
    {
        return app($this->handlerClass());
    }

    public function label(): string
    {
        return match ($this) {
            self::Multiple => 'არჩევანი',
            self::Gap => 'ჩასვი სიტყვა',
            self::Picture => 'სურათი',
            self::TrueFalse => 'მართალია თუ არა',
            self::Match => 'შეუხამე წყვილები',
            self::Label => 'დაასახელე ნაწილი',
            self::WordSearch => 'სიტყვების ძებნა',
            self::Riddle => 'გამოცანა',
        };
    }
}
