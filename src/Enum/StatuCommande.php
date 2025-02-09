<?php
namespace App\Enum;

enum StatuCommande: string {
    case EN_COURS = 'EN_COURS';
    case EN_ATTENTE = 'EN_ATTENTE';
    case FINI = 'FINI';
    case LIVRER = 'LIVRER';

    public static function getChoices(): array
    {
        return [
            'EN_COURS' => self::EN_COURS->value,
            'In EN_ATTENTE' => self::EN_ATTENTE->value,
            'FINI' => self::FINI->value,
            'LIVRER' => self::LIVRER->value,
        ];
    }

    public static function getValidStatuses(): array
    {
        return [
            self::EN_COURS->value,
            self::EN_ATTENTE->value,
            self::FINI->value,
            self::LIVRER->value,
        ];
    }

    public static function fromString(string $value): self
    {
        return match ($value) {
            'EN_COURS' => self::EN_COURS,
            'In Progress' => self::EN_ATTENTE,
            'FINI' => self::FINI,
            'FINI' => self::LIVRER,
            default => throw new \InvalidArgumentException("Invalid status: $value"),
        };
    }
}