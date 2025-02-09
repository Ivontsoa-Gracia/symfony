<?php
namespace App\Enum;

enum DetailStatu:string {
    case FINI='fini';
    case RECUPERER='recuperer';
    case EN_COURS='en cours';
}