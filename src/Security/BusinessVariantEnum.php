<?php

namespace App\Security;

enum BusinessVariantEnum: string
{
    case SOLE_TRADER = "sole-trader";
    case LTD = "limited-liability-company";
    case LLP = "limited-liability-partnership";

}
