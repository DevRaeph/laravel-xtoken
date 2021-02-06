<?php
/*
 * ██████╗ ███████╗██╗   ██╗███████╗████████╗ ██████╗ ██████╗ ███╗   ███╗
 * ██╔══██╗██╔════╝██║   ██║██╔════╝╚══██╔══╝██╔═══██╗██╔══██╗████╗ ████║
 * ██║  ██║█████╗  ██║   ██║███████╗   ██║   ██║   ██║██████╔╝██╔████╔██║
 * ██║  ██║██╔══╝  ╚██╗ ██╔╝╚════██║   ██║   ██║   ██║██╔══██╗██║╚██╔╝██║
 * ██████╔╝███████╗ ╚████╔╝ ███████║   ██║   ╚██████╔╝██║  ██║██║ ╚═╝ ██║
 * ╚═════╝ ╚══════╝  ╚═══╝  ╚══════╝   ╚═╝    ╚═════╝ ╚═╝  ╚═╝╚═╝     ╚═╝
 * ______________________________________________________________________
 * | Author:    DevStorm Solutions - rplan
 * | Project:   ds-laravel-jwttoken-project
 * | File:      XToken.php
 * | Created:   06.02.2021
 * | Todo:
 * |_____________________________________________________________________
 */

namespace DevRaeph\XToken\Facades;

use Illuminate\Support\Facades\Facade;
/**
 * Class Tokenizer
 *
 * @mixin \DevRaeph\XToken\Tokenizer
 *
 * @package DevRaeph\XToken\Facades
 */
class Tokenizer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'tokenizer';
    }
}
