<?php

namespace App\Enums;

enum DocumentType: string
{
    case DNI = 'D.N.I.';
    case CUIT = 'C.U.I.T.';
    case CUIL = 'C.U.I.L.';
}
