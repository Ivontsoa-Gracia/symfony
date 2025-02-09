<?php
namespace App\Enum;

enum StatuCommande: string {
    case EN_COURS = 'EN_COURS';
    case EN_ATTENTE = 'EN_ATTENTE';
    case FINI = 'FINI';
    case LIVRER = 'LIVRER';
}